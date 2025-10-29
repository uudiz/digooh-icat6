<?php

/**
 * 用户组织关系类，操作公司和用户
 */
class Membership extends MY_Model
{

    /**
     * 验证登陆信息
     *
     * @return
     */
    public function validate_login()
    {
        $uid = 0;
        $cid = 0;
        $auth = 0;
        $media_view = 1;
        $name = $this->input->post("username");
        $prompt_flag = 0;
        $data_entry_text = 0;
        $price_entry = 0;
        $code = 1;
        $result = array();
        /*
        $sql = 'select id, company_id as cid, auth,data_entry_text from cat_user where name = ? and password = ? limit 1';
        $query = $this->db->query($sql, array($name, $this->input->post("password")));
        if ($query->num_rows() == 1) {
        */
        $user = $this->get_user_pwd($name);
        $language = $this->config->item("language");
        if ($user) {

            $uid = $user->id;
            $cid = $user->company_id;
            $auth = $user->auth;
            $media_view = $user->media_view;
            $language = isset($user->language) ? $user->language : $this->config->item("language");
            $password = $this->input->post('password');
            $redirect = $this->input->post('redirect');



            $data = array();

            if (($redirect && $password == $user->password) || password_verify($password, $user->password)) {
                if ($cid != 0 && $auth != 10) {
                    $sql = 'select * from cat_company where id = ? and CURDATE() between start_date and stop_date limit 1';
                    $query = $this->db->query($sql, array($cid));
                    if ($query->num_rows() == 1) {
                        $row = $query->row();

                        if ($row->flag != 0) {
                            $code = 2;
                        } else {
                            $code = 0;
                            $data['time_zone'] = $row->time_zone;
                            $data['price_entry'] = $row->price_entry;
                            $dst = false;
                            if ($row->dst || $row->auto_dst == 0) {
                                $now = date('Y-m-d');
                                if ($now >= $row->dst_start && $now <= $row->dst_end) {
                                    $dst = true;
                                }
                            }
                            $data['dst'] = $dst;
                            $data['logo'] = $row->logo;
                            $data['sspfeature'] = $row->sspfeature;
                            if ($user->logo) {
                                $data['logo'] = $user->logo;
                            }

                            $data['nxslot'] = $row->nxslot;
                            $data['theme_color'] = $row->theme_color;
                            $data['touch_function'] = $row->touch_function;
                            $data['pId'] = $row->pId;
                        }
                    } else {
                        $code = 3;
                    }
                } else {
                    $code = 0;
                }
                if ($this->config->item('tfa_enabled') == 1) {
                    $data['tfa_secret'] = $user->tfa_secret;
                    $data['tfa_enabled'] = $user->tfa_enabled;
                }
                $data['email'] = $user->email;
            }
        }

        if ($code == 0 && $uid > 0) {
            $this->user_log($this->OP_TYPE_SYSTEM, 'Login', $uid, $cid);
        }

        $data['uid'] = $uid;
        $data['cid'] = $cid;
        $data['auth'] = $auth;
        $data['code'] = $code;
        $data['uname'] = $name;
        $data['prompt_flag'] = $prompt_flag;
        $data['language'] = $language;
        $data['data_entry_text'] = $data_entry_text;
        $data['media_view'] = $media_view;

        return array('code' => $code, 'data' => $data);
    }

    public function get_all_partners($parent_id)
    {
        $this->db->select('id');
        $this->db->from('cat_company');
        $this->db->where("pId", $parent_id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $ret = $query->result_array();
            return array_column($ret, 'id');
        }
        return false;
    }

    /**
     * 判断当前公司是否为DST开启
     *
     * @param object $company_id
     * @return
     */
    public function is_dst_on($company_id)
    {
        $sql = 'select dst,auto_dst, dst_start, dst_end from cat_company where id = ? limit 1';
        $query = $this->db->query($sql, array($company_id));
        if ($query->num_rows() == 1) {
            $row = $query->row();
            $dst = false;
            if ($row->dst || $row->auto_dst == 0) {
                $now = date('Y-m-d');
                if ($now >= $row->dst_start && $now <= $row->dst_end) {
                    $dst = true;
                }
            }

            return $dst;
        }
        return false;
    }

    /**
     * 获取所有的公司列表信息，返回id和名称
     * @return
     */
    public function get_all_company_list()
    {
        $sql = 'select * from cat_company where flag = 0 order by id desc';
        $query = $this->db->query($sql);
        $array = array();
        if ($query->num_rows() > 0) {
            $array = $query->result();
            $query->free_result();
        }

        return $array;
    }

    public function get_company_count()
    {
        $this->db->select("id");
        $this->db->from('cat_company');
        $query = $this->db->get();
        return $query->num_rows();
    }
    public function get_users_count($cid)
    {
        $this->db->select("id");
        $this->db->from('cat_user');
        if ($cid) {
            $this->db->where('company_id', $cid);
        }
        $query = $this->db->get();
        return $query->num_rows();
    }

    /**
     * 获取公司列表
     *
     */
    public function get_company_list($offset, $limit, $order_item = 'id', $order = 'desc', $name = '')
    {
        $total = 0;
        $array = array();


        $sql = "SELECT *
                FROM cat_company";

        if ($name != '') {
            $sql .= " WHERE name LIKE '%$name%'";
        }

        $query = $this->db->query($sql . ";");

        $total = $this->db->count_all_results();

        if ($total > 0) {
            $sql .= " ORDER BY $order_item $order";

            if ($limit > 0) {
                $sql .= " LIMIT $offset,$limit";
            }


            $query = $this->db->query($sql . ";");
            if ($query->num_rows() > 0) {
                $array = $query->result();

                $query->free_result();
            }
        }

        /*
        $sql = 'select count(*) as total from cat_company where flag = 0';
        $query = $this->db->query($sql);
        $total = $query->row()->total;
        if ($total > 0) {
            if ($name!= "") {
                $sql = "select c.*, count( p.company_id) as  player_count from cat_company c left join (select company_id from cat_player where status > 1)  p on p.company_id = c.id where c.flag=0 AND c.name LIKE '%$name%' group by c.id order by $order_item $order limit $offset,$limit";
            } else {
                $sql = "select c.*, count( p.company_id) as  player_count from cat_company c left join (select company_id from cat_player where status > 1)  p on p.company_id = c.id where c.flag=0 group by c.id order by $order_item $order limit $offset,$limit";
            }
            $query = $this->db->query($sql);
            if ($query->num_rows() > 0) {
                $total_search = $query->num_rows();
                $array = $query->result();
                $query->free_result();
            }
        }
        if ($name != "") {
            $sql = "select c.*, count( p.company_id) as  player_count from cat_company c left join (select company_id from cat_player where status > 1)  p on p.company_id = c.id where c.flag=0 AND c.name LIKE '%$name%' group by c.id order by $order_item $order";
            $query = $this->db->query($sql);
            $total=$query->num_rows();
        }

        $sqla = "select c.*, count( p.company_id) as player_count, count( p.company_id) as count from cat_company c left join (select company_id from cat_player where status >= 0)  p on p.company_id = c.id where c.flag=0 group by c.id order by $order_item $order";
        $querya = $this->db->query($sqla);
        $arr_player = $querya->result();
        foreach ($array as $arr) {
            for ($i=0; $i<count($arr_player); $i++) {
                if ($arr->id == $arr_player[$i]->id) {
                    $arr->all_player_count = $arr_player[$i]->count;
                }
            }
        }
        */

        return array('total' => $total, 'data' => $array);
    }

