<?php
/**
 * スタイリスト管理
 */

class Stylist extends MYSALON_Controller {

    public function __construct()
    {
        parent::__construct();

        if (empty($this->_login['manager_flag']))
        {
            $this->redirect("/{$this->_module}/");
        }

        $this->load->model('stylist_model');
    }

    // -----------------------------------------------------------------------------------------------------------

    public function index()
    {
        $stylists = $this->stylist_model->get_stylists($this->_login['salon_id']);
        log_debug($stylists);
        $this->view->assign('stylists', $stylists);
    }

    // -----------------------------------------------------------------------------------------------------------

    public function detail($stylist_id=NULL)
    {
        $stylist = $this->stylist_model->get_stylist($this->_login['salon_id'], $stylist_id);
        $this->view->assign('stylist',      $stylist);
        $this->view->assign('stylist_id',   $stylist_id);
    }

    // -----------------------------------------------------------------------------------------------------------

    public function form($stylist_id=NULL)
    {
        $stylist = $this->stylist_model->get_stylist($this->_login['salon_id'], $stylist_id);

        if (empty($stylist['agreement_flag']))
            $this->redirect("/{$this->_module}/{$this->_class}/detail/{$stylist_id}/");

        $this->view->assign('stylist',      $stylist);
        $this->view->assign('stylist_id',   $stylist_id);
    }

    // -----------------------------------------------------------------------------------------------------------

    public function update($stylist_id=NULL)
    {
        if (empty($stylist_id))
            $this->redirect("/{$this->_module}/{$this->_class}/");

        $required = [
            'kana'      => FALSE,
            'name'      => TRUE,
            'phone'     => FALSE,
            'note'      => FALSE,
            'manager_flag'  => FALSE,
            'status'    => TRUE,
        ];

        if ($this->validate('stylist', $required))
        {
            $this->_post['manager_flag'] = empty($this->_post['manager_flag']) ? '0' : '1';

            if ($this->stylist_model->update_stylist($this->_login['salon_id'], $stylist_id, $this->_post))
            {
                $this->_messages = '更新しました。';
                $this->redirect("/{$this->_module}/{$this->_class}/detail/{$stylist_id}/");
            }
            elseif ($error_messages = $this->stylist_model->error_messages())
            {
                log_error($error_messages);
                $this->_error_messages = $error_messages;
            }
            else
            {
                $this->_error_messages = '更新に失敗しました。';
            }
        }

        $this->redirect("/{$this->_module}/{$this->_class}/form/{$stylist_id}/");
    }

    // -----------------------------------------------------------------------------------------------------------

    public function delete()
    {
        $required = ['stylist_id' => TRUE];
        $stylist_id = '';

        if ($this->validate('stylist', $required))
        {
            $stylist_id = $this->_post['stylist_id'];

            if ($this->stylist_model->delete_stylist($this->_login['salon_id'], $stylist_id))
            {
                $this->_messages = 'スタイリスト登録を削除しました。';
                $this->redirect("/{$this->_module}/{$this->_class}/");
            }
            elseif ($error_messages = $this->stylist_model->error_messages())
            {
                log_error($error_messages);
                $this->_error_messages = $error_messages;
            }
            else
            {
                $this->_error_messages = 'スタイリスト登録の削除に失敗しました。';
            }
        }

        $this->redirect("/{$this->_module}/{$this->_class}/detail/{$stylist_id}/");
    }

}
