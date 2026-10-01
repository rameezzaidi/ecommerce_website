<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cart extends Front_Controller
{
    protected function summary()
    {
        $items = $this->shopcart->items();
        $sub = $this->shopcart->subtotal($items);
        list($disc, $code) = $this->shopcart->discount($sub);
        $ship = $this->shopcart->shipping($sub - $disc, 'PK');
        return array('items' => $items, 'subtotal' => $sub, 'discount' => $disc, 'coupon' => $code,
            'free_over' => (float)setting('free_shipping_over', 8000), 'total' => $sub - $disc);
    }

    public function index()
    {
        $s = $this->summary();
        $this->render('front/cart', $s, 'Shopping Bag');
    }

    /** Slide-out mini bag (html) */
    public function drawer()
    {
        $s = $this->summary();
        $this->load->view('front/partials/mini_cart', $s);
    }

    public function add()
    {
        if ($this->input->method() !== 'post') show_404();
        $r = $this->shopcart->add_product($this->input->post('product_id'), $this->input->post('qty') ?: 1, (string)$this->input->post('size'), (string)$this->input->post('color'));
        $this->json(array('ok' => $r[0], 'msg' => $r[1], 'count' => $this->shopcart->count()), $r[0] ? 200 : 422);
    }

    public function update()
    {
        if ($this->input->method() !== 'post') show_404();
        $this->shopcart->update($this->input->post('key'), $this->input->post('qty'));
        $s = $this->summary();
        $this->json(array('ok' => true, 'count' => $this->shopcart->count(), 'html' => $this->load->view('front/partials/cart_table', $s, true)));
    }

    public function remove()
    {
        if ($this->input->method() !== 'post') show_404();
        $this->shopcart->remove($this->input->post('key'));
        $s = $this->summary();
        $this->json(array('ok' => true, 'count' => $this->shopcart->count(), 'html' => $this->load->view('front/partials/cart_table', $s, true)));
    }

    public function coupon()
    {
        if ($this->input->method() !== 'post') show_404();
        $sub = $this->shopcart->subtotal();
        if ($this->input->post('remove')) {
            $this->session->unset_userdata('dressy_coupon');
            $msg = 'Coupon removed.'; $ok = true;
        } else {
            $r = $this->shopcart->apply_coupon($this->input->post('code'), $sub);
            $ok = $r[0]; $msg = $r[1];
            if ($ok) $this->session->set_userdata('dressy_coupon', strtoupper(trim($this->input->post('code'))));
        }
        $s = $this->summary();
        $this->json(array('ok' => $ok, 'msg' => $msg, 'count' => $this->shopcart->count(), 'html' => $this->load->view('front/partials/cart_table', $s, true)), $ok ? 200 : 422);
    }
}
