<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends Front_Controller
{
    public function index()
    {
        $d = array();
        $d['heroes'] = $this->db->order_by('sort_order, id')->get_where('banners', array('status' => 1, 'position' => 'hero'))->result();
        $d['promos'] = $this->db->order_by('sort_order, id')->limit(3)->get_where('banners', array('status' => 1, 'position' => 'promo'))->result();
        $d['featured'] = $this->catalog->featured(8);
        $d['best'] = $this->catalog->best_selling(8);
        $d['new'] = $this->catalog->new_arrivals(8);
        $d['wishlist'] = $this->wishlist();
        $d['reviews'] = $this->db->select('r.*, p.name AS product_name')->from('reviews r')->join('products p', 'p.id = r.product_id')
            ->where('r.status', 1)->where('r.rating >=', 4)->order_by('r.id', 'DESC')->limit(3)->get()->result();
        $this->render('front/home', $d);
    }
}
