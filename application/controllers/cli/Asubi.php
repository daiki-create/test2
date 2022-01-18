<?php

/**
 * Class Asubi
 *
 * @property Online_salon_model $online_salon_model;
 * @property Mail_model $mail_model;
 *
 */
class Asubi extends MYCLI_Controller {

    public function __construct()
    {
        parent::__construct();

        $this->config->load('asubi');

        $this->load->model('online_salon_model');
        $this->load->model('mail_model');

    }

    /**
     * 定期課金実行
     * @param null $param
     */
    public function subscription( $param=NULL ){

        // 当月課金待ち取得
        $month = date('Y-m-01');

        // 引数指定OK
        if( !empty( $param ) ){
            $t = strtotime( $param );
            if( empty( $t ) ){
                die( 'unknown date format.' );
            }
            $month = date('Y-m-01', $t );
        }

        // 課金免除履歴の更新
        if( $this->online_salon_model->put_ignore_result($month) === FALSE ){
            log_error('課金免除履歴の更新に失敗しました。処理は続行します');
        }

        // 課金対象リスト取得
        $target_stylists = $this->online_salon_model->get_receipt_target_stylists( $month );
        if( empty( $target_stylists ) ){
            log_debug('subscription target is empty');
            exit;
        }

        // 課金実行
        foreach( $target_stylists as $target_stylist ){
            if( !$this->online_salon_model->subscription_charge( $target_stylist['id'], $month ) ){

                // 事務局宛決済エラー通知
                $this->mail_model->initialize( NULL, config_item('asubi_notice_mail') );
                $this->mail_model->send_asubi_charge_error( $target_stylist, $month, $this->online_salon_model->error_messages() );

                // ユーザー宛決済エラー通知
                $this->mail_model->initialize( NULL, config_item('asubi_notice_mail') );
                $this->mail_model->send_asubi_charge_error_user( $target_stylist );

            }
        }

    }

}

