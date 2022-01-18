<?php

class Index extends MYWWW_Controller {

    public function __construct()
    {
        parent::__construct();
    }

    // ---------------------------------------------------------------------------------------------------------------------------

    public function index()
    {
        $test=array(
            array(
                "guide"=>"/",
                "catch_up_image_path"=>"/img/www/index/title-tips.png",
                "catch_up_image_title"=>"img-title1",
                "post_date_gmt"=>"2021-06-05",
                "post_title"=>"ブログ１",
                "post_content"=>"内容内容内容内容内容内容内容内容内容内容内容内容内容内容内容内容内容内容内容内容内容"
            ),
            array(
                "guide"=>"/",
                "catch_up_image_path"=>"/img/www/index/title-tips.png",
                "catch_up_image_title"=>"img-title2",
                "post_date_gmt"=>"2021-08-19",
                "post_title"=>"ブログ２",
                "post_content"=>"内容内容内容内容内容内容内容内容内容内容内容内容内容内容内容内容内容内容内容内容内容"

            ),
            array(
                "guide"=>"/",
                "catch_up_image_path"=>"/img/www/index/title-tips.png",
                "catch_up_image_title"=>"img-title3",
                "post_date_gmt"=>"2021-08-20",
                "post_title"=>"ブログ３",
                "post_content"=>"内容内容内容内容内容内容内容内容内容内容内容内容内容内容内容内容内容内容内容内容内容"

            ),
            array(
                "guide"=>"/",
                "catch_up_image_path"=>"/img/www/index/title-tips.png",
                "catch_up_image_title"=>"img-title2",
                "post_date_gmt"=>"2021-08-19",
                "post_title"=>"ブログ２",
                "post_content"=>"内容内容内容内容内容内容内容内容内容内容内容内容内容内容内容内容内容内容内容内容内容"

            ),
            array(
                "guide"=>"/",
                "catch_up_image_path"=>"/img/www/index/title-tips.png",
                "catch_up_image_title"=>"img-title2",
                "post_date_gmt"=>"2021-08-19",
                "post_title"=>"ブログ２",
                "post_content"=>"内容内容内容内容内容内容内容内容内容内容内容内容内容内容内容内容内容内容内容内容内容"

            ),
        );

        $this->load->model('word_press_model');
        $this->load->model('youtube_model');
        // $this->view->assign('wp_posts', $this->word_press_model->get_wp_posts());
        $this->view->assign('wp_posts', $test);

        $this->view->assign('yt_posts', $this->youtube_model->get_yt_posts(3));

//        $this->_template = "{$this->_class}/{$this->_action}.tpl";
    }

    // ---------------------------------------------------------------------------------------------------------------------------

    public function tos()
    {
    }

    public function manual()
    {
    }

    public function nps()
    {
    }

    public function repeater()
    {
    }
}

