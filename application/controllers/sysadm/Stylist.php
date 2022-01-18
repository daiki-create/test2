<?php
/**
 * スタイリスト管理
 */

class Stylist extends MYSYSADM_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('stylist_model');
    }

    // -----------------------------------------------------------------------------------------------------------

    public function index($offset='0')
    {
        $stylists = $this->stylist_model->get_stylists('0', $offset);
        $pagination = $this->stylist_model->pagination();
        //log_debug($stylists);
        //log_debug($pagination);

        $this->view->assign('stylists',     $stylists);
        $this->view->assign('pagination',   $pagination);
    }

    // -----------------------------------------------------------------------------------------------------------

    public function detail($stylist_id=NULL)
    {
        $this->load->model('answer_model');

        if ( ! $stylist = $this->stylist_model->get_stylist('0', $stylist_id))
        {
            $this->redirect("/{$this->_module}/{$this->_class}/");
        }
        log_debug($stylist);

        $replies = $this->answer_model->get_replies($stylist_id, '0');
        log_debug($replies);

        $this->view->assign('stylist',      $stylist);
        $this->view->assign('stylist_id',   $stylist_id);
        $this->view->assign('replies',      $replies);
    }

    // -----------------------------------------------------------------------------------------------------------

    public function form($stylist_id=NULL)
    {
        $this->detail($stylist_id);
    }

    // -----------------------------------------------------------------------------------------------------------

    /**
     * 無所属スタイリスト更新
     */
    public function update($stylist_id=NULL)
    {
        if (empty($stylist_id))
            $this->redirect("/{$this->_module}/{$this->_class}/");

        $required = [
            'loginid'   => TRUE,
            'kana'      => FALSE,
            'name'      => TRUE,
            'phone'     => FALSE,
            'trial_limited_on' => FALSE,
            'note'      => FALSE,
        ];

        if ($this->validate('stylist', $required))
        {
            if ($this->stylist_model->update_stylist('0', $stylist_id, $this->_post))
            {
                $this->_messages = '更新しました。';
                $this->redirect("/{$this->_module}/{$this->_class}/detail/{$stylist_id}/");
            }
            else
            {
                $this->_error_messages = '更新に失敗しました。';
            }
        }

        $this->redirect("/{$this->_module}/{$this->_class}/form/{$stylist_id}/");
    }

    // -----------------------------------------------------------------------------------------------------------

    public function update_status($stylist_id=NULL)
    {
        $status = 'NG';
        $data = NULL;

        $required = [
            'stylist_id'=> TRUE,
            'status'    => TRUE,
        ];

        $this->_post['stylist_id'] = $stylist_id;
        $this->_post['salon_id'] = '0';

        if ($this->validate('stylist', $required) && $stylist = $this->stylist_model->get_stylist('0', $stylist_id))
        {
            if ($this->stylist_model->update_stylist_status('0', $stylist_id, $this->_post['status']))
            {
                $this->_messages = '状態を更新しました。';
            }
            else
            {
                $this->_error_messages = '状態更新に失敗しました。';
            }
        }

        $this->redirect("/{$this->_module}/{$this->_class}/detail/{$stylist_id}/");
    }

    // -----------------------------------------------------------------------------------------------------------

    /**
     * 無所属スタイリスト削除
     */
    public function delete()
    {
        $required = ['stylist_id' => TRUE];

        if ($this->validate('stylist', $required))
        {
            if ($this->stylist_model->delete_stylist('0', $this->_post['stylist_id']))
            {
                $this->_messages = '削除しました。';
            }
            else
            {
                $this->_error_messages = '削除に失敗しました。';
            }
        }

        $this->redirect("/{$this->_module}/{$this->_class}/");
    }

    // -----------------------------------------------------------------------------------------------------------

    /**
     * 無所属スタイリストをサロンに所属登録
     */
    public function belong($stylist_id=NULL)
    {
        $required = ['salon_id' => TRUE];

        if ($this->validate('common', $required))
        {
            if ($this->stylist_model->belong_to($this->_post['salon_id'], $stylist_id))
            {
                $this->_messages = 'サロンへ所属登録しました。';
                $this->redirect("/{$this->_module}/{$this->_class}/");
            }
            else
            {
                $this->_error_messages = 'サロンへの所属登録に失敗しました。';
            }
        }

        $this->redirect("/{$this->_module}/{$this->_class}/detail/{$stylist_id}/");
    }
}

