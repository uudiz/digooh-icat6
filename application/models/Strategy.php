<?php
class Strategy extends MY_Model
{

    /**
     * 获取所有下载策略列表
     *
     * @param object $company_id
     * @return
     */
    public function get_all_download_list($company_id)
    {
        $this->db->select('id, name');
        $this->db->from("cat_download_strategy");
        $this->db->where('company_id', $company_id);
        $this->db->order_by('id', 'desc');
        $query = $this->db->get();
        $array = array();
        if ($query->num_rows()) {
            $array = $query->result();
            $query->free_result();
        }

        return $array;
    }


    public function get_download_list($company_id, $offset, $limit, $add_user_id = false)
    {
        $this->db->select('count(id) as total');
        $this->db->from("cat_download_strategy");
        $this->db->where('company_id', $company_id);
        if ($add_user_id) {
            $this->db->where('add_user_id', $add_user_id);
        }
        $query = $this->db->get();
        $total = $query->row()->total;
        $array = array();
        if ($total > 0) {
            $this->db->select('vc.*, u.name as username');
            $this->db->from("cat_download_strategy vc");
            $this->db->join("cat_user u", "u.id = vc.add_user_id", "left");
            $this->db->where('vc.company_id', $company_id);
            if ($add_user_id) {
                $this->db->where('vc.add_user_id', $add_user_id);
            }
            $this->db->order_by('vc.id', 'desc');
            $this->db->limit($limit, $offset);
            $query = $this->db->get();

            if ($query->num_rows()) {
                $array = $query->result();
                $query->free_result();
            }
        }


        return array('total' => $total, 'data' => $array);
    }

    /**
     * 获取单个下载策略
     *
     * @param object $id
     * @return
     */
    public function get_download($id)
    {
        $this->db->where('id', $id);
        $query = $this->db->get('cat_download_strategy');
        if ($query->num_rows()) {
            return $query->row();
        }

        return false;
    }
    /**
     * 根据策略名称 获取下载策略信息
     * @param object $id
     * @param object $cid
     * @param object $name
     * @return
     */
    public function get_download_by_name($id, $cid, $name)
    {
        if ($id > 0) {
            $sql = "select id from cat_download_strategy where id != $id and company_id = '$cid' and name = '$name'";
        } else {
            $sql = "select id from cat_download_strategy where company_id = '$cid' and name = '$name'";
        }
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
    }
    /**
     * 获取额外信息
     *
     * @param object $id
     * @return
     */
    public function get_download_extra($strategy_id)
    {
        $this->db->from("cat_download_strategy_extra");
        $this->db->where('strategy_id', $strategy_id);
        $this->db->order_by('id', 'asc');
        $query = $this->db->get();
        $array = array();
        if ($query->num_rows()) {
            $array = $query->result();
            $query->free_result();
        }

        return $array;
    }

    public function get_download_group($strategy_id)
    {
        $this->db->select('name');
        $this->db->from("cat_group");
        $this->db->where('download_strategy_id', $strategy_id);
        $this->db->order_by('id', 'asc');
        $query = $this->db->get();
        $array = array();
        if ($query->num_rows()) {
            $array = $query->result();
            $query->free_result();
        }

        return $array;
    }

    /**
     * 删除当前下载配置中的时间属性
     *
     * @param object $strategy_id
     * @return
     */
    public function delete_download_extra($strategy_id)
    {
        $this->db->from("cat_download_strategy_extra");
        $this->db->where('strategy_id', $strategy_id);
        return $this->db->delete();
    }


    public function delete_download($id)
    {
        $this->db->where('id', $id);
        if ($this->db->delete('cat_download_strategy')) {
            $this->delete_download_extra($id);
            $this->user_log($this->OP_TYPE_USER, 'delete cat_download_strategy[' . $id . '] success');
            return true;
        } else {
            return false;
        }
    }

    /**
     * 添加下载策略
     *
     * @param object $array
     * @param object $uid
     * @return
     */
    public function add_download($array, $uid)
    {
        if (empty($array)) {
            return false;
        }

        $array['add_user_id'] = $uid;

        if ($this->db->insert('cat_download_strategy', $array)) {
            $id = $this->db->insert_id();
            $this->user_log($this->OP_TYPE_USER, 'cat_download_strategy[' . $id . '] json[' . json_encode($array) . ']');
            return $id;
        } else {
            return false;
        }
    }

