<?php
/**
 * Online_salon_receipts Class
 *
 *   Onlineサロン決済状況テーブル
 *
 * @project     Hairlogy
 * @package     Table model
 * @author      yuki.hatano@gmail.com
 * @copyright   montecampo Co., Ltd. All Rights Reserved
 */

class Online_salon_receipts_tbl extends MY_Table {

    public function __construct()
    {
        parent::__construct();
    }

    // -----------------------------------------------------------------------------------------------------

    public function get_receipts( $stylist_id ){

        log_debug("Online_salon_receipts_tbl.get_receipts({$stylist_id}) run.");

        $this->_get_receipt_by_stylist( $stylist_id );
        if( $this->find( NULL, NULL, 'online_salon_receipts.month DESC', 0 ) ){
            return $this->get_records();
        }
        return NULL;

    }
    // -----------------------------------------------------------------------------------------------------

    public function get_last_receipt( $stylist_id ){

        log_debug("Online_salon_receipts_tbl.get_last_receipt({$stylist_id}) run.");

        $this->_get_receipt_by_stylist( $stylist_id );
        if( $this->find( NULL, NULL, 'online_salon_receipts.month ASC', 1 ) ){
            return $this->get_row();
        }
        return NULL;

    }

    // -----------------------------------------------------------------------------------------------------

    public function get_current_receipt( $stylist_id ){

        log_debug("Online_salon_receipts_tbl.get_current_receipt({$stylist_id}) run.");

        $this->_get_receipt_by_stylist( $stylist_id );
        $this->where( 'month', date('Y-m-01') );
        if( $this->find() ){
            return $this->get_row();
        }
        return NULL;

    }

    // -----------------------------------------------------------------------------------------------------

    public function put_receipt($stylist_id, $month, $status, $charge_id=null )
    {
        log_debug("Online_salon_receipts_tbl.put_receipt({$stylist_id}:{$month}:{$status}) run.");

        $cond = ['stylist_id' => $stylist_id, 'month' => $month];
        if ($this->delete($cond) === FALSE) {
            return FALSE;
        }
        $set = array_merge($cond, [
            'status' => $status,
            'charge_id' => $charge_id,
        ]);
        if ($this->insert($set) === FALSE) {
            return FALSE;
        }

        return TRUE;
    }

    // -----------------------------------------------------------------------------------------------------

    private function _get_receipt_by_stylist( $stylist_id ){
        $this->select([
            'online_salon_receipts.month',
            'online_salon_receipts.status',
            'online_salon_receipts.charge_id',
            'online_salon_receipts.created_at',
        ]);
        $this->where( 'stylist_id', $stylist_id );
    }

}
