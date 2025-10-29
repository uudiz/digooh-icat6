<?php
class Healthy_controller extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
    }
    public function index()
    {
        $data = $this->get_data();
        if ($this->get_auth() < 4) {
            $data['body_file'] = 'bootstrap/401';
        } else {
            $data['body_file'] = 'bootstrap/sensor_records/index';
        }
        $this->load->view('bootstrap/layout/basiclayout', $data);
    }


    public function getTableData()
    {
        $name = $this->input->post('search');

        $this->load->model('sensor_record');
        $data = $this->get_data();
        $offset = $this->input->post('offset');
        $limit = $this->input->post('limit');
        $order_item = $this->input->post('sort');
        $order = $this->input->post('order');

        $filter_array = array();
        if ($name) {
            $filter_array['name'] = $name;
        }
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        if ($start_date && $end_date) {
            $filter_array['start_date'] = $start_date;
            $filter_array['end_date'] = $end_date;
        }

        $notified_only =  $this->input->post('notified_only');
        if ($notified_only) {
            $filter_array['notified_only'] = $this->input->post('notified_only');
        }


        $rest = $this->sensor_record->get_list($this->get_cid(), $offset, $limit, $order_item, $order, $filter_array);
        $data['total'] = $rest['total'];
        $data['rows']  = $rest['data'];

        echo json_encode($data);
    }

    public function getChatDate()
    {
        $this->load->model('sensor_record');
        $res = $this->sensor_record->get_chart_list($this->get_cid());
        $data = array();
        if ($res) {
            foreach ($res as $record) {
                $item['x'] = $record->date;
                $item['y'] = $record->count;
                $data[] = $item;
            }
        }

        echo json_encode($data);
    }
}