    /**
     * 用户是否超过设定的限制
     *
     * @param object $company_id
     * @return
     */
    public function is_user_limited($company_id)
    {
        $sql = "select c.max_user, count(u.company_id) as user_count from cat_company c left join (select company_id from cat_user where flag = 0) u on c.id = u.company_id where c.id = $company_id";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $row = $query->row();
            return (intval($row->max_user) <= intval($row->user_count));
        }

        return true;
    }

    /**
     * 获取某个公司信息
     *
     * @param object $id
     * @return
     */
    public function get_company($id)
    {
        //$sql = 'select id, name, descr,start_date, stop_date,dst, dst_start, dst_end,time_zone,max_user from cat_company where flag = 0 and id = '.$id;
        $this->db->select("c.*");
        $this->db->from('cat_company c');

        //    $this->db->where('c.flag', 0);
        $this->db->where('c.id', $id);

        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $company = $query->row();
            if ($company->pId) {
                $this->db->select("p.quota,p.shareblock,p.player_quota, p.criterion_id,p.root_folder_id,GROUP_CONCAT(pp.player_id) as players");
                $this->db->from('cat_parter_fields p');
                $this->db->join('cat_partner_players pp', "pp.partner_id = p.partner_id", "LEFT");
                $this->db->group_by('p.id');
                $this->db->where('p.partner_id', $id);
                $query = $this->db->get();
                if ($query->num_rows() > 0) {
                    $partner_field =  $query->row();
                    foreach ($partner_field as $property => $value) {
                        $company->$property = $value;
                    }
                }
            }
            return $company;
        }

        return false;
    }
    /**
     * 通过公司名称  判断某个公司信息是否存在
     * @param object $id
     * @param object $cid
     * @param object $name
     * @return
     */
    public function get_company_by_name($id, $name)
    {
        if ($id > 0) {
            $sql = "select * from cat_company where id != $id and name = '$name'";
        } else {
            $sql = "select * from cat_company where name = '$name'";
        }
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
    }

    /**
     * 添加一个公司信息，成功返回true，否则返回false
     */
    public function add_company($array = array())
    {
        if (empty($array)) {
            return 0;
        }

        $this->load->helper('serial');
        $count = 0;
        do {
            $code = generate_company_code($array['name']);
            $count++;
        } while ($this->_exist_company_code($code) && $count < 3);

        $array['code'] = $code;

        if ($this->db->insert('cat_company', $array)) {
            $id = $this->db->insert_id();
            $this->user_log($this->OP_TYPE_USER, 'add_company[' . $id . '] code[' . $code . '] name[' . $array['name'] . ']');
            return $id;
        } else {
            return false;
        }
    }

    /**
     * 更新公司信息
     *
     * @param object $array
     * @param object $id
     * @return
     */
    public function update_company($array, $id)
    {
        if (empty($array)) {
            return 0;
        }

        $this->db->where('id', $id);
        if ($this->db->update('cat_company', $array)) {
            $this->user_log($this->OP_TYPE_USER, 'cat_company[' . $id . '] data[' . json_encode($array) . ']');
            return $id;
        } else {
            return false;
        }
    }

    /**
     * 判断当前公司是否存在
     *
     * @param object $cid
     * @return
     */
    public function exist_company($cid)
    {
        $this->db->select("id");
        $query = $this->db->get_where('cat_company', array('id' => $cid), 1);

        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 删除一个公司，只是修改其标志位
     *
     * @param object $cid
     * @return
     */
    public function delete_company($cid)
    {
        if ($cid > 0) {
            $this->db->where('id', $cid);

            if ($this->db->update('cat_company', array('flag' => 1))) {
                $this->delete_company_flag1($cid);
                $this->db->where('company_id', $cid);
                $this->db->delete('cat_user');

                $this->user_log($this->OP_TYPE_USER, 'delete_company[' . $cid . ']');
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    //彻底删除cat_company表中的一个公司
    public function delete_company_flag1($cid)
    {
        if ($cid > 0) {
            $this->db->where('id', $cid);
            $this->db->or_where('pid', $cid);
            $this->db->delete('cat_company');



            $this->db->where('company_id', $cid);
            $this->db->delete('cat_device_config');

            $this->db->where('company_id', $cid);
            $this->db->delete('cat_group');

            $this->db->where('company_id', $cid);
            $this->db->delete('cat_media');

            $this->db->where('company_id', $cid);
            $this->db->delete('cat_media_folder');

            $this->db->where('company_id', $cid);
            $this->db->delete('cat_playback');

            $this->db->where('company_id', $cid);
            $this->db->delete('cat_user_log');

            $this->db->where('company_id', $cid);
            $this->db->delete('cat_template');

            $this->db->where('company_id', $cid);
            $this->db->delete('cat_rss');
        }
    }

    /**
     * 是否存在当前码
     */
    private function _exist_company_code($company_code)
    {
        $sql = "select id from cat_company where code = ? ";
        $query = $this->db->query($sql, array($company_code));
        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * 获取公司下的所有用户信息
     *
     * @param object $cid
     * @return
     */
    public function get_all_user_list($cid)
    {
        $this->db->select('id, name');
        $this->db->from('cat_user');
        $this->db->where('company_id', $cid);
        $this->db->where('flag', 0);

        $query = $this->db->get(); //$this->db->query($sql, array(0, $cid, $offset, $limit));
        $total = $query->num_rows();
        $array = array();
        if ($total) {
            $array = $query->result();
            $query->free_result();
        }

        return $array;
    }

    /**
     * 获取某个公司的用户列表
     *
     * @param object $cid
     * @param object $offset
     * @param object $limit
     * @return
     */
    public function get_user_list($cid, $offset, $limit, $order_item = 'id', $order = 'desc', $filter_array = false)
    {
        $total = 0;
        $array = array();

        $this->db->select('u.*, c.name as company');
        $this->db->from('cat_user u');
        $this->db->where('u.flag', 0);
        if ($cid > 0) {
            $this->db->where('u.company_id', $cid);
        }
        $this->db->where('u.auth <=', $this->config->item('auth_admin'));
        $this->db->join('cat_company c', 'c.id = u.company_id', 'left');


        if (is_array($filter_array)) {
            foreach ($filter_array as $key => $value) {
                if ($key == 'company_id') {
                    $this->db->where('c.id', $value);
                } else if ($key == 'name') {
                    $this->db->like('u.name', $value);
                }
            }
        }

        $db = clone ($this->db);

        $total = $this->db->count_all_results();


        if ($total > 0) {
            $this->db = $db;

            if ($order_item == 'company_id') {
                $this->db->order_by('c.name', $order);
            } else {
                $this->db->order_by($order_item, $order);
            }
            if ($limit > 0) {
                $this->db->limit($limit, $offset);
            }

            $query = $this->db->get();
            $t = $query->num_rows();
            if ($t) {
                $array = $query->result();
                $query->free_result();
            }
        }

        return array('total' => $total, 'data' => $array);
    }

    /**
     * 获取用户信息
     *
     * @param object $id
     * @return
     */
    public function get_user($id, $withDetail = false)
    {
        $this->db->select('u.*');
        $this->db->from('cat_user u');
        $this->db->join('cat_user_campaign uc', 'u.id=uc.user_id', 'LEFT');
        $this->db->group_by("u.id");
        $this->db->where('u.id', $id);
        $query = $this->db->get();
        if ($query->num_rows()) {
            $user =  $query->row();
            //if ($this->config->item('user_with_more_previlege')) {
            if ($withDetail) {
                if ($user->auth < 4) {
                    if (!isset($user->all_campaigns) || (isset($user->all_campaigns) && $user->all_campaigns == 0)) {
                        $this->db->select("campaign_id");
                        $query = $this->db->get_where("cat_user_campaign", array('user_id' => $id));
                        $user->campaigns = array_column($query->result_array(), 'campaign_id');
                    }
                    if ($this->config->item("with_template") && $this->config->item('user_with_more_previlege')) {
                        if (!isset($user->all_templates) || (isset($user->all_templates) && $user->all_templates == 0)) {
                            $this->db->select("template_id");
                            $query = $this->db->get_where("cat_user_template", array('user_id' => $id));
                            $user->templates = array_column($query->result_array(), 'template_id');
                        }
                    }
                    if (!isset($user->all_players) || (isset($user->all_players) && $user->all_players == 0)) {
                        $this->db->select("player_id");
                        $query = $this->db->get_where("cat_user_player", array('user_id' => $id));
                        $user->players = array_column($query->result_array(), 'player_id');
                    }
                    if (!isset($user->all_folders) || (isset($user->all_folders) && $user->all_folders == 0)) {
                        $this->db->select("folder_id");
                        $query = $this->db->get_where("cat_user_folder", array('user_id' => $id));
                        $user->folders = array_column($query->result_array(), 'folder_id');
                    }

                    if ($user->use_player == 0) {
                        $this->db->select("criteria_id");
                        $query = $this->db->get_where("cat_user_criteria", array('user_id' => $id));
                        $user->criteria = array_column($query->result_array(), 'criteria_id');
                    }

                    if ($this->config->item('user_with_more_previlege')) {

                        $this->db->select("up.*");
                        $this->db->where("up.user_id", $id);
                        $query = $this->db->get('cat_user_privilege up');
                        if ($query->num_rows()) {
                            $user->privilege = $query->row();
                        }
                    }
                }
            }
            if ($this->config->item('with_register_feature')) {
                $this->db->select("store_id");
                $query = $this->db->get_where("cr_user_stores", array('user_id' => $id));
                $user->stores = array_column($query->result_array(), 'store_id');

                //chrome_log($user->stores);
            }
            //

            return $user;
        } else {
            return false;
        }
    }

    public function get_user_campaigns($uid)
    {
        $result = array();

        $this->db->select("uc.campaign_id");
        $this->db->from('cat_user_campaign uc');
        $this->db->join('cat_playlist p', 'p.id=uc.campaign_id');
        $this->db->where('uc.user_id', $uid);
        $this->db->where("p.deleted_at is null");
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $result =  array_column($query->result_array(), 'campaign_id');
            return $result;
        }

        return false;
    }

    /**
     * 添加一条用户
     * 默认设置初始密码
     *
     * @param object $array
     * @return 成功返回ID否则返回FALSE
     */
    public function add_user($array)
    {
        if (empty($array)) {
            return 0;
        }

        if (!isset($array['password'])) {
            $array['password'] = $this->config->item('default_passd');
        }

        //$array['auth'] = ($array['auth'] == 2) ? 5 : ($array['auth'] == 1 ? 3 : 0);

        if ($this->db->insert('cat_user', $array)) {
            $id = $this->db->insert_id();
            $this->user_log($this->OP_TYPE_USER, 'add_user[' . $id . '] name[' . $array['name'] . ']');
            return $id;
        } else {
            $this->user_log($this->OP_TYPE_USER, 'add_user[ name[' . $array['name'] . ']', $this->OP_STATUS_FAIL);
            return false;
        }
    }
    /**
     * 获取用户密码
     *
     * @param object $name
     * @return
     */
    public function get_user_pwd($name)
    {
        $this->db->select('*');
        if ($this->config->item('tfa_enabled') == 1) {
            $this->db->select('tfa_enabled,tfa_secret');
        }
        $this->db->from('cat_user');
        $this->db->where('name', $name);
        $query = $this->db->get();
        if ($query->num_rows()) {
            return $query->row();
        } else {
            return false;
        }
    }

    /**
     * 更新用户信息
     *
     * @param object $array
     * @param object $id
     * @return
     */
    public function update_user($array, $id)
    {
        if (empty($array)) {
            return 0;
        }

        $this->db->where('id', $id);
        if ($this->db->update('cat_user', $array)) {
            //$this->user_log($this->OP_TYPE_USER, 'cat_user[' . $id . '] data[' . json_encode($array) . ']');
            return $id;
        } else {
            return false;
        }
    }

    /**
     * 删除用户
     * @param object $uid
     * @return
     */
    public function delete_user($uid)
    {
        if ($uid > 0) {
            $this->db->where('id', $uid);
            //if($this->db->update('cat_user', array('flag'=>1)))
            if ($this->db->delete('cat_user')) {
                $this->user_log($this->OP_TYPE_USER, 'delete_user[' . $uid . ']');
                return true;
            } else {
                $this->user_log($this->OP_TYPE_USER, 'delete_user[' . $uid . ']', $this->OP_STATUS_FAIL);
                return false;
            }
        } else {
            return false;
        }
    }
    /**
     * 根据用户名判断用户是否存在
     *
     * @param object $name
     * @return
     */
    public function exist_user_name($name)
    {
        $this->db->select("id");
        $query = $this->db->get_where('cat_user', array('name' => $name), 1);

        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * 判断当前用户是否存在
     *
     * @param object $name
     * @return
     */
    public function exist_user($name, $id)
    {
        $this->db->select("id");
        $this->db->from('cat_user');
        $this->db->where('name', $name);
        $this->db->where('id !=', $id);
        $this->db->where('flag !=', 1);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * 是否为当前密码
     *
     * @param object $uid
     * @param object $passd
     * @return 密码匹配返回TRUE，否则FALSE
     */
    public function is_passd($uid, $passd)
    {
        $user = $this->get_user($uid);
        if ($user) {
            $password = $this->input->post('password');
            if (password_verify($passd, $user->password) || $passd == $user->password) {
                return true;
            }
        }
        return false;
        /*
        $query = $this->db->get_where('cat_user', array('id'=>$uid, 'password' => $passd));
        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
        */
    }

    /**
     * 修改用户密码
     *
     * @param object $uid
     * @param object $passd
     * @return 成功返回true
     */
    public function change_passd($uid, $passd)
    {
        $this->db->where('id', $uid);
        return $this->db->update('cat_user', array('password' => password_hash($passd, PASSWORD_DEFAULT)));
    }
    /**
     * 修改密码设置
     *
     * @param object $uid
     * @param object $email
     * @param object $enable_offline
     * @return 成功返回true
     */
    public function change_email_settings($uid, $email, $enable_offline)
    {
        $this->db->where('id', $uid);
        return $this->db->update('cat_user', array('email' => $email, 'offline_notify' => $enable_offline));
    }

    /**
     * 获取用户系统设置
     *
     * @param object $uid
     * @return
     */
    public function get_user_settings($uid)
    {
        $this->db->where('user_id', $uid);
        $query = $this->db->get('cat_user_settings');
        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            //初始化
            $this->db->insert('cat_user_settings', array('user_id' => $uid, 'media_view' => 0));
            $this->db->where('user_id', $uid);
            $query = $this->db->get('cat_user_settings');
            return $query->row();
        }
    }

    /**
     * 更新用户设置
     *
     * @param object $uid
     * @param object $array
     * @return
     */
    public function update_user_settings($uid, $array)
    {
        if (empty($array)) {
            return false;
        }

        $this->db->where('user_id', $uid);
        return $this->db->update('cat_user_settings', $array);
    }

    /**
     * 根据公司cid 获取该公司的管理员id
     * @param object $cid
     * @return
     */
    public function get_admin_by_cid($cid)
    {
        $sql = "select id from cat_user where company_id = ? and auth = 5";
        $query = $this->db->query($sql, array($cid));
        $array = array();
        if ($query->num_rows() > 0) {
            $array = $query->result();
            $query->free_result();
        }
        return $array;
    }

    //获取dst开启的公司
    public function get_company_ids()
    {
        $sql = "select id from cat_company where dst = 1";
        $query = $this->db->query($sql);
        $array = array();
        if ($query->num_rows() > 0) {
            $array = $query->result();
            $query->free_result();
        }
        return $array;
    }

    //获取auto_dst开启的公司
    public function get_company_ids_auto_dst()
    {
        $sql = "select id from cat_company where auto_dst = 0";
        $query = $this->db->query($sql);
        $array = array();
        if ($query->num_rows() > 0) {
            $array = $query->result();
            $query->free_result();
        }
        return $array;
    }

    //修改dst的开始结束时间，dst_start、dst_end
    public function update_company_dst($ids, $dst_start, $dst_end)
    {
        if (empty($ids)) {
            return false;
        }
        $sql = "UPDATE `cat_company` SET `dst_start` = '$dst_start', `dst_end` = '$dst_end' WHERE `id` IN ($ids)";
        $this->db->query($sql);
    }



    //修改user表中 prompt_flag 信息提示标记位
    public function update_user_promptflag()
    {
        $sql = "select id from cat_user where prompt_flag=0";
        $query = $this->db->query($sql);
        $array = array();
        $ids = '';
        if ($query->num_rows() > 0) {
            $array = $query->result();
            $query->free_result();
        }
        if ($array) {
            foreach ($array as $id) {
                $ids = $ids . $id->id . ',';
            }
            $ids = substr($ids, 0, -1);
            $sql2 = "UPDATE `cat_user` SET `prompt_flag` = '1' WHERE `id` IN ($ids)";
            $this->db->query($sql2);
        }
    }
    // 根据filter_name 和filter_type  匹配相应的字符串
    public function get_arr_by_filter($filter_name, $filter_type, $auth)
    {
        if ($filter_type == 'uname') {
            $sql = "select name from cat_user where name like '%$filter_name%' and flag=0 and auth<9";
        }

        if ($filter_type == 'cname') {
            $sql = "select name from cat_company where name like '%$filter_name%' and flag=0";
        }

        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            return $query->result();
        }
        return 0;
    }

    /**
     * 遍历所有company，
     * 如果发现Auto-Publish开关是打开的，那么修改此company下所有schedule的发布时间为当前时间
     *
     */
    public function auto_publish_schedule()
    {
        $sql = "select sch.id as id from cat_schedule as sch, cat_company as c where c.id=sch.company_id and sch.status=1 and c.auto_publish=1";
        $query = $this->db->query($sql);
        $array = array();
        $ids = '';
        if ($query->num_rows() > 0) {
            $array = $query->result();
            $query->free_result();
        }
        if ($array) {
            foreach ($array as $id) {
                $ids = $ids . $id->id . ',';
            }
            $ids = substr($ids, 0, -1);
            $sql2 = "UPDATE `cat_schedule` SET `publish_time` = '" . date('Y-m-d H:i:s') . "' WHERE `id` IN ($ids)";
            $this->db->query($sql2);
        }
    }

    /**
     * 复制 010010068 的内容
     * 添加group、player、template、playlist、scheduler、image、video、Rss
     *
     * 用户 id
     * 公司 id
     *
     */
    public function add_demo($id, $cid)
    {
        $playlist_ids = array();
        // 添加group
        $group = array();
        $gids = array();
        $sql_g = "select g.name, g.id from cat_group g, cat_user u where u.name='np100demo' and u.id = g.add_user_id";
        $query_g = $this->db->query($sql_g);
        if ($query_g->num_rows() > 0) {
            $data_g = $query_g->result();
        }
        foreach ($data_g as $g) {
            $group['name'] = $g->name;
            $group['company_id'] = $cid;
            $group['add_user_id'] = $id;
            $this->db->insert('cat_group', $group);
            $gid = $this->db->insert_id(); //组 id
            $gids[$g->name . $g->id] = $gid;
        }

        // 添加媒体文件
        $media = array();
        $mids = array(); //媒体文件 id
        $sql = "select m.* from cat_media m, cat_user u where u.name='np100demo' and u.id = m.add_user_id";
        $this->db->query($sql);
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $data = $query->result();
        }
        foreach ($data as $arr) {
            $media['name'] = $arr->name;
            $media['descr'] = $arr->descr;
            $media['ext'] = $arr->ext;
            $media['orig_name'] = $arr->orig_name;
            $media['full_path'] = $arr->full_path;
            $media['file_size'] = $arr->file_size;
            $media['media_type'] = $arr->media_type;
            $media['folder_id'] = $arr->folder_id;
            $media['company_id'] = $cid;
            $media['preview_status'] = $arr->preview_status;
            $media['tiny_url'] = $arr->tiny_url;
            $media['main_url'] = $arr->main_url;
            $media['source'] = $arr->source;
            $media['signature'] = $arr->signature;
            $media['add_user_id'] = $id;
            $media['width'] = $arr->width;
            $media['height'] = $arr->height;

            $this->db->insert('cat_media', $media);
            $mid = $this->db->insert_id();
            $mids[$arr->name] = $mid;
        }

        // 添加rss
        $rss = array();
        $rssids = array();
        $sql_r = "select r.* from cat_rss r, cat_user u where u.name='np100demo' and u.id = r.add_user_id";
        $query_r = $this->db->query($sql_r);
        if ($query_r->num_rows() > 0) {
            $data_r = $query_r->result();
        }
        foreach ($data_r as $r) {
            $rss['name'] = $r->name;
            $rss['url'] = $r->url;
            $rss['company_id'] = $cid;
            $rss['add_user_id'] = $id;
            $rss['add_time'] = date('Y-m-d H:i:s');
            $rss['type'] = $r->type;
            $this->db->insert('cat_rss', $rss);
            $rssid = $this->db->insert_id();
            $rssids[$r->name . $r->id] = $rssid;
        }

        // 添加template
        $tempalte = array();
        $tids = array();
        $sql_t = "select t.* from cat_template t, cat_user u where u.name='np100demo' and u.id = t.add_user_id";
        $query_t = $this->db->query($sql_t);
        if ($query_t->num_rows() > 0) {
            $data_t = $query_t->result();
        }
        foreach ($data_t as $t) {
            $template['name'] = $t->name;
            $template['width'] = $t->width;
            $template['height'] = $t->height;
            $template['w'] = $t->w;
            $template['h'] = $t->h;
            $template['company_id'] = $cid;
            $template['add_user_id'] = $id;
            $template['preview_url'] = $t->preview_url;
            $template['flag'] = 1;
            $template['update_time'] = date('Y-m-d H:i:s');
            $this->db->insert('cat_template', $template);
            $tid = $this->db->insert_id(); //模板id
            $tids[$t->name] = $tid;
        }

        // 添加template_area
        $template_area = array();
        $t_a_ids = array();
        $sql_t_a = "select t.name as tname, t_a.id, t_a.name, t_a.template_id, t_a.x, t_a.y, t_a.w, t_a.h, t_a.area_type, t_a.zindex from cat_template t, cat_template_area t_a, cat_user u where u.name='np100demo' and u.id=t.add_user_id and t.id=t_a.template_id";
        $query_a = $this->db->query($sql_t_a);
        if ($query_a->num_rows() > 0) {
            $data_a = $query_a->result();
        }
        foreach ($data_a as $a) {
            $template_area['name'] = $a->name;
            $template_area['template_id'] = $tids[$a->tname];
            $template_area['x'] = $a->x;
            $template_area['y'] = $a->y;
            $template_area['w'] = $a->w;
            $template_area['h'] = $a->h;
            $template_area['area_type'] = $a->area_type;
            $template_area['zindex'] = $a->zindex;
            $this->db->insert('cat_template_area', $template_area);
            $t_a_ids[$a->name . $a->id] = $this->db->insert_id();
        }

        // 添加playlist
        $playlist = array();
        $pids = array();
        $sql_p = "select t.name as tempalteName, p.name from cat_playlist p, cat_user u, cat_template t where p.template_id=t.id and u.name='np100demo' and u.id = p.add_user_id";
        $query_p = $this->db->query($sql_p);
        if ($query_p->num_rows() > 0) {
            $data_p = $query_p->result();
        }
        foreach ($data_p as $p) {
            $playlist['name'] = $p->name;
            $playlist['company_id'] = $cid;
            $playlist['add_user_id']  = $id;
            $playlist['template_id']  = $tids[$p->tempalteName];
            $playlist['published']  = 0;
            $playlist['add_time']  = date('Y-m-d H:i:s');
            $playlist['update_time']  = date('Y-m-d H:i:s');
            $this->db->insert('cat_playlist', $playlist);
            $pid = $this->db->insert_id();
            $pids[$p->name] = $pid;
            $playlist_ids[] = $pid;
        }

        // 添加playlist_area_media  （ 视频图片区域）
        $pamids = array();
        $sql_pam = "select p.name, p.id as pid, ta.name as ta_name, ta.id as ta_id, m.name as mname, m.id as mid from cat_user u, cat_template t, cat_template_area ta, cat_playlist p, cat_playlist_area_media pam, cat_media m where u.name='np100demo' and u.id=p.add_user_id and p.id=pam.playlist_id and pam.area_id=ta.id and pam.media_id=m.id and t.id=p.template_id and ta.name!='Text'";
        $query_pam = $this->db->query($sql_pam);
        if ($query_pam->num_rows() > 0) {
            $data_pam = $query_pam->result();
        }
        foreach ($data_pam as $pam) {
            $playlist_area_media = array();
            $playlist_area_media['playlist_id'] = $pids[$pam->name];
            $playlist_area_media['media_id'] = $mids[$pam->mname];
            $playlist_area_media['area_id'] = $t_a_ids[$pam->ta_name . $pam->ta_id];
            $playlist_area_media['duration'] = '00:10';
            $playlist_area_media['transmode'] = 7;
            $playlist_area_media['transtime'] = 0.5;
            $playlist_area_media['position'] = 1;
            $playlist_area_media['rotate'] = 1;
            $playlist_area_media['flag'] = 1;
            $playlist_area_media['add_user_id'] = $id;
            $this->db->insert('cat_playlist_area_media', $playlist_area_media);
            $pamids[] = $this->db->insert_id();
        }

        // 添加area_text_setting
        $settingids = array();
        $sql_set = "select p.name, p.id as pid, ta.name as ta_name, ta.id as ta_id, setting.content from cat_user u, cat_template t, cat_template_area ta, cat_playlist p, cat_area_text_setting setting where u.name='np100demo' and u.id=p.add_user_id and p.id=setting.playlist_id and setting.area_id=ta.id and t.id=p.template_id";
        $query_set = $this->db->query($sql_set);
        if ($query_set->num_rows() > 0) {
            $data_set = $query_set->result();
        }
        foreach ($data_set as $set) {
            $area_text_setting = array();
            $area_text_setting['area_id'] = $t_a_ids[$set->ta_name . $set->ta_id];
            $area_text_setting['playlist_id'] = $pids[$set->name];
            $area_text_setting['content'] = $set->content;
            $area_text_setting['font'] = 0;
            $area_text_setting['font_size'] = 60;
            $area_text_setting['color'] = '#FFFFFF';
            $area_text_setting['font_family'] = 'Aria';
            $area_text_setting['bg_color'] = '#000000';
            $area_text_setting['speed'] = 2;
            $area_text_setting['direction'] = 1;
            $area_text_setting['transparent'] = 0;
            $area_text_setting['rss_format'] = 0;
            $area_text_setting['add_user_id'] = $id;
            $area_text_setting['add_time'] = date('Y-m-d H:i:s');
            $this->db->insert('cat_area_text_setting', $area_text_setting);
            $settingids[] = $this->db->insert_id();
        }


        // 添加playlist_area_media  （ Text区域）
        $ptextids = array();
        $sql_ptext = "select p.name, p.id as pid, ta.name as ta_name, ta.id as ta_id ,setting.content, rss.name as rssname, rss.id as rssid from cat_user u, cat_template t, cat_template_area ta, cat_playlist p, cat_playlist_area_media pam, cat_area_text_setting setting,cat_rss rss  where u.name='np100demo' and u.id=p.add_user_id  and t.id=p.template_id and ta.id=pam.area_id and ta.name='Text'and p.id=setting.playlist_id and pam.playlist_id=setting.playlist_id and rss.id=pam.media_id";
        $query_ptext = $this->db->query($sql_ptext);
        if ($query_ptext->num_rows() > 0) {
            $data_ptext = $query_ptext->result();
        }
        foreach ($data_ptext as $text) {
            $playlist_area_media_text = array();
            $playlist_area_media_text['playlist_id'] = $pids[$text->name];
            $playlist_area_media_text['area_id'] = $t_a_ids[$text->ta_name . $text->ta_id];
            $playlist_area_media_text['duration'] = '00:10';
            $playlist_area_media_text['transmode'] = 7;
            $playlist_area_media_text['transtime'] = 0.5;
            $playlist_area_media_text['position'] = 1;
            $playlist_area_media_text['rotate'] = 1;
            $playlist_area_media_text['flag'] = 1;
            $playlist_area_media_text['add_user_id'] = $id;
            $playlist_area_media_text['media_id'] = $rssids[$text->rssname . $text->rssid];
            $this->db->insert('cat_playlist_area_media', $playlist_area_media_text);
        }


        //添加schedule
        $sch_ids = array();
        $sql_sch = "select sch.* from cat_schedule sch, cat_user u where u.name='np100demo' and u.id=sch.add_user_id";
        $query_sch = $this->db->query($sql_sch);
        if ($query_sch->num_rows() > 0) {
            $data_sch = $query_sch->result();
        }
        foreach ($data_sch as $sch) {
            $schedule = array();
            $schedule['name'] = $sch->name;
            $schedule['descr'] = $sch->descr;
            $schedule['status'] = $sch->status;
            $schedule['company_id'] = $cid;
            $schedule['add_user_id'] = $id;
            $schedule['publish_time'] = date('Y-m-d H:i:s');
            $schedule['add_time'] = date('Y-m-d H:i:s');
            $schedule['start_date'] = $sch->start_date;
            $schedule['end_date'] = $sch->end_date;
            $schedule['start_time'] = $sch->start_time;
            $schedule['end_time'] = $sch->end_time;
            $schedule['week'] = $sch->week;
            $this->db->insert('cat_schedule', $schedule);
            $sch_ids[$sch->name . $sch->id] = $this->db->insert_id();
        }

        //添加schedule_group
        $sql_sch_g = "select sch.name, sch.id, schg.group_id, g.name as group_name from cat_schedule sch, cat_user u, cat_group g, cat_schedule_group schg where u.name='np100demo' and u.id=sch.add_user_id and sch.id=schg.schedule_id and g.id=schg.group_id";
        $query_sch_g = $this->db->query($sql_sch_g);
        if ($query_sch_g->num_rows() > 0) {
            $data_sch_g = $query_sch_g->result();
        }
        foreach ($data_sch_g as $sch_g) {
            $schedule_group = array();
            $schedule_group['group_id'] = $gids[$sch_g->group_name . $sch_g->group_id];
            $schedule_group['schedule_id'] = $sch_ids[$sch_g->name . $sch_g->id];
            $schedule_group['add_time'] = date('Y-m-d H:i:s');
            $this->db->insert('cat_schedule_group', $schedule_group);
        }

        //添加schedule_playlist
        $sql_sch_p = "select sch.name, sch.id, p.id as pid, p.name as pname, schp.position from cat_schedule sch, cat_user u, cat_playlist p, cat_schedule_playlist schp where u.name='np100demo' and u.id=sch.add_user_id and sch.id=schp.schedule_id and p.id=schp.playlist_id";

        $query_sch_p = $this->db->query($sql_sch_p);
        if ($query_sch_p->num_rows() > 0) {
            $data_sch_p = $query_sch_p->result();
        }
        foreach ($data_sch_p as $sch_p) {
            $schedule_playlist = array();
            $schedule_playlist['playlist_id'] = $pids[$sch_p->pname];
            $schedule_playlist['schedule_id'] = $sch_ids[$sch_p->name . $sch_p->id];
            $schedule_playlist['position'] = $sch_p->position;
            $schedule_playlist['add_time'] = date('Y-m-d H:i:s');
            $this->db->insert('cat_schedule_playlist', $schedule_playlist);
        }

        return $playlist_ids;
    }

    /**
     * 判断该公司下面是否有自动添加的group、template、rss、playlist等内容
     * company_id
     */
    public function add_demo_check($id)
    {
        $sql_g = "select name from cat_group where company_id = $id";
        $query_g = $this->db->query($sql_g);

        $sql_p = "select name from cat_playlist where company_id = $id";
        $query_p = $this->db->query($sql_p);

        $sql_t = "select name from cat_template where company_id = $id";
        $query_t = $this->db->query($sql_t);

        $sql_sch = "select name from cat_schedule where company_id = $id";
        $query_sch = $this->db->query($sql_sch);
        if ($query_g->num_rows() > 0 || $query_p->num_rows() > 0 || $query_t->num_rows() > 0 || $query_sch->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    //获取所有的在线的Player
    public function all_online_player()
    {
        $array = array();
        $this->db->select('id');
        $this->db->from('cat_player');
        $this->db->where('status>1');
        $query = $this->db->get();
        return $query->num_rows();
        /*
        $sql = "select count( p.company_id) as count from cat_company c, cat_player p where c.flag=0 and c.id = p.company_id and p.status > 1";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $total_search = $query->num_rows();
            $array = $query->result();
            $query->free_result();
        }
        return $array;
        */
    }

    //激活码列表显示
    public function get_authorize_list($offset, $limit, $order_item = 'id', $order = 'desc')
    {
        $total = 0;
        $array = array();

        $this->db->select('count(id) as total');
        $this->db->from('cat_authorize a');
        $query = $this->db->get();
        $total = $query->row()->total;
        if ($total > 0) {
            //$this->db->select('id, mac, add_time, connect_time, code, ip');
            $this->db->from('cat_authorize');
            $this->db->order_by($order_item, $order);
            $this->db->limit($limit, $offset);

            $query = $this->db->get();
            $t = $query->num_rows();
            if ($t) {
                $array = $query->result();
                $query->free_result();
            }
        }
        return array('total' => $total, 'data' => $array);
    }

    //添加激活码
    public function add_authorize($array, $descr)
    {
        if (!empty($array)) {
            $sql = "INSERT INTO `cat_authorize` (`add_time`, `code`, `status`, `descr`) VALUES ";
            for ($i = 0; $i < count($array); $i++) {
                $sql .= " (CURRENT_TIMESTAMP, '" . $array[$i] . "', '0', '" . $descr . "'),";
            }
            $sql = substr($sql, 0, -1);
            $id = $this->db->query($sql);
            return $id;
        } else {
            return false;
        }
    }

    //删除
    public function delete_authorize($id)
    {
        if ($id > 0) {
            $this->db->where('id', $id);
            if ($this->db->delete('cat_authorize')) {
                $this->user_log($this->OP_TYPE_USER, 'delete_authorize[' . $id . ']');
                return true;
            } else {
                $this->user_log($this->OP_TYPE_USER, 'delete_uauthorize[' . $id . ']', $this->OP_STATUS_FAIL);
                return false;
            }
        } else {
            return false;
        }
    }

    //根据编号获取激活码信息
    public function get_authorize($id)
    {
        $this->db->where('id', $id);
        $query = $this->db->get('cat_authorize');
        if ($query->num_rows()) {
            return $query->row();
        } else {
            return false;
        }
    }

    //根据code获取激活码信息
    public function get_authorize_by_code($code, $mac)
    {
        $this->db->where('code', $code);
        $query = $this->db->get('cat_authorize');
        if ($query->num_rows()) {
            $array = $query->row();
            if ($array->mac == $mac || $array->mac == '') {
                return $array;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    //更新激活码信息
    public function update_authorize($array, $id)
    {
        $this->db->where('id', $id);
        if ($this->db->update('cat_authorize', $array)) {
            $this->user_log($this->OP_TYPE_USER, 'cat_authorize[' . $id . '] data[' . json_encode($array) . ']');
            return $id;
        } else {
            return false;
        }
    }

    public function update_company_auto_dst($company_id = FALSE)
    {
        $data = $this->get_dst_range();
        if ($data) {
            $this->db->update('cat_company', $data);
            if ($company_id) {
                $this->db->where('id', $company_id);
            }
        }
    }
    /**
     * 如果DST有设定，动态获取DST开始日期：
     * 美国：三月的第二个星期日到11月的第一个星期日
     * 德国：三月的最后一个星期日到10月的最后一个星期日
     */
    public function get_dst_range()
    {
        $months = [
            1  => 'January',
            2  => 'February',
            3  => 'March',
            4  => 'April',
            5  => 'May',
            6  => 'June ',
            7  => 'July',
            8  => 'August',
            9  => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December',
        ];

        $dst_country = $this->config->item('dst_country');


        $data_array = explode('-', date('Y-m-d'));
        $year = $data_array[0];
        $dst_start = 0;
        $dst_end = 0;
        switch ($dst_country) {
            case 0: //US
                $dst_start = date('Y-m-d', strtotime('first Sunday of ' . $months[3] . ' +7 days'));
                $dst_end = date('Y-m-d', strtotime('first Sunday of ' . $months[11]));
                break;
            case 1: //Gemany
                $dst_start = date('Y-m-d', strtotime('last Sunday of ' . $months[3]));
                $dst_end = date('Y-m-d', strtotime('last Sunday of ' . $months[10]));

                break;
            default:
                return false;
        }
        $data['dst_start'] = $dst_start;
        $data['dst_end'] = $dst_end;
        return $data;
    }

    public function get_offline_company_list()
    {
        $sql = 'select c.id,dst_start,dst_end,email,offline_email_flag,offline_email_inteval,offline_email_last_run,offline_email_last_run2,offline_email_inteval2,offline_email_flag2,name as company_name,
		 		(SELECT GROUP_CONCAT(distinct u.email) FROM company_notify_user u1 left join cat_user u on u.id=u1.user_id  where u1.company_id = c.id and priority=0) as useremail1,
                (SELECT GROUP_CONCAT(distinct u.email) FROM company_notify_user u2 left join cat_user u on u.id=u2.user_id  where u2.company_id = c.id and priority=1) as useremail2 
				from cat_company c where offline_email_flag = 1 or offline_email_flag2 = 1 ';
        $query = $this->db->query($sql);
        $array = array();
        if ($query->num_rows() > 0) {
            $array = $query->result();
            $query->free_result();
        }

        return $array;
    }

    public function update_cost($array, $cid)
    {
        if (empty($array)) {
            return 0;
        }

        $this->db->where('company_id', $cid);
        $this->db->delete('cat_costs');

        if ($this->db->insert('cat_costs', $array)) {
            $id = $this->db->insert_id();
            return $id;
        } else {
            return false;
        }
    }

    public function get_cost($cid)
    {
        $this->db->where('company_id', $cid);
        $query = $this->db->get('cat_costs');
        if ($query->num_rows() > 0) {
            return $query->row();
        }

        return false;
    }

    public function sync_notify_users($cid, $users, $type = 0)
    {
        $this->db->where('priority', $type);
        $this->db->where('company_id', $cid);
        $this->db->delete('company_notify_user');

        foreach ($users as $user) {
            $data[] = array('company_id' => $cid, "user_id" => $user, 'priority' => $type);
        }
        if (isset($data)) {
            $this->db->insert_batch('company_notify_user', $data);
        }
    }
    /**
     * 获取某个公司信息
     *
     * @param object $id
     * @return
     */
    /*
    public function get_company_with_nofifies($id)
    {
        $sql = "SELECT c.*,
                (SELECT GROUP_CONCAT(distinct u1.user_id) FROM company_notify_user u1 where u1.company_id = $id and priority=0) as users1,
                (SELECT GROUP_CONCAT(distinct u2.user_id) FROM company_notify_user u2 where u2.company_id = $id and priority=1) as users2 
            FROM cat_company c 
            WHERE c.id = $id";



        $query = $this->db->query($sql);

        if ($query->num_rows() > 0) {
            return $query->row();
        }
        return false;
    }
    */

    public function get_company_notification_users($company_id, $type)
    {
        $this->db->select("user_id");
        $this->db->from('company_notify_user');
        $this->db->where('company_id', $company_id);
        $this->db->where("priority", $type);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $user_ary = $query->result_array();
            return array_column($user_ary, 'user_id');
        }
        return false;
    }

    public function get_all_company_list_with_emails()
    {
        $sql = "SELECT c.*,
                (select GROUP_CONCAT(distinct email) from cat_user WHERE id in(SELECT user_id FROM company_notify_user where company_id = c.id and priority=0)) as emails1,
                (select GROUP_CONCAT(distinct email) from cat_user WHERE id in(SELECT user_id FROM company_notify_user where company_id = c.id and priority=1)) as emails2 

            FROM cat_company c ";
        $query = $this->db->query($sql);
        $array = array();
        if ($query->num_rows() > 0) {
            $array = $query->result();
            $query->free_result();
        }

        return $array;
    }

    public function get_cust_fields($parter_id)
    {
        $this->db->where('partner_id', $parter_id);
        $query = $this->db->get('cat_parter_fields');
        if ($query->num_rows() > 0) {
            return $query->row();
        }
        return false;
    }
    public function update_cust_fieds($array, $partner_id)
    {
        if (empty($array)) {
            return 0;
        }
        $this->db->where('partner_id', $partner_id);
        $query = $this->db->get('cat_parter_fields');
        if ($query->num_rows() > 0) {
            $this->db->where('partner_id', $partner_id);
            if ($this->db->update('cat_parter_fields', $array)) {
                return true;
            }
        } else {
            $array['partner_id'] = $partner_id;
            if ($this->db->insert('cat_parter_fields', $array)) {
                return true;
            }
        }
        return false;
    }



    public function sync_partner_players($players, $cid)
    {
        $this->db->where('partner_id', $cid);
        $this->db->delete('cat_partner_players');

        if (empty($players)) {
            return 0;
        }
        if (!is_array($players)) {
            $players = explode(',', $players);
        }

        $items = array();
        foreach ($players as $playerid) {
            $item = array("player_id" => $playerid, 'partner_id' => $cid);
            $items[] = $item;
        }


        if ($this->db->insert_batch('cat_partner_players', $items)) {
            return true;
        } else {
            return false;
        }
    }

    public function get_individual_player($player_id)
    {
        $this->db->select("partner_id,qutoa");
        $this->db->from('cat_partner_players');
        $this->db->where('player_id', $player_id);
        $this->db->distinct();


        $query = $this->db->get();

        if ($query->num_rows()) {
            return $query->row();
        }
        return false;
    }
    public function get_individual_playersid($parent_id, $partner_id = false)
    {
        $this->db->select("pp.player_id");
        $this->db->from('cat_partner_players pp');
        $this->db->join('cat_company c', "c.id=pp.partner_id", "LEFT");
        $this->db->where("c.pId", $parent_id);
        if ($partner_id !== false) {
            $this->db->where('pp.partner_id!=', $partner_id);
        }
        $this->db->distinct();


        $query = $this->db->get();

        if ($query->num_rows()) {
            $result =  $query->result_array();
            return array_column($result, 'player_id');
        }
        return false;
    }
    public function get_used_criteriaid($parent_id, $partner_id = false)
    {
        $this->db->select('cb.criterion_id');
        $this->db->from('criterionables cb');
        $this->db->join('cat_company c', "c.id=cb.criterionable_id", "LEFT");
        $this->db->where("c.pId", $parent_id);
        $this->db->where('cb.criterionable_type', 'App\Company');

        if ($partner_id !== false) {
            $this->db->where('cb.criterionable_id!=', $partner_id);
        }

        $this->db->distinct();

        $query = $this->db->get();

        if ($query->num_rows()) {
            $result =  $query->result_array();
            return array_column($result, 'criterion_id');
        }
    }

    public function get_user_by_name_and_email($name, $email)
    {
        $this->db->select("id,name,email");
        $this->db->where('name', $name);
        $this->db->where('email', $email);
        $query = $this->db->get('cat_user');

        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
    }

    public function update_partners($parent_id, $array)
    {
        $this->db->where("pId", $parent_id);
        if ($this->db->update('cat_company', $array)) {
            return true;
        }
        return false;
    }

    public function get_user_templates($uid)
    {
        $result = array();
        $this->db->select("template_id");
        $query = $this->db->get_where("cat_user_template", array('user_id' => $uid));
        if ($query->num_rows() > 0) {
            $result =  array_column($query->result_array(), 'template_id');
        }

        return $result;
    }

    public function delete_user_templates($uid)
    {
        $this->db->where('user_id', $uid);

        return $this->db->delete('cat_user_template');
    }

    public function assign_user_templates($templates, $uid)
    {
        if (empty($templates)) {
            return false;
        }
        $this->delete_user_templates($uid);

        foreach ($templates as $template) {
            $sql = "insert into cat_user_template(user_id, template_id) values($uid, $template)";
            $this->db->query($sql);
        }


        return true;
    }

    public function delete_user_stores($user_id)
    {
        $this->db->where('user_id', $user_id);

        return $this->db->delete('cr_user_stores');
    }
    public function assign_user_stores($stores, $uid)
    {
        $this->delete_user_stores($uid);
        if (!$stores || empty($stores)) {
            return false;
        }
        foreach ($stores as $store) {
            $sql = "insert into cr_user_stores(user_id, store_id) values($uid, $store)";
            $this->db->query($sql);
        }
    }
    public function get_user_stores($user_id)
    {
        $this->db->select("store_id");
        $this->db->from('cr_user_stores');
        $this->db->where('user_id', $user_id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $result =  array_column($query->result_array(), 'store_id');
            return $result;
        }
        return false;
    }
}
