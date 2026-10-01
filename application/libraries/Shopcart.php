<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Session based shopping cart.
 * Line types:  product  (normal catalogue item)
 *              package  (customised outfit built in the Customizer: jeans + top + shoes, all optional)
 * Prices are ALWAYS re-read from the database - the browser never sends a price.
 */
class Shopcart
{
    protected $CI;
    const KEY = 'dressy_cart';

    public function __construct()
    {
        $this->CI =& get_instance();
    }

    protected function raw()
    {
        $c = $this->CI->session->userdata(self::KEY);
        return is_array($c) ? $c : array();
    }

    protected function save($c)
    {
        $this->CI->session->set_userdata(self::KEY, $c);
    }

    public function count()
    {
        $n = 0;
        foreach ($this->raw() as $l) $n += (int)$l['qty'];
        return $n;
    }

    public function clear()
    {
        $this->CI->session->unset_userdata(array(self::KEY, 'dressy_coupon'));
    }

    /* ---------- add ---------- */

    public function add_product($pid, $qty, $size, $color)
    {
        $p = $this->CI->db->get_where('products', array('id' => (int)$pid, 'status' => 1))->row();
        if (!$p) return array(false, 'Product not found.');
        if ((int)$p->stock <= 0) return array(false, 'This item is out of stock. You can place an order on demand instead.');

        $sizes = $this->csv($p->sizes);
        $colors = $this->colors($p->colors);
        if ($sizes && !in_array($size, $sizes)) return array(false, 'Please select a size.');
        if ($colors && !isset($colors[$color])) return array(false, 'Please select a colour.');

        $qty = max(1, (int)$qty);
        $key = md5('p|' . $p->id . '|' . $size . '|' . $color);
        $cart = $this->raw();
        $have = isset($cart[$key]) ? (int)$cart[$key]['qty'] : 0;
        $new = min($qty + $have, (int)$p->stock);
        $cart[$key] = array('type' => 'product', 'product_id' => (int)$p->id, 'size' => $size, 'color' => $color, 'qty' => $new);
        $this->save($cart);
        return array(true, ($new < $qty + $have) ? 'Only ' . $p->stock . ' in stock - quantity adjusted.' : 'Added to your bag.');
    }

    public function add_package($sel, $qty = 1)
    {
        $parts = $this->resolve_package($sel);
        if (!$parts['lines']) return array(false, 'Please choose at least one item for your outfit.');
        $qty = max(1, min(10, (int)$qty));
        $key = md5('k|' . json_encode($parts['ids']) . '|' . json_encode($parts['sizes']));
        $cart = $this->raw();
        $have = isset($cart[$key]) ? (int)$cart[$key]['qty'] : 0;
        $cart[$key] = array('type' => 'package', 'sel' => $parts['ids'], 'sizes' => $parts['sizes'], 'qty' => min(10, $have + $qty));
        $this->save($cart);
        return array(true, 'Your custom outfit was added to the bag.');
    }

    public function update($key, $qty)
    {
        $cart = $this->raw();
        if (!isset($cart[$key])) return false;
        $qty = (int)$qty;
        if ($qty <= 0) {
            unset($cart[$key]);
        } else {
            if ($cart[$key]['type'] === 'product') {
                $p = $this->CI->db->select('stock')->get_where('products', array('id' => $cart[$key]['product_id']))->row();
                $qty = $p ? min($qty, (int)$p->stock) : 0;
            } else {
                $qty = min($qty, 10);
            }
            if ($qty <= 0) unset($cart[$key]); else $cart[$key]['qty'] = $qty;
        }
        $this->save($cart);
        return true;
    }

    public function remove($key)
    {
        $cart = $this->raw();
        unset($cart[$key]);
        $this->save($cart);
    }

    /* ---------- read ---------- */

    /** Fully priced lines */
    public function items()
    {
        $out = array();
        $cart = $this->raw();
        $changed = false;
        foreach ($cart as $key => $l) {
            if ($l['type'] === 'product') {
                $p = $this->CI->db->get_where('products', array('id' => $l['product_id'], 'status' => 1))->row();
                if (!$p || (int)$p->stock <= 0) { unset($cart[$key]); $changed = true; continue; }
                $qty = min((int)$l['qty'], (int)$p->stock);
                $unit = current_price($p);
                $out[] = array(
                    'key' => $key, 'type' => 'product', 'product_id' => (int)$p->id, 'name' => $p->name, 'slug' => $p->slug,
                    'image' => $p->image, 'size' => $l['size'], 'color' => $l['color'], 'unit_price' => $unit, 'old_price' => (float)$p->price,
                    'qty' => $qty, 'stock' => (int)$p->stock, 'line_total' => $unit * $qty, 'meta' => null, 'outfit' => null,
                );
            } else {
                $parts = $this->resolve_package(array_merge($l['sel'], array('sizes' => $l['sizes'])));
                if (!$parts['lines']) { unset($cart[$key]); $changed = true; continue; }
                $qty = (int)$l['qty'];
                $out[] = array(
                    'key' => $key, 'type' => 'package', 'product_id' => null, 'name' => 'Custom Outfit - ' . implode(' + ', array_column($parts['lines'], 'label')),
                    'slug' => null, 'image' => null, 'size' => null, 'color' => null, 'unit_price' => $parts['total'], 'old_price' => $parts['subtotal'],
                    'qty' => $qty, 'stock' => 10, 'line_total' => $parts['total'] * $qty, 'meta' => $parts, 'outfit' => $parts['outfit'],
                );
            }
        }
        if ($changed) $this->save($cart);
        return $out;
    }

