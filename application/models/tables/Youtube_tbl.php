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

class Youtube_tbl extends MY_Table {

    public function __construct()
    {
        parent::__construct();
    }

    // -----------------------------------------------------------------------------------------------------

    public function get_yt_posts($limit)
    {
        $this->select([
            'youtube.video_id',
            'youtube.title',
            'youtube.discription',
            'youtube.published_at',
        ]);
        $this->order_by('youtube.published_at DESC');
        if ( ! is_null($limit))
            $this->limit($limit);

        return $this->find();
    }
}
