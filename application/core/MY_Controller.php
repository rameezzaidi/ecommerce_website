<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/* ======================================================================
 | MY_Controller - base for everything
 ====================================================================== */
class MY_Controller extends CI_Controller
{
    public $settings = array();

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('Setting_model');
        $this->settings = $this->Setting_model->all();
        date_default_timezone_set(setting('timezone', 'Asia/Karachi'));
        $this->db->query("SET time_zone = '" . date('P') . "'");
        $this->output->set_header('X-Frame-Options: SAMEORIGIN');
        $this->output->set_header('X-Content-Type-Options: nosniff');
    }

    protected function json($arr, $code = 200)
    {
        // keep CSRF token available for the next ajax call
        $arr['csrf'] = array('name' => $this->security->get_csrf_token_name(), 'hash' => $this->security->get_csrf_hash());
        $this->output->set_status_header($code)->set_content_type('application/json')->set_output(json_encode($arr));
        return;
    }

    protected function unique_slug($table, $text, $ignoreId = 0, $col = 'slug')
    {
        $base = slugify($text);
        $slug = $base;
        $i = 1;
        while (true) {
            $this->db->where($col, $slug);
            if ($ignoreId) $this->db->where('id !=', $ignoreId);
            if ($this->db->count_all_results($table) == 0) return $slug;
            $slug = $base . '-' . (++$i);
        }
    }

    /** Upload one image from $_FILES[$field] into /uploads/$folder. Returns "folder/file.ext", '' (no file) or FALSE (error) */
    protected function upload_image($field, $folder, $maxKb = 4096)
    {
        if (empty($_FILES[$field]['name'])) return '';
        $dir = FCPATH . 'uploads/' . $folder . '/';
        if (!is_dir($dir)) @mkdir($dir, 0755, true);
        $this->load->library('upload');
        $this->upload->initialize(array(
            'upload_path' => $dir, 'allowed_types' => 'jpg|jpeg|png|webp|gif', 'max_size' => $maxKb,
            'encrypt_name' => true, 'file_ext_tolower' => true,
        ), true);
        if (!$this->upload->do_upload($field)) {
            $this->upload_error = strip_tags($this->upload->display_errors('', ''));
            return false;
        }
        return $folder . '/' . $this->upload->data('file_name');
    }
    public $upload_error = '';

    protected function delete_upload($path)
    {
        if ($path && strpos($path, '..') === false && !preg_match('#^https?://#', $path)) {
            $f = FCPATH . 'uploads/' . $path;
            if (is_file($f)) @unlink($f);
        }
    }
}

/* ======================================================================
 | Front_Controller - storefront
 ====================================================================== */
class Front_Controller extends MY_Controller
{
    protected $data = array();
    protected $cust = null;

    public function __construct()
    {
        parent::__construct();
        $this->load->library(array('geo', 'tracker', 'shopcart'));
        $this->load->model('Catalog_model', 'catalog');
        $this->tracker->hit();

        $this->data['nav_tree'] = $this->catalog->category_tree();
        $this->data['cart_count'] = $this->shopcart->count();
        $this->data['wish_count'] = count($this->wishlist());
        $this->data['customer'] = $this->customer();
        $this->data['footer_pages'] = $this->db->order_by('id')->get_where('pages', array('status' => 1, 'show_in_footer' => 1))->result();
        $this->data['seo'] = array('title' => '', 'description' => '', 'image' => '');
    }

    protected function customer()
    {
        if ($this->cust === null) {
            $id = (int)$this->session->userdata('customer_id');
            $this->cust = $id ? $this->db->get_where('customers', array('id' => $id, 'status' => 1))->row() : false;
            if (!$this->cust && $id) $this->session->unset_userdata('customer_id');
        }
        return $this->cust;
    }

    protected function require_login()
    {
        if (!$this->customer()) {
            $this->session->set_userdata('after_login', current_url());
            flash('info', 'Please sign in to continue.');
            redirect('account/login');
        }
    }

    protected function wishlist()
    {
        $w = $this->session->userdata('wishlist');
        return is_array($w) ? $w : array();
    }

    protected function render($view, $data = array(), $title = '', $description = '')
    {
        $d = array_merge($this->data, $data);
        $d['seo']['title'] = $title ? $title . ' | ' . setting('site_name', 'DRESSY') : setting('site_name', 'DRESSY') . ' - ' . setting('tagline', 'Premium Fashion');
        $d['seo']['description'] = $description ? $description : setting('meta_description', '');
        $this->load->view('front/layout/header', $d);
        $this->load->view($view, $d);
        $this->load->view('front/layout/footer', $d);
    }

    protected function paginate_links($total, $perPage, $page)
    {
        $pages = (int)ceil($total / $perPage);
        return array('total' => $total, 'pages' => $pages, 'page' => $page, 'per' => $perPage);
    }
}

