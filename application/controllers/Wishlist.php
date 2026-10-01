<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Wishlist extends Front_Controller
{
    public function index()
    {
        $w = $this->wishlist();
        $this->render('front/wishlist', array('items' => $this->catalog->by_ids($w), 'wishlist' => $w), 'My Wishlist');
    }

    public function toggle()
    {
        if ($this->input->method() !== 'post') show_404();
        $id = (int)$this->input->post('product_id');
        $w = $this->wishlist();
        if (in_array($id, $w)) {
            $w = array_values(array_diff($w, array($id)));
            $on = false;
        } else {
            if ($this->db->where(array('id' => $id, 'status' => 1))->count_all_results('products')) $w[] = $id;
            $on = true;
        }
        $this->session->set_userdata('wishlist', $w);
        $this->json(array('ok' => true, 'on' => $on, 'count' => count($w)));
    }
}
