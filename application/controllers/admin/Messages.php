<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Messages extends Crud_Controller
{
    protected $table = 'contact_messages'; protected $slug = 'messages'; protected $title = 'Messages'; protected $singular = 'Message'; protected $can_add = false;
    protected $search = array('name', 'email', 'subject', 'message');
    protected $columns = array('name' => array('label' => 'From', 'type' => 'strong'), 'email' => array('label' => 'Email', 'type' => 'text'), 'subject' => array('label' => 'Subject', 'type' => 'text'), 'message' => array('label' => 'Message', 'type' => 'trunc'), 'created_at' => array('label' => 'Date', 'type' => 'date'), 'is_read' => array('label' => 'Read', 'type' => 'switch'));
    public function __construct()
    {
        parent::__construct();
        $this->fields = array(
            'name' => array('label' => 'Name', 'type' => 'text', 'col' => 6), 'email' => array('label' => 'Email', 'type' => 'text', 'col' => 6),
            'subject' => array('label' => 'Subject', 'type' => 'text'), 'message' => array('label' => 'Message', 'type' => 'textarea', 'rows' => 8),
            'is_read' => array('label' => 'Mark as read', 'type' => 'switch'),
        );
    }
}
