<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Setting_model extends CI_Model
{
    protected static $cache = null;

    public function all()
    {
        if (self::$cache === null) {
            self::$cache = array();
            $q = $this->db->get('settings');
            if ($q) {
                foreach ($q->result() as $r) self::$cache[$r->skey] = $r->svalue;
            }
        }
        return self::$cache;
    }

    public function set($key, $value)
    {
        $this->db->replace('settings', array('skey' => $key, 'svalue' => $value));
        self::$cache = null;
    }
}
