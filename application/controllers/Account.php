<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Account extends Front_Controller
{
    public function login()
    {
        if ($this->customer()) redirect('account');
        if ($this->input->method() === 'post') {
            $email = strtolower(trim($this->input->post('email', true)));
            $c = $this->db->get_where('customers', array('email' => $email, 'status' => 1))->row();
            if ($c && password_verify((string)$this->input->post('password'), $c->password)) {
                $this->session->set_userdata('customer_id', $c->id);
                $to = $this->session->userdata('after_login');
                $this->session->unset_userdata('after_login');
                redirect($to ? $to : 'account');
            }
            flash('error', 'Incorrect email or password.');
            redirect('account/login');
        }
        $this->render('front/account_login', array('mode' => 'login'), 'Sign In');
    }

    public function register()
    {
        if ($this->customer()) redirect('account');
        if ($this->input->method() === 'post') {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('name', 'Name', 'required|max_length[120]');
            $this->form_validation->set_rules('email', 'Email', 'required|valid_email|is_unique[customers.email]', array('is_unique' => 'An account with this email already exists.'));
            $this->form_validation->set_rules('phone', 'Phone', 'required|max_length[25]');
            $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
            if ($this->form_validation->run()) {
                $this->db->insert('customers', array(
                    'name' => $this->input->post('name', true), 'email' => strtolower(trim($this->input->post('email', true))),
                    'phone' => $this->input->post('phone', true), 'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
                    'status' => 1, 'created_at' => date('Y-m-d H:i:s'),
                ));
                $this->session->set_userdata('customer_id', $this->db->insert_id());
                flash('success', 'Welcome to ' . setting('site_name', 'DRESSY') . '! Your account is ready.');
                redirect('account');
            }
            flash('error', strip_tags(validation_errors(' ', ' ')));
            redirect('account/register');
        }
        $this->render('front/account_login', array('mode' => 'register'), 'Create Account');
    }

    public function logout()
    {
        $this->session->unset_userdata('customer_id');
        flash('info', 'You have been signed out.');
        redirect('/');
    }

    public function index()
    {
        $this->require_login();
        $c = $this->customer();
        $orders = $this->db->order_by('id', 'DESC')->get_where('orders', array('customer_id' => $c->id))->result();
        $demands = $this->db->order_by('id', 'DESC')->get_where('demand_orders', array('customer_id' => $c->id))->result();
        $this->render('front/account', array('orders' => $orders, 'demands' => $demands, 'tab' => $this->input->get('tab') ?: 'orders'), 'My Account');
    }

    public function order($no = '')
    {
        $this->require_login();
        $o = $this->db->get_where('orders', array('order_no' => $no, 'customer_id' => $this->customer()->id))->row();
        if (!$o) show_404();
        $items = $this->db->get_where('order_items', array('order_id' => $o->id))->result();
        $this->render('front/order_view', array('o' => $o, 'items' => $items), 'Order ' . $o->order_no);
    }

    public function profile()
    {
        $this->require_login();
        $c = $this->customer();
        if ($this->input->method() === 'post') {
            $upd = array(
                'name' => $this->input->post('name', true), 'phone' => $this->input->post('phone', true),
                'address' => $this->input->post('address', true), 'city' => $this->input->post('city', true), 'country' => $this->input->post('country', true),
            );
            $pw = (string)$this->input->post('password');
            if ($pw !== '') {
                if (strlen($pw) < 6) { flash('error', 'Password must be at least 6 characters.'); redirect('account/profile'); }
                $upd['password'] = password_hash($pw, PASSWORD_DEFAULT);
            }
            $this->db->update('customers', $upd, array('id' => $c->id));
            flash('success', 'Profile updated.');
            redirect('account/profile');
        }
        $this->render('front/account_profile', array(), 'My Profile');
    }
}
