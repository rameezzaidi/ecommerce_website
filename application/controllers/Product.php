<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product extends Front_Controller
{
    public function view($slug = '')
    {
        $p = $this->catalog->product($slug);
        if (!$p) show_404();
        $this->db->set('views', 'views+1', false)->where('id', $p->id)->update('products');

        $gallery = $this->catalog->gallery($p->id);
        $images = array();
        if ($p->image) $images[] = $p->image;
        foreach ($gallery as $g) $images[] = $g->image;

        $this->render('front/product', array(
            'p' => $p, 'images' => $images,
            'sizes' => $this->shopcart->csv($p->sizes), 'colors' => $this->shopcart->colors($p->colors),
            'reviews' => $this->catalog->reviews($p->id), 'related' => $this->catalog->related($p, 4),
            'wishlist' => $this->wishlist(),
        ), $p->name, (string)$p->short_description);
    }

    public function review($id = 0)
    {
        if ($this->input->method() !== 'post') show_404();
        $p = $this->db->get_where('products', array('id' => (int)$id, 'status' => 1))->row();
        if (!$p) show_404();
        $name = trim($this->input->post('name', true));
        $rating = (int)$this->input->post('rating');
        $comment = trim($this->input->post('comment', true));
        if ($name === '' || $rating < 1 || $rating > 5 || mb_strlen($comment) < 5) {
            flash('error', 'Please enter your name, a rating and a short review.');
        } else {
            $this->db->insert('reviews', array('product_id' => $p->id, 'name' => mb_substr($name, 0, 100), 'rating' => $rating, 'comment' => mb_substr($comment, 0, 1500), 'status' => 0, 'created_at' => date('Y-m-d H:i:s')));
            flash('success', 'Thank you! Your review will appear once approved.');
        }
        redirect('product/' . $p->slug . '#reviews');
    }
}
