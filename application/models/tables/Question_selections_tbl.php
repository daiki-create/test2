<?php
/**
 * Question_selections_tbl Class
 *
 *   質問回答選択肢テーブル
 *
 * @project     Hairlogy
 * @package     Table model
 * @author      yuki.hatano@gmail.com
 * @copyright   montecampo Co., Ltd. All Rights Reserved
 */

class Question_selections_tbl extends MY_Table {

    public function __construct()
    {
        parent::__construct();
    }

    // -----------------------------------------------------------------------------------------------------

    public function get_selections($question_id)
    {
        log_debug("get_questions({$question_id}) run.");
        $this->select(['label', 'selection', 'color', 'default_flag']);
        $this->where('question_id', $question_id);
        return $this->find(NULL, NULL, 'priority ASC');
    }

}

