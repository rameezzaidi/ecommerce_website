<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Checkout extends Front_Controller
{
    protected function pay_methods()
    {
        $m = array();
        if (setting('pay_cod', '1') === '1') $m['cod'] = array('Cash on Delivery', 'Pay in cash when your order arrives.', '');
        if (setting('pay_bank', '1') === '1') $m['bank'] = array('Bank Transfer', 'Transfer the total and send us the receipt.', setting('bank_details', ''));
        if (setting('pay_wallet', '1') === '1') $m['wallet'] = array('Easypaisa / JazzCash', 'Send payment to our mobile wallet account.', setting('wallet_details', ''));
        return $m;
    }

    protected function totals($items, $cc)
    {
        $sub = $this->shopcart->subtotal($items);
        list($disc, $code) = $this->shopcart->discount($sub);
        $ship = $this->shopcart->shipping($sub - $disc, $cc);
        return array('subtotal' => $sub, 'discount' => $disc, 'coupon' => $code, 'shipping' => $ship, 'total' => $sub - $disc + $ship);
    }

    public function index()
    {
        $items = $this->shopcart->items();
        if (!$items) { flash('info', 'Your bag is empty.'); redirect('cart'); }
        $c = $this->customer();
        $cc = $c && $c->country ? array_search($c->country, country_list()) : 'PK';
        $geo = $this->tracker->current_geo();
        if (!$c && isset(country_list()[$geo['country_code']])) $cc = $geo['country_code'];
        if (!$cc) $cc = 'PK';

        $this->render('front/checkout', array(
            'items' => $items, 't' => $this->totals($items, $cc), 'cc' => $cc, 'methods' => $this->pay_methods(),
            'old' => $this->session->flashdata('old') ?: array(), 'cust' => $c,
        ), 'Checkout');
    }

    /** AJAX: recalc shipping when the country changes */
    public function quote()
    {
        $cc = strtoupper((string)$this->input->post('country'));
        if (!isset(country_list()[$cc])) $cc = 'PK';
        $items = $this->shopcart->items();
        $t = $this->totals($items, $cc);
        $this->json(array('ok' => true, 'shipping' => $t['shipping'], 'shipping_txt' => $t['shipping'] > 0 ? money($t['shipping']) : 'Free', 'total_txt' => money($t['total'])));
    }

    public function place()
    {
        if ($this->input->method() !== 'post') show_404();
        $items = $this->shopcart->items();
        if (!$items) redirect('cart');

        $this->load->library('form_validation');
        $this->form_validation->set_rules('name', 'Full name', 'required|max_length[120]');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        $this->form_validation->set_rules('phone', 'Phone', 'required|min_length[7]|max_length[25]');
        $this->form_validation->set_rules('address', 'Address', 'required|max_length[250]');
        $this->form_validation->set_rules('city', 'City', 'required|max_length[80]');
        $this->form_validation->set_rules('country', 'Country', 'required|exact_length[2]');
        $methods = $this->pay_methods();
        $this->form_validation->set_rules('payment_method', 'Payment method', 'required|in_list[' . implode(',', array_keys($methods)) . ']');

        if (!$this->form_validation->run()) {
            flash('error', strip_tags(validation_errors(' ', ' ')));
            $this->session->set_flashdata('old', $this->input->post());
            redirect('checkout');
        }

        $cc = strtoupper($this->input->post('country'));
        if (!isset(country_list()[$cc])) $cc = 'PK';

        // stock re-check
        foreach ($items as $i) {
            if ($i['type'] === 'product') {
                $p = $this->db->get_where('products', array('id' => $i['product_id']))->row();
                if (!$p || $p->stock < $i['qty']) {
                    flash('error', 'Sorry, "' . $i['name'] . '" just went low on stock. Please review your bag.');
                    redirect('cart');
                }
            }
        }

        $t = $this->totals($items, $cc);
        $hasPkg = false; $hasProd = false;
        foreach ($items as $i) { $i['type'] === 'package' ? $hasPkg = true : $hasProd = true; }

        $orderNo = '';
        do {
            $orderNo = 'DR' . date('ymd') . mt_rand(1000, 9999);
        } while ($this->db->where('order_no', $orderNo)->count_all_results('orders') > 0);

        $c = $this->customer();
        $this->db->trans_start();
        $this->db->insert('orders', array(
            'order_no' => $orderNo, 'customer_id' => $c ? $c->id : null,
            'name' => $this->input->post('name', true), 'email' => $this->input->post('email', true), 'phone' => $this->input->post('phone', true),
            'address' => $this->input->post('address', true), 'city' => $this->input->post('city', true), 'state' => $this->input->post('state', true),
            'postal_code' => $this->input->post('postal_code', true), 'country' => country_list()[$cc], 'country_code' => $cc,
            'payment_method' => $this->input->post('payment_method'), 'payment_status' => 'unpaid', 'status' => 'pending',
            'order_type' => ($hasPkg && $hasProd) ? 'mixed' : ($hasPkg ? 'custom' : 'standard'),
            'subtotal' => $t['subtotal'], 'discount' => $t['discount'], 'coupon_code' => $t['coupon'], 'shipping' => $t['shipping'], 'total' => $t['total'],
            'notes' => $this->input->post('notes', true), 'source' => $this->tracker->current_source(), 'ip' => $this->input->ip_address(),
            'created_at' => date('Y-m-d H:i:s'),
        ));
        $oid = $this->db->insert_id();
        foreach ($items as $i) {
            $this->db->insert('order_items', array(
                'order_id' => $oid, 'product_id' => $i['product_id'], 'item_type' => $i['type'], 'name' => $i['name'], 'image' => $i['image'],
                'size' => $i['size'], 'color' => $i['color'], 'price' => $i['unit_price'], 'qty' => $i['qty'], 'line_total' => $i['line_total'],
                'meta' => $i['meta'] ? json_encode($i['meta']) : null,
            ));
            if ($i['type'] === 'product') {
                $this->db->set('stock', 'GREATEST(stock - ' . (int)$i['qty'] . ', 0)', false)->set('sold_count', 'sold_count + ' . (int)$i['qty'], false)->where('id', $i['product_id'])->update('products');
            }
        }
        if ($t['coupon']) $this->db->set('used_count', 'used_count+1', false)->where('code', $t['coupon'])->update('coupons');
        $this->db->trans_complete();

        if (!$this->db->trans_status()) {
            flash('error', 'We could not place your order. Please try again.');
            redirect('checkout');
        }

        $this->shopcart->clear();
        $this->session->set_userdata('last_order', $orderNo);
        redirect('checkout/success/' . $orderNo);
    }

    public function success($no = '')
    {
        $o = $this->db->get_where('orders', array('order_no' => $no))->row();
        $mine = $o && ($this->session->userdata('last_order') === $no || ($this->customer() && $o->customer_id == $this->customer()->id));
        if (!$mine) show_404();
        $items = $this->db->get_where('order_items', array('order_id' => $o->id))->result();
        $m = $this->pay_methods();
        $this->render('front/checkout_success', array('o' => $o, 'items' => $items, 'methods' => $m), 'Order Confirmed');
    }
}
