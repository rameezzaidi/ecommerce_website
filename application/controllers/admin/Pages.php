<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pages extends Crud_Controller
{
    protected $table = 'pages'; protected $slug = 'pages'; protected $title = 'Pages'; protected $singular = 'Page';
    protected $search = array('title'); protected $slug_from = 'title';
    protected $columns = array('title' => array('label' => 'Title', 'type' => 'strong'), 'slug' => array('label' => 'URL', 'type' => 'muted'), 'show_in_footer' => array('label' => 'In footer', 'type' => 'switch'), 'status' => array('label' => 'Published', 'type' => 'switch'));
    public function __construct()
    {
        parent::__construct();
        $this->fields = array(
            'title' => array('label' => 'Title', 'type' => 'text', 'required' => true, 'col' => 8),
            'slug' => array('label' => 'URL slug', 'type' => 'text', 'col' => 4),
            'content' => array('label' => 'Content (HTML allowed)', 'type' => 'html', 'rows' => 14),
            'show_in_footer' => array('label' => 'Show link in footer', 'type' => 'switch', 'default' => 1),
            'status' => array('label' => 'Published', 'type' => 'switch', 'default' => 1),
        );
    }
}
