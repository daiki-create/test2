<?php

/**
 * Class Online_salon_user
 *
 * @property Stylist_model $stylist_model;
 * @property Online_salon_model $online_salon_model;
 * @property Mail_model $mail_model;
 */
class Online_salon_user extends MYSYSADM_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('stylist_model');
        $this->load->model('online_salon_model');
    }

    // -----------------------------------------------------------------------------------------------------------

    /**
     * 入会済み会員リスト
     * @param string $offset
     */
    public function index($offset='0')
    {
        if ( !$search = $this->session->userdata("{$this->_module}_{$this->_class}_search"))
        {
            $search['status'] = ['active','inactive'];
        }

        $online_salon_users = $this->stylist_model->get_online_salon_users($search, $offset);

        $this->view->assign('search',  $search);
        $this->view->assign('online_salon_users', $online_salon_users);
        $this->view->assign('pagination', $this->stylist_model->pagination());

    }

    // -----------------------------------------------------------------------------------------------------------

    public function search()
    {
        $search = [
            'name'          => $this->_post['name'],
            'loginid'       => $this->_post['loginid'],
            'status'        => $this->_post['status'],
            'charge_ignore' => $this->_post['charge_ignore'],
        ];

        $this->session->set_userdata("{$this->_module}_{$this->_class}_{$this->_action}", $search);
        $this->redirect("/{$this->_module}/{$this->_class}/index/");
    }

    // -----------------------------------------------------------------------------------------------------------

    /**
     * 入会審査待ちリスト
     */
    public function checking(){
        $online_salon_checking_users = $this->stylist_model->get_online_salon_checking_users();
        $this->view->assign('online_salon_checking_users', $online_salon_checking_users);
    }

    // -----------------------------------------------------------------------------------------------------------

    /**
     * 入会審査OK
     */
    public function activate(){

        if( !empty( $this->_post['stylist_id']) ){
            if( $this->online_salon_model->activate_online_salon_stylist( $this->_post['stylist_id'] ) ) {
                $this->_messages[] = '更新しました';
            }
            elseif( $error_messages = $this->online_salon_model->error_messages())
            {
                $this->_error_messages = $error_messages;
            }else{
                $this->_error_messages[] = '更新に失敗しました';
            }
        }

        $this->redirect("/{$this->_module}/{$this->_class}/checking");

    }


    // -----------------------------------------------------------------------------------------------------------

    /**
     * 入会審査NG
     */
    public function deactivate(){

        if( !empty( $this->_post['stylist_id']) ){

            if( $this->online_salon_model->delete_online_salon_stylist( $this->_post['stylist_id'] ) ){
                $this->_messages[] = '拒否しました';
            }else{
                $this->_error_messages[] = 'データ更新に失敗しました';
            }
        }
        $this->redirect("/{$this->_module}/{$this->_class}/checking");

    }

    // -----------------------------------------------------------------------------------------------------------

    /**
     * 会員詳細
     * @param $stylist_id
     */
    public function detail( $stylist_id ){

        $online_salon_user = $this->online_salon_model->get_online_salon_stylist( $stylist_id ?? 0 );
        if( empty( $online_salon_user ) ){
            $this->redirect("/{$this->_module}/{$this->_class}/");
        }
        $online_salon_user['receipts'] = $this->online_salon_model->get_online_salon_stylist_receipts($stylist_id);
        $this->view->assign('online_salon_user', $online_salon_user);

    }

    // -----------------------------------------------------------------------------------------------------------

    /**
     * 強制退会
     */
    public function left(){

        if( !empty( $this->_post['stylist_id']) ){

            if( $this->online_salon_model->left_online_salon_stylist( $this->_post['stylist_id'] ) ){
                $this->_messages[] = '退会しました';
            }else{
                $this->_error_messages[] = 'データの更新に失敗しました';
            }
        }
        $this->redirect("/{$this->_module}/{$this->_class}/detail/{$this->_post['stylist_id']}");

    }

    // -----------------------------------------------------------------------------------------------------------

    /**
     * 登録情報削除
     */
    public function delete(){

        if( !empty( $this->_post['stylist_id']) ){

            if( $this->online_salon_model->delete_online_salon_stylist( $this->_post['stylist_id'] ) ){
                $this->_messages[] = '削除しました';
            }else{
                $this->_error_messages[] = '削除に失敗しました';
            }
        }
        $this->redirect("/{$this->_module}/{$this->_class}/index");

    }

    // -----------------------------------------------------------------------------------------------------------

    /**
     * 課金免除
     */
    public function charge_ignore()
    {

        if( !empty( $this->_post['stylist_id']) && isset($this->_post['flag']) ){

            if( $this->online_salon_model->charge_ignore_online_salon_stylist( $this->_post['stylist_id'], $this->_post['flag'] ) ){
                if( $this->_post['flag'] == '1' )
                    $this->_messages[] = '課金を免除しました';
                else
                    $this->_messages[] = '課金免除を解除しました';
            }else{
                $this->_error_messages[] = 'データの更新に失敗しました';
            }
        }
        $this->redirect("/{$this->_module}/{$this->_class}/detail/{$this->_post['stylist_id']}");
    }



    // ======================================
    // テスト用
    // ======================================
    public function subscription_dev(){

//        if( !defined( 'ENVIRONMENT' ) || ENVIRONMENT == 'production' ){
//            header("HTTP/1.0 404 Not Found");
//            exit;
//        }

        if( empty( $this->_post['ym']) || ( $exec_time = strtotime( $this->_post['ym'] . '-01' ) ) === FALSE ){
            $this->_error_messages[] = '実行対象の年月を正しく設定してください';

        }else{

            $ym    = date('Y-m',    $exec_time );
            $cmdym = date('Y-m-01', $exec_time );

            $command = 'php ' . FCPATH . "index.php cli asubi subscription {$cmdym}";
            @exec( $command, $output, $retval );
            if( empty( $retval ) ){
                $this->_messages[] = "[{$ym}] 定期課金を実行しました";
            }else{
                $this->_error_messages[] = '定期課金バッチの起動に失敗しました';
                $this->_error_messages[] = $command;
                $this->_error_messages[] = print_r( $output, TRUE );
            }
        }

        $this->redirect("/{$this->_module}/{$this->_class}/index");

    }



}

