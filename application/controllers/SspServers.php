<?php

class SspServers extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->lang->load('ssp');
    }

    public function index()
    {
        $data = $this->get_data();
        if ($this->get_auth() <= 2) {
            $data['body_file'] = 'bootstrap/401';
        } else {
            $data['body_file'] = 'bootstrap/ssp_servers/index';
        }
        $this->load->view('bootstrap/layout/basiclayout', $data);
    }

    public function getTableData()
    {
        $this->load->model('Ssp_server_model');

        $search = $this->input->post('search');
        $offset = (int)$this->input->post('offset');
        $limit = (int)$this->input->post('limit');
        $order_item = $this->input->post('sort');
        $order = $this->input->post('order');

        $ret = $this->Ssp_server_model->get_server_list($offset, $limit, $order_item, $order, $search);

        $data['total'] = $ret['total'];
        $data['rows']  = $ret['data'];

        echo json_encode($data);
    }

    public function edit()
    {
        $this->addJs("/assets/js/form.js", false);
        $data = $this->get_data();

        if ($this->get_auth() <= 2) {
            $data['body_file'] = 'bootstrap/401';
            $this->load->view('bootstrap/layout/basiclayout', $data);
            return;
        }

        $id = $this->input->get('id');
        $data['title'] = $this->lang->line('ssp.create.server');

        if ($id) {
            $data['title'] = $this->lang->line('ssp.edit.server');
            $this->load->model('Ssp_server_model');
            $server = $this->Ssp_server_model->get_server($id);
            if ($server) {
                $data['data'] = $server;
            } else {
                $this->show_msg($this->lang->line('warn.param'), 'warn');
                return;
            }
        }

        $data['body_file'] = 'bootstrap/ssp_servers/form';
        $this->load->view('bootstrap/layout/basiclayout', $data);
    }

    public function do_save()
    {
        $result = array();

        if ($this->get_auth() < $this->config->item('auth_admin')) {
            echo json_encode(array('code' => 1, 'msg' => $this->lang->line('warn.system.user')));
            return;
        }

        $id = (int)$this->input->post('id');

        $this->load->library('form_validation');
        $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required');
        $this->form_validation->set_rules('host', $this->lang->line('ssp.host'), 'trim|required');
        $this->form_validation->set_rules('parser_class', $this->lang->line('ssp.parser.class'), 'trim|required');

        if ($this->form_validation->run() == false) {
            $result = array('code' => 1, 'msg' => validation_errors());
        } else {
            $this->load->model('Ssp_server_model');
            $name = $this->input->post('name');

            if ($this->Ssp_server_model->get_server_by_name($id, $name)) {
                $result = array('code' => 1, 'msg' => sprintf($this->lang->line('ssp.name.exists'), $name));
            } else {
                $data = array(
                    'name' => $name,
                    'host' => $this->input->post('host'),
                    'parser_class' => $this->input->post('parser_class'),
                    'priority' => max(0, (int)$this->input->post('priority')),
                    'is_active' => $this->input->post('is_active') ? 1 : 0,
                    'timeout' => max(1, (int)$this->input->post('timeout') ?: 5),
                );

                $old = $id > 0 ? $this->Ssp_server_model->get_server($id) : false;

                // a server used by profiles must not be deactivated
                if ($old && (int)$old->is_active === 1 && $data['is_active'] === 0) {
                    $used_by = $this->Ssp_server_model->get_profiles_by_server($id);
                    if ($used_by) {
                        $result = array('code' => 1, 'msg' => sprintf($this->lang->line('ssp.server.deactivate.blocked'), implode(', ', $used_by)));
                        echo json_encode($result);
                        return;
                    }
                }

                if ($id > 0) {
                    $ok = $this->Ssp_server_model->update_server($data, $id);
                } else {
                    $ok = $this->Ssp_server_model->add_server($data);
                }

                if ($ok !== false) {
                    $msg = $this->lang->line('save.success');

                    // when priority or the active state of a server used by
                    // profiles changed, remind the admin to re-edit them
                    if ($old && ((int)$old->priority !== $data['priority'] || (int)$old->is_active !== $data['is_active'])) {
                        $profiles = $this->Ssp_server_model->get_profiles_by_server($id);
                        if ($profiles) {
                            $msg .= ' ' . sprintf($this->lang->line('ssp.server.change.reminder'), implode(', ', $profiles));
                        }
                    }

                    $result = array('code' => 0, 'msg' => $msg);
                } else {
                    $result = array('code' => 1, 'msg' => sprintf($this->lang->line('save.fail'), $this->lang->line('ssp.servers')));
                }
            }
        }
        echo json_encode($result);
    }

    public function do_delete()
    {
        $result = array();

        if ($this->get_auth() < $this->config->item('auth_admin')) {
            echo json_encode(array('code' => 1, 'msg' => $this->lang->line('warn.system.user')));
            return;
        }

        $id = $this->input->post('id');
        $this->load->model('Ssp_server_model');

        // a server used by profiles must not be deleted
        if ($id) {
            $used_by = $this->Ssp_server_model->get_profiles_by_server($id);
            if ($used_by) {
                echo json_encode(array('code' => 1, 'msg' => sprintf($this->lang->line('ssp.server.delete.blocked'), implode(', ', $used_by))));
                return;
            }
        }

        if ($id && $this->Ssp_server_model->delete_server($id)) {
            $result['code'] = 0;
            $result['msg']  = $this->lang->line('delete.success');
        } else {
            $result['code'] = 1;
            $result['msg']  = sprintf($this->lang->line('delete.fail'), $this->lang->line('ssp.servers'));
        }

        echo json_encode($result);
    }
}