    public function add_download_extra($array)
    {
        if (empty($array)) {
            return false;
        }

        if ($this->db->insert('cat_download_strategy_extra', $array)) {
            $id = $this->db->insert_id();
            $this->user_log($this->OP_TYPE_USER, 'cat_download_strategy_extra[' . $id . '] json[' . json_encode($array) . ']');
            return $id;
        } else {
            return false;
        }
    }

    public function update_download($array, $id)
    {
        if (empty($array)) {
            return false;
        }

        $this->db->where('id', $id);
        if ($this->db->update('cat_download_strategy', $array)) {
            $this->user_log($this->OP_TYPE_USER, 'update cat_download_strategy[' . $id . '] json[' . json_encode($array) . ']');
            return true;
        } else {
            return false;
        }
    }


    /**
     * 获取显示列表
     *
     * @param object $company_id
     * @param object $offset
     * @param object $limit
     * @return
     */
    public function get_view_list($company_id, $offset, $limit)
    {
        $this->db->select('count(id) as total');
        $this->db->from("cat_view_config");
        $this->db->where('company_id', $company_id);

        $query = $this->db->get();
        $total = $query->row()->total;
        $array = array();
        if ($total > 0) {
            $this->db->select('vc.*, u.name as username');
            $this->db->from("cat_view_config vc");
            $this->db->join("cat_user u", "u.id = vc.add_user_id", "left");
            $this->db->where('vc.company_id', $company_id);
            $this->db->order_by('vc.id', 'desc');
            $this->db->limit($limit, $offset);
            $query = $this->db->get();

            if ($query->num_rows()) {
                $array = $query->result();
                $query->free_result();
            }
        }


        return array('total' => $total, 'data' => $array);
    }

    /**
     * 获取全部显示策略
     *
     * @param object $company_id
     * @return
     */
    public function get_all_view_list($company_id)
    {
        $this->db->select('id, name');
        $this->db->from("cat_view_config vc");
        $this->db->where('vc.company_id', $company_id);
        $this->db->order_by('vc.id', 'desc');
        $query = $this->db->get();
        $array = array();
        if ($query->num_rows()) {
            $array = $query->result();
            $query->free_result();
        }

        return $array;
    }


    /**
     * 查询单个显示设置
     *
     * @param object $id
     * @return
     */
    public function get_view($id)
    {
        $this->db->where('id', $id);
        $query = $this->db->get('cat_view_config');
        if ($query->num_rows()) {
            return $query->row();
        }

        return false;
    }


    /**
     * 添加显示策略
     *
     * @param object $array
     * @param object $uid
     * @return
     */
    public function add_view($array, $uid)
    {
        if (empty($array)) {
            return false;
        }

        $array['add_user_id'] = $uid;

        if ($this->db->insert('cat_view_config', $array)) {
            $id = $this->db->insert_id();
            $this->user_log($this->OP_TYPE_USER, 'add cat_view_config[' . $id . '] json[' . json_encode($array) . ']');
            return $id;
        } else {
            return false;
        }
    }

    /**
     * 更新显示设置
     *
     * @param object $array
     * @param object $id
     * @return
     */
    public function update_view($array, $id)
    {
        if (empty($array)) {
            return false;
        }

        $this->db->where('id', $id);
        if ($this->db->update('cat_view_config', $array)) {
            $this->user_log($this->OP_TYPE_USER, 'update cat_view_config[' . $id . '] json[' . json_encode($array) . ']');
            return true;
        } else {
            return false;
        }
    }

    /**
     * 删除显示设置
     *
     * @param object $id
     * @return
     */
    public function delete_view($id)
    {
        $this->db->where('id', $id);
        if ($this->db->delete('cat_view_config')) {
            $this->user_log($this->OP_TYPE_USER, 'delete cat_view_config[' . $id . '] success');
            return true;
        } else {
            return false;
        }
    }


    /************************************************
     *  Timer Config start
     **************************************************/

    public function get_all_timer_list($company_id)
    {
        $this->db->select('id, name');
        $this->db->from("cat_timer_config");
        $this->db->where('company_id', $company_id);
        $this->db->order_by('id', 'desc');
        $query = $this->db->get();
        $array = array();
        if ($query->num_rows()) {
            $array = $query->result();
            $query->free_result();
        }

        return $array;
    }

