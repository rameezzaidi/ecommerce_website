<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/* ---------------------------------------------------------------
 | DRESSY helpers: SVG icons, money, assets, formatting
 --------------------------------------------------------------- */

if (!function_exists('icon')) {
    /** Inline SVG stroke icon (Feather set). icon('cart', 20) */
    function icon($name, $size = 20, $class = '')
    {
        static $set = null;
        if ($set === null) {
            $set = include APPPATH . 'config/icons.php';
        }
        $inner = isset($set['stroke'][$name]) ? $set['stroke'][$name] : $set['stroke']['circle'];
        return '<svg class="ic ' . $class . '" width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $inner . '</svg>';
    }
}

if (!function_exists('brand_icon')) {
    /** Inline SVG brand mark (Simple Icons). brand_icon('facebook') */
    function brand_icon($name, $size = 20, $class = '')
    {
        static $set = null;
        if ($set === null) {
            $set = include APPPATH . 'config/icons.php';
        }
        if (!isset($set['brand'][$name])) {
            return icon('globe', $size, $class);
        }
        return '<svg class="ic ic-brand ' . $class . '" width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="' . $set['brand'][$name] . '"/></svg>';
    }
}

if (!function_exists('setting')) {
    function setting($key, $default = '')
    {
        $CI =& get_instance();
        $s = isset($CI->settings) ? $CI->settings : array();
        return (isset($s[$key]) && $s[$key] !== '') ? $s[$key] : $default;
    }
}

if (!function_exists('money')) {
    function money($amount, $withSymbol = true)
    {
        $sym = setting('currency_symbol', 'Rs.');
        $n = number_format((float)$amount, 0, '.', ',');
        return $withSymbol ? $sym . ' ' . $n : $n;
    }
}

if (!function_exists('asset')) {
    function asset($path)
    {
        $f = FCPATH . 'assets/' . ltrim($path, '/');
        $v = file_exists($f) ? filemtime($f) : 1;
        return base_url('assets/' . ltrim($path, '/')) . '?v=' . $v;
    }
}

if (!function_exists('upload_url')) {
    /** Public URL for an uploaded file stored as "folder/name.ext" inside /uploads */
    function upload_url($path, $fallback = 'assets/img/placeholder.svg')
    {
        if (empty($path)) {
            return base_url($fallback);
        }
        if (preg_match('#^https?://#', $path)) {
            return $path;
        }
        return base_url('uploads/' . ltrim($path, '/'));
    }
}

