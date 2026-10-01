<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Categories extends Crud_Controller
{
    protected $table = 'categories'; protected $slug = 'categories'; protected $title = 'Categories'; protected $singular = 'Category';
    protected $folder = 'categories'; protected $search = array('name', 'slug'); protected $slug_from = 'name'; protected $order = 't.parent_id, t.sort_order, t.id';
    protected $columns = array(
        'image' => array('label' => '', 'type' => 'image'), 'name' => array('label' => 'Name', 'type' => 'text'),
        'parent' => array('label' => 'Parent', 'type' => 'text'), 'slug' => array('label' => 'Slug', 'type' => 'muted'),
        'sort_order' => array('label' => 'Order', 'type' => 'text'), 'status' => array('label' => 'Active', 'type' => 'switch'),
    );

    public function __construct()
    {
        parent::__construct();
        $parents = array(0 => '- Top level -');
        foreach ($this->db->order_by('name')->get_where('categories', array('parent_id' => 0))->result() as $c) $parents[$c->id] = $c->name;
        $this->fields = array(
            'name' => array('label' => 'Name', 'type' => 'text', 'required' => true, 'col' => 6),
            'parent_id' => array('label' => 'Parent category', 'type' => 'select', 'options' => $parents, 'col' => 6),
            'slug' => array('label' => 'URL slug', 'type' => 'text', 'col' => 6, 'help' => 'Leave empty to generate from name'),
            'sort_order' => array('label' => 'Sort order', 'type' => 'number', 'col' => 6, 'default' => 0),
            'description' => array('label' => 'Short description', 'type' => 'text'),
            'image' => array('label' => 'Image (used for top-level tiles & mega menu)', 'type' => 'image'),
            'status' => array('label' => 'Active', 'type' => 'switch', 'default' => 1),
        );
    }

    protected function list_query()
    {
        $this->db->select('(SELECT p.name FROM categories p WHERE p.id = t.parent_id) AS parent', false);
    }
}
