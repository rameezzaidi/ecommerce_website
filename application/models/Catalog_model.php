<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Catalog_model extends CI_Model
{
    /** Common SELECT with category name + rating */
    protected function base()
    {
        $this->db->select('p.*, c.name AS category_name, c.slug AS category_slug,
            (SELECT ROUND(AVG(r.rating),1) FROM reviews r WHERE r.product_id = p.id AND r.status = 1) AS rating,
            (SELECT COUNT(*) FROM reviews r WHERE r.product_id = p.id AND r.status = 1) AS review_count', false)
            ->from('products p')
            ->join('categories c', 'c.id = p.category_id', 'left')
            ->where('p.status', 1);
    }

    public function category_tree()
    {
        $rows = $this->db->order_by('sort_order, name')->get_where('categories', array('status' => 1))->result();
        $tree = array();
        foreach ($rows as $r) if ($r->parent_id == 0) { $r->children = array(); $tree[$r->id] = $r; }
        foreach ($rows as $r) if ($r->parent_id != 0 && isset($tree[$r->parent_id])) $tree[$r->parent_id]->children[] = $r;
        return array_values($tree);
    }

    public function category_by_slug($slug)
    {
        return $this->db->get_where('categories', array('slug' => $slug, 'status' => 1))->row();
    }

    public function category_ids($cat)
    {
        $ids = array((int)$cat->id);
        if ($cat->parent_id == 0) {
            foreach ($this->db->select('id')->get_where('categories', array('parent_id' => $cat->id))->result() as $c) $ids[] = (int)$c->id;
        }
        return $ids;
    }

    public function product($slug)
    {
        $this->base();
        return $this->db->where('p.slug', $slug)->get()->row();
    }

    public function gallery($pid)
    {
        return $this->db->order_by('sort_order, id')->get_where('product_images', array('product_id' => $pid))->result();
    }

    public function featured($n = 8)
    {
        $this->base();
        return $this->db->where('p.is_featured', 1)->order_by('p.id', 'DESC')->limit($n)->get()->result();
    }

    public function best_selling($n = 8)
    {
        $this->base();
        return $this->db->order_by('p.sold_count', 'DESC')->limit($n)->get()->result();
    }

    public function new_arrivals($n = 8)
    {
        $this->base();
        return $this->db->order_by('p.created_at', 'DESC')->limit($n)->get()->result();
    }

    public function related($p, $n = 4)
    {
        $this->base();
        return $this->db->where('p.category_id', $p->category_id)->where('p.id !=', $p->id)->order_by('p.sold_count', 'DESC')->limit($n)->get()->result();
    }

    public function by_ids($ids)
    {
        if (!$ids) return array();
        $this->base();
        return $this->db->where_in('p.id', $ids)->get()->result();
    }

    /** Shop listing. $f: cat(object), q, min, max, sort, stock, sale, size */
    public function listing($f, $limit, $offset, $countOnly = false)
    {
        $catIds = !empty($f['cat']) ? $this->category_ids($f['cat']) : array();   // run before the builder is primed
        $this->base();
        if ($catIds) $this->db->where_in('p.category_id', $catIds);
        if (!empty($f['q'])) {
            $this->db->group_start()->like('p.name', $f['q'])->or_like('p.short_description', $f['q'])->or_like('p.sku', $f['q'])->group_end();
        }
        if (!empty($f['min'])) $this->db->where('IFNULL(NULLIF(p.sale_price,0), p.price) >=', (float)$f['min'], false);
        if (!empty($f['max'])) $this->db->where('IFNULL(NULLIF(p.sale_price,0), p.price) <=', (float)$f['max'], false);
        if (!empty($f['stock'])) $this->db->where('p.stock >', 0);
        if (!empty($f['sale'])) $this->db->where('p.sale_price >', 0, false)->where('p.sale_price < p.price', null, false);
        if (!empty($f['size'])) $this->db->where("FIND_IN_SET(" . $this->db->escape($f['size']) . ", p.sizes) >", 0, false);
        if ($countOnly) return $this->db->count_all_results();

        switch (isset($f['sort']) ? $f['sort'] : '') {
            case 'price_asc':  $this->db->order_by('IFNULL(NULLIF(p.sale_price,0), p.price)', 'ASC', false); break;
            case 'price_desc': $this->db->order_by('IFNULL(NULLIF(p.sale_price,0), p.price)', 'DESC', false); break;
            case 'popular':    $this->db->order_by('p.sold_count', 'DESC'); break;
            case 'rating':     $this->db->order_by('rating', 'DESC'); break;
            default:           $this->db->order_by('p.id', 'DESC');
        }
        return $this->db->limit($limit, $offset)->get()->result();
    }

    public function price_range()
    {
        $r = $this->db->select('MIN(IFNULL(NULLIF(sale_price,0), price)) mn, MAX(price) mx', false)->get_where('products', array('status' => 1))->row();
        return array((int)floor($r->mn), (int)ceil($r->mx));
    }

    public function reviews($pid)
    {
        return $this->db->order_by('id', 'DESC')->get_where('reviews', array('product_id' => $pid, 'status' => 1))->result();
    }
}
