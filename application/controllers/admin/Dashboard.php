<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends Admin_Controller
{
    public function index()
    {
        $today = date('Y-m-d');
        $d = array();
        $d['kpi'] = array(
            'revenue' => (float)$this->db->select_sum('total')->where('status !=', 'cancelled')->get('orders')->row()->total,
            'revenue_30' => (float)$this->db->select_sum('total')->where('status !=', 'cancelled')->where('created_at >=', date('Y-m-d', strtotime('-30 days')))->get('orders')->row()->total,
            'orders' => (int)$this->db->count_all('orders'),
            'orders_today' => (int)$this->db->where('DATE(created_at)', $today)->count_all_results('orders'),
            'customers' => (int)$this->db->count_all('customers'),
            'products' => (int)$this->db->count_all('products'),
            'visits_30' => (int)$this->db->where('is_new_visit', 1)->where('created_at >=', date('Y-m-d', strtotime('-30 days')))->count_all_results('traffic_logs'),
            'demands' => (int)$this->db->where_in('status', array('pending', 'reviewing'))->count_all_results('demand_orders'),
        );
        $d['recent'] = $this->db->order_by('id', 'DESC')->limit(8)->get('orders')->result();
        $d['low'] = $this->db->order_by('stock')->where('stock <=', 5)->where('status', 1)->limit(6)->get('products')->result();
        $d['top'] = $this->db->order_by('sold_count', 'DESC')->limit(5)->get_where('products', array('status' => 1))->result();
        $d['demands'] = $this->db->order_by('id', 'DESC')->limit(5)->get('demand_orders')->result();
        $this->render('admin/dashboard', $d, 'Dashboard');
    }

    /** JSON: revenue & orders by day (last 14 days) */
    public function sales_data()
    {
        $days = 14; $labels = array(); $rev = array(); $ord = array();
        for ($i = $days - 1; $i >= 0; $i--) {
            $dt = date('Y-m-d', strtotime("-$i days"));
            $r = $this->db->select('COUNT(*) c, IFNULL(SUM(total),0) s', false)->where('DATE(created_at)', $dt)->where('status !=', 'cancelled')->get('orders')->row();
            $labels[] = date('d M', strtotime($dt)); $rev[] = (float)$r->s; $ord[] = (int)$r->c;
        }
        $this->json(array('labels' => $labels, 'revenue' => $rev, 'orders' => $ord));
    }
}
