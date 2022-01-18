<?php

/**
 * Class MY_payjp
 *
 * @property MY_http_client $http_client
 */
class MY_payjp {

    private $_payjp_config = [];

    private $_response_error = NULL;

    // -------------------------------------------------------------------------------------

    /**
     * A constructor.
     * Any default configurations can be overridden with a given parameter.
     */
    public function __construct($config=[])
    {
        $this->initialize( $config );
    }

    // -------------------------------------------------------------------------------------

    /**
     * Initialize this object.
     */
    public function initialize( $config=[] )
    {

        log_debug( "MY_payjp.initialize run." );

        $this->_payjp_config = $config;

        $http_config = [

        ];

        $CI =& get_instance();
        $CI->load->library( 'MY_http_client', $http_config, 'http_client' );
        $this->http_client = $CI->http_client;
        log_debug( "MY_payjp Class Initialized" );

    }

    // -------------------------------------------------------------------------------------

    /**
     * クレジットカード情報を登録する
     * @param $customer_id
     * @param $card_token
     * @param bool $default
     * @return false|mixed
     */
    public function create_customer_card( $customer_id, $card_token, $default=TRUE ){

        log_debug( "MY_payjp.regist_card ({$customer_id}) run." );

        $path = "v1/customers/{$customer_id}/cards";
        $res  = $this->_request( $path, [
            'method' => 'POST',
        ],[
            'card'    => $card_token,
            'default' => empty( $default ) ? 'false' : 'true',
        ]);
        if( isset( $res['object'] ) && $res['object'] == 'card' ){
            return $res;
        }
        return $this->_err_return( $res );

    }

    // -------------------------------------------------------------------------------------

    /**
     * 顧客取得
     * @param $customer_id
     * @param false $with_create
     * @param null $email
     * @return false|mixed
     */
    public function load_customer( $customer_id, $with_create=FALSE, $email=NULL ){

        log_debug( "MY_payjp.load_customer ({$customer_id}) run." );

        $path = "v1/customers/{$customer_id}";
        $res  = $this->_request( $path, [
            'method' => 'POST',
        ]);
        if( isset( $res['object'] ) && $res['object'] == 'customer' ){
            return $res;
        }

        if( !empty( $with_create ) ){
            return $this->create_customer( $customer_id, NULL, $email );
        }

        return $this->_err_return( $res );

    }

    // -------------------------------------------------------------------------------------

    /**
     * 顧客が登録しているクレジットカードリストを取得する
     * @param $customer_id
     * @return false|mixed
     */
    public function load_customer_cards( $customer_id ){

        log_debug( "MY_payjp.load_customer_cards ({$customer_id}) run." );

        $path = "v1/customers/{$customer_id}/cards";
        $res  = $this->_request( $path, [
            'method' => 'GET',
        ]);
        if( isset( $res['object'] ) && $res['object'] == 'list' ){
            return $res;
        }
        return $this->_err_return( $res );

    }

    // -------------------------------------------------------------------------------------

    /**
     * 顧客登録
     * @param null $customer_id
     * @param null $card_token
     * @param null $email
     * @return false|mixed
     */
    public function create_customer( $customer_id=NULL, $card_token=NULL, $email=NULL ){

        log_debug( "MY_payjp.create_customer ({$customer_id}) run." );

        $query = [];
        if( !empty( $customer_id ) ) $query['id']    = $customer_id;
        if( !empty( $card_token  ) ) $query['card']  = $card_token;
        if( !empty( $email       ) ) $query['email'] = $email;

        $path = "v1/customers";
        $res  = $this->_request( $path, [
            'method' => 'POST',
        ], $query );
        if( isset( $res['object'] ) && $res['object'] == 'customer' ){
            return $res;
        }
        return $this->_err_return( $res );

    }

    // -------------------------------------------------------------------------------------

    /**
     * 顧客削除
     * @param $customer_id
     * @return bool
     */
    public function delete_customer( $customer_id ){

        log_debug( "MY_payjp.delete_customer ({$customer_id}) run." );

        $path = "v1/customers/{$customer_id}";
        $res  = $this->_request( $path, [
            'method' => 'DELETE',
        ]);
        if( !empty( $res['deleted'] ) ){
            return TRUE;
        }
        return $this->_err_return( $res );

    }

