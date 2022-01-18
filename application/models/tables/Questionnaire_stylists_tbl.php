<?php
/**
 * Questionnaire_stylists_tbl Class
 *
 *   スタイリスト毎のアンケート管理テーブル
 *
 * @project     Hairlogy
 * @package     Table model
 * @author      yuki.hatano@gmail.com
 * @copyright   montecampo Co., Ltd. All Rights Reserved
 */

class Questionnaire_stylists_tbl extends MY_Table {

    public function __construct()
    {
        parent::__construct();
    }

    // -----------------------------------------------------------------------------------------------------

    public function get_questionnaire_of_stylist($code)
    {
        $this->select([
            'questionnaires.id',
            'questionnaires.status',
            'questionnaires.salon_id',
            'questionnaire_stylists.stylist_id',
            'stylists.salon_id as stylist_salon_id',
        ]);
        $this->join('questionnaires', 'questionnaires.id = questionnaire_stylists.questionnaire_id', 'left');
        $this->join('stylists', 'stylists.id = questionnaire_stylists.stylist_id', 'left');
        $this->where('questionnaire_stylists.code', $code);
        $this->where('stylists.status', '1');
        $this->where('questionnaires.deleted_flag', '0');

        if ($this->find())
            return $this->get_row();

        return NULL;
    }

    // -----------------------------------------------------------------------------------------------------

}

