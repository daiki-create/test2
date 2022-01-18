<?php

class Questionnaire_validation extends Common_validation {

    public function __construct($post=[])
    {
        parent::__construct($post);
        $this->_key_maps = array_merge($this->_key_maps, array(
            'type'          => 'アンケート種別',
            'title'         => 'アンケート名',
            /*
            'type'          => '回答種別',
            'question'      => '質問',
            'min_label'     => '最低評価ラベル',
            'min_level'     => '最低評価値',
            'max_label'     => '最高評価ラベル',
            'max_level'     => '最高評価値',
            'selections'    => '選択肢',
            'message'       => 'メッセージ',
            'sub_question'  => 'フリー入力ラベル',
            */
        ));
        log_debug('Questionnaire_validation Initialized.');
    }

    public function _is_type($val)
    {
        switch ($val)
        {
            case 'hair_salon':
            case 'esthetic_salon':
                return TRUE;
            default:
                return FALSE;
        }
    }

    public function _is_title($val, $max=50)
    {
        return parent::_is_name($val, $max);
    }

    /*
    public function _is_type($val)
    {
        switch ($val)
        {
            case 'level':
            case 'select_one':
            case 'select_multi':
            case 'text':
            case 'message':
                return TRUE;
            default:
                return FALSE;
        }
    }

    public function _is_question($val, $max=100)
    {
        return parent::_is_name($val, $max);
    }

    public function _is_min_label($val, $max=20)
    {
        return parent::_is_name($val, $max);
    }

    public function _is_max_label($val, $max=20)
    {
        return parent::_is_name($val, $max);
    }

    public function _is_message($val, $max=100)
    {
        return parent::_is_name($val, $max);
    }

    public function _is_sub_question($val, $max=100)
    {
        return parent::_is_name($val, $max);
    }

    public function _is_min_level($val)
    {
        return parent::_is_int($val, 0, 10);
    }

    public function _is_max_level($val)
    {
        return parent::_is_int($val, 0, 10);
    }

    public function _is_selections($val)
    {
        $result = TRUE;

        if (is_array($val)) foreach ($val as $v)
        {
            if ( ! parent::_is_name($v, 20))
            {
                $result = FALSE;
                break;
            }
        }

        return $result;
    }
    */

}

