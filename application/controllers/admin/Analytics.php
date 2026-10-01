<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/** Traffic sources (Facebook / Instagram / TikTok / WhatsApp ...) and visitor regions */
class Analytics extends Admin_Controller
{
    protected function range()
    {
        $r = (int)$this->input->get('range');
        if (!in_array($r, array(1, 7, 30, 90))) $r = 30;
        return $r;
    }

    public function index()
    {
        $this->render('admin/analytics', array('range' => $this->range(), 'page_js' => array('analytics.js')), 'Traffic Analytics');
    }

    /** JSON feed for the charts (polled every 30s for a live feel) */
    public function data()
    {
        $r = $this->range();
        $from = date('Y-m-d 00:00:00', strtotime('-' . ($r - 1) . ' days'));
        $db = $this->db;
        $meta = source_meta();

        // visits per source
        $rows = $db->select('source, COUNT(*) visits, COUNT(DISTINCT visit_token) uniq', false)->where('is_new_visit', 1)->where('created_at >=', $from)->group_by('source')->get('traffic_logs')->result();
        $bySrc = array(); $totalVisits = 0;
        foreach ($rows as $x) { $bySrc[$x->source] = (int)$x->visits; $totalVisits += (int)$x->visits; }
        $pv = (int)$db->where('created_at >=', $from)->count_all_results('traffic_logs');

        // orders + revenue per source
        $o = $db->select('source, COUNT(*) orders, IFNULL(SUM(total),0) revenue', false)->where('created_at >=', $from)->where('status !=', 'cancelled')->group_by('source')->get('orders')->result();
        $ordBy = array();
        foreach ($o as $x) $ordBy[$x->source] = array((int)$x->orders, (float)$x->revenue);

        $sources = array();
        foreach ($meta as $k => $m) {
            $v = isset($bySrc[$k]) ? $bySrc[$k] : 0;
            $sources[] = array('key' => $k, 'label' => $m['label'], 'color' => $m['color'], 'visits' => $v,
                'share' => $totalVisits ? round($v / $totalVisits * 100, 1) : 0,
                'orders' => isset($ordBy[$k]) ? $ordBy[$k][0] : 0, 'revenue' => isset($ordBy[$k]) ? $ordBy[$k][1] : 0,
                'conv' => ($v && isset($ordBy[$k])) ? round($ordBy[$k][0] / $v * 100, 2) : 0);
        }
        usort($sources, function ($a, $b) { return $b['visits'] - $a['visits']; });

        // daily series per source (top 6 sources by visits)
        $days = array(); $labels = array();
        $span = $r === 1 ? 1 : $r;
        for ($i = $span - 1; $i >= 0; $i--) { $dt = date('Y-m-d', strtotime("-$i days")); $days[] = $dt; $labels[] = date('d M', strtotime($dt)); }
        $daily = $db->select('DATE(created_at) d, source, COUNT(*) c', false)->where('is_new_visit', 1)->where('created_at >=', $from)->group_by('d, source')->get('traffic_logs')->result();
        $grid = array();
        foreach ($daily as $x) $grid[$x->source][$x->d] = (int)$x->c;
        $series = array();
        foreach (array_slice($sources, 0, 6) as $s) {
            $vals = array();
            foreach ($days as $dt) $vals[] = isset($grid[$s['key']][$dt]) ? $grid[$s['key']][$dt] : 0;
            $series[] = array('key' => $s['key'], 'label' => $s['label'], 'color' => $s['color'], 'data' => $vals);
        }

        // countries / regions
        $countries = $db->select('country, country_code, COUNT(*) visits', false)->where('is_new_visit', 1)->where('created_at >=', $from)->group_by('country, country_code')->order_by('visits', 'DESC')->limit(12)->get('traffic_logs')->result();
        $cities = $db->select('country, country_code, region, city, COUNT(*) visits', false)->where('is_new_visit', 1)->where('created_at >=', $from)->where('city IS NOT NULL', null, false)->group_by('country, country_code, region, city')->order_by('visits', 'DESC')->limit(15)->get('traffic_logs')->result();

        $dev = $db->select('device, COUNT(*) c', false)->where('is_new_visit', 1)->where('created_at >=', $from)->group_by('device')->get('traffic_logs')->result();
        $pages = $db->select('page, COUNT(*) c', false)->where('created_at >=', $from)->group_by('page')->order_by('c', 'DESC')->limit(8)->get('traffic_logs')->result();
        $live = (int)$db->select('COUNT(DISTINCT visit_token) n', false)->where('created_at >=', date('Y-m-d H:i:s', time() - 300))->get('traffic_logs')->row()->n;

        $this->json(array(
            'range' => $r, 'visits' => $totalVisits, 'pageviews' => $pv, 'live' => $live,
            'orders' => array_sum(array_column($sources, 'orders')), 'revenue' => array_sum(array_column($sources, 'revenue')),
            'sources' => $sources, 'labels' => $labels, 'series' => $series,
            'countries' => $countries, 'cities' => $cities, 'devices' => $dev, 'pages' => $pages,
            'currency' => setting('currency_symbol', 'Rs.'), 'updated' => date('H:i:s'),
        ));
    }

    /** Tracking link generator - guarantees correct attribution for WhatsApp / bio links / ads */
    public function links()
    {
        $this->render('admin/tracking_links', array(), 'Tracking Links');
    }
}
