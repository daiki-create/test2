<?php

/**
 * Class Creditcard
 *
 * @property Stylist_model $stylist_model;
 * @property Online_salon_model $online_salon_model;
 * @property Mail_model $mail_model;
 *
 */
class Creditcard extends MYASUBI_Controller {

    public function __construct()
    {
        parent::__construct();

        $this->load->model('stylist_model');
        $this->load->model('online_salon_model');

        $this->config->load('asubi');
        $payjp_config = config_item('payjp');
        $this->view->assign( 'payjp_config', $payjp_config );

        $this->load->model('mail_model');
        $this->mail_model->initialize(NULL, config_item('asubi_notice_mail'));

    }

    // --------------------------------------------------------------------------------------------------------------------------

    /**
     * デフォルト
     */
    public function index()
    {

        if( $this->_login['online_salon_status'] == 'new' ){
            $this->redirect("/{$this->_module}/{$this->_class}/form/");
        }
        if( $this->_login['online_salon_status'] == 'checking' ){
            $this->redirect("/{$this->_module}/{$this->_class}/checking/");
        }

        $this->redirect("/{$this->_module}/mypage/");

    }

    /**
     * (旧)リンク先
     */
    public function change(){
        $this->redirect("/{$this->_module}/{$this->_class}/form/");
    }

    /**
     * クレジットカード登録
     */
    public function form()
    {
        if( $this->_login['online_salon_status'] == 'checking' ){
            $this->redirect("/{$this->_module}/{$this->_class}/checking/");
        }
        $this->_token_set();
    }


    // --------------------------------------------------------------------------------------------------------------------------

    /**
     * クレジットカード初回登録実行
     */
    public function register()
    {

        if( !$this->_token_check() || empty( $this->_post['payjp_token'] ) || $this->_login['online_salon_status'] != 'new' ){
            $this->_error_messages = '最初からやり直して下さい';

        }else{

            if( $this->online_salon_model->regist_creditcard( $this->_login['id'], $this->_post['payjp_token'] ) ){

                // 事務局宛カメール送信
                $this->mail_model->initialize(NULL, config_item('asubi_notice_mail'));
                $this->mail_model->send_regist_creditcard($this->_login);

                // ユーザー宛メール送信
                $this->mail_model->initialize(NULL, config_item('asubi_notice_mail'));
                $this->mail_model->send_regist_creditcard_user($this->_login);

                $this->redirect("/{$this->_module}/{$this->_class}/created/");

            }else{
                $this->_error_messages[] = 'クレジットカード情報の登録に失敗しました';
            }

        }

        $this->redirect("/{$this->_module}/{$this->_class}/form/");

    }

    // --------------------------------------------------------------------------------------------------------------------------

    /**
     * クレジットカード初回登録完了
     */
    public function created()
    {
        if( $this->_login['online_salon_status'] != 'checking' ){
            $this->redirect("/{$this->_module}/{$this->_class}/");
        }
    }

    // --------------------------------------------------------------------------------------------------------------------------

    /**
     * クレジットカード確認中
     */
    public function checking()
    {
        if( $this->_login['online_salon_status'] != 'checking' ){
            $this->redirect("/{$this->_module}/{$this->_class}/");
        }
    }

    // --------------------------------------------------------------------------------------------------------------------------

    /**
     * クレジットカード再登録実行
     */
    public function re_register(){

        if( !$this->_token_check() || empty( $this->_post['payjp_token'] )
            || $this->_login['online_salon_status'] == 'new' || $this->_login['online_salon_status'] == 'checking' ){
            $this->_error_messages = '最初からやり直して下さい';

        }else{

            if( $this->online_salon_model->update_creditcard( $this->_login['id'], $this->_post['payjp_token'] ) ){

                // 状況確認のために再ロード
                $stylist = $this->online_salon_model->get_online_salon_stylist( $this->_login['id'] );
                if( $stylist['online_salon_status'] == 'active' ){

                    // ユーザー宛変更通知メール送信
                    $this->mail_model->initialize(NULL, config_item('asubi_notice_mail'));
                    $this->mail_model->send_change_creditcard_user($this->_login);

                    // 変更完了
                    $this->redirect("/{$this->_module}/{$this->_class}/updated/");

                }elseif( $stylist['online_salon_status'] == 'checking' ){

                    // 事務局宛再登録通知メール送信
                    $this->mail_model->initialize(NULL, config_item('asubi_notice_mail'));
                    $this->mail_model->send_regist_creditcard($this->_login, TRUE );

                    // ユーザー宛再登録通知メール送信
                    $this->mail_model->initialize(NULL, config_item('asubi_notice_mail'));
                    $this->mail_model->send_re_regist_creditcard_user($this->_login);

                    // 登録完了
                    $this->redirect("/{$this->_module}/{$this->_class}/created");

                }


            }else{
                $this->_error_messages[] = 'クレジットカード情報の更新に失敗しました';
            }

        }

        $this->redirect("/{$this->_module}/{$this->_class}/form/");

    }

    // --------------------------------------------------------------------------------------------------------------------------

    /**
     * クレジットカード更新完了
     */
    public function updated(){
        if( $this->_login['online_salon_status'] != 'active' ){
            $this->redirect("/{$this->_module}/{$this->_class}/");
        }
    }

    // ==========================================================================================================================

    /**
     * カード登録Token作成
     */
    private function _token_set(){
        $asubi_creditcard_token = substr(base_convert(md5(uniqid()), 16, 36), 0, 32);
        $this->view->assign('asubi_creditcard_token', $asubi_creditcard_token );
        $this->session->set_userdata('asubi_creditcard_token', $asubi_creditcard_token );
    }

    // --------------------------------------------------------------------------------------------------------------------------

    /**
     * カード登録Tokenチェック
     * @return bool
     */
    private function _token_check(){

        $asubi_creditcard_token = $this->session->userdata('asubi_creditcard_token');
        $this->session->unset_userdata('asubi_creditcard_token');
        if( empty( $asubi_creditcard_token ) ||
            empty( $this->_post['asubi_creditcard_token'] ) ||
            strcmp ( $asubi_creditcard_token, $this->_post['asubi_creditcard_token'] ) !== 0 ) {

            return FALSE;
        }

        return TRUE;

    }

    // --------------------------------------------------------------------------------------------------------------------------

}
