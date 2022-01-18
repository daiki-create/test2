<?php

/**
 * Class Salon
 *
 * @property Stylists_model $stylists_model;
 * @property Salon_mail_model $salon_mail_model;
 *
 */
class Salon extends MYCLI_Controller {

    public function __construct()
    {
        parent::__construct();

        $this->config->load('hairlogy');

        $this->load->model('stylist_model');
        $this->load->model('salon_mail_model');

    }

    /**
     * 強制退会
     * @param null $param
     */
    // public function unsubscribe( $param=NULL )
    public function unsubscribe()
    {
        
        $month = date('Y-m-01');
        $min_month=date('Y-m-01',strtotime('-2 month'));

         // 手動強制退会用　引数指定OK
        // if( !empty( $param ) ){
        //     $t = strtotime( $param );
        //     if( empty( $t ) ){
        //         die( 'unknown date format.' );
        //     }
        //     $month = date('Y-m-01', $t );
        // }

        // 強制退会対象リスト取得　支払いが3か月間失敗し続けている人
        $target_stylists = $this->stylist_model->get_unsubscribe_target_stylists( $min_month, $month );
        if( empty( $target_stylists ) ){
            log_debug('unsubscribe target is empty');
            exit;
        }

        // 強制退会実行
        foreach( $target_stylists as $target_stylist )
        {

            // 退会処理
            if( $this->stylist_model->left_stylist( $target_stylist['salon_id'], $target_stylist['id'] ))
            {
                // 管理者へ通知(↓適当)
                // $this->salon_mail_model->initialize( NULL, config_item('notice_mail') );
                // $this->salon_mail_model->send_unsubscribe( $target_stylist, $month, $this->salon_mail_model->error_messages() );

                // ユーザーへ通知(↓適当)
                //     $this->salon_mail_model->initialize( NULL, config_item('notice_mail') );
                //     $this->salon_mail_model->send_unsubscribe_user( $target_stylist );

                log_debug('強制退会に成功');
            }
            else
            {
                log_debug('強制退会に失敗');
            }
        }
    }

}

