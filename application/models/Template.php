<?php
class Template extends MY_Model
{
    public function get_template_list($cid, $offset = 0, $limit = -1, $order_item = 'update_time', $order = 'desc', $filter_array = array())
    {
        $this->db->select("cat_template.*");

        $this->db->where('company_id', $cid);

        if (!empty($filter_array)) {
            if (isset($filter_array['name']) && $filter_array['name']) {
                $this->db->like('name', $filter_array['name']);
            }
            if (isset($filter_array['type']) && $filter_array['type']) {
                $this->db->like('type', $filter_array['type']);
            }
            if (isset($filter_array['user_id']) && $filter_array['user_id']) {
                $this->db->join('cat_user_template ut', 'ut.template_id=cat_template.id', 'left');
                $this->db->where_in('ut.user_id', $filter_array['user_id']);
            }
        }
        $query = $this->db->from('cat_template');
        $db = clone ($this->db);
        $total = $this->db->count_all_results();

        $data = array();
        if ($total > 0) {
            $this->db = $db;
            $this->db->order_by($order_item, $order);

            if ($limit != -1) {
                $this->db->limit($limit, $offset);
            }
            $query = $this->db->get();
            if ($query->num_rows()) {
                $data = $query->result();
                $query->free_result();
            }
        }


        return array('total' => $total, 'data' => $data);
    }

    /**
     * 获取某个模板详情
     *
     * @param object $id
     * @return
     */
    public function get_template($id)
    {
        $this->db->where('id', $id);
        $query = $this->db->get('cat_template');
        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return FALSE;
        }
    }
    /**
     * 根据模板名称查询 此名称的模板是否存在
     * @param object $id
     * @param object $cid
     * @param object $name
     * @return
     */
    public function get_template_by_name($id, $cid, $name)
    {
        if ($id > 0) {
            $sql = "select id from cat_template where id != $id and company_id = '$cid' and name = '$name'";
        } else {
            $sql = "select id from cat_template where company_id = '$cid' and name = '$name'";
        }
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return FALSE;
        }
    }

    public function is_template_using($id)
    {
        $this->db->select('count(t.id) as total');

        $this->db->from('cat_template t');
        $this->db->join('cat_playlist p', 't.id=p.template_id', 'left');
        $this->db->where('t.id', $id);
        $this->db->where('p.published', $this->config->item('playlist.status.published'));
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->row()->total > 0;
        } else {
            return FALSE;
        }
    }
    public function add_template($array, $cid, $uid, $init_video_area = FALSE)
    {
        if (empty($array)) {
            return 0;
        }

        $array['company_id'] = $cid;
        $array['add_user_id'] = $uid;

        if ($this->db->insert('cat_template', $array)) {
            $id = $this->db->insert_id();
            //  $this->user_log($this->OP_TYPE_USER, 'add_template[' . $id . '] ' . json_encode($array));

            return $id;
        } else {
            return FALSE;
        }
    }

    /**
     * 更新模板属性
     *
     * @param object $array
     * @param object $id
     * @return
     */
    public function update_template($array, $id)
    {
        if (empty($array)) {
            return 0;
        }

        $this->db->where('id', $id);
        if ($this->db->update('cat_template', $array)) {
            $this->user_log($this->OP_TYPE_USER, 'update_template[' . $id . '] ' . json_encode($array));
            return $id;
        } else {
            return FALSE;
        }
    }
    /**
     * 删除当前模板，删除与当前模板相关联的记录信息
     *
     * @param object $id
     * @return
     */
    public function delete_template($id)
    {
        $this->db->delete('cat_template', array('id' => $id));
        return TRUE;
    }

    /**
     * 添加区域信息
     *
     * @param object $array
     * @param object $template_id
     * @return
     */
    public function add_template_area($array, $template_id)
    {
        if (empty($array)) {
            return 0;
        }

        $array['template_id'] = $template_id;

        if ($this->db->insert('cat_template_area', $array)) {
            $id = $this->db->insert_id();
            //$this->user_log($this->OP_TYPE_USER, 'add_template_area[' . $id . '] ' . json_encode($array));
            return $id;
        } else {
            return FALSE;
        }
    }

    public function get_area_list($template_id)
    {
        $this->db->where('template_id', $template_id);
        $this->db->from('cat_template_area');
        $this->db->order_by('area_type', 'asc');
        //$this->db->order_by('name', 'asc');
        $this->db->order_by('CAST(substring(name,3) AS signed integer) ASC');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $result = $query->result();
            $query->free_result();
            return $result;
        } else {
            return FALSE;
        }
    }

    public function get_area_extra_setting($area_id)
    {
        $this->db->where('area_id', $area_id);
        $query = $this->db->get('cat_area_extra_setting');
        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return FALSE;
        }
    }

    public function get_area_image_setting($area_id)
    {
        $this->db->select('s.media_id, m.main_url, m.tiny_url,m.full_path,m.file_size,m.signature,m.name,m.id');
        $this->db->from('cat_area_image_setting s');
        $this->db->join('cat_media m', 'm.id=s.media_id', 'left');
        $this->db->where('s.area_id', $area_id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return FALSE;
        }
    }




    public function update_area_extra_setting($array, $id)
    {
        if (empty($array)) {
            return 0;
        }
        $this->db->where('area_id', $id);
        $this->db->delete('cat_area_extra_setting');
        $array['area_id'] = $id;
        if (isset($array['id'])) {
            unset($array['id']);
        }
        if ($this->db->insert('cat_area_extra_setting', $array)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function delete_template_area($id, $area_type = 0, $media_id = 0)
    {
        $this->db->delete('cat_template_area', array('id' => $id));
    }
    public function delete_area_media($area_id, $media_id = 0)
    {
        $where = array('area_id' => $area_id);
        if ($media_id) {
            $where['media_id'] = $media_id;
        }
        return $this->db->delete('cat_playlist_area_media', $where);
    }

    public function update_template_area($array, $area_id)
    {
        if (empty($array) || $area_id <= 0) {
            return 0;
        }

        $this->db->where('id', $area_id);
        if ($this->db->update('cat_template_area', $array)) {

            return TRUE;
        } else {

            return FALSE;
        }
    }

    public function add_area_image_setting($area_id, $media_id)
    {
        if (!$area_id) {
            return FALSE;
        }

        $this->db->where('area_id', $area_id);
        $this->db->delete('cat_area_image_setting');

        if ($media_id > 0) {
            if (!$this->db->insert('cat_area_image_setting', array('area_id' => $area_id, 'media_id' => $media_id))) {
                return FALSE;
            }
        }
        return TRUE;
    }
}
