<?php

class Landing_page_model extends MY_Model {

    private $_db = 'master';

    public function __construct()
    {
        parent::__construct();
        $this->load->model('tables/landing_pages_tbl');
    }

    // -----------------------------------------------------------------------------------------------------

    public function current_landing_page($salon_id)
    {
        log_debug("Questionnaire_model.current_landing_pages({$salon_id}) run.");
        $this->_db = $this->landing_pages_tbl->initialize($this->_db);
        $this->landing_pages_tbl->where('salon_id', $salon_id);
        $this->landing_pages_tbl->group_start();
        $this->landing_pages_tbl->where('since_date <= CURDATE()', NULL, FALSE);
        $this->landing_pages_tbl->or_where('since_date IS NULL', NULL, FALSE);
        $this->landing_pages_tbl->group_end();
        $this->landing_pages_tbl->group_start();
        $this->landing_pages_tbl->where('until_date >= CURDATE()', NULL, FALSE);
        $this->landing_pages_tbl->or_where('until_date IS NULL', NULL, FALSE);
        $this->landing_pages_tbl->group_end();
        $order_by = ['since_date asc', 'until_date asc'];

        if ($current_lps = $this->landing_pages_tbl->find(NULL, NULL, $order_by))
        {

            $i = rand(0, count($current_lps) - 1);
            return $current_lps[$i]['lp_url'];
        }

        return [];
    }

    // -----------------------------------------------------------------------------------------------------

    public function get_landing_pages($salon_id)
    {
        log_debug("Questionnaire_model.get_landing_pages({$salon_id}) run.");
        $this->_db = $this->landing_pages_tbl->initialize($this->_db);

        $this->landing_pages_tbl->where('salon_id', $salon_id);
        /*
        $this->landing_pages_tbl->group_start();
        $this->landing_pages_tbl->where('since_date <= CURDATE()', NULL, FALSE);
        $this->landing_pages_tbl->or_where('since_date IS NULL', NULL, FALSE);
        $this->landing_pages_tbl->group_end();
        $this->landing_pages_tbl->group_start();
        $this->landing_pages_tbl->where('until_date >= CURDATE()', NULL, FALSE);
        $this->landing_pages_tbl->or_where('until_date IS NULL', NULL, FALSE);
        $this->landing_pages_tbl->group_end();
        */
        $order_by = ['since_date asc', 'until_date asc'];
        return $this->landing_pages_tbl->find(NULL, NULL, $order_by);
    }

    // -----------------------------------------------------------------------------------------------------

    public function create_landing_page($salon_id, $landing_page)
    {
        log_debug("Questionnaire_model.create_landing_page({$salon_id}, ) run.");
        $this->_db = $this->landing_pages_tbl->initialize($this->_db);
        $this->landing_pages_tbl->trans_start();
        $landing_pages = $this->get_landing_pages($salon_id);

        if (count($landing_pages) >= 10)
        {
            $this->landing_pages_tbl->trans_rollback();
            return FALSE;
        }

        $_landing_page = [
            'salon_id'  => $salon_id,
            'since_date'    => empty($landing_page['since_date']) ? NULL : $landing_page['since_date'],
            'until_date'    => empty($landing_page['until_date']) ? NULL : $landing_page['until_date'],
            'lp_url'    => $landing_page['lp_url'],
        ];

        $this->landing_pages_tbl->insert($_landing_page);
        return $this->landing_pages_tbl->trans_complete();
    }

    // -----------------------------------------------------------------------------------------------------

    public function update_landing_page($salon_id, $landing_page)
    {
        log_debug("Questionnaire_model.update_landing_page({$salon_id}, landing_page) run.");
        $this->_db = $this->landing_pages_tbl->initialize('master');
        $_landing_page = [
            'since_date'    => empty($landing_page['since_date']) ? NULL : $landing_page['since_date'],
            'until_date'    => empty($landing_page['until_date']) ? NULL : $landing_page['until_date'],
            'lp_url'    => $landing_page['lp_url'],
        ];

        $cond = [
            'id'    => $landing_page['landing_page_id'],
            'salon_id'  => $salon_id,
        ];

        return ($this->landing_pages_tbl->update($_landing_page, $cond) !== FALSE);
    }

    // -----------------------------------------------------------------------------------------------------

    public function delete_landing_page($salon_id, $landing_page_id)
    {
        log_debug("Questionnaire_model.delete_landing_page({$salon_id}, {$landing_page_id}) run.");
        $this->landing_pages_tbl->initialize('master');
        $cond = [
            'id'        => $landing_page_id,
            'salon_id'  => $salon_id,
        ];

        return ($this->landing_pages_tbl->delete($cond) !== FALSE);
    }

    // -----------------------------------------------------------------------------------------------------

}

