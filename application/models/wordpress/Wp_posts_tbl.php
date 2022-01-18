<?php
/**
 * Wp_posts_tbl Class
 *
 *   WordPress 投稿記事/ページ/ナビゲーションメニュー 管理テーブル
 *
 * @project     Hairlogy
 * @package     Table model
 * @author      monte.ishida@gmail.com
 * @copyright   montecampo Co., Ltd. All Rights Reserved
 */

class Wp_posts_tbl extends MY_Table {

    public function __construct()
    {
        parent::__construct();
    }

    // -----------------------------------------------------------------------------------------------------

    public function get_wp_posts($limit=NULL)
    {
        $this->select([
            'wp_posts.ID',
            'wp_posts.post_author',
            'wp_posts.post_date',
            'wp_posts.post_date_gmt',
            'wp_posts.post_content',
            'wp_posts.post_title',
            'wp_posts.post_excerpt',
            'wp_posts.post_status',
            'wp_posts.comment_status',
            'wp_posts.ping_status',
            'wp_posts.post_password ',
            'wp_posts.post_name',
            'wp_posts.to_ping',
            'wp_posts.pinged',
            'wp_posts.post_modified',
            'wp_posts.post_modified_gmt',
            'wp_posts.post_content_filtered',
            'wp_posts.post_parent',
            'wp_posts.guid',
            'wp_posts.menu_order',
            'wp_posts.post_type',
            'wp_posts.post_mime_type',
            'wp_posts.comment_count',
            'wp_post_attachments.guid AS catch_up_image_path',
            'wp_post_attachments.post_title AS catch_up_image_title',
        ]);

        $this->join('wp_posts AS wp_post_attachments', 'wp_post_attachments.post_parent = wp_posts.ID', 'LEFT');
        $this->join('wp_postmeta', 'wp_postmeta.post_id = wp_posts.ID', 'left');
        $this->where('wp_posts.post_type', 'post');
        $this->where('wp_posts.post_status', 'publish');
        $this->where('wp_postmeta.meta_key', '_thumbnail_id');
        $this->where('wp_post_attachments.ID = wp_postmeta.meta_value', NULL);
        $this->order_by('wp_posts.post_date_gmt DESC');
        if ( ! is_null($limit))
            $this->limit($limit);

        return $this->find();
    }
}