    // -------------------------------------------------------------------------------------

    /**
     * 顧客メールアドレスを更新する
     * @param $customer_id
     * @param $email
     * @return bool
     */
    public function update_customer_email( $customer_id, $email ){

        log_debug( "MY_payjp.update_customer_email ({$customer_id}:{$email}) run." );

        $path = "v1/customers/{$customer_id}";
        $res  = $this->_request( $path, [
            'method' => 'POST',
        ],[
            'email' => $email,
        ]);
        if( isset( $res['object'] ) && $res['object'] == 'customer' ){
            return TRUE;
        }
        return $this->_err_return( $res );

    }

    // -------------------------------------------------------------------------------------

    /**
     * 顧客の登録クレジットカードを全て削除する
     * @param $customer_id
     * @param null $customer_cards
     * @return bool
     */
    public function delete_customer_cards( $customer_id, $customer_cards=NULL ){

        log_debug( "MY_payjp.delete_customer_cards ({$customer_id}) run." );

        if( empty( $customer_cards ) ){
            if( ( $customer = $this->load_customer( $customer_id ) ) === FALSE ){
                return FALSE;
            }
            $customer_cards = $customer['cards']['data'] ?? [];
        }
        if( !empty( $customer_cards ) ){
            foreach( $customer_cards as $customer_card ){
                if( $this->delete_customer_card( $customer_id, $customer_card['id'] ) === FALSE ){
                    return FALSE;
                }
            }
        }
        return TRUE;

    }

    // -------------------------------------------------------------------------------------

    /**
     * クレジットカード情報削除
     * @param $customer_id
     * @param $customer_card_id
     * @return false|mixed
     */
    public function delete_customer_card( $customer_id, $customer_card_id ){

        log_debug( "MY_payjp.delete_card ({$customer_id}:{$customer_card_id}) run." );

        $path = "v1/customers/{$customer_id}/cards/{$customer_card_id}";
        $res  = $this->_request( $path, [
            'method' => 'DELETE',
        ]);
        if( !empty( $res['deleted'] ) ){
            return $res;
        }
        return $this->_err_return( $res );


    }

    // -------------------------------------------------------------------------------------

    /**
     * 定期課金プラン取得（存在しなければ作成します）
     * @param $subscription_plan_conf
     * @return false|mixed
     */
    public function get_subscription_plan( $subscription_plan_conf ){

        log_debug( "MY_payjp.get_subscription_plan run." );

        $path = "v1/plans/{$subscription_plan_conf['id']}";
        $res  = $this->_request( $path, [
            'method' => 'GET',
        ]);
        if( isset( $res['object'] ) && $res['object'] == 'plan' ) {
            return $res;
        }

        // なければ作成しちゃう
        $path = "v1/plans";
        $res  = $this->_request( $path, [
            'method' => 'POST',
        ], $subscription_plan_conf );
        if( isset( $res['object'] ) && $res['object'] == 'plan' ) {
            return $res;
        }
        return $this->_err_return( $res );

    }


    // -------------------------------------------------------------------------------------

    /**
     * 顧客に紐付いた定期課金リストを取得する
     * @param $customer_id
     * @return false|mixed
     */
    public function get_subscriptions( $customer_id ){

        log_debug( "MY_payjp.get_subscriptions ({$customer_id}) run." );

        $path = "v1/customers/{$customer_id}/subscriptions";
        $res  = $this->_request( $path, [
            'method' => 'GET',
        ]);
        if( isset( $res['object'] ) && $res['object'] == 'list' ){
            return $res;
        }
        return $this->_err_return( $res );

    }

    // -------------------------------------------------------------------------------------

    /**
     * 顧客に紐付いた定期課金情報をプランから取得する
     * @param $customer_id
     * @param $subscription_plan_id
     * @return false|mixed
     */
    public function get_subscription_by_plan( $customer_id, $subscription_plan_id ){

        log_debug( "MY_payjp.get_subscription_by_plan ({$customer_id}:{$subscription_plan_id}) run." );

        $subscriptions = $this->get_subscriptions( $customer_id );
        if( !empty( $subscriptions['data'] ) ){
            foreach( $subscriptions['data'] as $data ){
                if( isset( $data['plan']['id'] ) && $data['plan']['id'] == $subscription_plan_id ){
                    return $data;
                }
            }
        }
        return FALSE;

    }

