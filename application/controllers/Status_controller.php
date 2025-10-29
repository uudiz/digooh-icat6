<?php
class Status_controller extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->lang->load('software');
        $this->load->model('charger_status');
    }
    /**
     * 
     *
     * @param object $curpage [optional]
     * @param object $order_item [optional]
     * @param object $order [optional]
     * @return
     */
    public function index()
    {
        $this->load->model('device');
        $data = $this->get_data();
        if ($this->get_auth() != 5) {
            $data['body_file'] = 'bootstrap/401';
        } else {
            $data['body_file'] = 'bootstrap/charger_setup/index';
        }

        $this->load->view('bootstrap/layout/basiclayout', $data);
    }

    public function getTableData()
    {

        $name = $this->input->post('search');

        $data = $this->get_data();
        $offset = $this->input->post('offset');
        $limit = $this->input->post('limit');
        $order_item = $this->input->post('sort');
        $order = $this->input->post('order');


        $filter_array = array();
        if ($name) {
            $filter_array['name'] = $name;
        }

        $rest = $this->charger_status->get_list($offset, $limit, $order_item, $order, $filter_array);
        $model_arr = $this->lang->line('HW.model');

        $data['total'] = $rest['total'];
        $data['rows']  = $rest['data'];

        echo json_encode($data);
    }

    public function getStatusTableData()
    {

        $this->load->model('charger_status');
        $setting_id = $this->input->post('id');

        $rest = $this->charger_status->get_status_list($this->input->post('id'));


        $data['total'] = ($rest && is_array($rest)) ? count($rest) : 0;
        $data['rows']  = $rest;

        echo json_encode($data);
    }


    public function edit()
    {
        $this->addJs("/assets/js/form.js", false);
        $this->lang->load('font');
        $data = $this->get_data();
        $id = $this->input->get('id');
        $data['title'] = $this->lang->line('create') . " " . $this->lang->line('evCharger');
        if ($id) {
            $data['title'] = $this->lang->line('edit') . " " . $this->lang->line('evCharger');
            $region = $this->charger_status->get_item($id);
            if ($region) {
                $data['data'] = $region;
                $data['body_file'] = 'bootstrap/charger_setup/form';
            } else {
                $data['body_file'] = 'bootstrap/401';
            }
        } else {
            $data['body_file'] = 'bootstrap/charger_setup/form';
        }


        $this->load->view('bootstrap/layout/basiclayout', $data);
    }

    /**
     * Save
     *
     * @return
     */
    public function do_save()
    {
        $result = array();

        $id = $this->input->post('id');

        $this->load->library('form_validation');
        $this->form_validation->set_rules('name', $this->lang->line('firmware'), 'trim|required');

        if ($this->form_validation->run() == false) {
            $result = array('code' => 1, 'msg' => validation_errors());
        } else {
            $this->load->model('device');

            foreach ($this->input->post() as $key => $val) {
                if (
                    $key != "id" && $key != "statusData"
                ) {
                    $data[$key] = $val;
                }
            }
            $data['company_id'] = $this->get_cid();

            $data['updated_at'] = date("Y-m-d H:i:s");

            if ($id > 0) {
                $this->charger_status->update_item($data, $id);
            } else {
                $data['created_at'] = date("Y-m-d H:i:s");
                $id = $this->charger_status->add_item($data);
            }

            if ($id !== false) {
                $statusData = $this->input->post('statusData');
                $this->charger_status->update_status($statusData, $id);
                $data['id'] = $id;
                $result = array('code' => 0, 'msg' => $this->lang->line('save.success'));
                $result = array_merge($data, $result);
            } else {
                $result = array('code' => 1, 'msg' => sprintf($this->lang->line('save.fail'), $this->lang->line('evCharger')));
            }
        }

        echo json_encode($result);
    }


    public function do_delete()
    {
        $result = array();
        $id = $this->input->post("id");


        if ($this->charger_status->delete_item($id)) {

            $result['code'] = 0;
            $result['msg']  = $this->lang->line('delete.success');
        } else {
            $result['code'] = 1;
            $result['msg']  = $this->lang->line('delete.fail');
        }

        echo json_encode($result);
    }


    function detail()
    {
        $data['mpeg_core'] = $this->input->get('mpeg_core');
        $data['firmware_id'] = $this->input->get('id');
        $data['version'] = $this->input->get('version');
        $this->load->view('bootstrap/firmware/detail', $data);
    }
}
