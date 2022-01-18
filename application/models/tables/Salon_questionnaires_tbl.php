<?php
/**
 * Salon_questionnaires_tbl Class
 *
 *   サロン アンケート管理テーブル
 *
 * @project     Hairlogy
 * @package     Table model
 * @author      yuki.hatano@gmail.com
 * @copyright   montecampo Co., Ltd. All Rights Reserved
 */

class Salon_questionnaires_tbl extends MY_Table {

    public function __construct()
    {
        parent::__construct();
    }

    // -----------------------------------------------------------------------------------------------------

    public function get_questionnaires($salon_id)
    {
        $this->select([
            'questionnaires.id',
            'questionnaires.title',
            'questionnaires.status',
        ]);
        $this->join('questionnaires', 'questionnaires.id = salon_questionnaires.questionnaire_id');
        $this->where('salon_questionnaires.salon_id', $salon_id);
        $this->where('questionnaires.status', '1');
        $this->or_where('questionnaires.status', '0');

        if ($questionnaires = $this->find())
        {
            return array_column($questionnaires, NULL, 'id');
        }

        return [];
    }

}