    // -------------------------------------------------------------------------------------

    /**
     * 顧客に定期課金プランを紐付ける
     * @param $customer_id
     * @param $subscription_plan_id
     * @param null $trial_end_date
     * @return false|mixed
     */
    public function set_subscription( $customer_id, $subscription_plan_id, $trial_end_date=NULL ){

        log_debug( "MY_payjp.set_subscription ({$customer_id}:{$subscription_plan_id}) run." );

        if( empty( $customer_id ) || empty( $subscription_plan_id ) ){
            return FALSE;
        }

        $trial_end_time = strtotime( 'first day of next month', strtotime(date('Y-m-1 00:00:00')) );
        if( !empty( $trial_end_date ) ){
            $trial_end_time = strtotime( $trial_end_date );
        }

        $path = "v1/subscriptions";
        $res  = $this->_request( $path, [
            'method' => 'POST',
        ],[
            'customer'  => $customer_id,
            'plan'      => $subscription_plan_id,
            'trial_end' => $trial_end_time,
        ]);
        if( isset( $res['object'] ) && $res['object'] == 'subscription' ){
            return $res;
        }

        // 既に設定済ならロードして返す
        if( isset( $res['error']['code'] ) && strtolower($res['error']['code']) == 'already_subscribed' ){
            return $this->get_subscription_by_plan( $customer_id, $subscription_plan_id );
        }
        return $this->_err_return( $res );

    }

    // -------------------------------------------------------------------------------------

    /**
     * 顧客に紐付けた定期課金プランを削除する
     * @param $subscription_id
     * @return bool
     */
    public function delete_subscription( $subscription_id ){

        log_debug( "MY_payjp.delete_subscription ({$subscription_id}) run." );

        $path = "v1/subscriptions/{$subscription_id}";
        $res  = $this->_request( $path, [
            'method' => 'DELETE',
        ]);
        if( !empty( $res['deleted'] ) || ( isset($res['error']['code'] ) && strtolower($res['error']['code']) == 'invalid_id' ) ){
            return TRUE;
        }
        return $this->_err_return( $res );

    }

    // -------------------------------------------------------------------------------------

    /**
     * 定期課金結果検索を行う
     * @param null $since
     * @param null $until
     * @param int $offset
     * @param int $limit
     * @return false|mixed
     */
    public function get_subscription_charges( $since=NULL, $until=NULL, $offset=0, $limit=100 ){

        log_debug( "MY_payjp.get_subscription_charges run." );

        $query = [
            'offset'  => $offset,
            'limit'   => min( $limit, 100 ),
            'object'  => 'charge',
        ];
        if( !empty( $since ) ) $query['since'] = is_numeric($since) ? $since : strtotime($since);
        if( !empty( $until ) ) $query['until'] = is_numeric($until) ? $until : strtotime($until);

        $path = "v1/events";
        $res  = $this->_request( $path, [
            'method' => 'GET',
        ], $query );

        if( isset( $res['object'] ) && $res['object'] == 'list' ){

            $subscription_data = [];
            if( !empty( $res['data'] ) ){
                foreach( $res['data'] as $rec ){
                    if( !empty( $rec['data']['subscription'] ) ){
                        $subscription_data[] = $rec;
                    }
                }
            }
            $res['count'] = count( $subscription_data );
            $res['data']  = $subscription_data;
            return $res;

        }
        return $this->_err_return( $res );

    }

    // -------------------------------------------------------------------------------------

