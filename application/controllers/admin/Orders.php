<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Orders extends Admin_Controller
{
    public function index()
    {
        $q = trim((string)$this->input->get('q')); $st = $this->input->get('status'); $src = $this->input->get('source');
        $page = max(1, (int)$this->input->get('page')); $per = 20;
        $b = function () use ($q, $st, $src) {
            $this->db->from('orders t');
            if ($q !== '') $this->db->group_start()->like('t.order_no', $q)->or_like('t.name', $q)->or_like('t.phone', $q)->or_like('t.email', $q)->group_end();
            if ($st) $this->db->where('t.status', $st);
            if ($src) $this->db->where('t.source', $src);
        };
        $b(); $total = $this->db->count_all_results();
        $b(); $rows = $this->db->order_by('t.id', 'DESC')->limit($per, ($page - 1) * $per)->get()->result();
        $this->render('admin/orders', array('rows' => $rows, 'total' => $total, 'page' => $page, 'pages' => (int)ceil($total / $per), 'q' => $q, 'st' => $st, 'src' => $src), 'Orders');
    }

    public function view($id = 0)
    {
        $o = $this->db->get_where('orders', array('id' => (int)$id))->row();
        if (!$o) show_404();
        $items = $this->db->get_where('order_items', array('order_id' => $o->id))->result();
        $this->render('admin/order_view', array('o' => $o, 'items' => $items), 'Order ' . $o->order_no);
    }

    public function update($id = 0)
    {
        if ($this->input->method() !== 'post') show_404();
        $o = $this->db->get_where('orders', array('id' => (int)$id))->row();
        if (!$o) show_404();
        $st = $this->input->post('status'); $pay = $this->input->post('payment_status');
        if (!in_array($st, order_statuses())) $st = $o->status;
        if (!in_array($pay, array('unpaid', 'paid', 'refunded'))) $pay = $o->payment_status;
        // restock when an order is cancelled
        if ($st === 'cancelled' && $o->status !== 'cancelled') {
            foreach ($this->db->get_where('order_items', array('order_id' => $o->id, 'item_type' => 'product'))->result() as $it) {
                if ($it->product_id) $this->db->set('stock', 'stock + ' . (int)$it->qty, false)->set('sold_count', 'GREATEST(sold_count - ' . (int)$it->qty . ',0)', false)->where('id', $it->product_id)->update('products');
            }
        }
        if ($o->status === 'cancelled' && $st !== 'cancelled') {
            foreach ($this->db->get_where('order_items', array('order_id' => $o->id, 'item_type' => 'product'))->result() as $it) {
                if ($it->product_id) $this->db->set('stock', 'GREATEST(stock - ' . (int)$it->qty . ',0)', false)->set('sold_count', 'sold_count + ' . (int)$it->qty, false)->where('id', $it->product_id)->update('products');
            }
        }
        $this->db->update('orders', array('status' => $st, 'payment_status' => $pay, 'admin_note' => $this->input->post('admin_note', true), 'updated_at' => date('Y-m-d H:i:s')), array('id' => $o->id));
        flash('success', 'Order updated.');
        redirect('admin/orders/view/' . $o->id);
    }

    public function invoice($id = 0)
    {
        $o = $this->db->get_where('orders', array('id' => (int)$id))->row();
        if (!$o) show_404();
        $items = $this->db->get_where('order_items', array('order_id' => $o->id))->result();
        $this->load->view('admin/invoice', array('o' => $o, 'items' => $items));
    }

    public function delete($id = 0)
    {
        if ($this->input->method() !== 'post') show_404();
        $this->db->delete('order_items', array('order_id' => (int)$id));
        $this->db->delete('orders', array('id' => (int)$id));
        flash('success', 'Order deleted.');
        redirect('admin/orders');
    }
}
