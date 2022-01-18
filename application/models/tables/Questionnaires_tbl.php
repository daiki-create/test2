<?php
/**
 * Questionnaires_tbl Class
 *
 *   アンケート管理テーブル
 *
 * @project     Hairlogy
 * @package     Table model
 * @author      yuki.hatano@gmail.com
 * @copyright   montecampo Co., Ltd. All Rights Reserved
 */

class Questionnaires_tbl extends MY_Table {

    public function __construct()
    {
        parent::__construct();
    }

    // -----------------------------------------------------------------------------------------------------

    public function get_mc_questionnaires($salon_id)
    {
        $this->select([
            'questionnaires.id',
            'questionnaires.title',
            'questionnaires.type',
            'questionnaires.status',
        ]);
        $this->join('salon_questionnaires', 'salon_questionnaires.questionnaire_id = questionnaires.id');
        $this->where('salon_questionnaires.salon_id', $salon_id);
        $this->where('questionnaires.salon_id', '0'); // サロン共通
        $this->where('questionnaires.status', '1');
        $this->where('questionnaires.deleted_flag', '0');

        return $this->find();
    }

    // -----------------------------------------------------------------------------------------------------

    public function get_questionnaires($salon_id)
    {
        $this->select([
            'questionnaires.id',
            'questionnaires.title',
            'questionnaires.type',
            'questionnaires.status',
        ]);
        $this->where('questionnaires.salon_id', $salon_id);
        $this->where('questionnaires.status', '1');
        $this->where('questionnaires.deleted_flag', '0');

        return $this->find();
    }

    // -----------------------------------------------------------------------------------------------------

    public function get_my_questionnaires($stylist_id, $salon_id, $with_total=FALSE)
    {
        $this->select([
            'questionnaire_stylists.*',
            'questionnaires.title',
            'questionnaires.status',
        ]);

        if ($with_total === TRUE)
        {
            // 今月
            $first_day = date('Y-m-01');
            $last_day = date('Y-m-t');

            $this->select("(SELECT count(`replies`.`id`) FROM replies
                            WHERE `replies`.`questionnaire_id` = `questionnaires`.`id`
                            AND stylist_id = `questionnaire_stylists`.`stylist_id`
                            AND created_at BETWEEN '{$first_day}' AND '{$last_day}')
                            AS this_month_total", FALSE);

            // 3ヵ月
            $first_day = date('Y-m-01', strtotime('-2 month'));

            $this->select("(SELECT count(`replies`.`id`) FROM replies
                            WHERE `replies`.`questionnaire_id` = `questionnaires`.`id`
                            AND stylist_id = `questionnaire_stylists`.`stylist_id`
                            AND created_at BETWEEN '{$first_day}' AND '{$last_day}')
                            AS three_month_total", FALSE);

            // 6ヵ月
            $first_day = date('Y-m-01', strtotime('-5 month'));

            $this->select("(SELECT count(`replies`.`id`) FROM replies
                            WHERE `replies`.`questionnaire_id` = `questionnaires`.`id`
                            AND stylist_id = `questionnaire_stylists`.`stylist_id`
                            AND created_at BETWEEN '{$first_day}' AND '{$last_day}')
                            AS six_month_total", FALSE);
        }

        $this->join('questionnaire_stylists', 'questionnaire_stylists.questionnaire_id = questionnaires.id');
        $this->join(
            'salon_questionnaires',
            'salon_questionnaires.questionnaire_id = questionnaires.id AND salon_questionnaires.salon_id = ' . $this->escape($salon_id)
        );
        // $this->where('questionnaires.status', '1');
        $this->where('questionnaires.deleted_flag', '0');
        $this->where('questionnaire_stylists.stylist_id', $stylist_id);
        $this->group_start();
        $this->where('questionnaires.salon_id', $salon_id);
        $this->or_where('salon_questionnaires.salon_id', $salon_id);
        $this->group_end();

        return $this->find();
    }

}

