<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Settings extends Admin_Controller
{
    protected $groups = array(
        'Store' => array('site_name' => 'Store name', 'tagline_small' => 'Logo sub-text', 'tagline' => 'Tagline', 'meta_description' => 'SEO description|textarea', 'about_short' => 'Footer blurb|textarea', 'announcement' => 'Announcement bar (separate with |)', 'currency_symbol' => 'Currency symbol', 'timezone' => 'Timezone (e.g. Asia/Karachi)'),
        'Shipping & offers' => array('shipping_domestic' => 'Domestic shipping fee|number', 'shipping_international' => 'International shipping fee|number', 'free_shipping_over' => 'Free domestic shipping over (0 = never)|number', 'package_discount' => 'Full outfit package discount %|number'),
        'Contact & social' => array('contact_phone' => 'Phone', 'contact_email' => 'Email', 'contact_address' => 'Address', 'contact_hours' => 'Opening hours', 'whatsapp_number' => 'WhatsApp number (with country code)', 'social_facebook' => 'Facebook URL', 'social_instagram' => 'Instagram URL', 'social_tiktok' => 'TikTok URL'),
        'Payments' => array('pay_cod' => 'Enable Cash on Delivery|switch', 'pay_bank' => 'Enable Bank Transfer|switch', 'bank_details' => 'Bank transfer details|textarea', 'pay_wallet' => 'Enable Easypaisa / JazzCash|switch', 'wallet_details' => 'Wallet details|textarea'),
        'Analytics' => array('geo_lookup' => 'Look up visitor country/city via ip-api.com|switch'),
    );

    public function index()
    {
        if ($this->input->method() === 'post') {
            foreach ($this->groups as $fields) foreach ($fields as $k => $label) {
                $type = strpos($label, '|') !== false ? explode('|', $label)[1] : 'text';
                $v = $type === 'switch' ? ($this->input->post($k) ? '1' : '0') : trim((string)$this->input->post($k));
                $this->Setting_model->set($k, $v);
            }
            flash('success', 'Settings saved.');
            redirect('admin/settings');
        }
        $this->render('admin/settings', array('groups' => $this->groups), 'Settings');
    }

    public function password()
    {
        if ($this->input->method() !== 'post') show_404();
        $new = (string)$this->input->post('new_password');
        if (!password_verify((string)$this->input->post('current_password'), $this->admin->password)) flash('error', 'Current password is incorrect.');
        elseif (strlen($new) < 8) flash('error', 'New password must be at least 8 characters.');
        else {
            $this->db->update('admins', array('password' => password_hash($new, PASSWORD_DEFAULT), 'name' => $this->input->post('name', true) ?: $this->admin->name), array('id' => $this->admin->id));
            flash('success', 'Password updated.');
        }
        redirect('admin/settings');
    }
}