    public function get_timer_list($company_id, $offset, $limit, $order_item = 'name', $order = 'ASC', $name = null)
    {
        $this->db->select('count(id) as total');
        $this->db->from("cat_timer_config");
        if ($name) {
            $this->db->like('name', $name, 'both');
        }
        $this->db->where('company_id', $company_id);

        $query = $this->db->get();
        $total = $query->row()->total;
        $array = array();
        if ($total > 0) {
            $this->db->select('vc.*, u.name as username');
            $this->db->from("cat_timer_config vc");
            $this->db->join("cat_user u", "u.id = vc.add_user_id", "left");
            $this->db->where('vc.company_id', $company_id);
            if ($name) {
                $this->db->like('vc.name', $name, 'both');
            }

            $this->db->order_by('vc.' . $order_item, $order);


            if ($limit > 0) {
                $this->db->limit($limit, $offset);
            }
            $query = $this->db->get();

            if ($query->num_rows()) {
                $array = $query->result();
                $query->free_result();
            }
        }


        return array('total' => $total, 'data' => $array);
    }


    /**
     * 获取单个定时下载策略
     *
     * @param object $id
     * @return
     */
    public function get_timer($id)
    {
        $this->db->where('id', $id);
        $query = $this->db->get('cat_timer_config');
        if ($query->num_rows()) {
            return $query->row();
        }

        return false;
    }
    /**
     * 通过定时下载策略名称  获取定时下载策略信息
     * @param object $id
     * @param object $cid
     * @param object $name
     * @return
     */
    public function get_timer_by_name($id, $cid, $name)
    {
        if ($id > 0) {
            $sql = "select id from cat_timer_config where id != $id and company_id = '$cid' and name = '$name'";
        } else {
            $sql = "select id from cat_timer_config where company_id = '$cid' and name = '$name'";
        }
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
    }

    /**
     * 获取定时下载策略的配置信息
     *
     * @param object $timer_id
     * @param object $week [optional] 默认获取全部
     * @return
     */
    public function get_timer_extra($timer_id, $week = false)
    {
        $this->db->from("cat_timer_config_extra");
        $this->db->where('timer_id', $timer_id);
        if ($week !== false) {
            $this->db->where('week', $week);
        }

        $this->db->order_by('id', 'asc');
        $query = $this->db->get();
        $array = array();
        if ($query->num_rows()) {
            $array = $query->result();
            $query->free_result();
        }

        return $array;
    }

    public function add_timer($array, $uid)
    {
        if (empty($array)) {
            return false;
        }

        $array['add_user_id'] = $uid;

        if ($this->db->insert('cat_timer_config', $array)) {
            $id = $this->db->insert_id();
            $this->user_log($this->OP_TYPE_USER, 'cat_timer_config[' . $id . '] json[' . json_encode($array) . ']');
            return $id;
        } else {
            return false;
        }
    }

    public function update_timer($array, $id)
    {
        if (empty($array)) {
            return false;
        }

        $this->db->where('id', $id);
        if ($this->db->update('cat_timer_config', $array)) {
            $this->user_log($this->OP_TYPE_USER, 'update cat_timer_config[' . $id . '] json[' . json_encode($array) . ']');
            return true;
        } else {
            return false;
        }
    }

    public function add_timer_extra($array)
    {
        if (empty($array)) {
            return false;
        }

        if ($this->db->insert('cat_timer_config_extra', $array)) {
            $id = $this->db->insert_id();
            $this->user_log($this->OP_TYPE_USER, 'cat_timer_config_extra[' . $id . '] json[' . json_encode($array) . ']');
            return $id;
        } else {
            return false;
        }
    }

    public function update_timer_extra($array, $id)
    {
        if (empty($array)) {
            return false;
        }
        $this->db->where('id', $id);
        if ($this->db->update('cat_timer_config_extra', $array)) {
            $id = $this->db->insert_id();
            $this->user_log($this->OP_TYPE_USER, 'cat_timer_config_extra[' . $id . '] json[' . json_encode($array) . ']');
            return $id;
        } else {
            return false;
        }
    }

    public function delete_timer($id)
    {
        if (is_array($id)) {
            $this->db->where_in('id', $id);
        } else {
            $this->db->where('id', $id);
        }
        if ($this->db->delete('cat_timer_config')) {
            //$this->delete_timer_extra($id);
            //$this->user_log($this->OP_TYPE_USER, 'delete cat_timer_config[' . $id . '] success');
            return true;
        } else {
            return false;
        }
    }

    public function delete_timer_extra($timer_id)
    {
        $this->db->from("cat_timer_config_extra");
        $this->db->where('timer_id', $timer_id);
        return $this->db->delete();
    }


    /************************************************
     *  Timer Config end
     **************************************************/

