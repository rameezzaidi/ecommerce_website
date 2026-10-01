<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Products extends Crud_Controller
{
    protected $table = 'products'; protected $slug = 'products'; protected $title = 'Products'; protected $singular = 'Product';
    protected $folder = 'products'; protected $search = array('name', 'sku'); protected $slug_from = 'name'; protected $nullable = array('sale_price');
    protected $filter = array('field' => 'category_id', 'label' => 'Category', 'options' => array());
    protected $columns = array(
        'image' => array('label' => '', 'type' => 'image'), 'name' => array('label' => 'Product', 'type' => 'strong'), 'category' => array('label' => 'Category', 'type' => 'text'),
        'price' => array('label' => 'Price', 'type' => 'money'), 'sale_price' => array('label' => 'Sale', 'type' => 'money'), 'stock' => array('label' => 'Stock', 'type' => 'stock'),
        'sold_count' => array('label' => 'Sold', 'type' => 'text'), 'is_featured' => array('label' => 'Featured', 'type' => 'switch'), 'status' => array('label' => 'Active', 'type' => 'switch'),
    );

    public function __construct()
    {
        parent::__construct();
        $cats = array(); $filter = array();
        foreach ($this->db->order_by('parent_id, sort_order, name')->get('categories')->result() as $c) {
            if ($c->parent_id == 0) { $cats[$c->id] = $c->name; $filter[$c->id] = $c->name; }
        }
        // children grouped under their parent in the select
        $opts = array();
        foreach ($this->db->order_by('sort_order, name')->get_where('categories', array('parent_id' => 0))->result() as $p) {
            foreach ($this->db->order_by('sort_order, name')->get_where('categories', array('parent_id' => $p->id))->result() as $ch) $opts[$ch->id] = $p->name . ' / ' . $ch->name;
            $opts[$p->id] = $p->name . ' (top level)';
        }
        $this->filter['options'] = $opts;
        $this->fields = array(
            'name' => array('label' => 'Product name', 'type' => 'text', 'required' => true, 'col' => 8),
            'sku' => array('label' => 'SKU', 'type' => 'text', 'col' => 4),
            'category_id' => array('label' => 'Category', 'type' => 'select', 'options' => $opts, 'required' => true, 'col' => 6),
            'slug' => array('label' => 'URL slug', 'type' => 'text', 'col' => 6, 'help' => 'Leave empty to generate'),
            'price' => array('label' => 'Regular price', 'type' => 'number', 'required' => true, 'col' => 4),
            'sale_price' => array('label' => 'Sale price (optional)', 'type' => 'number', 'col' => 4),
            'stock' => array('label' => 'Stock quantity', 'type' => 'number', 'required' => true, 'col' => 4, 'help' => '0 = sold out; customers can then place an order on demand'),
            'sizes' => array('label' => 'Sizes (comma separated)', 'type' => 'text', 'col' => 6, 'help' => 'e.g. S,M,L,XL or 30,32,34 - leave empty if not applicable'),
            'colors' => array('label' => 'Colours (Name|#hex, comma separated)', 'type' => 'text', 'col' => 6, 'help' => 'e.g. Black|#111111,White|#ffffff'),
            'short_description' => array('label' => 'Short description', 'type' => 'text'),
            'description' => array('label' => 'Full description', 'type' => 'textarea', 'rows' => 6),
            'image' => array('label' => 'Main image', 'type' => 'image'),
            'is_featured' => array('label' => 'Featured on home page', 'type' => 'switch', 'col' => 3),
            'is_new' => array('label' => 'Mark as New', 'type' => 'switch', 'col' => 3),
            'allow_demand' => array('label' => 'Allow order on demand when sold out', 'type' => 'switch', 'col' => 3, 'default' => 1),
            'status' => array('label' => 'Active', 'type' => 'switch', 'default' => 1, 'col' => 3),
        );
    }

    protected function list_query()
    {
        $this->db->select('(SELECT c.name FROM categories c WHERE c.id = t.category_id) AS category', false);
    }

    protected function form_extra($row)
    {
        return array('gallery' => $row ? $this->db->order_by('sort_order, id')->get_where('product_images', array('product_id' => $row->id))->result() : array(), 'has_gallery' => true);
    }

    protected function after_save($id, $row, $isNew)
    {
        // gallery uploads (multiple)
        if (!empty($_FILES['gallery']['name'][0])) {
            $files = $_FILES['gallery'];
            $this->load->library('upload');
            for ($i = 0; $i < count($files['name']); $i++) {
                if ($files['error'][$i] !== 0) continue;
                $_FILES['g_tmp'] = array('name' => $files['name'][$i], 'type' => $files['type'][$i], 'tmp_name' => $files['tmp_name'][$i], 'error' => $files['error'][$i], 'size' => $files['size'][$i]);
                $up = $this->upload_image('g_tmp', 'products');
                if ($up) $this->db->insert('product_images', array('product_id' => $id, 'image' => $up, 'sort_order' => $i + 10));
            }
        }
        foreach ((array)$this->input->post('remove_gallery') as $gid) {
            $g = $this->db->get_where('product_images', array('id' => (int)$gid, 'product_id' => $id))->row();
            if ($g) { $this->delete_upload($g->image); $this->db->delete('product_images', array('id' => $g->id)); }
        }
    }

    protected function before_delete($id)
    {
        foreach ($this->db->get_where('product_images', array('product_id' => $id))->result() as $g) $this->delete_upload($g->image);
        $this->db->delete('product_images', array('product_id' => $id));
    }
}
