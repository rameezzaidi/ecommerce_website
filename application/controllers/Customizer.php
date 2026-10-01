<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customizer extends Front_Controller
{
    public function index()
    {
        $parts = $this->db->order_by('sort_order, id')->get_where('custom_parts', array('status' => 1))->result();
        $grouped = array('jeans' => array(), 'tshirt' => array(), 'shirt' => array(), 'shoes' => array());
        foreach ($parts as $p) $grouped[$p->part_type][] = array(
            'id' => (int)$p->id, 'name' => $p->name, 'style' => $p->style, 'color' => $p->color_hex, 'color_name' => $p->color_name,
            'price' => (float)$p->price, 'type' => $p->part_type,
        );
        $this->render('front/customizer', array(
            'grouped' => $grouped, 'pkg_discount' => (float)setting('package_discount', 10), 'page_js' => array('customizer.js'),
        ), 'Design Your Outfit', 'Build a complete outfit - jeans, t-shirt or shirt and shoes - and preview it live.');
    }

    /** AJAX - add the chosen package to the bag */
    public function add()
    {
        if ($this->input->method() !== 'post') show_404();
        $sel = array(
            'jeans' => (int)$this->input->post('jeans'), 'top' => (int)$this->input->post('top'), 'shoes' => (int)$this->input->post('shoes'),
            'sizes' => array(
                'jeans' => (string)$this->input->post('size_jeans'), 'top' => (string)$this->input->post('size_top'), 'shoes' => (string)$this->input->post('size_shoes'),
            ),
        );
        $r = $this->shopcart->add_package($sel, $this->input->post('qty') ?: 1);
        $this->json(array('ok' => $r[0], 'msg' => $r[1], 'count' => $this->shopcart->count()), $r[0] ? 200 : 422);
    }
}
