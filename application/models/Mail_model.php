<?php

require_once APPPATH.'core/MY_Table.php';

/**
 * Mail_model class
 */

class Mail_model extends MY_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->library('MY_mailer');
        $this->initialize();
    }

    // -------------------------------------------------------------------------------------------------

    public function initialize($config=NULL, $mail=NULL)
    {
        $this->my_mailer->initialize([]);

        if (is_null($config))
            $config = config_item('smtp_server');

        if (is_null($mail))
            $mail = config_item('notice_mail');

        if ( ! empty($config['server']) && ! empty($config['port']))
        {
            $this->my_mailer->set_smtp_host($config['server']);
            $this->my_mailer->set_smtp_port($config['port']);
        }

        if (ENVIRONMENT != 'production')
            $this->my_mailer->set_to($mail['to']);

        $this->my_mailer->set_from($mail['from']);
    }

    // -------------------------------------------------------------------------------------------------

    /**
     * お問い合わせ通知
     */
    public function send_inquiry($inquiry, $login)
    {
        log_debug("Mail_model.send_inquiry() run.");
        //log_debug($inquiry);

        $mail = config_item('asubi_notice_mail');
        $subject = $this->_set_to_subject( $mail['to'], "【Asubi】 お問い合わせ" );

        $assigns = [
            'name'    => empty($login) ? $inquiry['name'] : $login['name'],
            'mail'    => empty($login) ? $inquiry['mail'] : $login['loginid'],
            'inquiry' => $inquiry['inquiry'],
            'date'    => date('Y-m-d H:i:s'),
        ];
        //log_info($assigns);

        $this->my_mailer->set_subject($subject);
        $this->my_mailer->set_body_by_smarty('asubi/inquiry/send.tpl', $assigns);
        return $this->my_mailer->send();
    }

    // -------------------------------------------------------------------------------------------------

    /**
     * お問い合わせ受付完了通知
     */
    public function send_inquiry_reception($inquiry, $login)
    {
        log_debug("Mail_model.send_inquiry_reception(inquiry, login) run.");
        //log_debug($inquiry);

        $to = empty($login) ? $inquiry['mail'] : $login['loginid'];
        $subject = $this->_set_to_subject( $to, "オンラインサロン「ASuBi」お問い合わせありがとうございます" );

        $assigns = [
            'name'        => empty($login) ? $inquiry['name'] : $login['name'],
            'mail'        => $to,
            'inquiry'     => $inquiry['inquiry'],
            'date'        => date('Y-m-d H:i:s'),
            'inquiry_url' => site_url('/asubi/inquiry/form','https'),
        ];
        //log_info($assigns);

        $this->my_mailer->set_subject($subject);
        $this->my_mailer->set_body_by_smarty('asubi/inquiry/reception.tpl', $assigns);
        return $this->my_mailer->send();
    }

    // -------------------------------------------------------------------------------------------------

    /**
     * 事務局宛：クレジットカード登録通知
     */
    public function send_regist_creditcard($login, $is_re=FALSE)
    {
        log_debug("Mail_model.send_regist_creditcard run.");

        $mail = config_item('asubi_notice_mail');
        $this->my_mailer->set_to($mail['to']);
        $re = empty( $is_re ) ? '' : '再';
        $subject = $this->_set_to_subject( $mail['to'], "【Asubi】クレジットカード{$re}登録通知" );

        $assigns = [
            'name'    => $login['name'],
            'loginid' => $login['loginid'],
            'date'    => date('Y-m-d H:i:s'),
            'url'     => site_url('/sysadm/online_salon_user/checking','https'),
            'is_re'   => $is_re,
        ];
        //log_info($assigns);

        $this->my_mailer->set_subject($subject);
        $this->my_mailer->set_body_by_smarty('asubi/creditcard/register.tpl', $assigns);
        return $this->my_mailer->send();
    }

    // -------------------------------------------------------------------------------------------------

    /**
     * ユーザー宛：初回クレジットカード登録通知
     * @param $login
     * @return mixed
     */
    public function send_regist_creditcard_user($login)
    {

        log_debug("Mail_model.send_regist_creditcard_user run.");

        if( empty( $login['loginid'] ) ){
            log_error( 'mailaddress not found' );
            return FALSE;
        }

        $subject = $this->_set_to_subject( $login['loginid'], "オンラインサロン「ASuBi」クレジットカード登録完了のお知らせ" );

        $assigns = [
            'name'        => $login['name'] ?? '',
            'inquiry_url' => site_url('asubi/inquiry/form','https'),
        ];

        $this->my_mailer->set_subject($subject);
        $this->my_mailer->set_body_by_smarty('asubi/creditcard/register_user.tpl', $assigns);
        return $this->my_mailer->send();

    }

    // -------------------------------------------------------------------------------------------------

    /**
     * ユーザー宛：クレジットカード変更通知
     * @param $login
     * @return mixed
     */
    public function send_change_creditcard_user($login)
    {

        log_debug("Mail_model.send_change_creditcard run.");

        if( empty( $login['loginid'] ) ){
            log_error( 'mailaddress not found' );
            return FALSE;
        }

        $subject = $this->_set_to_subject( $login['loginid'], "オンラインサロン「ASuBi」クレジットカード変更完了のお知らせ" );

        $assigns = [
            'name'    => $login['name'] ?? '',
            'inquiry_url' => site_url('asubi/inquiry/form','https'),
        ];

        $this->my_mailer->set_subject($subject);
        $this->my_mailer->set_body_by_smarty('asubi/creditcard/change_user.tpl', $assigns);
        return $this->my_mailer->send();

    }

    // -------------------------------------------------------------------------------------------------

    /**
     * ユーザー宛：クレジットカード再登録通知
     * @param $login
     * @return mixed
     */
    public function send_re_regist_creditcard_user($login)
    {

        log_debug("Mail_model.send_re_regist_creditcard_user run.");

        if( empty( $login['loginid'] ) ){
            log_error( 'mailaddress not found' );
            return FALSE;
        }

        $subject = $this->_set_to_subject( $login['loginid'], "オンラインサロン「ASuBi」クレジットカード再登録完了のお知らせ" );

        $assigns = [
            'name'        => $login['name'] ?? '',
            'inquiry_url' => site_url('asubi/inquiry/form','https'),
        ];

        $this->my_mailer->set_subject($subject);
        $this->my_mailer->set_body_by_smarty('asubi/creditcard/re_register_user.tpl', $assigns);
        return $this->my_mailer->send();

    }

    // -------------------------------------------------------------------------------------------------

    /**
     * 事務局宛：自己退会
     */
    public function send_asubi_withdrawal( $login )
    {
        log_debug("Mail_model.send_asubi_withdrawal({$login['id']}) run.");

        // 事務局宛
        $mail = config_item('asubi_notice_mail');
        $subject = $this->_set_to_subject($mail['to'], "【Asubi】自己退会通知");

        $assigns = [
            'name'    => $login['name'],
            'loginid' => $login['loginid'],
            'date'    => date('Y-m-d H:i:s'),
        ];

        $this->my_mailer->set_subject($subject);
        $this->my_mailer->set_body_by_smarty('asubi/left/withdrawal.tpl', $assigns);
        return $this->my_mailer->send();
    }

    /**
     * ユーザー宛：自己退会
     */
    public function send_asubi_withdrawal_user( $login )
    {

        log_debug("Mail_model.send_asubi_withdrawal_user({$login['id']}) run.");

        if (empty($login['loginid'])) {
            log_error('mailaddress not found');
            return FALSE;
        }

        $subject = $this->_set_to_subject( $login['loginid'], "オンラインサロン「ASuBi」退会手続き完了のお知らせ" );

        $assigns = [
            'name'         => $login['name'] ?? '',
            'inquiry_url'  => site_url('asubi/inquiry/form','https'),
        ];

        $this->my_mailer->set_subject($subject);
        $this->my_mailer->set_body_by_smarty('asubi/left/withdrawal_user.tpl', $assigns);
        return $this->my_mailer->send();

    }


    // -------------------------------------------------------------------------------------------------

    /**
     * 事務局宛：決済失敗通知
     */
    public function send_asubi_charge_error( $login, $month, $errors )
    {
        log_debug("Mail_model.send_asubi_charge_error({$login['id']}) run.");

        if (empty($login['loginid'])) {
            log_error('mailaddress not found');
            return FALSE;
        }

        // 事務局宛
        $mail = config_item('asubi_notice_mail');
        $subject = $this->_set_to_subject($mail['to'], "【Asubi】定期課金決済エラー通知");

        $assigns = [
            'name'    => $login['name'] ?? '',
            'loginid' => $login['loginid'] ?? '',
            'month'   => $month ?? NULL,
            'errors'  => $errors ?? [],
        ];

        $this->my_mailer->set_subject($subject);
        $this->my_mailer->set_body_by_smarty('asubi/creditcard/charge_error.tpl', $assigns);
        return $this->my_mailer->send();

    }

    /**
     * ユーザー宛：決済失敗通知
     * @param $login
     * @return mixed
     */
    public function send_asubi_charge_error_user($login)
    {

        // ユーザー宛
        $subject = $this->_set_to_subject( $login['loginid'], "オンラインサロン「ASuBi」クレジットカードによる支払不能のお知らせ" );

        // 期限は7日後
        $limit = date('Y/m/d', strtotime('+7Days') );

        $assigns = [
            'name'         => $login['name'] ?? '',
            'limit'        => $limit,
            // TODO::バッチからの送信なので、本番URLを直接指定しています。
            // いづれ、configを整備して環境に合わせたURLになるようにしてください。
            'register_url' => 'https://hairlogy.jp/asubi/creditcard/form/',
            'inquiry_url'  => 'https://hairlogy.jp/asubi/inquiry/form/',
        ];

        $this->my_mailer->set_subject($subject);
        $this->my_mailer->set_body_by_smarty('asubi/creditcard/charge_error_user.tpl', $assigns);
        return $this->my_mailer->send();

    }

    // =================================================================================================

    /**
     * 送信先確定＆顧客宛メールの件名補完共通
     * @param $to
     * @param $subject
     * @return mixed|string
     */
    private function _set_to_subject( $to, $subject ){

        if (ENVIRONMENT == 'production')
        {
            $this->my_mailer->set_to($to);
        }
        else
        {
            if (ENVIRONMENT == 'staging')
                $subject .= "（STG）[本来の宛先:{$to}]";
            else
                $subject .= "（DEV）[本来の宛先:{$to}]";
        }

        return $subject;

    }


}
