<?php

class Word_press_model extends MY_Model {

    private $_wordpress_db = NULL;

    public function __construct()
    {
        parent::__construct();
        $this->_wordpress_db = 'wordpress';
        $this->load->model('wordpress/wp_posts_tbl');
    }

    // ----------------------------------------------------------------------------------------------------------

    /**
     * WordPress　投稿取得
     */
    public function get_wp_posts($limit='5')
    {
        log_debug("Word_press_model.get_wp_posts() run.");
        $this->wp_posts_tbl->initialize($this->_wordpress_db);
        return $this->wp_posts_tbl->get_wp_posts($limit);
    }

}