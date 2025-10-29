<?php

use function Complex\ln;

class FtpSites extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->lang->load('ftp');
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
        $data = $this->get_data();
        if ($this->get_auth() <= 2) {
            $data['body_file'] = 'bootstrap/401';
        } else {
            $data['body_file'] = 'bootstrap/ftpsites/index';
        }
        $this->load->view('bootstrap/layout/basiclayout', $data);
    }

    public function getTableData()
    {
        $name = $this->input->post('search');

        $this->load->model('material');
        $data = $this->get_data();
        $offset = $this->input->post('offset');
        $limit = $this->input->post('limit');
        $order_item = $this->input->post('sort');
        $order = $this->input->post('order');

        $filter_array = array();
        if ($name) {
            $filter_array['name'] = $name;
        }


        $rest = $this->material->get_ftp_list($this->get_cid(), $filter_array, $offset, $limit, $order_item, $order);
        $data['total'] = $rest['total'];
        $data['rows']  = $rest['data'];

        echo json_encode($data);
    }


    public function edit()
    {
        $this->addJs("/assets/js/form.js", false);
        $this->load->model('material');

        $cid = $this->get_cid();
        $data = $this->get_data();
        $id = $this->input->get('id');
        $data['title'] = $this->lang->line('create.ftp');

        if ($id) {
            $data['title'] = $this->lang->line('edit.ftp');
            $region = $this->material->get_ftp_config($id, true);
            if ($region) {
                $data['data'] = $region;
            } else {
                $this->show_msg($this->lang->line('warn.param'), 'warn');
                return;
            }
        }

        $data['body_file'] = 'bootstrap/ftpsites/form';
        $this->load->view('bootstrap/layout/basiclayout', $data);
    }

    public function check_url()
    {
        $url = $this->input->get('url');
        $this->load->helper('media');
        if (check_ftp_url($url)) {
            echo 'true';
        } else {
            echo 'false';
        }
    }

    /**
     * 保存FTP配置信息
     *
     * @return
     */
    public function do_save()
    {
        $result = array();

        $this->load->library('form_validation');
        $this->form_validation->set_rules('profile', $this->lang->line('ftp.profile'), 'trim|required');
        $this->form_validation->set_rules('server', $this->lang->line('ftp.server'), 'trim|required');
        $this->form_validation->set_rules('port', $this->lang->line('ftp.port'), 'trim|required|numeric');
        $this->form_validation->set_rules('account', $this->lang->line('ftp.account'), 'trim|required');
        $this->form_validation->set_rules('password', $this->lang->line('ftp.password'), 'trim|required');
        if ($this->form_validation->run() == false) {
            //false
            $result = array('code' => 1, 'msg' => validation_errors());
        } else {
            $this->load->helper('media');
            $this->load->model('material');
            $flag = $this->material->get_ftp_by_name($this->input->post('id'), $this->get_cid(), $this->input->post('profile'));
            if ($flag) {
                $result = array('code' => 1, 'msg' => sprintf($this->lang->line('ftp.name.exsit'), $this->input->post('profile')));
            } else {
                $code = 0;
                $msg = '';
                if (check_ftp_url($this->input->post('server'))) {
                    $data = array('profile' => $this->input->post('profile'), 'server' => $this->input->post('server'), 'port' => $this->input->post('port'), 'pasv' => $this->input->post('pasv') == 1 ? 1 : 0, 'account' => $this->input->post('account'), 'password' => $this->input->post('password'));
                    $id = $this->input->post('id');
                    if ($id) {
                        $this->material->update_ftp_config($data, $id);
                    } else {
                        $id = $this->material->add_ftp_config($data, $this->get_cid(), $this->get_uid());
                    }
                    if ($id) {
                        $data['id'] = $id;
                        $result['data'] = $data;
                        $msg = $this->lang->line('save.success');
                    } else {
                        $code = 1;
                        $msg = $this->lang->line('save.fail');
                    }
                } else {
                    $code = 1;
                    $msg = $this->lang->line('warn.ftp.format');
                }
                $result['code'] = $code;
                $result['msg'] = $msg;
            }
        }

        echo json_encode($result);
    }

    public function do_delete()
    {
        $id = $this->input->post('id');
        $code = 0;
        $msg = '';
        if ($id) {
            $this->load->model('material');
            if ($this->material->delete_ftp($this->get_cid(), $id)) {
                $msg = $this->lang->line('delete.success');
            } else {
                $code = 1;
                $msg = $this->lang->line('param.error');
            }
        } else {
            $code = 1;
            $msg = $this->lang->line('param.error');
        }

        echo json_encode(array('code' => $code, 'msg' => $msg));
    }

    public function get_ftp_list()
    {
        $name = $this->input->post('search');

        $this->load->model('material');
        $data = $this->get_data();

        $filter_array = array();
        if ($name) {
            $filter_array['name'] = $name;
        }
        $rest = $this->material->get_ftp_list($this->get_cid());

        $data['total'] = $rest['total'];
        $data['rows']  = $rest['data'];

        echo json_encode($data);
    }


    public function list_ftp_files()
    {
        $this->load->helper('chrome_logger');
        $result = array();
        $config_id = $this->input->post('config_id');
        $pwd = $this->input->post('pwd');
        $nodes = array();


        $ftp = null;


        $code = 0;
        $msg = '';

        if (!$config_id) {
            $code = 1;
            $msg = $this->lang->line('ftp.config.error');
        } else {

            $this->load->model('material');
            $config = $this->material->get_ftp_config($config_id);

            if ($config) {
                $root = false;
                if (strlen($config->server) > 6) {
                    $protocal = strtolower(substr($config->server, 0, 6));
                    if ('ftp://' == $protocal) {
                        $config->server = substr($config->server, 6);
                        $pos = strpos($config->server, '/');
                        if ($pos) {
                            $root = substr($config->server, $pos + 1);
                            $config->server = substr($config->server, 0, $pos);
                        }
                    }
                }

                try {
                    $ftp = new \FtpClient\FtpClient();
                    $ftp->connect($config->server, FALSE, $config->port);
                    $ftp->login($config->account, $config->password);
                } catch (\FtpClient\FtpException $e) {
                    $result = array('code' => 1, 'msg' => $this->lang->line('ftp.connect.error'));
                    echo json_encode($result);
                    return;
                };
            }
            $ftp->pasv($config->pasv ? true : false);

            $pwd = $ftp->getPwd();
            $result['rootPwd'] = $pwd;
            $items = $ftp->scanDir($pwd);
            foreach ($items as $item) {
                if ($item['type'] != "directory" && $item['type'] != "file") {
                    continue;
                }
                $hasChildren = false;
                $isDir = false;
                if ($item['type'] == "directory") {
                    $isDir = true;
                    if ($item['number'] > 1) {
                        $hasChildren = true;
                    }
                }
                $node =  array('id' => $item['name'], "text" => $item['name'], "data" => $item['size']);
                if ($hasChildren) {
                    $node['children'] = true;
                }
                if ($isDir) {
                    $node['type'] = "folder";
                } else {
                    $node['type'] = "file";
                }
                $nodes[] = $node;
            }


            $code = 0;
            $msg = "login ftp server successfully";
        }

        $result['code'] = $code;
        $result['msg'] = $msg;
        $result['nodes'] = $nodes;

        echo json_encode($result);
    }
}