/* ======================================================================
 | Admin_Controller - needs admin login
 ====================================================================== */
class Admin_Controller extends MY_Controller
{
    protected $data = array();
    protected $admin;

    public function __construct()
    {
        parent::__construct();
        $id = (int)$this->session->userdata('admin_id');
        $this->admin = $id ? $this->db->get_where('admins', array('id' => $id))->row() : null;
        if (!$this->admin) {
            if ($this->input->is_ajax_request()) {
                $this->json(array('ok' => false, 'msg' => 'Session expired. Please log in again.'), 401);
                echo $this->output->get_output();
                exit;
            }
            redirect('admin/login');
        }
        $this->data['admin'] = $this->admin;
        $this->data['badges'] = array(
            'orders' => (int)$this->db->where('status', 'pending')->count_all_results('orders'),
            'demands' => (int)$this->db->where('status', 'pending')->count_all_results('demand_orders'),
            'messages' => (int)$this->db->where('is_read', 0)->count_all_results('contact_messages'),
            'reviews' => (int)$this->db->where('status', 0)->count_all_results('reviews'),
        );
    }

    protected function render($view, $data = array(), $title = 'Dashboard')
    {
        $d = array_merge($this->data, $data);
        $d['page_title'] = $title;
        $this->load->view('admin/layout/header', $d);
        $this->load->view($view, $d);
        $this->load->view('admin/layout/footer', $d);
    }
}

/* ======================================================================
 | Crud_Controller - generic list / add / edit / delete screens
 |
 | Child classes only describe the table, the list columns and the form
 | fields. See admin/Categories.php for the smallest example.
 ====================================================================== */
class Crud_Controller extends Admin_Controller
{
    protected $table = '';
    protected $slug = '';           // admin/<slug>
    protected $title = '';          // "Categories"
    protected $singular = '';       // "Category"
    protected $columns = array();   // key => array(label, type, ...)
    protected $fields = array();    // key => array(label, type, required, options, col, help, default)
    protected $search = array();    // columns searched by ?q=
    protected $order = 't.id DESC';
    protected $per_page = 20;
    protected $folder = 'misc';     // upload folder
    protected $can_add = true;
    protected $can_edit = true;
    protected $can_delete = true;
    protected $filter = null;       // array('field'=>'status','label'=>'Status','options'=>array(...))
    protected $slug_from = '';      // e.g. 'name' => auto-fill slug field
    protected $nullable = array();  // fields saved as NULL when empty
    protected $old = array();
    protected $form_errors = '';

    public function __construct()
    {
        parent::__construct();
        $this->data['crud'] = $this;
    }

    public function cfg($k) { return $this->$k; }

    /* ---- hooks for children ---- */
    protected function list_query() {}                           // add joins/wheres to $this->db
    protected function before_save(&$row, $id) {}
    protected function after_save($id, $row, $isNew) {}
    protected function before_delete($id) {}
    protected function form_extra($row) { return array(); }

    /* ---- LIST ---- */
    public function index()
    {
        $q = trim((string)$this->input->get('q'));
        $page = max(1, (int)$this->input->get('page'));
        $flt = $this->filter ? $this->input->get($this->filter['field']) : '';

        $build = function () use ($q, $flt) {
            $this->db->from($this->table . ' t');
            if ($q !== '' && $this->search) {
                $this->db->group_start();
                foreach ($this->search as $i => $c) {
                    $i == 0 ? $this->db->like('t.' . $c, $q) : $this->db->or_like('t.' . $c, $q);
                }
                $this->db->group_end();
            }
            if ($this->filter && $flt !== '' && $flt !== null) $this->db->where('t.' . $this->filter['field'], $flt);
            $this->list_query();
        };

        $build();
        $total = $this->db->count_all_results();
        $build();
        $this->db->select('t.*', false);
        $rows = $this->db->order_by($this->order, '', false)->limit($this->per_page, ($page - 1) * $this->per_page)->get()->result();

        $this->render('admin/crud/list', array(
            'rows' => $rows, 'total' => $total, 'page' => $page, 'pages' => (int)ceil($total / $this->per_page),
            'q' => $q, 'flt' => $flt,
        ), $this->title);
    }

    /* ---- FORM ---- */
    public function form($id = 0)
    {
        $id = (int)$id;
        $row = null;
        if ($id) {
            $row = $this->db->get_where($this->table, array('id' => $id))->row();
            if (!$row) show_404();
        }
        $data = array('row' => $row, 'id' => $id, 'old' => $this->old, 'errors' => $this->form_errors);
        $data = array_merge($data, $this->form_extra($row));
        $this->render('admin/crud/form', $data, ($id ? 'Edit ' : 'Add ') . $this->singular);
    }

