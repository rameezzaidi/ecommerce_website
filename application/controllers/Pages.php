<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pages extends Front_Controller
{
    public function view($slug = '')
    {
        $p = $this->db->get_where('pages', array('slug' => $slug, 'status' => 1))->row();
        if (!$p) show_404();
        $this->render('front/page', array('page' => $p), $p->title);
    }

    public function contact()
    {
        $this->render('front/contact', array(), 'Contact Us', 'Get in touch with our customer care team.');
    }

    public function send_contact()
    {
        if ($this->input->method() !== 'post') show_404();
        $this->load->library('form_validation');
        $this->form_validation->set_rules('name', 'Name', 'required|max_length[100]');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        $this->form_validation->set_rules('message', 'Message', 'required|min_length[10]|max_length[3000]');
        if ($this->form_validation->run()) {
            $this->db->insert('contact_messages', array(
                'name' => $this->input->post('name', true), 'email' => $this->input->post('email', true),
                'subject' => $this->input->post('subject', true), 'message' => $this->input->post('message', true), 'created_at' => date('Y-m-d H:i:s'),
            ));
            flash('success', 'Thank you - your message has been sent. We will reply shortly.');
        } else {
            flash('error', strip_tags(validation_errors(' ', ' ')));
        }
        redirect('contact');
    }

    /** AJAX newsletter signup */
    public function subscribe()
    {
        if ($this->input->method() !== 'post') show_404();
        $email = strtolower(trim((string)$this->input->post('email', true)));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return $this->json(array('ok' => false, 'msg' => 'Please enter a valid email address.'), 422);
        if ($this->db->where('email', $email)->count_all_results('subscribers')) return $this->json(array('ok' => true, 'msg' => 'You are already subscribed. Thank you!'));
        $this->db->insert('subscribers', array('email' => $email, 'status' => 1, 'created_at' => date('Y-m-d H:i:s')));
        $this->json(array('ok' => true, 'msg' => 'Welcome to the list! Look out for our next drop.'));
    }
}
