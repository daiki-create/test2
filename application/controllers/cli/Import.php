<?php

class Import extends MYCLI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('questionnaire_model');
        $this->load->model('answer_model');
        $this->load->helper('security');
    }

    public function replies($questionnaire_id, $salon_id, $stylist_id, $csv_file_name)
    {
        $csv_file_name = sanitize_filename($csv_file_name);
        $success = 0;
        $total = 0;

        if ($questionnaire_id && $stylist_id && file_exists("{$this->_tmp_dir}/import/{$csv_file_name}"))
        {
            if ( ! ($questionnaires = $this->questionnaire_model->get_my_questionnaires($stylist_id, '0', FALSE)))
            {
                log_error("This stylist dose not have any questionnaires.");
                echo "This stylist dose not have any questionnaires.\n";
                return FALSE;
            }

            $questionnaires = array_column($questionnaires, NULL, 'questionnaire_id');

            if ( ! isset($questionnaires[$questionnaire_id]['code']) OR ! $code = $questionnaires[$questionnaire_id]['code'])
            {
                log_error("This stylist dose not have the questionnaire. [questionnaie_id: {$questionnaire_id}]");
                echo "This stylist dose not have the questionnaire. [questionnaie_id: {$questionnaire_id}]\n";
                return FALSE;
            }

            if ($questions = $this->questionnaire_model->get_questions($questionnaire_id))
            {
                $count = count($questions);
                $questions = array_column($questions, NULL, 'number');
                unset($questions[0]);

                $questionnaire_stylist = [
                    'stylist_id'       => $stylist_id,
                    'questionnaire_id' => $questionnaire_id
                ];

                $csv_file = new SplFileObject("{$this->_tmp_dir}/import/{$csv_file_name}");
                $csv_file->setFlags(SplFileObject::READ_CSV);

                foreach ($csv_file as $i => $row)
                {
                    $total++;

                    if ($count == count($row))
                    {
                        foreach ($questions as $num => $question)
                        {
                            if ($$question['type'] == 'level')
                                $value = $row[$num-1];
                            else
                                $value = mb_convert_encoding($row[$num-1], 'UTF-8');
                                //$value = mb_convert_encoding($row[$num-1], 'UTF-8', 'sjis-win');

                            $answer[$question['type']][$question['id']] = $value;
                        }

                        $created_at = array_pop($row);

                        //log_debug($answer);
                        if ($this->answer_model->create_reply($questionnaire_stylist, $answer,  $created_at))
                        {
                            $success++;
                        }
                        else
                        {
                            log_error("Failed to insert answer data.");
                            log_error($answer);
                            echo "Failed to insert answer data.\n";
                            var_export($answer);
                        }
                    }
                }
            }
        }
        else
        {
            log_error("Invalid Arguments or Not found CSV File [{$this->_tmp_dir}/import/{$csv_file_name}");
            echo "Invalid Arguments or Not found CSV File [{$this->_tmp_dir}/import/{$csv_file_name}]\n";
        }

        echo "Data imported {$success} / {$total}.\n";

        exit;
    }

}
