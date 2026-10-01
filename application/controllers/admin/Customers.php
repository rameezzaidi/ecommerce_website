<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customers extends Crud_Controller
{
    protected $table = 'customers'; protected $slug = 'customers'; protected $title = 'Customers'; protected $singular = 'Customer'; protected $can_add = false;
    protected $search = array('name', 'email', 'phone', 'city');
    protected $columns = array('name' => array('label' => 'Name', 'type' => 'strong'), 'email' => array('label' => 'Email', 'type' => 'text'), 'phone' => array('label' => 'Phone', 'type' => 'text'), 'city' => array('label' => 'City', 'type' => 'text'), 'country' => array('label' => 'Country', 'type' => 'text'), 'orders' => array('label' => 'Orders', 'type' => 'text'), 'status' => array('label' => 'Active', 'type' => 'switch'));
    public function __construct()
    {
        parent::__construct();
        $this->fields = array(
            'name' => array('label' => 'Name', 'type' => 'text', 'required' => true, 'col' => 6), 'email' => array('label' => 'Email', 'type' => 'email', 'required' => true, 'col' => 6, 'unique' => true),
            'phone' => array('label' => 'Phone', 'type' => 'text', 'col' => 6), 'password' => array('label' => 'New password (leave blank to keep)', 'type' => 'password', 'col' => 6),
            'address' => array('label' => 'Address', 'type' => 'text'), 'city' => array('label' => 'City', 'type' => 'text', 'col' => 6), 'country' => array('label' => 'Country', 'type' => 'text', 'col' => 6),
            'status' => array('label' => 'Active', 'type' => 'switch', 'default' => 1),
        );
    }
    protected function list_query() { $this->db->select('(SELECT COUNT(*) FROM orders o WHERE o.customer_id = t.id) AS orders', false); }
}
