<?php
    class Occupancy extends MY_Controller
    {
        public function __construct()
        {
            parent::__construct();
            $this->lang->load('player');
            $this->lang->load('criteria');
        }
        /**
         * Ĭ�ϵ���ҳ
         *
         * @param object $curpage [optional]
         * @param object $order_item [optional]
         * @param object $order [optional]
         * @return
         */
        public function index($curpage = 1, $order_item='name', $order = 'desc')
        {
            $this->refresh($curpage, $order_item, $order, true);
        }
        
        /**
         * ˢ��ҳ��������Ϣ
         *
         * @param object $curpage [optional]
         * @return
         */
        public function refresh($curpage = 1, $order_item='name', $order = 'desc', $main = false)
        {
            $this->addJs("criteria.js");
            $this->addCss("criteria.css");
            
            $name = $this->input->get('name');
            
            $this->load->model('device');
            $data = $this->get_data();
            //$limit = $this->config->item('page_default_size');
            $limit = -1;
            $offset = ($curpage - 1) * $limit;
            $filter_array = array();
            if ($name) {
                $filter_array['name'] = $name;
            }


            $rest = $this->device->get_criteria_list($this->get_cid(), $offset, $limit, $order_item, $order, $filter_array);
            $data['total'] = $rest['total'];
            $data['data']  = $rest['data'];
            $data['cid']   = $this->get_cid();
            $data['curpage']=$curpage;
            $data['limit']=$limit;
            $data['order_item']=$order_item;
            $data['order']=$order;
            
            if ($main) {
                $data['body_view'] = 'org/criteria/criterialist';
                $data['body_file'] = 'org/criteria/criteria';
                $this->load->view('include/main2', $data);
            } else {
                $this->load->view('org/criteria/criterialist', $data);
            }
        }
        
        /**
         * ��ӷ���ҳ��
         * @return
         */
        public function add()
        {
            $cid = $this->get_cid();
            $data = $this->get_data();
            $this->load->view('org/criteria/add_criteria', $data);
        }
        
        public function edit()
        {
            $id = $_GET['id'];
            $this->load->model('device');

            $cid = $this->get_cid();
            $data = $this->get_data();
        
            
            $region = $this->device->get_criteria($id);
            if ($region) {
                $data['region'] = $region;
                $this->load->view('org/criteria/edit_criteria', $data);
            } else {
                $this->show_msg($this->lang->line('warn.param'), 'warn');
            }
        }
        
        /**
         * ����һ������
         *
         * @return
         */
        public function do_save()
        {
            $result = array();
            
            $cid = $this->get_cid();
            $id = $this->input->post('id');
            if ($cid <=0) {
                $result = array('code'=>1, 'msg'=>$this->lang->line('warn.system.user'));
            } else {
                $this->load->library('form_validation');
                $this->form_validation->set_rules('name', $this->lang->line('criteria'), 'trim|required');
                
                if ($this->form_validation->run() == false) {
                    $result = array('code'=>1, 'msg'=>validation_errors());
                } else {
                    $this->load->model('device');
                    $data = array(
                                    'name' => $this->input->post('name'),
                                    'descr'=> $this->input->post('descr'),
                                    'company_id' => $cid
                                    );
                    $flag = $this->device->get_criteria_byname($id, $cid, $this->input->post('name'));   //�ж�ͬһ����˾�£��Ƿ��������ķ���
                    if ($id >= 0 && $flag) {
                        $result = array('code'=>1, 'msg'=>sprintf($this->lang->line('criteria.name.exsit'), $this->input->post('name')));
                    } else {
                        if ($id > 0) {
                            $this->device->update_criteria($data, $id);
                        } else {
                            $id = $this->device->add_criteria($data);
                        }
                        if ($id !== false) {
                            $data['id'] = $id;
                            $data['add_time'] = date('Y-m-d H:i:s');
                            $result = array('code'=>0, 'msg'=>$this->lang->line('save.success'));
                            $result = array_merge($data, $result);
                        } else {
                            $result = array('code'=>1, 'msg'=>sprintf($this->lang->line('save.fail'), $this->lang->line('group')));
                        }
                    }
                }
            }
            echo json_encode($result);
        }
        
        /**
         * ִ��ɾ��ĳ������
         *
         * @return
         */
        public function do_delete()
        {
            $result = array();
            $gid = $this->input->post("id");
            $this->load->model('device');
            $this->load->model('program');
            if ($this->program->get_playlist_by_criteria($this->get_cid(), $gid)>0) {
                $result['needPublish']=1;
                $result['repubmsg']=$this->lang->line('need.refresh.campaign');
            }
            if ($this->device->delete_criteria($gid)) {
                $result['code'] = 0;
                $result['msg']  = $this->lang->line('delete.success');
            } else {
                $result['code'] = 1;
                $result['msg']  = $this->lang->line('delete.fail');
            }
            
            echo json_encode($result);
        }
    }
