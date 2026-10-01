<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends MY_Controller
{
    public function login()
    {
        if ($this->session->userdata('admin_id')) redirect('admin');
        if ($this->input->method() === 'post') {
            $a = $this->db->get_where('admins', array('email' => strtolower(trim($this->input->post('email', true)))))->row();
            if ($a && password_verify((string)$this->input->post('password'), $a->password)) {
                $this->session->sess_regenerate(true);
                $this->session->set_userdata('admin_id', $a->id);
                $this->db->update('admins', array('last_login' => date('Y-m-d H:i:s')), array('id' => $a->id));
                redirect('admin');
            }
            flash('error', 'Incorrect email or password.');
            redirect('admin/login');
        }
        $this->load->view('admin/login');
    }

    public function logout()
    {
        $this->session->unset_userdata('admin_id');
        redirect('admin/login');
    }
}
