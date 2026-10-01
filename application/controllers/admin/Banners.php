<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Banners extends Crud_Controller
{
    protected $table = 'banners'; protected $slug = 'banners'; protected $title = 'Banners'; protected $singular = 'Banner';
    protected $folder = 'banners'; protected $search = array('title'); protected $order = 't.position, t.sort_order';
    protected $columns = array(
        'image' => array('label' => '', 'type' => 'image'), 'title' => array('label' => 'Title', 'type' => 'text'),
        'position' => array('label' => 'Position', 'type' => 'badge'), 'sort_order' => array('label' => 'Order', 'type' => 'text'),
        'status' => array('label' => 'Active', 'type' => 'switch'),
    );
    public function __construct()
    {
        parent::__construct();
        $this->fields = array(
            'position' => array('label' => 'Position', 'type' => 'select', 'options' => array('hero' => 'Home hero slider', 'promo' => 'Home promo tiles (3 shown)'), 'col' => 6),
            'sort_order' => array('label' => 'Sort order', 'type' => 'number', 'col' => 6, 'default' => 0),
            'eyebrow' => array('label' => 'Small label', 'type' => 'text', 'col' => 6),
            'title' => array('label' => 'Headline', 'type' => 'text', 'required' => true, 'col' => 6),
            'subtitle' => array('label' => 'Sub-headline', 'type' => 'text'),
            'button_text' => array('label' => 'Button text', 'type' => 'text', 'col' => 6),
            'link' => array('label' => 'Link (e.g. shop/men or full URL)', 'type' => 'text', 'col' => 6),
            'image' => array('label' => 'Image (hero: 1600x760 recommended, promo: 800x680)', 'type' => 'image'),
            'status' => array('label' => 'Active', 'type' => 'switch', 'default' => 1),
        );
    }
}
