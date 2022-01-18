<?php

/**
 * Class Online_salon_model
 * @property Online_salon_receipts_tbl $online_salon_receipts_tbl
 * @property Sns_authentications_tbl $sns_authentications_tbl
 * @property Stylists_tbl $stylists_tbl
 *
 * @property MY_payjp $payjp
 *
 */
class Online_salon_model extends MY_Model {

    private $_payjp_conf = NULL;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('tables/stylists_tbl');
        $this->load->model('tables/online_salon_receipts_tbl');
        $this->load->model('tables/sns_authentications_tbl');

        $this->config->load('asubi');
        $this->_payjp_conf = config_item('payjp');
        $this->load->library( 'MY_payjp', $this->_payjp_conf, 'payjp' );

    }

    // ----------------------------------------------------------------------------------

    /**
     * クレジットカード登録（初回）
     * @param $stylist_id
     * @param $card_token
     * @return false
     */
    public function regist_creditcard( $stylist_id, $card_token ){

        log_debug("Online_salon_model.regist_creditcard({$stylist_id}) run.");

        $this->_db = $this->online_salon_receipts_tbl->initialize(
            $this->stylists_tbl->initialize( $this->_master )
        );

        // 直前チェック
        $stylist = $this->get_online_salon_stylist( $stylist_id );
        if( empty( $stylist ) ){
            log_error('online salon stylist not found');
            return FALSE;
        }
        if( empty( $stylist['online_salon_status'] ) || $stylist['online_salon_status'] != 'new' ){
            log_error('online_salon_status is not new');
            return FALSE;
        }

        // 顧客登録しつつ、クレジットカード情報を登録する
        if( !$this->payjp->create_customer( $stylist['payjp_customer_id'], $card_token, $stylist['loginid'] ?? NULL ) ) {
            log_error('payjp register card failed');
            return FALSE;
        }

        // new -> checking に更新
        $cond = [
            'online_salon_status' => 'new',
            'id' => $stylist_id,
        ];
        $set = [
            'online_salon_status' => 'checking',
        ];
        return $this->stylists_tbl->update( $set, $cond );

    }

    // ----------------------------------------------------------------------------------

    /**
     * 入会審査中のスタイリストを有効化する
     * @param $stylist_id
     * @return bool
     */
    public function activate_online_salon_stylist( $stylist_id ){

        log_debug("Online_salon_model.activate_online_salon_stylist({$stylist_id}) run.");

        $this->_db = $this->online_salon_receipts_tbl->initialize(
            $this->stylists_tbl->initialize( $this->_master )
        );

        // 最新ロード
        $stylist = $this->get_online_salon_stylist( $stylist_id );
        if( empty( $stylist ) ){
            log_error('online salon stylist not found');
            return FALSE;
        }

        // 再入会の場合
        if( !empty( $stylist['online_salon_activate_at'] ) ){

            // 当月分の即時決済が必要？
            $current_receipt = $this->online_salon_receipts_tbl->get_current_receipt($stylist_id);
            if( empty( $current_receipt['status'] ) || $current_receipt['status'] == 'failed' ){

                // 決済！
                if( !$this->subscription_charge( $stylist_id, $this->_make_month() ) ){

                    // 決済できなかった場合は、審査中に戻す
                    $this->stylists_tbl->update(['online_salon_status'=>'checking'], ['id'=>$stylist_id]);
                    $this->error_messages(['当月分の決済に失敗しました']);
                    return FALSE;

                }

            }

            // おかえりなさい
            $set = [
                'online_salon_status' => 'active',
                'online_salon_activate_at' => date('Y-m-d H:i:s'),
            ];
            $cond = [
                'id' => $stylist_id,
                'online_salon_status' => 'checking',
            ];
            return $this->stylists_tbl->update( $set, $cond );

        }

        // --- 通常はこっち

        // DB更新開始
        $this->stylists_tbl->trans_start();

        // checking -> active に更新
        $cond = [
            'id' => $stylist_id,
            'online_salon_status' => 'checking',
            'online_salon_activate_at' => NULL,
        ];
        $set = [
            'online_salon_status'=>'active',
            'online_salon_activate_at'=> date('Y-m-d H:i:s'),
        ];
        if( !$this->stylists_tbl->update( $set, $cond ) ){
            $this->stylists_tbl->trans_rollback();
            return FALSE;
        }

        // 登録月は無料
        if( !$this->online_salon_receipts_tbl->insert([
            'stylist_id' => $stylist_id,
            'status'     => 'free',
            'month'      => $this->_make_month(),
        ]) ){
            $this->stylists_tbl->trans_rollback();
            return FALSE;
        }

        // いらっしゃいませ
        return $this->stylists_tbl->trans_complete();

    }


    // ----------------------------------------------------------------------------------

    /**
     * クレジットカード再登録
     * @param $stylist_id
     * @param $card_token
     * @return bool
     */
    public function update_creditcard( $stylist_id, $card_token ){

        log_debug("Online_salon_model.update_creditcard({$stylist_id}) run.");

        $this->_db = $this->online_salon_receipts_tbl->initialize(
            $this->stylists_tbl->initialize( $this->_master )
        );

        // 直前チェック
        $stylist = $this->get_online_salon_stylist( $stylist_id );
        if( empty( $stylist ) ){
            log_error('online salon stylist not found');
            return FALSE;
        }
        if( empty( $stylist['online_salon_status'] ) || $stylist['online_salon_status'] == 'new' || $stylist['online_salon_status'] == 'checking' ){
            log_error('online_salon_status is invalid');
            return FALSE;
        }

        // Payjp顧客情報取得
        $payjp_customer = $this->payjp->load_customer( $stylist['payjp_customer_id'] );
        if( empty( $payjp_customer ) ){
            log_error('payjp customer load failed');
            return FALSE;
        }

        // クレジットカード情報削除
        if( !empty( $payjp_customer['cards']['data'] ) ){
            if( !$this->payjp->delete_customer_cards( $stylist['payjp_customer_id'], $payjp_customer['cards']['data'] ) ){
                log_error('payjp card delete failed');
                // 失敗しても続行します
            }
        }

        // クレジットカード再登録
        if( !$this->payjp->create_customer_card( $stylist['payjp_customer_id'], $card_token ) ){
            log_error('payjp register card failed');
            return FALSE;
        }

        // 有効会員なら、おしまい
        $stylist = $this->get_online_salon_stylist( $stylist_id );
        if( $stylist['online_salon_status'] == 'active' ) {
            return TRUE;

        // 有効でない課金免除会員なら、決済が成功した事にする（免除）
        }elseif(!empty($stylist['online_salon_charge_ignore_flag'])) {

            // 有効に更新
            $this->online_salon_receipts_tbl->trans_start();
            $this->stylists_tbl->where('id',$stylist_id);
            if( FALSE === $this->stylists_tbl->update(['online_salon_status' => 'active']) ){
                return FALSE;
            }
            // 免除履歴を挿入
            if (FALSE === $this->online_salon_receipts_tbl->put_receipt($stylist_id, $this->_make_month(), 'ignore')) {
                $this->online_salon_receipts_tbl->trans_rollback();
                return FALSE;
            }
            return $this->online_salon_receipts_tbl->trans_complete();

        // 無効会員なら、即時決済実行
        }elseif( $stylist['online_salon_status'] == 'inactive' ){
            return $this->subscription_charge( $stylist_id, $this->_make_month() );

        // 退会中なら、審査中に更新
        }else if( $stylist['online_salon_status'] == 'left' ){
            return $this->stylists_tbl->update([
                'online_salon_status' => 'checking',
            ],[
                'id' => $stylist_id,
                'online_salon_status' => 'left',
            ]);

        }

        // 上記に引っかからないケースはありえません
        return FALSE;

    }

    // ----------------------------------------------------------------------------------

    /**
     * 退会（ with クレジットカード情報削除）
     * @param $stylist_id
     * @return false
     */
    public function left_online_salon_stylist( $stylist_id ){

        log_debug("Online_salon_model.left_online_salon_stylist({$stylist_id}) run.");

        $this->_db = $this->online_salon_receipts_tbl->initialize(
            $this->stylists_tbl->initialize( $this->_master )
        );

        // 直前ロード
        $stylist = $this->get_online_salon_stylist( $stylist_id );
        if( empty( $stylist ) ){
            log_error('online salon stylist not found');
            return FALSE;
        }

        // 退会に更新
        $this->stylists_tbl->where('id',$stylist_id);
        $this->stylists_tbl->where_in('online_salon_status',['active','inactive']);
        if( !$this->stylists_tbl->update(['online_salon_status' => 'left']) ){
            return FALSE;
        }

        // クレジットカード情報削除
        $this->payjp->delete_customer_cards( $stylist['payjp_customer_id'] );

        // 退会しました
        return TRUE;

    }

    // ----------------------------------------------------------------------------------

    /**
     * 会員削除・入会審査NG
     * @param $stylist_id
     * @return bool
     */
    public function delete_online_salon_stylist( $stylist_id ){

        log_debug("Online_salon_model.delete_online_salon_stylist({$stylist_id}) run.");

        $this->_db = $this->sns_authentications_tbl->initialize(
            $this->stylists_tbl->initialize( $this->_master )
        );

        $stylist = $this->get_online_salon_stylist( $stylist_id );
        if( empty( $stylist ) ){
            log_error('online salon stylist not found');
            return FALSE;
        }

        // 再登録ユーザーの削除は退会処理を行う
        if( !empty( $stylist['online_salon_activate_at'] ) ){
            $this->stylists_tbl->where('id',$stylist_id);
            $this->stylists_tbl->where('online_salon_status','checking');
            if( !$this->stylists_tbl->update(['online_salon_status' => 'left']) ){
                return FALSE;
            }

            // クレジットカード情報削除
            $this->payjp->delete_customer_cards( $stylist['payjp_customer_id'] );
            return TRUE;

        }

        // 会員論理削除
        $data = [
            'deleted_flag' => '1',
            'loginid'      => NULL,
        ];
        $this->stylists_tbl->where('id',$stylist_id);
        $this->stylists_tbl->where_in('online_salon_status',['new','checking']);
        if( !$this->stylists_tbl->update( $data, ['id' =>$stylist_id]) ){
            return FALSE;
        }

        // payjp顧客情報を削除
        $this->payjp->delete_customer( $stylist['payjp_customer_id'] );

        // 抹殺しました
        return TRUE;

    }

    // ----------------------------------------------------------------------------------

    /**
     * 定期課金除外フラグを更新する
     * @param $stylist_id
     * @return bool
     */
    public function charge_ignore_online_salon_stylist($stylist_id, $flag ){

        log_debug("Online_salon_model.charge_ignore_online_salon_stylist({$stylist_id}:{$flag}) run.");

        $this->_db = $this->sns_authentications_tbl->initialize(
            $this->stylists_tbl->initialize( $this->_master )
        );

        $stylist = $this->get_online_salon_stylist( $stylist_id );
        if( empty( $stylist ) ){
            log_error('online salon stylist not found');
            return FALSE;
        }

        // 課金除外フラグ更新
        $data = [
            'online_salon_charge_ignore_flag' => empty($flag) ? '0' : '1'
        ];
        $this->stylists_tbl->where('id', $stylist_id);
        if (!$this->stylists_tbl->update($data, ['id' => $stylist_id])) {
            return FALSE;
        }

        // 更新完了
        return TRUE;

    }

    // ----------------------------------------------------------------------------------

    /**
     * オンラインサロン登録スタイリスト取得
     * @param $stylist_id
     * @return mixed|null
     */
    public function get_online_salon_stylist( $stylist_id )
    {

        log_debug("Online_salon_model.get_online_salon_stylist({$stylist_id}) run.");

        $this->_db = $this->online_salon_receipts_tbl->initialize(
            $this->stylists_tbl->initialize($this->_master)
        );

        $stylist = $this->stylists_tbl->get_online_salon_stylist( $stylist_id );
        if( !empty( $stylist ) ){
            $stylist['payjp_customer_id'] = $this->make_payjp_customer_id($stylist['id']);
            return $stylist;
        }

        // いねーよ
        return NULL;

    }

    // ----------------------------------------------------------------------------------

    /**
     * 定期課金履歴を取得する
     * @param $stylist_id
     * @return mixed|null
     */
    public function get_online_salon_stylist_receipts( $stylist_id ){

        log_debug("Online_salon_model.get_online_salon_stylist_receipts({$stylist_id}) run.");

        $this->_db = $this->online_salon_receipts_tbl->initialize($this->_master);
        return $this->online_salon_receipts_tbl->get_receipts( $stylist_id );

    }

    // ----------------------------------------------------------------------------------

    /**
     * Payjp用の顧客IDを整形する
     * @param $customer_id
     * @return string
     */
    public function make_payjp_customer_id( $stylist_id ){
        $this->config->load('asubi');
        $payjp_config = config_item('payjp');
        return "{$payjp_config['customer_prefix']}_{$stylist_id}";
    }

    // ----------------------------------------------------------------------------------

    /**
     * 定期課金対象ユーザーリスト取得
     * @param $month
     * @return array|false
     */
    public function get_receipt_target_stylists($month){

        log_debug("Online_salon_model.get_receipt_target_stylists({$month}) run.");

        if( ( $month = $this->_make_month($month) ) === FALSE ){
            return FALSE;
        }
        $this->stylists_tbl->initialize( $this->_master );
        $res = $this->stylists_tbl->get_online_salon_receipt_target_stylists($month);
        if( !empty( $res ) ){
            foreach ( $res as $k => $rec ){
                $res[$k]['payjp_customer_id'] = $this->make_payjp_customer_id($rec['id']);
            }
        }
        return $res;

    }

    // ----------------------------------------------------------------------------------

    /**
     * 定期課金除外対象ユーザーの定期課金結果（除外）を設定する
     * @param $month
     * @return bool
     */
    public function put_ignore_result($month)
    {
        log_debug("Online_salon_model.set_receipt_ignore({$month}) run.");

        if( ( $month = $this->_make_month($month) ) === FALSE ){
            return FALSE;
        }

        $this->_db = $this->online_salon_receipts_tbl->initialize(
            $this->stylists_tbl->initialize($this->_master)
        );
        // DB更新開始
        $this->online_salon_receipts_tbl->trans_start();

        // 免除対象リスト取得
        $search = [
            'status' => ['active','inactive'],  // 有効or無効ステータス
            'charge_ignore' => '1',  // 課金免除フラグ：オン
        ];
        $users = $this->stylists_tbl->get_online_salon_users($search);

        // 免除履歴を挿入
        foreach ( $users ?? [] as $user ){
            if (FALSE === $this->online_salon_receipts_tbl->put_receipt($user['id'], $month, 'ignore')) {
                $this->online_salon_receipts_tbl->trans_rollback();
                return FALSE;
            }
        }

        // 登録完了
        return $this->online_salon_receipts_tbl->trans_complete();
    }

    // ----------------------------------------------------------------------------------

    /**
     * 定期課金を実行する
     * @param $stylist_id
     * @param $month
     * @return bool
     */
    public function subscription_charge( $stylist_id, $month ){

        log_debug("Online_salon_model.subscription_charge({$stylist_id}:{$month}) run.");

        // 定期課金設定取得
        $amount = $this->_payjp_conf['subscription_plan']['amount'] ?? NULL;
        if( empty( $amount ) ){
            log_error( 'plan amount not found' );
            return FALSE;
        }

        // 与信実行
        $charge_res = $this->payjp->do_charge( $this->make_payjp_customer_id($stylist_id), $amount, FALSE );
        if( empty( $charge_res['id'] ) ){

            // 与信失敗 -> 結果登録
            $errors = $this->payjp->get_response_error();
            $this->error_messages( $errors );
            if( !$this->_put_receipt_result( $stylist_id, $this->_make_month($month), $errors['charge'] ?? '', FALSE ) ){
                $this->error_messages( 'DB update error.');
            }
            return FALSE;

        }

        // 与信成功 -> 結果登録
        if( !$this->_put_receipt_result( $stylist_id, $this->_make_month($month), $charge_res['id'] ) ){

            $this->error_messages( 'DB update error!!!' );
            log_error( 'DB update error.');

            // 与信の取り消し
            if( !$this->payjp->refund($charge_res['id']) ){
                $this->error_messages( 'charge refund error' );
            }
            return FALSE;

        }

        // 売上確定
        if( !$this->payjp->do_capture($charge_res['id']) ){
            $this->error_messages( 'charge capture failed' );
            $errors = $this->payjp->get_response_error();
            if( empty( $errors ) ){
                $this->error_messages( $errors );
            }
            return FALSE;

        }

        // OK
        return TRUE;

    }

    // ----------------------------------------------------------------------------------

    /**
     * 決済明細を取得する
     * @param $charge_id
     * @return false|mixed
     */
    public function get_charge( $charge_id ){
        log_debug("Online_salon_model.get_charge ({$charge_id}) run.");
        return $this->payjp->get_charge( $charge_id );
    }

    // ==================================================================================

    /**
     * 定期課金結果を登録する
     * @param $stylist_id
     * @param $month
     * @param $charge_id
     * @param $is_success
     * @return bool
     */
    private function _put_receipt_result( $stylist_id, $month, $charge_id, $is_success=TRUE ){

        log_debug("Online_salon_model.put_receipt_result ({$stylist_id}:{$month}:{$charge_id}:{$is_success}) run.");

        if( ( $month = $this->_make_month($month) ) === FALSE ){
            return FALSE;
        }

        $this->_db = $this->online_salon_receipts_tbl->initialize(
            $this->stylists_tbl->initialize($this->_master)
        );

        // DB更新開始
        $this->stylists_tbl->trans_start();

        // オンラインサロンステータス更新
        $set  = ['online_salon_status' => ( empty($is_success) ? 'inactive' : 'active' )];
        $cond = ['id'=>$stylist_id];
        if( $this->stylists_tbl->update( $set, $cond ) === FALSE ){
            $this->stylists_tbl->trans_rollback();
            return FALSE;
        }

        // 定期課金履歴挿入
        $status = (empty($is_success) ? 'failed' : 'paid');
        if (FALSE === $this->online_salon_receipts_tbl->put_receipt($stylist_id, $month, $status, $charge_id)) {
            $this->stylists_tbl->trans_rollback();
            return FALSE;
        }

        // 登録完了
        return $this->stylists_tbl->trans_complete();

    }

    // ----------------------------------------------------------------------------------

    /**
     * 対象月を整形する
     * @param $month
     * @return false|string
     */
    private function _make_month($month=NULL){
        if( empty( $month ) ){
            $month = 'today';
        }
        if( ( $time = strtotime($month) ) === FALSE ){
            return FALSE;
        }
        return date('Y-m-01',$time);
    }

}

