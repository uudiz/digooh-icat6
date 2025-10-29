<?php
class SspController extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->lang->load('ssp');
    }

    public function index()
    {
        $data = $this->get_data();
        if ($this->get_auth() != 10) {
            $data['body_file'] = 'bootstrap/401';
        } else {
            $data['body_file'] = 'bootstrap/ssp_status/index';
        }

        $this->load->view('bootstrap/layout/basiclayout', $data);
    }

    public function getHourlyData()
    {
        $this->load->model('SspModel');
        $ret = $this->SspModel->getHourlyData();
        $data['total'] = $ret['total'];
        $data['rows']  = $ret['data'];

        echo json_encode($data);
    }
    public function getDailyData()
    {
        $this->load->model('SspModel');
        $ret = $this->SspModel->getDailyData();
        $data['total'] = $ret['total'];
        $data['rows']  = $ret['data'];

        echo json_encode($data);
    }

    public function getPlayerLogData()
    {

        $filter = array();
        foreach ($this->input->post() as $key => $value) {
            if ($key == 'player_filter') {
                $filter['player_filter'] = $value;
            } else if ($key == 'compaign_filter') {
                $filter['compaign_filter'] = $value;
            } else if ($key == 'media_filter') {
                $filter['media_filter'] = $value;
            } else if ($key == "start_date") {
                $filter['start_date'] = $value;
            } else if ($key == "end_date") {
                $filter['end_date'] = $value;
            }
        }
        $this->load->model('SspModel');
        $ret = $this->SspModel->getPlayerLog($filter);
        $data['total'] = $ret['total'];
        $data['rows']  = $ret['data'];

        echo json_encode($data);
    }
}
