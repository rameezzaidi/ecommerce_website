<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Subscribers extends Crud_Controller
{
    protected $table = 'subscribers'; protected $slug = 'subscribers'; protected $title = 'Subscribers'; protected $singular = 'Subscriber';
    protected $search = array('email');
    protected $columns = array('email' => array('label' => 'Email', 'type' => 'strong'), 'created_at' => array('label' => 'Subscribed', 'type' => 'date'), 'status' => array('label' => 'Active', 'type' => 'switch'));
    public function __construct()
    {
        parent::__construct();
        $this->fields = array('email' => array('label' => 'Email', 'type' => 'email', 'required' => true, 'unique' => true), 'status' => array('label' => 'Active', 'type' => 'switch', 'default' => 1));
    }
    public function export()
    {
        $rows = $this->db->select('email, created_at')->where('status', 1)->get('subscribers')->result();
        $out = "email,subscribed_at\n";
        foreach ($rows as $r) $out .= $r->email . ',' . $r->created_at . "\n";
        $this->output->set_content_type('text/csv')->set_header('Content-Disposition: attachment; filename="subscribers.csv"')->set_output($out);
    }
}
