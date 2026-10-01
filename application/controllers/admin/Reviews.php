<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reviews extends Crud_Controller
{
    protected $table = 'reviews'; protected $slug = 'reviews'; protected $title = 'Reviews'; protected $singular = 'Review'; protected $can_add = false;
    protected $search = array('name', 'comment');
    protected $filter = array('field' => 'status', 'label' => 'Status', 'options' => array('0' => 'Pending', '1' => 'Approved'));
    protected $columns = array('product_name' => array('label' => 'Product', 'type' => 'text'), 'name' => array('label' => 'Customer', 'type' => 'text'), 'rating' => array('label' => 'Rating', 'type' => 'stars'), 'comment' => array('label' => 'Review', 'type' => 'trunc'), 'created_at' => array('label' => 'Date', 'type' => 'date'), 'status' => array('label' => 'Approved', 'type' => 'switch'));
    public function __construct()
    {
        parent::__construct();
        $this->fields = array(
            'name' => array('label' => 'Customer name', 'type' => 'text', 'required' => true, 'col' => 8),
            'rating' => array('label' => 'Rating (1-5)', 'type' => 'number', 'col' => 4),
            'comment' => array('label' => 'Review', 'type' => 'textarea'),
            'status' => array('label' => 'Approved (visible on site)', 'type' => 'switch'),
        );
    }
    protected function list_query() { $this->db->select('(SELECT name FROM products p WHERE p.id = t.product_id) AS product_name', false); }
}
