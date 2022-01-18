<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Index extends MYWWW_Controller {

    public function __construct()
    {
        parent::__construct();
    }

    // ---------------------------------------------------------------------------------------------------------------------------------------

	public function index($code=NULL)
	{
        if ($cookie = get_cookie("questionnaire-{$code}"))
        {
            $yyyymmdd = $this->encrypt->decode($cookie);
            log_debug("last access: {$yyyymmdd}");
            $reply_interval_days = config_item('reply_interval_days');

            if ($yyyymmdd > date('Ymd', strtotime("-{$reply_interval_days} days")))
            {
                $this->redirect("/{$this->_module}/index/done/{$code}");
            }
            else
            {
                delete_cookie("questionnaire-{$code}");
            }
        }
        
        $this->load->model('questionnaire_model');
        $questionnaire = $this->questionnaire_model->get_questionnaire_of_stylist($code);
        //log_debug($questionnaire);

        if ( ! empty($questionnaire['questions']))
            $progress_width = round((1 / count($questionnaire['questions'])) * 70);

        $this->view->assign('questionnaire',  $questionnaire);
        $this->view->assign('code', $code);
        $this->view->assign('progress_width', $progress_width);
        
	}

    // ---------------------------------------------------------------------------------------------------------------------------------------

    public function complete()
    {
        $code = $this->post('code');

        $this->load->model('questionnaire_model');

        if ($questionnaire = $this->questionnaire_model->get_questionnaire_of_stylist($code, FALSE))
        {
            $this->load->model('answer_model');

            if ($this->answer_model->create_reply($questionnaire, $this->post()))
            {
                $yyyymmdd = $this->encrypt->encode(date('Ymd'));
                log_debug("questionnaire-{$code}: {$yyyymmdd}");
                set_cookie("questionnaire-{$code}", $yyyymmdd, 60*60*24*config_item('reply_interval_days'));
            }

            $this->redirect("/{$this->_module}/index/done/{$code}");
        }

        $this->redirect("/q/{$code}");
    }

    // ---------------------------------------------------------------------------------------------------------------------------------------

    public function done($code=NULL)
    {
        $this->load->model('questionnaire_model');
        $this->load->model('landing_page_model');

        $yyyymmdd = NULL;

        if ($cookie = get_cookie("questionnaire-{$code}"))
        {
            $yyyymmdd = date('Y-m-d', strtotime($this->encrypt->decode($cookie)));
        }

        if ($questionnaire = $this->questionnaire_model->get_questionnaire_of_stylist($code, FALSE))
        {
            $lp_url = $this->landing_page_model->current_landing_page($questionnaire['stylist_salon_id']);
            $this->view->assign('lp_url', $lp_url);
        }
        $this->view->assign('yyyymmdd', $yyyymmdd);
    }

}
