<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Coupons extends Crud_Controller
{
    protected $table = 'coupons'; protected $slug = 'coupons'; protected $title = 'Coupons'; protected $singular = 'Coupon';
    protected $search = array('code'); protected $nullable = array('expires_at');
    protected $columns = array(
        'code' => array('label' => 'Code', 'type' => 'strong'), 'type' => array('label' => 'Type', 'type' => 'badge'), 'value' => array('label' => 'Value', 'type' => 'text'),
        'min_order' => array('label' => 'Min order', 'type' => 'money'), 'used_count' => array('label' => 'Used', 'type' => 'text'),
        'expires_at' => array('label' => 'Expires', 'type' => 'text'), 'status' => array('label' => 'Active', 'type' => 'switch'),
    );
    public function __construct()
    {
        parent::__construct();
        $this->fields = array(
            'code' => array('label' => 'Code', 'type' => 'text', 'required' => true, 'col' => 6, 'unique' => true),
            'type' => array('label' => 'Type', 'type' => 'select', 'options' => array('percent' => 'Percent (%)', 'fixed' => 'Fixed amount'), 'col' => 6),
            'value' => array('label' => 'Value', 'type' => 'number', 'required' => true, 'col' => 4),
            'min_order' => array('label' => 'Minimum order', 'type' => 'number', 'col' => 4, 'default' => 0),
            'max_uses' => array('label' => 'Max uses (0 = unlimited)', 'type' => 'number', 'col' => 4, 'default' => 0),
            'expires_at' => array('label' => 'Expiry date', 'type' => 'date', 'col' => 6),
            'status' => array('label' => 'Active', 'type' => 'switch', 'default' => 1),
        );
    }
    protected function before_save(&$row, $id) { $row['code'] = strtoupper(preg_replace('/\s+/', '', $row['code'])); }
}
