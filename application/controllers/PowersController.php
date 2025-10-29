<?php
class PowersController extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->lang->load('onoff');
    }

    public function index()
    {
        $data = $this->get_data();
        if ($this->get_auth() < 4) {
            $data['body_file'] = 'bootstrap/401';
        } else {
            $data['body_file'] = 'bootstrap/offtimes/index';
        }
        $this->load->view('bootstrap/layout/basiclayout', $data);
    }


    public function getTableData()
    {
        $name = $this->input->post('search');

        $this->load->model('power_record');
        $data = $this->get_data();
        $offset = $this->input->post('offset');
        $limit = $this->input->post('limit');
        $order_item = $this->input->post('sort');
        $order = $this->input->post('order');

        $filter_array = array();
        if ($name) {
            $filter_array['name'] = $name;
        }

        $rest = $this->power_record->get_OnOff_list($this->get_cid(), $offset, $limit, $order_item, $order, $filter_array);
        $data['total'] = $rest['total'];
        $data['rows']  = $rest['data'];

        echo json_encode($data);
    }
}
