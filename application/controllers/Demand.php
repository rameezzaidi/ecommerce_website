<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/** Order on demand: the customer needs something that is out of stock (or not in the catalogue) */
class Demand extends Front_Controller
{
    public function index()
    {
        $p = null;
        $pid = (int)$this->input->get('product');
        if ($pid) $p = $this->catalog->by_ids(array($pid));
        $p = $p ? $p[0] : null;
        $c = $this->customer();
        $this->render('front/demand', array(
            'p' => $p, 'cust' => $c, 'old' => $this->session->flashdata('old') ?: array(),
            'sizes' => $p ? $this->shopcart->csv($p->sizes) : array(), 'colors' => $p ? $this->shopcart->colors($p->colors) : array(),
        ), 'Order on Demand', 'Cannot find what you need? Tell us and we will source it for you.');
    }

    public function submit()
    {
        if ($this->input->method() !== 'post') show_404();
        $this->load->library('form_validation');
        $this->form_validation->set_rules('name', 'Name', 'required|max_length[120]');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        $this->form_validation->set_rules('phone', 'Phone', 'required|min_length[7]|max_length[25]');
        $this->form_validation->set_rules('product_name', 'Product', 'required|max_length[200]');
        $this->form_validation->set_rules('qty', 'Quantity', 'required|integer|greater_than[0]|less_than[100]');
        if (!$this->form_validation->run()) {
            flash('error', strip_tags(validation_errors(' ', ' ')));
            $this->session->set_flashdata('old', $this->input->post());
            redirect('demand' . ($this->input->post('product_id') ? '?product=' . (int)$this->input->post('product_id') : ''));
        }
        $img = $this->upload_image('reference_image', 'demand');
        if ($img === false) {
            flash('error', 'Reference image: ' . $this->upload_error);
            $this->session->set_flashdata('old', $this->input->post());
            redirect('demand');
        }

        do { $no = 'OD' . date('ymd') . mt_rand(1000, 9999); } while ($this->db->where('demand_no', $no)->count_all_results('demand_orders') > 0);
        $c = $this->customer();
        $pid = (int)$this->input->post('product_id');
        $this->db->insert('demand_orders', array(
            'demand_no' => $no, 'product_id' => $pid ?: null, 'customer_id' => $c ? $c->id : null,
            'name' => $this->input->post('name', true), 'email' => $this->input->post('email', true), 'phone' => $this->input->post('phone', true),
            'product_name' => $this->input->post('product_name', true), 'description' => $this->input->post('description', true),
            'size' => $this->input->post('size', true), 'color' => $this->input->post('color', true), 'qty' => (int)$this->input->post('qty'),
            'budget' => $this->input->post('budget') !== '' ? (float)$this->input->post('budget') : null, 'reference_image' => $img ?: null,
            'address' => $this->input->post('address', true), 'city' => $this->input->post('city', true), 'country' => $this->input->post('country', true),
            'status' => 'pending', 'source' => $this->tracker->current_source(), 'created_at' => date('Y-m-d H:i:s'),
        ));
        $this->session->set_userdata('last_demand', $no);
        redirect('demand/success/' . $no);
    }

    public function success($no = '')
    {
        $d = $this->db->get_where('demand_orders', array('demand_no' => $no))->row();
        if (!$d || $this->session->userdata('last_demand') !== $no) show_404();
        $this->render('front/demand_success', array('d' => $d), 'Request Received');
    }
}