if (!function_exists('e')) {
    function e($v)
    {
        return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('slugify')) {
    function slugify($text)
    {
        $text = strtolower(trim($text));
        $text = preg_replace('/[^a-z0-9]+/', '-', $text);
        $text = trim($text, '-');
        return $text === '' ? 'item' : $text;
    }
}

if (!function_exists('current_price')) {
    function current_price($p)
    {
        $p = (object)$p;
        return ($p->sale_price !== null && (float)$p->sale_price > 0 && (float)$p->sale_price < (float)$p->price) ? (float)$p->sale_price : (float)$p->price;
    }
}

if (!function_exists('discount_percent')) {
    function discount_percent($p)
    {
        $p = (object)$p;
        $cur = current_price($p);
        if ($cur < (float)$p->price && (float)$p->price > 0) {
            return (int)round((1 - $cur / (float)$p->price) * 100);
        }
        return 0;
    }
}

if (!function_exists('time_ago')) {
    function time_ago($datetime)
    {
        $t = strtotime($datetime);
        $d = time() - $t;
        if ($d < 60) return 'just now';
        if ($d < 3600) return floor($d / 60) . ' min ago';
        if ($d < 86400) return floor($d / 3600) . ' hr ago';
        if ($d < 2592000) return floor($d / 86400) . ' days ago';
        return date('d M Y', $t);
    }
}

if (!function_exists('status_badge')) {
    function status_badge($status)
    {
        $map = array(
            'pending' => 'warn', 'reviewing' => 'info', 'quoted' => 'info', 'accepted' => 'ok', 'confirmed' => 'ok',
            'processing' => 'info', 'shipped' => 'info', 'delivered' => 'ok', 'fulfilled' => 'ok', 'paid' => 'ok',
            'cancelled' => 'bad', 'rejected' => 'bad', 'refunded' => 'bad', 'unpaid' => 'warn',
        );
        $c = isset($map[$status]) ? $map[$status] : 'muted';
        return '<span class="badge-st st-' . $c . '">' . e(ucfirst($status)) . '</span>';
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field()
    {
        $CI =& get_instance();
        return '<input type="hidden" name="' . $CI->security->get_csrf_token_name() . '" value="' . $CI->security->get_csrf_hash() . '">';
    }
}

if (!function_exists('stars')) {
    function stars($rating, $size = 14)
    {
        $out = '<span class="stars" aria-label="' . e($rating) . ' out of 5">';
        for ($i = 1; $i <= 5; $i++) {
            $out .= '<svg width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" class="' . ($i <= round($rating) ? 'on' : 'off') . '"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>';
        }
        return $out . '</span>';
    }
}

if (!function_exists('country_list')) {
    function country_list()
    {
        return array(
            'PK' => 'Pakistan', 'AE' => 'United Arab Emirates', 'SA' => 'Saudi Arabia', 'GB' => 'United Kingdom', 'US' => 'United States',
            'CA' => 'Canada', 'AU' => 'Australia', 'DE' => 'Germany', 'FR' => 'France', 'IT' => 'Italy', 'NL' => 'Netherlands',
            'TR' => 'Turkey', 'QA' => 'Qatar', 'KW' => 'Kuwait', 'OM' => 'Oman', 'BH' => 'Bahrain', 'IN' => 'India', 'BD' => 'Bangladesh',
            'MY' => 'Malaysia', 'SG' => 'Singapore', 'CN' => 'China', 'JP' => 'Japan', 'ZA' => 'South Africa', 'EG' => 'Egypt',
            'NG' => 'Nigeria', 'KE' => 'Kenya', 'BR' => 'Brazil', 'ES' => 'Spain', 'SE' => 'Sweden', 'NO' => 'Norway',
        );
    }
}

if (!function_exists('order_statuses')) {
    function order_statuses()
    {
        return array('pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled');
    }
}

if (!function_exists('demand_statuses')) {
    function demand_statuses()
    {
        return array('pending', 'reviewing', 'quoted', 'accepted', 'confirmed', 'fulfilled', 'rejected');
    }
}

if (!function_exists('source_meta')) {
    /** Label / brand / colour for every traffic source */
    function source_meta($key = null)
    {
        $m = array(
            'facebook'  => array('label' => 'Facebook',  'color' => '#1877F2'),
            'instagram' => array('label' => 'Instagram', 'color' => '#E1306C'),
            'tiktok'    => array('label' => 'TikTok',    'color' => '#0FB5AE'),
            'whatsapp'  => array('label' => 'WhatsApp',  'color' => '#25D366'),
            'google'    => array('label' => 'Google',    'color' => '#F4B400'),
            'youtube'   => array('label' => 'YouTube',   'color' => '#FF0000'),
            'other'     => array('label' => 'Other sites', 'color' => '#8A8A8A'),
            'direct'    => array('label' => 'Direct',    'color' => '#111111'),
        );
        if ($key === null) return $m;
        return isset($m[$key]) ? $m[$key] : $m['other'];
    }
}

if (!function_exists('source_icon')) {
    function source_icon($key, $size = 18)
    {
        $brand = array('facebook', 'instagram', 'tiktok', 'whatsapp', 'google', 'youtube');
        if (in_array($key, $brand)) return brand_icon($key, $size);
        if ($key === 'direct') return icon('compass', $size);
        return icon('link', $size);
    }
}

if (!function_exists('flash_html')) {
    function flash_html()
    {
        $CI =& get_instance();
        $out = '';
        foreach (array('success', 'error', 'info') as $t) {
            $m = $CI->session->flashdata('flash_' . $t);
            if ($m) {
                $out .= '<div class="alert-d alert-' . $t . '" role="alert">' . ($t === 'success' ? icon('check-circle', 18) : icon('alert-circle', 18)) . '<span>' . e($m) . '</span></div>';
            }
        }
        return $out;
    }
}

if (!function_exists('flash')) {
    function flash($type, $msg)
    {
        $CI =& get_instance();
        $CI->session->set_flashdata('flash_' . $type, $msg);
    }
}

if (!function_exists('uri_active')) {
    function uri_active($seg1, $seg2 = null)
    {
        $CI =& get_instance();
        if ($CI->uri->segment(1) !== $seg1) return '';
        if ($seg2 !== null && $CI->uri->segment(2) !== $seg2) return '';
        return 'active';
    }
}
