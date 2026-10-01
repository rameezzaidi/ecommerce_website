<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Visitor tracking: figures out where each visitor came from
 * (Facebook / Instagram / TikTok / WhatsApp / Google / ...) and which country/city.
 *
 * Detection order (first match wins):
 *   1. ?utm_source= / ?src= / ?ref=   (use the Tracking Links page in admin to create these)
 *   2. In-app browser user-agent (FBAN/FBAV, Instagram, TikTok/musical_ly)
 *   3. HTTP referrer host (l.facebook.com, l.instagram.com, tiktok.com, wa.me ...)
 *   4. click ids (fbclid, ttclid)
 * The first-touch source is remembered for the whole visit and saved on any order placed.
 */
class Tracker
{
    protected $CI;

    public function __construct()
    {
        $this->CI =& get_instance();
    }

    public function hit()
    {
        $in = $this->CI->input;
        if ($in->is_ajax_request() || strtoupper($in->method()) !== 'GET') {
            return;
        }
        $ua = (string)$in->user_agent();
        if ($ua === '' || preg_match('/bot|crawl|spider|slurp|facebookexternalhit|preview|monitor|curl|wget|python|headless/i', $ua)) {
            return;
        }

        $sess = $this->CI->session;
        $isNew = !$sess->userdata('vt_token');

        if ($isNew) {
            $sess->set_userdata('vt_token', bin2hex(random_bytes(8)));
            $sess->set_userdata('vt_source', $this->detect_source($ua));
            $sess->set_userdata('vt_geo', $this->CI->geo->lookup($in->ip_address()));
        }

        $geo = $sess->userdata('vt_geo');
        $ref = (string)$in->server('HTTP_REFERER');
        $dev = $this->device($ua);

        $this->CI->db->insert('traffic_logs', array(
            'visit_token'  => $sess->userdata('vt_token'),
            'is_new_visit' => $isNew ? 1 : 0,
            'source'       => $sess->userdata('vt_source'),
            'referrer'     => $isNew ? mb_substr($ref, 0, 250) : null,
            'page'         => mb_substr('/' . $this->CI->uri->uri_string(), 0, 250),
            'country'      => $geo['country'],
            'country_code' => $geo['country_code'],
            'region'       => $geo['region'],
            'city'         => $geo['city'],
            'device'       => $dev,
            'browser'      => $this->browser($ua),
            'ip'           => $in->ip_address(),
            'created_at'   => date('Y-m-d H:i:s'),
        ));
    }

    public function current_source()
    {
        $s = $this->CI->session->userdata('vt_source');
        return $s ? $s : 'direct';
    }

    public function current_geo()
    {
        $g = $this->CI->session->userdata('vt_geo');
        return $g ? $g : array('country' => 'Unknown', 'country_code' => '--', 'region' => null, 'city' => null);
    }

    public function detect_source($ua)
    {
        $in = $this->CI->input;
        $q = strtolower((string)($in->get('utm_source') ?: ($in->get('src') ?: $in->get('ref'))));
        if ($q !== '') {
            return $this->normalize($q);
        }

        if (preg_match('/FBAN|FBAV|FB_IAB|FBIOS/i', $ua)) return 'facebook';
        if (stripos($ua, 'Instagram') !== false) return 'instagram';
        if (preg_match('/TikTok|musical_ly|BytedanceWebview|trill_/i', $ua)) return 'tiktok';
        if (stripos($ua, 'WhatsApp') !== false) return 'whatsapp';

        $ref = strtolower((string)$in->server('HTTP_REFERER'));
        if ($ref !== '') {
            $host = (string)parse_url($ref, PHP_URL_HOST);
            if ($host !== '' && $host === parse_url(base_url(), PHP_URL_HOST)) {
                return 'direct';
            }
            $s = $this->normalize($host);
            if ($s !== 'other' || $host !== '') {
                return $s;
            }
        }

        if ($in->get('fbclid')) return 'facebook';
        if ($in->get('ttclid')) return 'tiktok';
        if ($in->get('igshid')) return 'instagram';
        return 'direct';
    }

    protected function normalize($s)
    {
        $s = strtolower($s);
        $map = array(
            'facebook' => '/(^|\.)(facebook|fb|fbcdn|messenger)\./', 'instagram' => '/(^|\.)instagram\.|^ig$|^insta$/',
            'tiktok' => '/(^|\.)tiktok\.|^tt$/', 'whatsapp' => '/whatsapp|(^|\.)wa\.me|^wa$/',
            'google' => '/(^|\.)google\.|(^|\.)bing\.|(^|\.)duckduckgo\./', 'youtube' => '/youtube|youtu\.be/',
        );
        foreach ($map as $k => $re) {
            if ($s === $k || preg_match($re, $s)) return $k;
        }
        return ($s === 'direct' || $s === '') ? 'direct' : 'other';
    }

    protected function device($ua)
    {
        if (preg_match('/ipad|tablet|kindle|silk/i', $ua)) return 'tablet';
        if (preg_match('/mobi|android|iphone|ipod/i', $ua)) return 'mobile';
        return 'desktop';
    }

    protected function browser($ua)
    {
        if (preg_match('/FBAN|FBAV/i', $ua)) return 'Facebook App';
        if (stripos($ua, 'Instagram') !== false) return 'Instagram App';
        if (preg_match('/TikTok|musical_ly/i', $ua)) return 'TikTok App';
        if (preg_match('/Edg\//', $ua)) return 'Edge';
        if (preg_match('/OPR\/|Opera/', $ua)) return 'Opera';
        if (preg_match('/Chrome\//', $ua)) return 'Chrome';
        if (preg_match('/Firefox\//', $ua)) return 'Firefox';
        if (preg_match('/Safari\//', $ua)) return 'Safari';
        return 'Other';
    }
}