    /**
     * 与信を行う（即時売上確定可）
     * @param $customer_id
     * @param $amount
     * @param bool $capture
     * @param null $currency
     * @return false|mixed
     */
    public function do_charge( $customer_id, $amount, $capture=TRUE, $currency=NULL ){

        log_debug( "MY_payjp.do_charge ({$customer_id}:{$amount}) run." );

        if( empty( $customer_id ) || empty( $amount ) ){
            return FALSE;
        }
        if( empty( $currency ) ){
            $currency = $this->_payjp_config['default_currency'] ?? NULL;
        }
        if( !$this->_check_amount_renge($amount) ){
            return FALSE;
        }

        $path = "v1/charges";
        $res  = $this->_request( $path, [
            'method' => 'POST',
        ],[
            'customer'  => $customer_id,
            'amount'    => intval($amount),
            'currency'  => $currency,
            'capture'   => empty($capture) ? 'false' : 'true',
        ]);
        if( isset( $res['object'] ) && $res['object'] == 'charge' && !empty( $res['paid'] ) ){
            return $res;
        }
        return $this->_err_return( $res );

    }

    /**
     * 与信売上を確定する
     * @param $charge_id
     * @param null $amount
     * @return false|mixed
     */
    public function do_capture( $charge_id, $amount=NULL ){

        log_debug( "MY_payjp.do_capture ({$charge_id}:{$amount}) run." );

        $query = [];
        if( !empty( $amount ) ){
            if( !$this->_check_amount_renge( $amount ) ) {
                return FALSE;
            }
            $query['amount'] = intval($amount);
        }


        $path = "v1/charges/{$charge_id}/capture";
        $res  = $this->_request( $path, [
            'method' => 'POST',
        ], $query );
        if( isset( $res['object'] ) && $res['object'] == 'charge' && !empty( $res['paid'] ) ){
            return $res;
        }
        return $this->_err_return( $res );

    }

    // -------------------------------------------------------------------------------------

    /**
     * 返金（与信取消）を行う
     * @param $charge_id
     * @param null $amount
     * @return false|mixed
     */
    public function refund( $charge_id, $amount=NULL ){

        log_debug( "MY_payjp.refund ({$charge_id}:{$amount}) run." );

        $query = [];
        if( !empty( $amount ) ){
            $query['amount'] = intval($amount);
        }

        $path = "v1/charges/{$charge_id}/refund";
        $res  = $this->_request( $path, [
            'method' => 'POST',
        ], $query );
        if( isset( $res['object'] ) && $res['object'] == 'charge' ){
            return $res;
        }
        return $this->_err_return( $res );

    }

    // -------------------------------------------------------------------------------------

    /**
     * 決済明細を取得する
     * @param $charge_id
     * @return false|mixed
     */
    public function get_charge( $charge_id ){

        log_debug( "MY_payjp.get_charge ({$charge_id}) run." );

        $path = "v1/charges/{$charge_id}";
        $res  = $this->_request( $path, [
            'method' => 'POST',
        ] );
        if( isset( $res['object'] ) && $res['object'] == 'charge' ){
            return $res;
        }
        return $this->_err_return( $res );

    }

    // -------------------------------------------------------------------------------------

    /**
     * リクエスト送信
     * @param $path
     * @param array $config
     * @param array $query
     * @return false|mixed
     */
    private function _request( $path, $config=[], $query=[] ){

        $url = "https://{$this->_payjp_config['private_key']}:@api.pay.jp/{$path}";

        try{

            log_debug( $query );
            $this->_response_error = NULL;

            $this->http_client->initialize();

            $this->http_client->set_url( $url );
            $this->http_client->set_config( $config );
            $this->http_client->set_query( $query );
            $this->http_client->request();

            if( $response = $this->http_client->json_decode() )
            {
                log_debug( $response );
                return $response;
            }

        }catch( Exception $ex ){
            log_error( $ex->getMessage() );
        }
        return FALSE;

    }

    // -------------------------------------------------------------------------------------

    /**
     * APIレスポンスエラーを取得する
     * @return mixed
     */
    public function get_response_error(){
        return $this->_response_error;
    }

    // -------------------------------------------------------------------------------------

    /**
     * エラー返却
     * @param $res
     * @return false
     */
    private function _err_return( $res ){
        $this->_response_error = $res['error'] ?? NULL;
        return FALSE;
    }

    // -------------------------------------------------------------------------------------

    /**
     * 決済額範囲チェック
     * @param $amount
     * @return bool
     */
    private function _check_amount_renge( $amount ){
        if( intval($amount) < 50 || 9999999 < intval($amount) ){
            log_error( 'amount range error' );
            return FALSE;
        }
        return TRUE;
    }

}

