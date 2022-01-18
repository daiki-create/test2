<?php
/**
 * Lp Class
 *  ランディングページ管理
 *
 */

class Lp extends MYSALON_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('landing_page_model');
    }

    public function index()
    {
        $landing_pages = $this->landing_page_model->get_landing_pages($this->_login['salon_id']);
        $this->view->assign('landing_pages', $landing_pages);
        $this->view->assign('today', date('Y-m-d'));
    }

    // ----------------------------------------------------------------------------------------------------------------------------

    public function create()
    {
        $required = [
            'since_date' => FALSE,
            'until_date' => FALSE,
            'lp_url'   => TRUE,
        ];

        if ($this->validate('common', $required) &&
            (empty($this->_post['since_date']) || empty($this->_post['until_date']) || $this->_post['since_date'] < $this->_post['until_date']))
        {
            if ($this->landing_page_model->create_landing_page($this->_login['salon_id'], $this->_post))
            {
                $this->_messages[] = 'ランディングページを登録しました。';
            }
            else
            {
                $this->_error_messages[] = 'ランディングページ登録に失敗しました。';
            }
        }

        $this->redirect("/{$this->_module}/{$this->_class}/index/");
    }

    // ----------------------------------------------------------------------------------------------------------------------------

    public function update()
    {
        $required = [
            'landing_page_id' => TRUE,
            'since_date' => FALSE,
            'until_date' => FALSE,
            'lp_url'   => TRUE,
        ];

        if ($this->validate('common', $required) &&
            (empty($this->_post['since_date']) || empty($this->_post['until_date']) || $this->_post['since_date'] < $this->_post['until_date']))
        {
            if ($this->landing_page_model->update_landing_page($this->_login['salon_id'], $this->_post))
            {
                $this->_messages[] = 'ランディングページを更新しました。';
            }
            else
            {
                $this->_error_messages[] = 'ランディングページ情報の更新に失敗しました。';
            }
        }

        $this->redirect("/{$this->_module}/{$this->_class}/index/");
    }

    // ----------------------------------------------------------------------------------------------------------------------------

    public function delete()
    {
        $required = ['landing_page_id' => TRUE];

        if ($this->validate('common', $required))
        {
            if ($this->landing_page_model->delete_landing_page($this->_login['salon_id'], $this->_post['landing_page_id']))
            {
                $this->_messages[] = 'ランディングページを削除しました。';
            }
            else
            {
                $this->_error_messages[] = 'ランディングページの削除に失敗しました。';
            }
        }

        $this->redirect("/{$this->_module}/{$this->_class}/index/");
    }

}
