<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Demands extends Admin_Controller
{
    public function index()
    {
        $q = trim((string)$this->input->get('q')); $st = $this->input->get('status');
        $page = max(1, (int)$this->input->get('page')); $per = 20;
        $b = function () use ($q, $st) {
            $this->db->from('demand_orders t');
            if ($q !== '') $this->db->group_start()->like('t.demand_no', $q)->or_like('t.name', $q)->or_like('t.product_name', $q)->or_like('t.phone', $q)->group_end();
            if ($st) $this->db->where('t.status', $st);
        };
        $b(); $total = $this->db->count_all_results();
        $b(); $rows = $this->db->order_by('t.id', 'DESC')->limit($per, ($page - 1) * $per)->get()->result();
        $this->render('admin/demands', array('rows' => $rows, 'total' => $total, 'page' => $page, 'pages' => (int)ceil($total / $per), 'q' => $q, 'st' => $st), 'Demand Orders');
    }

    public function view($id = 0)
    {
        $d = $this->db->get_where('demand_orders', array('id' => (int)$id))->row();
        if (!$d) show_404();
        $p = $d->product_id ? $this->db->get_where('products', array('id' => $d->product_id))->row() : null;
        $this->render('admin/demand_view', array('d' => $d, 'p' => $p), 'Demand ' . $d->demand_no);
    }

    public function update($id = 0)
    {
        if ($this->input->method() !== 'post') show_404();
        $st = $this->input->post('status');
        if (!in_array($st, demand_statuses())) $st = 'pending';
        $q = $this->input->post('quote_price');
        $this->db->update('demand_orders', array(
            'status' => $st, 'quote_price' => $q !== '' ? (float)$q : null,
            'eta_days' => $this->input->post('eta_days') !== '' ? (int)$this->input->post('eta_days') : null,
            'admin_note' => $this->input->post('admin_note', true), 'updated_at' => date('Y-m-d H:i:s'),
        ), array('id' => (int)$id));
        flash('success', 'Demand request updated.');
        redirect('admin/demands/view/' . (int)$id);
    }

    public function delete($id = 0)
    {
        if ($this->input->method() !== 'post') show_404();
        $d = $this->db->get_where('demand_orders', array('id' => (int)$id))->row();
        if ($d) { $this->delete_upload($d->reference_image); $this->db->delete('demand_orders', array('id' => $d->id)); }
        flash('success', 'Request deleted.');
        redirect('admin/demands');
    }
}
