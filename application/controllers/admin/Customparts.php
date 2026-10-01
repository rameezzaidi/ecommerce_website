<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/** Items offered in the outfit customizer (jeans, t-shirts, shirts, shoes) */
class Customparts extends Crud_Controller
{
    protected $table = 'custom_parts'; protected $slug = 'custom-parts'; protected $title = 'Customizer Items'; protected $singular = 'Customizer item';
    protected $search = array('name', 'color_name'); protected $order = 't.part_type, t.sort_order, t.id';
    protected $filter = array('field' => 'part_type', 'label' => 'Type', 'options' => array('jeans' => 'Jeans', 'tshirt' => 'T-Shirts', 'shirt' => 'Shirts', 'shoes' => 'Shoes'));
    protected $columns = array(
        'color_hex' => array('label' => '', 'type' => 'color'), 'name' => array('label' => 'Name', 'type' => 'strong'), 'part_type' => array('label' => 'Type', 'type' => 'badge'),
        'style' => array('label' => 'Style', 'type' => 'muted'), 'color_name' => array('label' => 'Colour', 'type' => 'text'), 'price' => array('label' => 'Price', 'type' => 'money'),
        'status' => array('label' => 'Active', 'type' => 'switch'),
    );
    public function __construct()
    {
        parent::__construct();
        $this->fields = array(
            'part_type' => array('label' => 'Item type', 'type' => 'select', 'options' => array('jeans' => 'Jeans', 'tshirt' => 'T-Shirt', 'shirt' => 'Shirt', 'shoes' => 'Shoes'), 'col' => 6),
            'style' => array('label' => 'Shape on mannequin', 'type' => 'select', 'col' => 6, 'help' => 'Jeans: slim/straight/bootcut/shorts/jogger - T-shirt: crew/vneck/polo/oversized - Shirt: formal/casual - Shoes: sneaker/boot/loafer/sandal',
                'options' => array('slim' => 'Jeans - Slim', 'straight' => 'Jeans - Straight', 'bootcut' => 'Jeans - Bootcut', 'shorts' => 'Jeans - Shorts', 'jogger' => 'Jeans - Jogger',
                    'crew' => 'T-Shirt - Crew', 'vneck' => 'T-Shirt - V-Neck', 'polo' => 'T-Shirt - Polo', 'oversized' => 'T-Shirt - Oversized', 'formal' => 'Shirt - Formal', 'casual' => 'Shirt - Casual',
                    'sneaker' => 'Shoes - Sneaker', 'boot' => 'Shoes - Boot', 'loafer' => 'Shoes - Loafer', 'sandal' => 'Shoes - Sandal')),
            'name' => array('label' => 'Display name', 'type' => 'text', 'required' => true, 'col' => 6),
            'color_name' => array('label' => 'Colour name', 'type' => 'text', 'required' => true, 'col' => 3),
            'color_hex' => array('label' => 'Colour', 'type' => 'color', 'col' => 3),
            'price' => array('label' => 'Price', 'type' => 'number', 'required' => true, 'col' => 6),
            'sort_order' => array('label' => 'Sort order', 'type' => 'number', 'col' => 6, 'default' => 0),
            'status' => array('label' => 'Active', 'type' => 'switch', 'default' => 1),
        );
    }
}
