<?php
class ActivationController extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('activation');
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
        if ($this->get_auth() < 10) {
            $data['body_file'] = 'bootstrap/401';
        } else {
            $data['body_file'] = 'bootstrap/activation/index';
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
            $filter_array['mac'] = $name;
        }

        $rest = $this->activation->get_list($offset, $limit, $order_item, $order, $filter_array);

        $data['total'] = $rest['total'];
        $data['rows']  = $rest['data'];

        echo json_encode($data);
    }


    public function edit()
    {
        $this->addJs("/assets/js/form.js", false);

        $data = $this->get_data();
        $id = $this->input->get('id');
        $data['title'] = "";



        if ($id) {
            $data['title'] = $this->lang->line('edit') . " Activation";
            $region = $this->activation->get_item($id);
            if ($region) {
                $data['data'] = $region;
            } else {
                $this->show_msg($this->lang->line('warn.param'), 'warn');
                return;
            }
        }

        $data['body_file'] = 'bootstrap/activation/form';
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
        $this->form_validation->set_rules('mac', $this->lang->line('mac'), 'trim|required');

        if ($this->form_validation->run() == false) {
            $result = array('code' => 1, 'msg' => validation_errors());
        } else {
            $this->load->model('activation');

            $mac = $this->input->post('mac');

            $flag = $this->activation->get_item_by_mac($id, $mac);
            if ($id >= 0 && $flag) {
                $result = array('code' => 1, 'msg' => "Mac " . $this->input->post('mac') . " exist");
            } else {
                foreach ($this->input->post() as $key => $val) {
                    if (
                        $key != "id" && $key != "players"
                    ) {
                        $data[$key] = $val;
                    }
                }


                if ($id > 0) {
                    $this->activation->update_item($data, $id);
                } else {
                    $id = $this->activation->add_item($data);
                }

                if ($id !== false) {

                    $data['id'] = $id;
                    $result = array('code' => 0, 'msg' => $this->lang->line('save.success'));
                    $result = array_merge($data, $result);
                } else {
                    $result = array('code' => 1, 'msg' => sprintf($this->lang->line('save.fail'), $this->lang->line('group')));
                }
            }
        }

        echo json_encode($result);
    }


    public function do_delete()
    {
        $result = array();
        $id = $this->input->post("id");


        if ($this->activation->delete_item($id)) {
            $result['code'] = 0;
            $result['msg']  = $this->lang->line('delete.success');
        } else {
            $result['code'] = 1;
            $result['msg']  = $this->lang->line('delete.fail');
        }

        echo json_encode($result);
    }
}
