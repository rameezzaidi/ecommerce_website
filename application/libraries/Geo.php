<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * IP -> country / region / city.
 * 1) Cloudflare headers (if the site is behind Cloudflare, free + instant)
 * 2) geo_cache table
 * 3) ip-api.com (free, no key, 2s timeout) - result cached forever per IP
 * Private / local IPs return "Local".
 */
class Geo
{
    protected $CI;

    public function __construct()
    {
        $this->CI =& get_instance();
    }

    public function lookup($ip)
    {
        $unknown = array('country' => 'Unknown', 'country_code' => '--', 'region' => null, 'city' => null);

        if (!empty($_SERVER['HTTP_CF_IPCOUNTRY']) && $_SERVER['HTTP_CF_IPCOUNTRY'] !== 'XX') {
            $cc = strtoupper(substr($_SERVER['HTTP_CF_IPCOUNTRY'], 0, 2));
            $list = country_list();
            return array(
                'country' => isset($list[$cc]) ? $list[$cc] : $cc,
                'country_code' => $cc,
                'region' => isset($_SERVER['HTTP_CF_REGION']) ? $_SERVER['HTTP_CF_REGION'] : null,
                'city' => isset($_SERVER['HTTP_CF_IPCITY']) ? $_SERVER['HTTP_CF_IPCITY'] : null,
            );
        }

        if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return array('country' => 'Local network', 'country_code' => '--', 'region' => null, 'city' => null);
        }

        $row = $this->CI->db->get_where('geo_cache', array('ip' => $ip))->row_array();
        if ($row) {
            return $row;
        }

        if (setting('geo_lookup', '1') !== '1') {
            return $unknown;
        }

        $res = $unknown;
        $ctx = stream_context_create(array('http' => array('timeout' => 2, 'ignore_errors' => true)));
        $raw = @file_get_contents('http://ip-api.com/json/' . urlencode($ip) . '?fields=status,country,countryCode,regionName,city', false, $ctx);
        if ($raw) {
            $j = json_decode($raw, true);
            if (is_array($j) && isset($j['status']) && $j['status'] === 'success') {
                $res = array('country' => $j['country'], 'country_code' => $j['countryCode'], 'region' => $j['regionName'], 'city' => $j['city']);
                $this->CI->db->replace('geo_cache', array_merge(array('ip' => $ip, 'updated_at' => date('Y-m-d H:i:s')), $res));
            }
        }
        return $res;
    }
}