    /* ---- SAVE ---- */
    public function save($id = 0)
    {
        if ($this->input->method() !== 'post') show_404();
        $id = (int)$id;
        $existing = $id ? $this->db->get_where($this->table, array('id' => $id))->row() : null;
        if ($id && !$existing) show_404();

        $this->load->library('form_validation');
        foreach ($this->fields as $k => $f) {
            $rules = array();
            if (!empty($f['required'])) $rules[] = 'required';
            if (isset($f['type']) && $f['type'] === 'email') $rules[] = 'valid_email';
            if (isset($f['type']) && $f['type'] === 'number') $rules[] = 'numeric';
            if (!empty($f['unique'])) {
                $rules[] = 'callback__unique[' . $k . ',' . $id . ']';
            }
            if ($rules && !in_array($f['type'], array('image', 'password'))) $this->form_validation->set_rules($k, $f['label'], implode('|', $rules));
        }
        if (!$this->form_validation->run()) {
            $this->old = $this->input->post();
            $this->form_errors = validation_errors('<div>', '</div>');
            return $this->form($id);
        }

        $row = array();
        foreach ($this->fields as $k => $f) {
            $t = $f['type'];
            if ($t === 'image') {
                $up = $this->upload_image($k, $this->folder);
                if ($up === false) {
                    $this->old = $this->input->post();
                    $this->form_errors = '<div>' . e($f['label']) . ': ' . e($this->upload_error) . '</div>';
                    return $this->form($id);
                }
                if ($up !== '') {
                    $row[$k] = $up;
                    if ($existing && !empty($existing->$k)) $this->delete_upload($existing->$k);
                } elseif ($this->input->post('remove_' . $k) && $existing) {
                    $this->delete_upload($existing->$k);
                    $row[$k] = null;
                }
            } elseif ($t === 'switch') {
                $row[$k] = $this->input->post($k) ? 1 : 0;
            } elseif ($t === 'password') {
                $pw = (string)$this->input->post($k);
                if ($pw !== '') $row[$k] = password_hash($pw, PASSWORD_DEFAULT);
            } elseif ($t === 'html') {
                $row[$k] = $this->input->post($k);   // trusted: admin only
            } else {
                $v = $this->input->post($k);
                $v = is_string($v) ? trim($v) : $v;
                if (in_array($k, $this->nullable) && ($v === '' || $v === null)) $v = null;
                $row[$k] = $v;
            }
        }
        if ($this->slug_from && isset($this->fields['slug'])) {
            $src = $this->input->post('slug') ?: $this->input->post($this->slug_from);
            $row['slug'] = $this->unique_slug($this->table, $src, $id);
        }
        if (!$id) $row['created_at'] = date('Y-m-d H:i:s');
        if ($this->db->field_exists('updated_at', $this->table) && $id) $row['updated_at'] = date('Y-m-d H:i:s');

        $this->before_save($row, $id);
        if ($id) {
            $this->db->update($this->table, $row, array('id' => $id));
        } else {
            $this->db->insert($this->table, $row);
            $id = $this->db->insert_id();
        }
        $this->after_save($id, $row, !$existing);
        flash('success', $this->singular . ' saved successfully.');
        redirect('admin/' . $this->slug);
    }

    public function _unique($val, $params)
    {
        list($field, $id) = explode(',', $params);
        $this->db->where($field, $val);
        if ((int)$id) $this->db->where('id !=', (int)$id);
        if ($this->db->count_all_results($this->table) > 0) {
            $this->form_validation->set_message('_unique', 'The {field} already exists.');
            return false;
        }
        return true;
    }

    /* ---- DELETE ---- */
    public function delete($id = 0)
    {
        if ($this->input->method() !== 'post' || !$this->can_delete) show_404();
        $id = (int)$id;
        $row = $this->db->get_where($this->table, array('id' => $id))->row();
        if ($row) {
            $this->before_delete($id);
            foreach ($this->fields as $k => $f) if ($f['type'] === 'image' && !empty($row->$k)) $this->delete_upload($row->$k);
            $this->db->delete($this->table, array('id' => $id));
            flash('success', $this->singular . ' deleted.');
        }
        redirect('admin/' . $this->slug);
    }

    /* ---- AJAX toggle for switch columns ---- */
    public function toggle($id = 0, $field = '')
    {
        if ($this->input->method() !== 'post') show_404();
        if (!isset($this->columns[$field]) || $this->columns[$field]['type'] !== 'switch') return $this->json(array('ok' => false), 400);
        $row = $this->db->get_where($this->table, array('id' => (int)$id))->row();
        if (!$row) return $this->json(array('ok' => false), 404);
        $new = $row->$field ? 0 : 1;
        $this->db->update($this->table, array($field => $new), array('id' => (int)$id));
        return $this->json(array('ok' => true, 'value' => $new));
    }
}