    public function subtotal($items = null)
    {
        $items = $items === null ? $this->items() : $items;
        $s = 0;
        foreach ($items as $i) $s += $i['line_total'];
        return $s;
    }

    /* ---------- coupon ---------- */

    public function coupon_code()
    {
        return $this->CI->session->userdata('dressy_coupon');
    }

    public function apply_coupon($code, $subtotal)
    {
        $code = strtoupper(trim($code));
        $c = $this->CI->db->get_where('coupons', array('code' => $code, 'status' => 1))->row();
        if (!$c) return array(false, 'Invalid coupon code.', 0);
        if ($c->expires_at && strtotime($c->expires_at . ' 23:59:59') < time()) return array(false, 'This coupon has expired.', 0);
        if ((int)$c->max_uses > 0 && (int)$c->used_count >= (int)$c->max_uses) return array(false, 'This coupon has reached its usage limit.', 0);
        if ($subtotal < (float)$c->min_order) return array(false, 'Minimum order of ' . money($c->min_order) . ' required for this coupon.', 0);
        $disc = $c->type === 'percent' ? round($subtotal * $c->value / 100) : min($subtotal, (float)$c->value);
        return array(true, 'Coupon applied.', $disc, $c);
    }

    public function discount($subtotal)
    {
        $code = $this->coupon_code();
        if (!$code) return array(0, null);
        $r = $this->apply_coupon($code, $subtotal);
        if (!$r[0]) { $this->CI->session->unset_userdata('dressy_coupon'); return array(0, null); }
        return array($r[2], $code);
    }

    public function shipping($subtotal, $countryCode)
    {
        if ($subtotal <= 0) return 0;
        if ($countryCode === 'PK') {
            $free = (float)setting('free_shipping_over', 8000);
            return ($free > 0 && $subtotal >= $free) ? 0 : (float)setting('shipping_domestic', 250);
        }
        return (float)setting('shipping_international', 2500);
    }

    /* ---------- customised package ---------- */

    /**
     * $sel = array('jeans'=>id, 'top'=>id, 'shoes'=>id, 'sizes'=>array('jeans'=>'32','top'=>'M','shoes'=>'42'))
     */
    public function resolve_package($sel)
    {
        $ids = array(); $lines = array(); $sizes = array(); $outfit = array(); $subtotal = 0;
        $sz = isset($sel['sizes']) && is_array($sel['sizes']) ? $sel['sizes'] : array();
        foreach (array('jeans' => array('jeans'), 'top' => array('tshirt', 'shirt'), 'shoes' => array('shoes')) as $slot => $types) {
            $id = isset($sel[$slot]) ? (int)$sel[$slot] : 0;
            if (!$id) continue;
            $part = $this->CI->db->where_in('part_type', $types)->get_where('custom_parts', array('id' => $id, 'status' => 1))->row();
            if (!$part) continue;
            $ids[$slot] = (int)$part->id;
            $sizes[$slot] = isset($sz[$slot]) ? preg_replace('/[^A-Za-z0-9\- ]/', '', $sz[$slot]) : '';
            $subtotal += (float)$part->price;
            $lines[] = array('slot' => $slot, 'id' => (int)$part->id, 'type' => $part->part_type, 'label' => $part->name, 'color' => $part->color_name, 'size' => $sizes[$slot], 'price' => (float)$part->price);
            $outfit[$slot] = array('type' => $part->part_type, 'style' => $part->style, 'color' => $part->color_hex);
        }
        $pct = count($lines) === 3 ? (float)setting('package_discount', 10) : 0;
        $save = round($subtotal * $pct / 100);
        return array('ids' => $ids, 'sizes' => $sizes, 'lines' => $lines, 'outfit' => $outfit, 'subtotal' => $subtotal,
            'package_discount_pct' => $pct, 'package_saving' => $save, 'total' => $subtotal - $save);
    }

    /* ---------- utils ---------- */

    public function csv($s)
    {
        $a = array_filter(array_map('trim', explode(',', (string)$s)), 'strlen');
        return array_values($a);
    }

    /** "Black|#111,White|#fff" => array('Black'=>'#111','White'=>'#fff') */
    public function colors($s)
    {
        $out = array();
        foreach ($this->csv($s) as $c) {
            $p = explode('|', $c);
            $out[trim($p[0])] = isset($p[1]) ? trim($p[1]) : '#111111';
        }
        return $out;
    }
}
