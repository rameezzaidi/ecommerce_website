<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Track extends Front_Controller
{
    public function index()
    {
        $no = strtoupper(trim((string)$this->input->get('no', true)));
        $who = strtolower(trim((string)$this->input->get('who', true)));
        $o = $d = null; $items = array(); $searched = false;
        if ($no !== '' && $who !== '') {
            $searched = true;
            if (strpos($no, 'OD') === 0) {
                $d = $this->db->group_start()->where('LOWER(email)', $who)->or_where('phone', $who)->group_end()->get_where('demand_orders', array('demand_no' => $no))->row();
            } else {
                $o = $this->db->group_start()->where('LOWER(email)', $who)->or_where('phone', $who)->group_end()->get_where('orders', array('order_no' => $no))->row();
                if ($o) $items = $this->db->get_where('order_items', array('order_id' => $o->id))->result();
            }
        }
        $this->render('front/track', array('o' => $o, 'd' => $d, 'items' => $items, 'searched' => $searched, 'no' => $no, 'who' => $who), 'Track Your Order');
    }
}
