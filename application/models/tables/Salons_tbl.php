<?php
/**
 * Salons_tbl Class
 *
 *   サロン管理テーブル
 *
 * @project     Hairlogy
 * @package     Table model
 * @author      yuki.hatano@gmail.com
 * @copyright   montecampo Co., Ltd. All Rights Reserved
 */

class Salons_tbl extends MY_Table {

    public function __construct()
    {
        parent::__construct();
    }

    // -----------------------------------------------------------------------------------------------------

    public function get_any_salon($salon_id)
    {
        $this->select([
            'salons.id',
            'salons.name',
            'salons.phone',
            'salons.fax',
            'salons.prefecture',
            'salons.postcode1',
            'salons.postcode2',
            'salons.address',
            'salons.note',
            'salons.created_at',
            'salons.updated_at',
            'salons.status',
            'salons.deleted_flag',
        ]);
        $this->where('salons.id', $salon_id);

        if ($this->find())
            return $this->get_row();

        return NULL;
    }

}

