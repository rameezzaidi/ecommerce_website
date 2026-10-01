<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Shop extends Front_Controller
{
    public function index($slug = null)
    {
        $cat = null;
        if ($slug) {
            $cat = $this->catalog->category_by_slug($slug);
            if (!$cat) show_404();
        }
        $f = array(
            'cat' => $cat,
            'q' => trim((string)$this->input->get('q')),
            'min' => $this->input->get('min'), 'max' => $this->input->get('max'),
            'sort' => $this->input->get('sort'), 'stock' => $this->input->get('stock'),
            'sale' => $this->input->get('sale'), 'size' => $this->input->get('size'),
        );
        $page = max(1, (int)$this->input->get('page'));
        $per = 12;
        $total = $this->catalog->listing($f, 0, 0, true);
        $items = $this->catalog->listing($f, $per, ($page - 1) * $per);

        $parent = null;
        if ($cat && $cat->parent_id) $parent = $this->db->get_where('categories', array('id' => $cat->parent_id))->row();

        $title = $cat ? $cat->name : ($f['q'] !== '' ? 'Search: ' . $f['q'] : 'Shop All');
        $this->render('front/shop', array(
            'cat' => $cat, 'parent_cat' => $parent, 'f' => $f, 'items' => $items, 'pg' => $this->paginate_links($total, $per, $page),
            'range' => $this->catalog->price_range(), 'wishlist' => $this->wishlist(),
        ), $title, $cat ? (string)$cat->description : '');
    }
}
