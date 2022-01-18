<?php

class Salon_model extends MY_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('tables/salons_tbl');
        $this->load->model('tables/salon_questionnaires_tbl');
        $this->load->model('tables/questionnaire_stylists_tbl');
    }

    // ----------------------------------------------------------------------------------

    public function get_salons($offset=NULL, $status=NULL)
    {
        log_debug("Salon_model.get_salons() run.");
        $this->salons_tbl->initialize('master');

        if ($offset !== NULL)
        {
            $limit = 20;
            $this->sanitize_offset($offset);
            $this->sanitize_limit($limit);
            $this->salons_tbl->init_pagination($offset, $limit);
        }

        if ($status !== NULL)
        {
            $this->salons_tbl->where('status', $status);
        }

        $this->salons_tbl->where('deleted_flag', '0');
        $salons = $this->salons_tbl->find();

        $this->pagination($this->salons_tbl->load_pagination());

        return $salons;
    }

    // ----------------------------------------------------------------------------------

    public function get_salon($salon_id)
    {
        log_debug("Salon_model.get_salon({$salon_id}) run.");
        $this->salon_questionnaires_tbl->initialize($this->salons_tbl->initialize('master'));

        $cond = ['id' => $salon_id];

        $this->salons_tbl->where('deleted_flag', '0');

        if ($this->salons_tbl->find($cond))
        {
            $salon = $this->salons_tbl->get_row();
            $salon['questionnaires'] = $this->salon_questionnaires_tbl->get_questionnaires($salon_id);
            return $salon;
        }

        return NULL;
    }

    // ----------------------------------------------------------------------------------

    public function update_salon($salon_id, $salon)
    {
        log_debug("Salon_model.update_salon({$salon_id}) run.");
        $db = $this->questionnaire_stylists_tbl->initialize(
            $this->salon_questionnaires_tbl->initialize(
                $this->stylists_tbl->initialize(
                    $this->salons_tbl->initialize('master')
                )
            )
        );
        $this->salons_tbl->trans_start();

        $cond = [
            'id' => $salon_id,
        ];

        $data = [
            'name'  => $salon['name'],
            'phone' => $salon['phone'],
            'fax'   => $salon['fax'],
            'postcode1' => $salon['postcode1'],
            'postcode2' => $salon['postcode2'],
            'prefecture'=> $salon['prefecture'],
            'address'   => $salon['address'],
            'note'  => $salon['note'],
        ];

        $this->salons_tbl->where('deleted_flag', '0');

        if ($this->salons_tbl->update($data, $cond) !== FALSE)
        {
            $this->salon_questionnaires_tbl->delete(['salon_id' => $salon_id]);

            $this->load->model('stylist_model');

            foreach ($salon['questionnaire_id'] as $questionnaire_id)
            {
                $salon_questionnaires = [
                    'salon_id' => $salon_id,
                    'questionnaire_id' => $questionnaire_id,
                    'created_at' => date('Y-m-d H:i:s'),
                ];
                $cond = ['salon_id' => $salon_id, 'questionnaire_id' => $questionnaire_id];

                $this->salon_questionnaires_tbl->insert_update($salon_questionnaires, $cond);

                if ($stylists = $this->stylists_tbl->find(['salon_id' => $salon_id, 'deleted_flag' => '0']))
                foreach ($stylists as $stylist)
                {
                    $this->stylist_model->append_questionnaire($salon_id, $questionnaire_id, $stylist['id'], $db);
                }
            }

            return $this->salons_tbl->trans_complete();
        }

        $this->salons_tbl->trans_rollback();
        return FALSE;
    }

    // ----------------------------------------------------------------------------------

    public function update_salon_status($salon_id, $status)
    {
        log_debug("Salon_model.update_salon_status({$salon_id}, {$status}) run.");
        $this->salons_tbl->initialize('master');

        $cond = [
            'id' => $salon_id,
        ];

        if (empty($status))
        {
            $cond['status'] = '1';
        }
        else
        {
            $cond['status'] = '0';
        }

        $data = ['status' => empty($status) ? '0': '1'];
        $this->salons_tbl->where('deleted_flag', '0');
        return ($this->salons_tbl->update($data, $cond) !== FALSE);
    }

    // ----------------------------------------------------------------------------------

    public function create_salon($salon)
    {
        log_debug("Salon_model.create_salon() run.");
        $this->salon_questionnaires_tbl->initialize($this->salons_tbl->initialize('master'));
        $this->salons_tbl->trans_start();

        $data = [
            'name'  => $salon['name'],
            'phone' => $salon['phone'],
            'fax'   => $salon['fax'],
            'prefecture'=> $salon['prefecture'],
            'postcode1' => $salon['postcode1'],
            'postcode2' => $salon['postcode2'],
            'address'   => $salon['address'],
            'note'  => $salon['note'],
        ];

        if ($salon_id = $this->salons_tbl->insert($data))
        {
            foreach ($salon['questionnaire_id'] as $questionnaire_id)
            {
                $this->salon_questionnaires_tbl->insert(['salon_id' => $salon_id, 'questionnaire_id' => $questionnaire_id]);
            }

            if ($this->salons_tbl->trans_complete())
            {
                return $salon_id;
            }
        }

        $this->salons_tbl->trans_rollback();
        return FALSE;
    }

    // ----------------------------------------------------------------------------------

    public function update_status($salon_id, $status)
    {
        log_debug("Salon_model.update_status({$salon_id}, {$status}) run.");
        $this->salons_tbl->initialize('master');

        if (empty($status))
            $new_status = '0';
        else
            $new_status = '1';

        $update_data = ['status' => $new_status];
        $cond = ['id' => $salon_id, 'deleted_flag' => '0'];

        return ($this->salons_tbl->update($update_data, $cond) !== FALSE);
    }

    // ----------------------------------------------------------------------------------

    public function delete_salon($salon_id)
    {
        log_debug("Salon_model.delete_salon({$salon_id}) run.");
        $this->salons_tbl->initialize('master');

        $update_data = ['deleted_flag' => '1'];
        $cond = ['id' => $salon_id, 'status' => '0'];

        return ($this->salons_tbl->update($update_data, $cond) !== FALSE);
    }

}