    /************************************************
     *  config.xml
     **************************************************/
    public function add_config_setting($array)
    {
        if (empty($array)) {
            return 0;
        }
        if ($this->db->insert('cat_device_config', $array)) {
            $id = $this->db->insert_id();
            return $id;
        } else {
            return false;
        }
    }
    public function update_config_setting($id, $array)
    {
        if (empty($array)) {
            return 0;
        }
        $this->db->where('id', $id);
        $this->db->update('cat_device_config', $array);
    }

    public function get_config_list($cid, $offset = 0, $limit = -1, $order_item = 'id', $order = 'desc', $name = null)
    {
        $this->db->select('count(id) as total');
        $this->db->from("cat_device_config");
        if ($name) {
            $this->db->like('name', $name, 'both');
        }
        $this->db->where('company_id', $cid);

        //echo $this->db->get_sql();die();
        $query = $this->db->get();
        $total = $query->row()->total;
        $array = array();
        if ($total > 0) {
            $this->db->from("cat_device_config");
            $this->db->where('company_id', $cid);
            $this->db->order_by($order_item, $order);
            if ($name) {
                $this->db->like('name', $name, 'both');
            }

            if ($limit != -1) {
                $this->db->limit($limit, $offset);
            }

            $query = $this->db->get();
            if ($query->num_rows() > 0) {
                $array = $query->result();
                $query->free_result();
            }
        }
        return array('total' => $total, 'data' => $array);
    }

    public function get_config_setting($id)
    {
        $array = array();
        $sql = "SELECT * FROM `cat_device_config` WHERE id =$id";
        $query = $this->db->query($sql);

        if ($query->num_rows() > 0) {
            $array = $query->row();
            return $array;
        }

        return false;
    }
    public function remove_config($id)
    {
        $sql = "DELETE FROM `cat_device_config` WHERE id =$id";
        $query = $this->db->query($sql);
        if ($query) {
            return true;
        } else {
            return false;
        }
    }


    public function get_timer_details($timer_id)
    {
        if (!$timer_id) {
            return false;
        }
        $timer = $this->get_timer($timer_id);

        if (!$timer) {
            return false;
        }


        $this->db->select("start_time,end_time,status,week");
        $this->db->from("cat_timer_config_extra");
        $this->db->where('timer_id', $timer_id);
        $this->db->where('status', '0');

        $query = $this->db->get();

        if ($query->num_rows()) {
            $result = array();
            $data = $query->result();

            $result['type'] = $timer->type;
            $result['offwds'] = explode(',', $timer->offweekdays);
            $result['data'] = $data;

            for ($week = 0; $week <= 7; $week++) {
                $dayarray = array();
                foreach ($data as $item) {
                    if ($item->week == $week) {
                        sscanf($item->start_time, "%02d:%02d", $startH, $startM);
                        sscanf($item->end_time, "%02d:%02d", $endH, $endM);

                        $dayarray[] = array('startH' => $startH, 'startM' => $startM, 'endH' => $endH, 'endM' => $endM);
                    }
                }
                if (empty($dayarray)) {
                    $dayarray[] = array('startH' => 0, 'startM' => 0, 'endH' => 24, 'endM' => 0);
                }

                $weekary[$week] = $dayarray;
            }

            $result['data'] = $weekary;


            $query->free_result();

            return $result;
        }

        return false;
    }

    //return values: -1, whole day off
    public function get_todays_timer_detail($timer_id, $today)
    {
        $data = array();
        $week = false;

        $timer = $this->get_timer($timer_id);
        if (!$timer) {
            return false;
        }

        if ($timer->type != 0) {
            $week = date('w', $today);
            if ($week == 0) {
                $week = 7;
            }
        } else {
            $week = 0;
        }

        if ($week > 0 && in_array($week, explode(',', $timer->offweekdays))) {
            return -1;
        }

        $this->db->select("start_time,end_time");
        $this->db->from("cat_timer_config_extra");
        $this->db->where('timer_id', $timer_id);

        if ($week !== false) {
            $this->db->where('week', $week);
        }

        $this->db->where('status', 0);

        $this->db->order_by('start_time', 'asc');
        $query = $this->db->get();
        $array = array();
        if ($query->num_rows()) {
            $array = $query->result();
            $query->free_result();
            foreach ($array as $item) {
                sscanf($item->start_time, "%02d:%02d", $startH, $startM);
                sscanf($item->end_time, "%02d:%02d", $endH, $endM);
                $dayarray[] = array('startH' => $startH, 'startM' => $startM, 'endH' => $endH, 'endM' => $endM);
            }
            return $dayarray;
        }

        return false;
    }

    /************************************************
     *  config.xml  end
     **************************************************/
}
