<?php

use Spatie\ArrayToXml\ArrayToXml;
use KubAT\PhpSimple\HtmlDomParser;

/**
 * 节目相关DB业务
 */
class Program extends MY_Model
{
    //*******************************************
    //Template start
    //*******************************************

    public function get_company_template_count($cid)
    {
        $this->db->select("count(*) as total");
        $this->db->where('company_id', $cid);
        $this->db->where('system', 0);
        $query = $this->db->get('cat_template');

        return $query->row()->total;
    }

    /**
     * 获取模板列表
     *
     * @param object $cid
     * @param object $offset
     * @param object $limit
     * @param object $system
     * @param object $only_valid 是否有效的，默认是全部
     * @return
     */
    public function get_template_list($cid, $offset, $limit, $system, $only_valid = false)
    {
        $this->db->select("count(*) as total");
        if ($system) {
            $this->db->where('system', 1);
        } else {
            $this->db->where('company_id', $cid);
            $this->db->where('system', 0);
        }
        if ($only_valid) {
            $this->db->where('flag', 1);
        }


        $query = $this->db->get('cat_template');
        $total = $query->row()->total;
        $data = array();
        if ($total > 0) {
            //$this->db->select("id, name, descr, system, preview_url,add_time");
            if ($system) {
                $this->db->where('system', 1);
            } else {
                $this->db->where('company_id', $cid);
                $this->db->where('system', 0);
            }
            if ($only_valid) {
                $this->db->where('flag', 1);
            }
            $this->db->order_by('id', 'desc');
            $this->db->limit($limit, $offset);

            $query = $this->db->get('cat_template');

            if ($query->num_rows() > 0) {
                $data = $query->result();
                $query->free_result();
            }
        }

        return array('total' => $total, 'data' => $data);
    }

    /**
     * 获取所有已经发布的模板
     *
     * @param object $cid
     * @param object $system
     * @return
     */
    // 原来的
    public function get_all_published_template_list1($cid, $system)
    {
        $data = array();
        //$this->db->select("id, name, descr, system, preview_url,add_time");
        if ($system) {
            $this->db->where('company_id', $cid);
            $this->db->where('template_type', 1);
        } else {
            $this->db->where('company_id', $cid);
            $this->db->where('template_type', 0);
        }
        $this->db->where('flag', 1);
        $this->db->order_by('id', 'desc');

        $query = $this->db->get('cat_template');

        if ($query->num_rows() > 0) {
            $data = $query->result();
            $query->free_result();
        }

        return $data;
    }

    /**
     * 获取所有已经发布的模板
     *
     * @param object $cid
     * @param object $system
     * @return
     */
    public function get_all_published_template_list($cid, $system, $admin_id = false, $uid = false, $group_userId = false)
    {
        $data = array();
        $aids = '';
        foreach ($admin_id as $adminId) {
            $aids = $aids . $adminId->id . ',';
        }
        $aids = $aids . $uid; //该公司下所有的admin的编号  转换成字符串
        if (is_array($group_userId)) {
            $guids = '';
            foreach ($group_userId as $userId) {
                $guids = $guids . $userId->user_id . ',';
            }
            $guids = substr($guids, 0, -1); //该用户所在组中所有的 用户编号
            $sql = "select * from cat_template where (add_user_id in($aids) or add_user_id in($guids))";
        } else {
            $sql = "select * from cat_template where add_user_id in($aids)";
        }
        if ($system) {
            $sql .= " and template_type=1 and company_id=$cid";
        } else {
            $sql .= " and template_type=0 and company_id=$cid";
        }
        $sql .= " and flag=1 order by id desc";
        $query = $this->db->query($sql);

        if ($query->num_rows() > 0) {
            $data = $query->result();
            $query->free_result();
        }

        return $data;
    }

    //根据group_manager获取模板 2013-12-17
    public function group_manager_get_template_list($cid, $admin_id, $uid, $group_userId, $offset, $limit, $system, $only_valid = false)
    {
        $sql_total = ''; //获取template数量的sql
        $sql_list = '';  //获取template列表的sql
        $aids = '';
        foreach ($admin_id as $adminId) {
            $aids = $aids . $adminId->id . ',';
        }
        $aids = $aids . $uid; //该公司下所有的admin的编号  转换成字符串
        if (is_array($group_userId)) {
            $guids = '';
            foreach ($group_userId as $userId) {
                $guids = $guids . $userId->user_id . ',';
            }
            $guids = substr($guids, 0, -1); //该用户所在组中所有的 用户编号
            $sql_total = "select count(*) as total from cat_template where company_id = $cid and (add_user_id in($aids) or add_user_id in($guids))";
            $sql_list = "select * from cat_template where company_id = $cid and (add_user_id in($aids) or add_user_id in($guids)) order by id desc limit $offset,$limit";
        } else {
            $sql_total = "select count(*) as total from cat_template where company_id = $cid and add_user_id in($aids)";
            $sql_list = "select * from cat_template where company_id = $cid and add_user_id in($aids) order by id desc limit $offset,$limit";
        }

        $query = $this->db->query($sql_total);
        $total = $query->row()->total;
        $data = array();
        if ($total > 0) {
            $query = $this->db->query($sql_list);
            if ($query->num_rows() > 0) {
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
            return false;
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
            return false;
        }
    }

    /**
     * 当前模板是否正在被播放列表使用
     *
     * @param object $id
     * @return 正在被用返回true
     */
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
            return false;
        }
    }

    /**
     * 当前模板是否正在被播放列表使用
     *
     * @param object $id
     * @return 正在被用返回true
     */
    public function is_touch_template_using($id)
    {
        $this->db->select('count(t.id) as total');

        $this->db->from('cat_interaction t');
        $this->db->join('cat_interaction_playlist p', 't.id=p.interaction_id', 'left');
        $this->db->where('t.id', $id);
        $this->db->where('p.published', $this->config->item('playlist.status.published'));
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->row()->total > 0;
        } else {
            return false;
        }
    }

    /**
     * 播放列表的模板是否为竖屏
     *
     * @param object $playlist_id
     * @return
     */
    public function is_portrait_template_playlist($playlist_id)
    {
        $this->db->select('t.width, t.height');
        $this->db->from('cat_template t');
        $this->db->join('cat_playlist p', 't.id=p.template_id', 'left');
        $this->db->where('p.id', $playlist_id);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $row = $query->row();
            return $row->width < $row->height;
        } else {
            return false;
        }
    }



    /**
     * interaction添加区域信息
     *
     * @param object $array
     * @param object $template_id
     * @return
     */
    public function add_interaction_area($array, $interaction_id)
    {
        if (empty($array)) {
            return 0;
        }

        $array['interaction_id'] = $interaction_id;

        if ($this->db->insert('cat_interaction_area', $array)) {
            $id = $this->db->insert_id();
            $this->user_log($this->OP_TYPE_USER, 'add_interaction_area[' . $id . '] ' . json_encode($array));
            return $id;
        } else {
            return false;
        }
    }

    /**
     * 更新某个区域设置
     *
     * @param object $array
     * @param object $area_id
     * @return
     */
    public function update_template_area($array, $area_id)
    {
        if (empty($array) || $area_id <= 0) {
            return 0;
        }

        $this->db->where('id', $area_id);
        if ($this->db->update('cat_template_area', $array)) {
            $this->user_log($this->OP_TYPE_USER, 'update_template_area[' . $area_id . '] ' . json_encode($array));
            return true;
        } else {
            $this->user_log($this->OP_TYPE_USER, 'update_template_area[' . $area_id . '] ' . json_encode($array), $this->OP_STATUS_FAIL);
            return false;
        }
    }

    /**
     * 更新interaction某个区域设置
     *
     * @param object $array
     * @param object $area_id
     * @return
     */
    public function update_interaction_area($array, $area_id)
    {
        if (empty($array) || $area_id <= 0) {
            return 0;
        }

        $this->db->where('id', $area_id);
        if ($this->db->update('cat_interaction_area', $array)) {
            $this->user_log($this->OP_TYPE_USER, 'update_interaction_area[' . $area_id . '] ' . json_encode($array));
            return true;
        } else {
            $this->user_log($this->OP_TYPE_USER, 'update_interaction_area[' . $area_id . '] ' . json_encode($array), $this->OP_STATUS_FAIL);
            return false;
        }
    }

    public function get_template_bg_area($template_id)
    {
        $area_type = $this->config->item('area_type_bg');
        return $this->get_template_area($template_id, $area_type);
    }

    public function get_template_area($template_id, $area_type = false)
    {
        $this->db->where('ta.template_id', $template_id);
        if ($area_type !== false) {
            $this->db->where('ta.area_type', $area_type);
        }
        $this->db->from('cat_template_area ta');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            if ($query->num_rows() == 1) {
                return $query->row();
            } else {
                return $query->result();
            }
        }

        return false;
    }

    public function get_interaction_bg_area($interaction_id, $screen_id)
    {
        $area_type = $this->config->item('area_type_bg');
        return $this->get_interaction_area($interaction_id, $area_type, $screen_id);
    }

    public function get_interaction_all_area($interaction_id, $area_type = false)
    {
        $this->db->where('ia.interaction_id', $interaction_id);
        if ($area_type !== false) {
            $this->db->where('ia.area_type', $area_type);
        }
        $this->db->from('cat_interaction_area ia');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            if ($query->num_rows() == 1) {
                return $query->row();
            } else {
                return $query->result();
            }
        }

        return false;
    }

    public function get_interaction_area($interaction_id, $area_type = false, $screen_id = false)
    {
        $this->db->where('ia.interaction_id', $interaction_id);
        if ($screen_id !== false) {
            $this->db->where('ia.page_id', $screen_id);
        }
        if ($area_type !== false) {
            $this->db->where('ia.area_type', $area_type);
        }
        $this->db->from('cat_interaction_area ia');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            if ($query->num_rows() == 1) {
                return $query->row();
            } else {
                return $query->result();
            }
        }

        return false;
    }

    /**
     * 删除模板中的背景区域
     * @param object $template_id
     * @return
     */
    public function delete_template_bg_area($template_id)
    {
        $area_type = $this->config->item('area_type_bg');
        $this->db->select('ta.id, ais.media_id');
        $this->db->where('ta.template_id', $template_id);
        $this->db->where('ta.area_type', $area_type);
        $this->db->from('cat_template_area ta');
        $this->db->join('cat_area_image_setting ais', 'ais.area_id=ta.id', 'left');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $row = $query->row();
            $this->delete_template_area($row->id, $area_type, $row->media_id);
        }
    }

    /**
     * 删除模板中的某个区域
     * @param object $id
     * @param object $area_type [optional]
     * @return
     */
    public function delete_template_area($id, $area_type = 0, $media_id = 0)
    {
        $this->db->delete('cat_template_area', array('id' => $id));

        //if time then delete time config
        if ($area_type == $this->config->item('area_type_date') || $area_type == $this->config->item('area_type_time')) {
            $this->db->delete('cat_area_time_setting', array('area_id' => $id));
        } elseif ($area_type == $this->config->item('area_type_text')) {
            //删除文本
            $this->delete_area_text($id);
        } elseif ($area_type == $this->config->item('area_type_weather')) {
            $this->db->delete('cat_area_weather_setting', array('area_id' => $id));
        } else {
            //删除媒体文件
            $this->delete_area_media($id, $media_id);
        }
    }


    /**
     * 删除应用中的某个区域
     * @param object $id
     * @param object $area_type [optional]
     * @return
     */
    public function delete_interaction_area($id, $area_type = 0, $media_id = 0)
    {
        $this->db->delete('cat_interaction_area', array('id' => $id));

        //if time then delete time config
        if ($area_type == $this->config->item('area_type_date') || $area_type == $this->config->item('area_type_time')) {
            $this->db->delete('cat_area_time_setting', array('area_id' => $id));
        } elseif ($area_type == $this->config->item('area_type_text')) {
            //删除文本
            $this->delete_area_text($id);
        } elseif ($area_type == $this->config->item('area_type_weather')) {
            $this->db->delete('cat_area_weather_setting', array('area_id' => $id));
        } else {
            //删除媒体文件
            $this->delete_area_media($id, $media_id);
        }
    }

    /**
     * 删除某个区域中的字幕设置
     *
     * @param object $area_id
     * @return
     */
    public function delete_area_text($area_id)
    {
        $this->db->delete('cat_area_text_setting', array('area_id' => $area_id));
    }

    /**
     * 删除某个播放列表区域中的字幕设置
     *
     * @param object $playlist_id
     * @return
     */
    public function delete_playlist_area_text($playlist_id)
    {
        $this->db->delete('cat_area_text_setting', array('playlist_id' => $playlist_id));
    }

    /**
     * 删除某个区域的媒体文件
     *
     * @param object $area_id
     * @return
     */
    public function delete_area_media($area_id, $media_id = 0)
    {
        $where = array('area_id' => $area_id);
        if ($media_id) {
            $where['media_id'] = $media_id;
        }
        return $this->db->delete('cat_playlist_area_media', $where);
    }

    public function delete_campaign_area_media($cam_id, $area_id = 101, $media_id = 0)
    {
        $this->db->where('playlist_id', $cam_id);
        if ($area_id > 0) {
            if ($this->config->item('with_template') && $area_id = 101) {
            } else {
                $this->db->where('area_id', $area_id);
            }
        }
        if ($media_id) {
            $this->db->where('media_id', $media_id);
        }
        return $this->db->delete('cat_playlist_area_media');
    }

    public function soft_delete_campaign_area_media($cam_id)
    {
        $this->db->where('playlist_id', $cam_id);

        return $this->db->update('cat_playlist_area_media', array('flag' => 2));
    }

    public function real_delete_campaign_area_media($cam_id)
    {
        $this->db->where('playlist_id', $cam_id);
        $this->db->where('flag', 2);
        return $this->db->delete('cat_playlist_area_media');
    }


    /**
     * 添加区域数据
     *
     * @param object $array
     * @param object $uid
     * @return
     */
    public function add_area_media($array)
    {
        if (empty($array)) {
            return 0;
        }


        if ($this->db->insert('cat_playlist_area_media', $array)) {
            $id = $this->db->insert_id();
            return $id;
        } else {
            return false;
        }

        return false;
    }

    public function update_area_media($array, $id)
    {
        if (empty($array) || $id == 0) {
            return false;
        }
        $this->db->where('id', $id);

        return $this->db->update('cat_playlist_area_media', $array);
    }

    /**
     * 删除临时数据，修改所有状态为删除的为ok状态
     * @param object $playlist_id
     * @return
     */
    public function update_area_media_commit($playlist_id)
    {
        $this->db->where('playlist_id', $playlist_id);
        $this->db->where('flag', $this->config->item('area_media_flag_temp'));
        $this->db->delete('cat_playlist_area_media');

        $this->db->where('playlist_id', $playlist_id);
        $this->db->where('flag', $this->config->item('area_media_flag_delete'));
        $this->db->update('cat_playlist_area_media', array('flag' => $this->config->item('area_media_flag_ok')));
    }

    /**
     * 删除某个播放列表区域中的媒体文件
     *
     * @param object $playlist_id
     * @param object $area_id [optional] 删除播放列表中某个区域下的所有文件
     * @param object $id [optional] 删除某个区域下的某个文件
     * @param object $flag [optional] 删除某个区域下的特定文件
     * @return
     */
    public function delete_playlist_area_media($playlist_id, $area_id = 0, $id = 0, $flag = false)
    {
        $this->db->where('playlist_id', $playlist_id);

        if ($area_id > 0) {
            $this->db->where('area_id', $area_id);
        }

        if ($id) {
            if (is_array($id)) {
                $this->db->where_in('id', $id);
            } else {
                $this->db->where('id', $id);
            }
        }

        if ($flag !== false) {
            $this->db->where('flag', $flag);
        }

        return $this->db->delete('cat_playlist_area_media');
    }

    /**
     * 删除某个播放列表中的某条记录
     *
     * @param object $id
     * @return
     */
    public function delete_media($id)
    {
        if (is_array($id)) {
            $this->db->where_in('id', $id);
        } else {
            $this->db->where('id', $id);
        }

        $this->user_log($this->OP_TYPE_USER, 'delete_media cat_playlist_area_media' . json_encode($id));
        return $this->db->delete('cat_playlist_area_media');
    }

    /**
     * 更新媒体文件的状态
     * @param object $id
     * @param object $flag [optional]
     * @return
     */
    public function update_media_flag($id, $flag = false)
    {
        if (is_array($id)) {
            $this->db->where_in('id', $id);
        } else {
            $this->db->where('id', $id);
        }
        if ($flag !== false) {
            $this->db->set(array('flag' => $flag));
        } else {
            $this->db->set(array('flag' => $this->config->item('area_media_flag_ok')));
        }

        return $this->db->update('cat_playlist_area_media');
    }



    /**
     * interaction获取某个模板下的区域信息
     *
     * @param object $interaction_id
     * @return
     */
    public function get_interaction_area_list($interaction_id)
    {
        $this->db->where('interaction_id', $interaction_id);
        $this->db->from('cat_interaction_area');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $result = $query->result();
            $query->free_result();
            return $result;
        } else {
            return false;
        }
    }

    /**
     * interaction获取某个模板下的区域信息
     * @param object $interaction_id, $folderId, $pageId
     * @return
     */
    public function get_interaction_area_list_by_fp($interaction_id, $pageId)
    {
        $sql = "select * from cat_interaction_area where page_id = " . $pageId . " and interaction_id =" . $interaction_id;
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $result = $query->result();
            $query->free_result();
            return $result;
        } else {
            return false;
        }
    }

    /**
     * 获取interaction 的树形结构json
     * @param object $id
     * @return
     */
    public function get_interaction_tree($id)
    {
        $this->db->where('interaction_id', $id);
        $query = $this->db->get('cat_interaction_tree');
        if ($query->num_rows() > 0) {
            $result = $query->row();
            return $result;
        } else {
            return false;
        }
    }

    /**
     * 获取interaction 树形结构的最大page_id
     * @param object $id
     * @return
     */
    public function get_interaction_screenID($id)
    {
        $this->db->distinct('page_id');
        $this->db->where('interaction_id', $id);
        $this->db->order_by('page_id', 'desc');
        $query = $this->db->get('cat_interaction_area');
        if ($query->num_rows() > 0) {
            $result = $query->row();
            return $result;
        } else {
            return false;
        }
    }
    /**
     * 获取interaction树形结构tree_id
     * @param object $id
     * @return
     */
    public function get_interaction_treeNodeCount($id)
    {
        $this->db->select('count(id) as counts');
        $this->db->where('interaction_id', $id);
        $query = $this->db->get('cat_interaction_area');
        if ($query->num_rows() > 0) {
            $result = $query->row();
            return $result;
        } else {
            return false;
        }
    }

    /**
     * 更新 interaction 的树形结构json
     * @param object $array
     * @param object $id
     * @return
     */
    public function update_interaction_tree($array, $id)
    {
        if (empty($array)) {
            return 0;
        }
        $this->db->where('interaction_id', $id);
        if ($this->db->update('cat_interaction_tree', $array)) {
            $this->user_log($this->OP_TYPE_USER, 'update_interaction_tree[' . $id . '] ' . json_encode($array));
            return true;
        } else {
            return false;
        }
    }
    /**
     * 更新 interaction 的树形结构json
     * @param object $array
     * @return
     */
    public function add_interaction_tree($array)
    {
        if (empty($array)) {
            return 0;
        }
        if ($this->db->insert('cat_interaction_tree', $array)) {
            $id = $this->db->insert_id();
            $this->user_log($this->OP_TYPE_USER, 'add_interaction_tree' . json_encode($array));
            return $id;
        } else {
            return false;
        }
    }

    /**
     * 获取某个区域详细信息
     *
     * @param object $id
     * @return
     */
    public function get_area($id)
    {
        $this->db->where('id', $id);
        $query = $this->db->get('cat_area');
        if ($query->num_rows() > 0) {
            $result = $query->row();
            return $result;
        } else {
            return false;
        }

        /*       $this->db->where('id', $id);
               $query = $this->db->get('cat_template_area');
               if ($query->num_rows() > 0) {
                   $result = $query->row();
                   return $result;
               } else {
                   return FALSE;
               }
         */
    }

    /**
     * 返回当前区域的类型
     *
     * @param object $area_id
     * @return
     */
    public function get_area_type($area_id)
    {
        $this->db->select('area_type');
        $this->db->where('id', $area_id);
        $query = $this->db->get('cat_template_area');
        if ($query->num_rows() > 0) {
            $result = $query->row();
            return $result->area_type;
        } else {
            return false;
        }
    }

    /**
     * 获取某个播放列表区域的传输类型，是transmode_type_full or transmode_type_part
     *
     * @param object $id
     * @return
     */
    public function get_area_transmode_type($id)
    {
        $this->db->select('ta.area_type, ta.w, ta.h, t.w as tw, t.h as th');
        $this->db->from('cat_template_area ta');
        $this->db->join('cat_template t', 't.id=ta.template_id', 'left');
        $this->db->where('ta.id', $id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $result = $query->row();
            if ($result->area_type == $this->config->item('area_type_image')) {
                return $this->config->item('transmode_type_image');
            } elseif ($result->area_type == $this->config->item('area_type_movie')) {
                if ($result->w == $result->tw && $result->h == $result->th) {
                    return $this->config->item('transmode_type_full');
                } else {
                    return $this->config->item('transmode_type_part');
                }
            }
        } else {
            return false;
        }
    }

    /**
     * 获取某个模板的字幕区域
     *
     * @param object $template_id
     * @return
     */
    public function get_text_area($template_id)
    {
        $this->db->where('template_id', $template_id);
        $this->db->where('area_type', $this->config->item('area_type_text'));
        $query = $this->db->get('cat_template_area');
        if ($query->num_rows() > 0) {
            $result = $query->row();
            return $result;
        } else {
            return false;
        }
    }

    /**
     * 添加区域设置保存
     *
     * @param object $array
     * @param object $uid
     * @return
     */
    public function add_area_time_setting($array, $uid)
    {
        if (empty($array)) {
            return 0;
        }

        $array['add_user_id'] = $uid;

        if ($this->db->insert('cat_area_time_setting', $array)) {
            $id = $this->db->insert_id();
            $this->user_log($this->OP_TYPE_USER, 'add_area_time_setting' . json_encode($array));
            return $id;
        } else {
            return false;
        }
    }

    /**
     * 获取某个tempate中已知名字的区域
     *
     * @param object $area_name
     * @return
     */
    public function get_area_byname($area_name, $template_id)
    {
        $this->db->like('name', $area_name, 'after');
        $this->db->where('template_id', $template_id);
        $this->db->from('cat_template_area');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            return $result;
        } else {
            return false;
        }
    }


    /**
     * 获取某个区域的时间设置
     *
     * @param object $area_id
     * @return
     */
    public function get_area_time_setting($area_id, $playlist_id)
    {
        $this->db->where('area_id', $area_id);
        $this->db->where('playlist_id', $playlist_id);
        $query = $this->db->get('cat_area_time_setting');
        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
    }

    /**
     * 获取天气预报的设置
     *
     * @param object $area_id
     * @return
     */
    public function get_area_weather_setting($area_id, $playlist_id)
    {
        $this->db->where('area_id', $area_id);
        $this->db->where('playlist_id', $playlist_id);
        $query = $this->db->get('cat_area_weather_setting');
        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
    }

    /**
     * 更新时间区域设置
     *
     * @param object $array
     * @param object $area_id
     * @return
     */
    public function update_area_time_setting($array, $id)
    {
        if (empty($array)) {
            return 0;
        }

        //$array['add_user_id'] = $uid;
        $this->db->where('id', $id);
        if ($this->db->update('cat_area_time_setting', $array)) {
            $this->user_log($this->OP_TYPE_USER, 'update_area_time_setting[' . $id . '] ' . json_encode($array));
            return true;
        } else {
            return false;
        }
    }

    /**
     * 添加天气预报设置
     *
     * @param object $array
     * @param object $uid
     * @return
     */
    public function add_area_weather_setting($array, $uid)
    {
        if (empty($array)) {
            return 0;
        }

        $array['add_user_id'] = $uid;

        if ($this->db->insert('cat_area_weather_setting', $array)) {
            $id = $this->db->insert_id();
            $this->user_log($this->OP_TYPE_USER, 'add_area_weather_setting' . json_encode($array));
            return $id;
        } else {
            return false;
        }
    }

    /**
     * 更新天气设置
     *
     * @param object $array
     * @param object $area_id
     * @return
     */
    public function update_area_weather_setting($array, $id)
    {
        if (empty($array)) {
            return 0;
        }

        $this->db->where('id', $id);
        if ($this->db->update('cat_area_weather_setting', $array)) {
            $this->user_log($this->OP_TYPE_USER, 'update_area_weather_setting[' . $id . '] ' . json_encode($array));
            return true;
        } else {
            return false;
        }
    }

    /**
     * 添加文字区域设置
     *
     * @param object $array
     * @param object $uid
     * @return
     */
    public function add_area_text_setting($array, $uid)
    {
        if (empty($array)) {
            return 0;
        }

        $array['add_user_id'] = $uid;

        if ($this->db->insert('cat_area_text_setting', $array)) {
            $id = $this->db->insert_id();
            $this->user_log($this->OP_TYPE_USER, 'add_area_text_setting[' . $id . '] ' . json_encode($array));
            return $id;
        } else {
            return false;
        }
    }

    /**
     * 更新文本区域设置
     *
     * @param object $array
     * @return
     */
    public function update_area_text_setting($array, $id)
    {
        if (empty($array)) {
            return 0;
        }

        $this->db->where('id', $id);
        if ($this->db->update('cat_area_text_setting', $array)) {
            $this->user_log($this->OP_TYPE_USER, 'update_area_text_setting  id[' . $id . '] ' . json_encode($array));
            return true;
        } else {
            return false;
        }
    }

    /**
     * 添加静态文字区域设置
     *
     * @param object $array
     * @param object $uid
     * @return
     */
    public function add_area_static_text_setting($array, $uid)
    {
        if (empty($array)) {
            return 0;
        }

        $array['add_user_id'] = $uid;

        if ($this->db->insert('cat_area_static_text_setting', $array)) {
            $id = $this->db->insert_id();
            $this->user_log($this->OP_TYPE_USER, 'add_area_text_setting[' . $id . '] ' . json_encode($array));
            return $id;
        } else {
            return false;
        }
    }

    /**
     * 更新静态文本区域设置
     *
     * @param object $array
     * @return
     */
    public function update_area_static_text_setting($array, $id)
    {
        if (empty($array)) {
            return 0;
        }
        $this->db->where('id', $id);
        if ($this->db->update('cat_area_static_text_setting', $array)) {
            $this->user_log($this->OP_TYPE_USER, 'update_area_static_text_setting  id[' . $id . '] ' . json_encode($array));
            return true;
        } else {
            return false;
        }
    }

    /**
     * template添加照片区域的设置
     *
     * @param object $array
     * @param object $uid
     * @return
     */
    public function add_area_image_setting($array, $uid)
    {
        if (empty($array)) {
            return 0;
        }

        $array['add_user_id'] = $uid;

        if ($this->db->insert('cat_area_image_setting', $array)) {
            $this->user_log($this->OP_TYPE_USER, 'add_area_image_setting ' . json_encode($array));
            return true;
        } else {
            return false;
        }
    }

    /**
     * interaction添加照片区域的设置
     *
     * @param object $array
     * @param object $uid
     * @return
     */
    public function add_interaction_area_image_setting($array, $uid)
    {
        if (empty($array)) {
            return 0;
        }

        $array['add_user_id'] = $uid;

        if ($this->db->insert('cat_interaction_area_image_setting', $array)) {
            $this->user_log($this->OP_TYPE_USER, 'add_interaction_area_image_setting ' . json_encode($array));
            return true;
        } else {
            return false;
        }
    }

    /**
     * template更新照片区域设置
     *
     * @param object $array
     * @param object $area_id
     * @return
     */
    public function update_area_image_setting($array, $area_id)
    {
        if (empty($array)) {
            return 0;
        }

        //$array['add_user_id'] = $uid;
        $this->db->where('area_id', $area_id);
        if ($this->db->update('cat_interaction_area_image_setting', $array)) {
            $this->user_log($this->OP_TYPE_USER, 'update_interaction_area_image_setting area_id[' . $area_id . '] ' . json_encode($array));
            return true;
        } else {
            return false;
        }
    }

    /**
     * interaction更新照片区域设置
     *
     * @param object $array
     * @param object $area_id
     * @return
     */
    public function update_interaction_area_image_setting($array, $area_id)
    {
        if (empty($array)) {
            return 0;
        }

        //$array['add_user_id'] = $uid;
        $this->db->where('area_id', $area_id);
        if ($this->db->update('cat_interaction_area_image_setting', $array)) {
            $this->user_log($this->OP_TYPE_USER, 'update_area_image_setting area_id[' . $area_id . '] ' . json_encode($array));
            return true;
        } else {
            return false;
        }
    }

    /**
     * 获取某个区域的照片设置
     *
     * @param object $area_id
     * @return
     */
    public function get_area_image_setting($area_id)
    {
        $this->db->select('s.media_id, m.main_url, m.tiny_url');
        $this->db->from('cat_area_image_setting s');
        $this->db->join('cat_media m', 'm.id=s.media_id', 'left');
        $this->db->where('s.area_id', $area_id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
    }

    /**
     * interaction获取某个区域的照片设置
     *
     * @param object $area_id
     * @return
     */
    public function get_interaction_area_image_setting($area_id)
    {
        $this->db->select('s.media_id, m.main_url, m.tiny_url');
        $this->db->from('cat_interaction_area_image_setting s');
        $this->db->join('cat_media m', 'm.id=s.media_id', 'left');
        $this->db->where('s.area_id', $area_id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
    }


    //*******************************************
    //Template end
    //*******************************************


    //*******************************************
    //campaign/ start
    //*******************************************

    public function get_company_playlist_count($cid)
    {
        $this->db->select('count(*) as total');
        $this->db->where('company_id', $cid);
        $this->db->where('playlist_type', 0);
        $query = $this->db->get('cat_playlist');
        return $query->row()->total;
    }




    public function get_campiagn_list($cid)
    {
        $sql = "SELECT p.*
    	FROM (cat_playlist p)
    	WHERE `p`.`company_id` = '$cid'
    	ORDER BY p.name ASC";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            return $query->result();
        }
        return false;
    }

    /**
     * 获取某个公司下的播放列表
     *
     * @param object $cid
     * @param object $condition 查询条件
     * @param object $offset
     * @param object $limit -1返回全部记录
     * @return
     */
    public function get_playlist_list($cid, $condition = false, $withDetail = false, $offset = 0, $limit = -1, $order_item = 'name', $order = 'ace')
    {
        $showstaff = false;
        $showweather = false;
        $showevent = false;
        $showproperty = false;

        $results = array();

        $this->db->from("cat_playlist p");

        if (!$withDetail) {
            $this->db->select("p.id,p.name,p.priority");
        } else {
            $this->db->select("p.*,
                GROUP_CONCAT(distinct cc.name order by cc.name asc) as criteria_name,
                GROUP_CONCAT(distinct player.name  order by player.name asc) as player_name,
                GROUP_CONCAT(distinct t.name order by t.name asc) as tag_name,
                template.name as template_name");
            if ($this->config->item('with_template')) {
                $this->db->join('criterionables cm', "cm.criterionable_id = p.id and cm.cam_bindtype<2 and cm.criterionable_type='App\\\Playlist'", "LEFT");
                $this->db->join("taggables tm", "tm.taggable_id=p.id and tm.taggable_type='App\\\Playlist'", "LEFT");
            } else {
                $this->db->join('criterionables cm', "cm.criterionable_id = p.id and cm.cam_bindtype<2 and cm.criterionable_type='App\\\Campaign'", "LEFT");
                $this->db->join("taggables tm", "tm.taggable_id=p.id and tm.taggable_type='App\\\Campaign'", "LEFT");
            }


            $this->db->join("cat_criteria cc", "cc.id = cm.criterion_id ", "LEFT");


            $this->db->join("cat_tag t", "t.id = tm.tag_id", "LEFT");
            $this->db->join('cat_template template', 'template.id=p.template_id', 'LEFT');

            $this->db->join("cat_player_campaign pc", "pc.campaign_id = p.id and pc.type=0", "LEFT");
            $this->db->join("cat_player player", " player.id = pc.player_id", "LEFT");

            $this->db->group_by("p.id");
        }

        if (is_array($cid)) {
            $this->db->where_in('p.company_id', $cid);
        } else {
            $this->db->where('p.company_id', $cid);
        }
        if ($condition) {
            $start_date = false;
            foreach ($condition as $key => $value) {
                if ($key == 'name') {
                    $this->db->group_start()
                        ->like("p.name", $value)
                        ->or_like('p.customer_id', $value)
                        ->or_like('p.agency_id', $value)
                        ->or_like('p.customer_name', $value)
                        ->or_like('p.customer_name', $value)
                        ->or_like('p.agency_id', $value)
                        ->or_like('p.contract_id', $value)
                        ->group_end();
                } elseif ($key == 'criteria') {
                    $this->db->where('cm.criterion_id', $value);
                } elseif ($key == 'tag') {
                    $this->db->where('tm.tag_id', $value);
                } elseif ($key == 'player') {
                    $this->db->join("campaign_player cp", "cp.campaign_id = p.id");
                    $this->db->where('cp.player_id', $value);
                } elseif ($key == 'campaigns') {
                    if (!isset($condition['add_user_id'])) {

                        if (!is_array($value)) {
                            $value = explode(",", $value);
                        }
                        $this->db->where_in('p.id', $value);
                    }
                } elseif ($key == 'start') {
                    $start_date = $value;
                } elseif ($key == 'end' && $start_date) {
                    $datecondtion = ' not (p.start_date>"' . $value . '" or p.end_date<"' . $start_date . '")';

                    $this->db->where($datecondtion);
                } elseif ($key == 'withExpired') {
                    if ($value != 1) {
                        $this->db->where('p.end_date>=', date('Y-m-d'));
                    }
                } elseif ($key == 'add_user_id') {
                    $this->db->group_start();
                    $this->db->where('p.add_user_id', $value);

                    if (isset($condition['campaigns'])) {
                        $or_cams = is_array($condition['campaigns']) ? $condition['campaigns'] : explode(",", $condition['campaigns']);

                        $this->db->or_where_in('p.id', $or_cams);
                    }
                    $this->db->group_end();
                } else {
                    $this->db->where("p.$key", $value);
                }
            }
        }
        $this->db->where("p.deleted_at is null");

        $total = $this->db->count_all_results('', false);
        if ($total > 0) {

            // $sqlorder = '';
            if ($order_item != "criteria_name" && $order_item != "tag_name" && $order_item != "template_name") {
                $order_item = "p." . $order_item;
            }

            $this->db->order_by($order_item, $order);

            if ($limit > 0) {
                $this->db->limit($limit, $offset);
            }
            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                $results = $query->result();
                $query->free_result();
            }
        }
        $this->db->reset_query();
        return array('total' => $total, 'data' => $results);
    }



    /**
     * 获取单个播放列表信息
     *
     * @param object $id
     * @return
     */
    public function get_playlist($id)
    {
        $this->db->select("p.*,GROUP_CONCAT(distinct cc.criterion_id) as criterias,GROUP_CONCAT(distinct ca.criterion_id) as and_criterias,GROUP_CONCAT(distinct co.criterion_id) as and_criteria_or,
                GROUP_CONCAT(distinct ce.criterion_id) as ex_criterias,GROUP_CONCAT(distinct tm.tag_id) as tags, GROUP_CONCAT(distinct pc.player_id) as players,GROUP_CONCAT(distinct pc_ex.player_id) as ex_players");
        $this->db->from('cat_playlist p');
        if ($this->config->item('with_template')) {
            $this->db->join('criterionables cc', "cc.criterionable_id = p.id and cc.cam_bindtype=0 and (cc.criterionable_type='App\\\Playlist' or cc.criterionable_type='App\\\Campaign')", "LEFT");
            $this->db->join('criterionables ca', "ca.criterionable_id = p.id and ca.cam_bindtype=1 and (ca.criterionable_type='App\\\Playlist' or ca.criterionable_type='App\\\Campaign')", "LEFT");
            $this->db->join('criterionables ce', "ce.criterionable_id = p.id and ce.cam_bindtype=2 and (ce.criterionable_type='App\\\Playlist' or ce.criterionable_type='App\\\Campaign')", "LEFT");
            $this->db->join('criterionables co', "co.criterionable_id = p.id and co.cam_bindtype=3 and (co.criterionable_type='App\\\Playlist' or co.criterionable_type='App\\\Campaign')", "LEFT");
        } else {
            $this->db->join('criterionables cc', "cc.criterionable_id = p.id and cc.cam_bindtype=0 and cc.criterionable_type='App\\\Campaign'", "LEFT");
            $this->db->join('criterionables ca', "ca.criterionable_id = p.id and ca.cam_bindtype=1 and ca.criterionable_type='App\\\Campaign'", "LEFT");
            $this->db->join('criterionables ce', "ce.criterionable_id = p.id and ce.cam_bindtype=2 and ce.criterionable_type='App\\\Campaign'", "LEFT");
            $this->db->join('criterionables co', "co.criterionable_id = p.id and co.cam_bindtype=3 and co.criterionable_type='App\\\Campaign'", "LEFT");
        }

        $this->db->join('taggables tm', "tm.taggable_id=p.id and tm.taggable_type='App\\\Campaign'", "LEFT");
        $this->db->join('cat_player_campaign pc', "pc.campaign_id = p.id and pc.type=0", "LEFT");
        $this->db->join('cat_player_campaign pc_ex', "pc_ex.campaign_id = p.id and pc_ex.type=1", "LEFT");

        $this->db->where('id', $id);
        $this->db->group_by("p.id");
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $cam = $query->row();

            $this->fill_campaign_media_info($cam);

            return $cam;
        } else {
            return false;
        }
    }

    /**
     * 根据area_id获取媒体url
     *
     * @param object $id
     * @return
     */
    public function get_media_url($area_id, $playlist_id)
    {
        $this->db->select('m.main_url,m.tiny_url,m.ext,m.signature,m.full_path,m.source,m.media_type,p.duration,p.transmode,p.position,p.rotate,p.publish_url,p.preview_url');
        $this->db->from('cat_playlist_area_media p');
        $this->db->join('cat_media m', 'p.media_id = m.id');
        $this->db->where('p.area_id', $area_id);
        $this->db->where('p.playlist_id', $playlist_id);
        $this->db->where('m.source', 0);
        $this->db->where('p.status', 0);
        $this->db->order_by('p.position');
        $media = $this->db->get();
        if ($media->num_rows() > 0) {
            return $media->result();
        } else {
            return false;
        }
    }
    /**
     * 根据列表名称  查询此名称的播放列表是否存在
     * @param object $id
     * @param object $cid
     * @param object $name
     * @return
     */
    public function get_playlist_by_name($id, $cid, $name)
    {
        /*
        if ($id > 0) {
            $sql = "select id from cat_playlist where id != '$id' and company_id = '$cid' and name = '$name'";
        } else {
            $sql = "select id from cat_playlist where company_id = '$cid' and name = '$name'";
        }
        $sql .= " and deleted_at is null";
        */

        $this->db->select("id");
        $this->db->from("cat_playlist");
        $this->db->where('company_id', $cid);
        $this->db->where('name', $name);
        if ($id > 0) {
            $this->db->where("id!=", $id);
        }
        $this->db->where('deleted_at is null');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
    }
    /**
     * 获取播放列表信息通过模板
     *
     * @param object $template_id
     * @return
     */
    public function get_playlist_by_template($template_id)
    {
        $this->db->select('id, name');
        $this->db->where('template_id', $template_id);
        $this->db->where('deleted_at is null');
        $query = $this->db->get('cat_playlist');
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }

    /**
     * 创建播放列表
     *
     * @param object $array
     * @param object $cid
     * @param object $uid
     * @return
     */
    public function add_playlist($array, $cid, $uid)
    {
        if (empty($array)) {
            return 0;
        }

        $array['company_id'] = $cid;
        $array['add_user_id'] = $uid;

        if ($this->db->insert('cat_playlist', $array)) {
            $id = $this->db->insert_id();

            return $id;
        } else {
            return false;
        }
    }
    /**
     * 初始化播放列表背景图片信息
     *
     * @param object $playlist_id
     * @param object $template_id
     * @return
     */
    private function initPlaylistAreaBg($playlist_id, $template_id, $uid)
    {
        $this->db->select("ais.area_id, ais.media_id, t.preview_url, t.company_id");
        $this->db->from("cat_area_image_setting ais");
        $this->db->join("cat_template_area ta", "ta.id = ais.area_id", "LEFT");
        $this->db->join("cat_template t", "t.id = ta.template_id", "LEFT");
        $this->db->where("ta.template_id", $template_id);
        $this->db->where("ta.area_type", $this->config->item("area_type_bg"));

        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $row = $query->row();
            $data = array('playlist_id' => $playlist_id, 'area_id' => $row->area_id, 'media_id' => $row->media_id, 'add_user_id' => $uid, 'duration' => '--', 'transmode' => -1, 'transtime' => -1, 'flag' => 1);
            $this->db->insert("cat_playlist_area_media", $data);

            //初始化预览图
            if (!empty($row->preview_url)) {
                $base = $this->config->item('base_path');
                $template_url = $base . $row->preview_url;
                if (file_exists($template_url)) {
                    $playlist_url = $this->get_playlist_template_preview_url($playlist_id, true);
                    if ($playlist_url) {
                        @copy($template_url, $playlist_url);
                    }
                }
            }
        }
    }

    public function copy_playlist_template_preview_url($template, $playlist_id)
    {
        if ($template) {
            //初始化预览图
            if (!empty($template->preview_url)) {
                $base = $this->config->item('base_path');
                $template_url = $base . $template->preview_url;
                if (file_exists($template_url)) {
                    $playlist_url = $this->get_playlist_template_preview_url($template->company_id, $playlist_id, true);
                    if ($playlist_url) {
                        @copy($template_url, $playlist_url);

                        return $playlist_url;
                    }
                }
            }
        }
        return false;
    }

    /**
     * 更新当前播放列表的预览图
     *
     * @param object $playlist_id
     * @return
     */
    public function update_playlist_template_preview_url($playlist_id)
    {
        $this->db->select('template_id');
        $this->db->from('cat_playlist');
        $this->db->where('id', $playlist_id);
        $query = $this->db->get();
        if ($query->num_rows() == 0) {
            return false;
        }

        $template_id = $query->row()->template_id;
        $this->db->select("ta.area_type, pam.area_id, pam.media_id");
        $this->db->from("cat_playlist_area_media pam");
        $this->db->join("cat_template_area ta", "ta.id = pam.area_id", "LEFT");
        $this->db->where_in("ta.area_type", array($this->config->item("area_type_bg"), $this->config->item("area_type_logo")));
        $this->db->where("pam.playlist_id", $playlist_id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            //首先必须存在一个背景区域
            $result = $query->result();
            $bg_media_id = false;
            $logo_media_id = false;
            foreach ($result as $r) {
                if ($r->area_type == $this->config->item("area_type_bg")) {
                    $bg_media_id = $r->media_id;
                } else {
                    $logo_media_id = $r->media_id;
                }
            }
            return $this->update_template_preview_url($template_id, $playlist_id, $bg_media_id, $logo_media_id);
        }

        return false;
    }

    /**
     * 更新模板的预览图
     *
     * @param object $template_id
     * @param object $playlist_id [optional]
     * @param object $media_id [optional]
     * @param object $logo_media_id [optional]
     * @return
     */
    public function update_template_preview_url($template_id, $playlist_id = false, $media_id = false, $logo_media_id = false)
    {
        $template = $this->get_template($template_id);
        if ($template) {
            $rpath = $playlist_id ? sprintf($this->config->item('playlist_preview_path'), $template->company_id) : sprintf($this->config->item('tempate_preview_path'), $template->company_id);
            if ($rpath) {
                $pwidth = $this->config->item('template_preview_width');
                $pheight = $this->config->item('template_preview_height');
                $swidth = $template->w;
                $sheight = $template->h;
                if ($template->w < $template->h) {
                    $pwidth = $this->config->item('template_preview_reverse_width');
                }
                $rwidth = $pwidth / $swidth; //宽度比
                $rheight = $pheight / $sheight; //高度比
                $bg_file = false;
                if ($media_id) {
                    $media = $this->material->get_media($media_id);
                    if ($media) {
                        $bg_file = '.' . $media->main_url;
                    }
                }
                $logo_file = false;
                if ($logo_media_id) {
                    $media = $this->material->get_media($logo_media_id);
                    if ($media) {
                        $logo_file = '.' . $media->main_url;
                    }
                }

                $this->image->create($pwidth, $pheight, $bg_file);
                $area_list = $this->template->get_area_list($template_id);
                if ($area_list) {
                    $i = 1;
                    foreach ($area_list as $area) {
                        switch ($area->area_type) {
                            case $this->config->item('area_type_staticText'):
                                $this->image->add_area($area->x * $rwidth, $area->y * $rheight, $area->w * $rwidth, $area->h * $rheight, $area->name, $this->config->item('area_type_staticText_color'));
                                break;
                            case $this->config->item('area_type_movie'):
                                $this->image->add_area($area->x * $rwidth, $area->y * $rheight, $area->w * $rwidth, $area->h * $rheight, $area->name, $this->config->item('area_type_movie_color'));
                                break;
                            case $this->config->item('area_type_image'):
                                $this->image->add_area($area->x * $rwidth, $area->y * $rheight, $area->w * $rwidth, $area->h * $rheight, $area->name, $this->config->item('area_type_image' . $i . '_color'));
                                $i++;
                                break;
                            case $this->config->item('area_type_webpage'):
                                $this->image->add_area($area->x * $rwidth, $area->y * $rheight, $area->w * $rwidth, $area->h * $rheight, $area->name, $this->config->item('area_type_webpage_color'));
                                break;
                            case $this->config->item('area_type_mask'):
                                $this->image->add_area($area->x * $rwidth, $area->y * $rheight, $area->w * $rwidth, $area->h * $rheight, $area->name, $this->config->item('area_type_mask_color'));
                                break;
                            case $this->config->item('area_type_date'):
                                $this->image->add_area($area->x * $rwidth, $area->y * $rheight, $area->w * $rwidth, $area->h * $rheight, $area->name, $this->config->item('area_type_date_color'));
                                break;
                            case $this->config->item('area_type_weather'):
                                $this->image->add_area($area->x * $rwidth, $area->y * $rheight, $area->w * $rwidth, $area->h * $rheight, $area->name, $this->config->item('area_type_weather_color'));
                                break;
                            case $this->config->item('area_type_time'):
                                $this->image->add_area($area->x * $rwidth, $area->y * $rheight, $area->w * $rwidth, $area->h * $rheight, $area->name, $this->config->item('area_type_time_color'));
                                break;
                            case $this->config->item('area_type_text'):
                                $this->image->add_area($area->x * $rwidth, $area->y * $rheight, $area->w * $rwidth, $area->h * $rheight, $area->name, $this->config->item('area_type_text_color'));
                                break;
                            case $this->config->item('area_type_logo'):
                                $this->image->add_area($area->x * $rwidth, $area->y * $rheight, $area->w * $rwidth, $area->h * $rheight, $area->name, $this->config->item('area_type_logo_color'), $this->config->item('area_border_color'), $logo_file);
                                break;
                        }
                    }
                }

                if ($playlist_id) {
                    $absolut_path = $this->config->item('base_path') . $rpath;
                    if ($this->image->save($absolut_path, $playlist_id . '.jpg')) {
                        return $rpath . '/' . $playlist_id . '.jpg';
                    }
                } else {
                    $absolut_path = $this->config->item('base_path') . $rpath;
                    if ($this->image->save($absolut_path, $template_id . '.jpg')) {
                        $this->update_template(array('preview_url' => $rpath . '/' . $template_id . '.jpg', 'flag' => 1, 'update_time' => date('Y-m-d H:i:s')), $template_id);
                        return $rpath . '/' . $template_id . '.jpg';
                    }
                }
            }
        }

        return false;
    }

    /**
     * 更新触摸模板的预览图
     *
     * @param object $template_id
     * @param object $playlist_id [optional]
     * @param object $media_id [optional]
     * @param object $logo_media_id [optional]
     * @return
     */
    public function update_touch_template_preview_url($template_id, $playlist_id = false, $media_id = false, $logo_media_id = false)
    {
        $template = $this->get_interaction($template_id);
        if ($template) {
            $rpath = $playlist_id ? sprintf($this->config->item('playlist_preview_path'), $template->company_id) : sprintf($this->config->item('tempate_preview_path'), $template->company_id);
            if ($rpath) {
                $pwidth = $this->config->item('template_preview_width');
                $pheight = $this->config->item('template_preview_height');
                $swidth = $template->w;
                $sheight = $template->h;
                if ($template->w < $template->h) {
                    $pwidth = $this->config->item('template_preview_reverse_width');
                }
                $rwidth = $pwidth / $swidth; //宽度比
                $rheight = $pheight / $sheight; //高度比
                $bg_file = false;
                if ($media_id) {
                    $media = $this->material->get_media($media_id);
                    if ($media) {
                        $bg_file = '.' . $media->main_url;
                    }
                }
                $logo_file = false;
                if ($logo_media_id) {
                    $media = $this->material->get_media($logo_media_id);
                    if ($media) {
                        $logo_file = '.' . $media->main_url;
                    }
                }

                $this->image->create($pwidth, $pheight, $bg_file);
                $area_list = $this->get_interaction_area_list($template_id);
                $i = 1;
                if ($area_list) {
                    foreach ($area_list as $area) {
                        if ($area->page_id == 2) {
                            switch ($area->area_type) {
                                case $this->config->item('area_type_staticText'):
                                    $this->image->add_area($area->x * $rwidth, $area->y * $rheight, $area->w * $rwidth, $area->h * $rheight, $area->name, $this->config->item('area_type_staticText_color'));
                                    break;
                                case $this->config->item('area_type_movie'):
                                    $this->image->add_area($area->x * $rwidth, $area->y * $rheight, $area->w * $rwidth, $area->h * $rheight, $area->name, $this->config->item('area_type_movie_color'));
                                    break;
                                case $this->config->item('area_type_image'):
                                    $this->image->add_area($area->x * $rwidth, $area->y * $rheight, $area->w * $rwidth, $area->h * $rheight, $area->name, $this->config->item('area_type_image' . $i . '_color'));
                                    $i++;
                                    break;
                                case $this->config->item('area_type_btn'):
                                    $this->image->add_area($area->x * $rwidth, $area->y * $rheight, $area->w * $rwidth, $area->h * $rheight, $area->name, $this->config->item('area_type_btn_color'));
                                    break;
                                case $this->config->item('area_type_webpage'):
                                    $this->image->add_area($area->x * $rwidth, $area->y * $rheight, $area->w * $rwidth, $area->h * $rheight, $area->name, $this->config->item('area_type_webpage_color'));
                                    break;
                                case $this->config->item('area_type_date'):
                                    $this->image->add_area($area->x * $rwidth, $area->y * $rheight, $area->w * $rwidth, $area->h * $rheight, $area->name, $this->config->item('area_type_date_color'));
                                    break;
                                case $this->config->item('area_type_time'):
                                    $this->image->add_area($area->x * $rwidth, $area->y * $rheight, $area->w * $rwidth, $area->h * $rheight, $area->name, $this->config->item('area_type_time_color'));
                                    break;
                                case $this->config->item('area_type_weather'):
                                    $this->image->add_area($area->x * $rwidth, $area->y * $rheight, $area->w * $rwidth, $area->h * $rheight, $area->name, $this->config->item('area_type_weather_color'));
                                    break;
                                case $this->config->item('area_type_text'):
                                    $this->image->add_area($area->x * $rwidth, $area->y * $rheight, $area->w * $rwidth, $area->h * $rheight, $area->name, $this->config->item('area_type_text_color'));
                                    break;
                                case $this->config->item('area_type_logo'):
                                    $this->image->add_area($area->x * $rwidth, $area->y * $rheight, $area->w * $rwidth, $area->h * $rheight, $area->name, $this->config->item('area_type_logo_color'), $this->config->item('area_border_color'), $logo_file);
                                    break;
                            }
                        }
                    }
                }

                if ($playlist_id) {
                    $absolut_path = $this->config->item('base_path') . $rpath;
                    if ($this->image->save($absolut_path, 't' . $playlist_id . '.jpg')) {
                        return $rpath . '/t' . $playlist_id . '.jpg';
                    }
                } else {
                    $absolut_path = $this->config->item('base_path') . $rpath;
                    if ($this->image->save($absolut_path, 't' . $template_id . '.jpg')) {
                        $this->update_interaction(array('preview_url' => $rpath . '/t' . $template_id . '.jpg', 'save_flag' => 1, 'add_time' => date('Y-m-d H:i:s')), $template_id);
                        return $rpath . '/t' . $template_id . '.jpg';
                    }
                }
            }
        }

        return false;
    }

    /**
     * 拼装图片预览图
     *
     * @param object $company_id
     * @param object $playlist_id
     * @param object $abs_path [optional]
     * @return
     */
    public function get_playlist_template_preview_url($company_id, $playlist_id, $abs_path = false)
    {
        $base = $this->config->item('base_path');
        $rpath = sprintf($this->config->item('playlist_preview_path'), $company_id);
        $playlist_url = $base . $rpath;
        if (!is_dir($playlist_url)) {
            @mkdir($playlist_url, 0777, true);
        }
        if (!is_dir($playlist_url)) {
            return false;
        }
        if ($abs_path) {
            $playlist_url .= '/' . $playlist_id . '.jpg';
            return $playlist_url;
        } else {
            $rpath .= '/' . $playlist_id . '.jpg';
            return $rpath;
        }
    }


    /**
     * 初始化播放列表的Logo信息
     *
     * @param object $playlist_id
     * @param object $template_id
     * @return
     */
    private function initPlaylistAreaLogo($playlist_id, $template_id, $uid)
    {
        $this->db->select("area_id, media_id");
        $this->db->from("cat_area_image_setting ais");
        $this->db->join("cat_template_area ta", "ta.id = ais.area_id");
        $this->db->where("ta.template_id", $template_id);
        $this->db->where("ta.area_type", $this->config->item("area_type_logo"));

        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $row = $query->row();
            if ($row->media_id > 0) {
                $data = array('playlist_id' => $playlist_id, 'area_id' => $row->area_id, 'media_id' => $row->media_id, 'add_user_id' => $uid, 'duration' => '--', 'transmode' => -1, 'transtime' => -1, 'flag' => 1);
                $this->db->insert("cat_playlist_area_media", $data);
            }
        }
    }

    /**
     * 初始化播放列表默认参数信息
     *
     * @param object $playlist_id
     * @param object $template_id
     * @param object $uid
     * @return
     */
    private function initPlaylistAreaDefault($playlist_id, $template_id, $uid)
    {
        $this->initPlaylistAreaBg($playlist_id, $template_id, $uid);
        $this->initPlaylistAreaLogo($playlist_id, $template_id, $uid);
        //date
        $a = $this->get_template_area($template_id, $this->config->item('area_type_date'));
        if ($a) {
            $this->add_area_time_setting(array('playlist_id' => $playlist_id, 'area_id' => $a->id), $uid);
        }
        //weather
        $a = $this->get_template_area($template_id, $this->config->item('area_type_weather'));
        if ($a) {
            $this->add_area_weather_setting(array('playlist_id' => $playlist_id, 'area_id' => $a->id), $uid);
        }
    }


    /**
     * 更新播放列表
     *
     * @param object $array
     * @param object $id
     * @return
     */
    public function update_playlist($array, $id)
    {
        if (empty($array)) {
            return 0;
        }

        $array['update_time'] = date('Y-m-d H:i:s');
        $this->db->where('id', $id);
        if ($this->db->update('cat_playlist', $array)) {
            $this->user_log($this->OP_TYPE_USER, 'update_playlist[' . $id . '] ' . json_encode($array));
            return true;
        } else {
            return false;
        }
    }

    /**
     * 删除某个播放列表，同时删除与当前播放列相关的媒体记录
     * 删除播放列表中的媒体文件
     * 删除与播放列表相关的设置，文本区域设置
     *
     * @param object $ids
     * @return
     */
    public function delete_playlist($ids, $force = true)
    {

        $pls = $this->get_playlist($ids);

        if ($force && $pls && $pls->published) {
            $this->reset_player_least_while_update_campaign($pls);
            $this->delete_planed_records($pls->id, date("Y-m-d"));
        }


        $delete_at = array('published' => 0, 'deleted_at' => date("Y-m-d H:i:s"));
        if ($this->update_playlist($delete_at, $ids)) {
            return true;
        }
        return false;
    }

    public function real_delete_playlist($ids)
    {

        $pls = $this->get_playlist($ids);

        if ($pls && $pls->published) {
            $this->reset_player_least_while_update_campaign($pls);
            $this->delete_planed_records($pls->id, date("Y-m-d"));
        }
        if (is_array($ids)) {
            $this->db->where_in('id', $ids);
        } else {
            $this->db->where('id', $ids);
        }
        return $this->db->delete('cat_playlist');
    }
    /**
     * 获取播放列表下区域中的媒体数
     *
     * @param object $playlist_id
     * @param object $area_id
     * @param object $flag [optional]
     * @return
     */
    public function get_playlist_area_media_count($playlist_id, $area_id, $flag = false)
    {
        $this->db->select('count(*) as total');
        $this->db->where('playlist_id', $playlist_id);
        $this->db->where('area_id', $area_id);
        if ($flag) {
            if (is_array($flag)) {
                $this->db->where_in('flag', $flag);
            } else {
                $this->db->where('flag', $flag);
            }
        } else {
            $this->db->where('flag >', $this->config->item('area_media_flag_temp'));
        }

        $query = $this->db->get('cat_playlist_area_media');

        return $query->row()->total;
    }

    /**
     * 获取当前播放列表区域中最大的位置
     * @param object $playlist_id
     * @param object $area_id
     * @return
     */
    public function get_playlist_area_media_max_position($playlist_id, $area_id)
    {
        $this->db->select('max(position) as position');
        $this->db->from('cat_playlist_area_media p');
        $this->db->where('p.playlist_id', $playlist_id);
        $this->db->where('p.area_id', $area_id);
        $query = $this->db->get();
        if ($query->num_rows()) {
            return $query->row()->position;
        } else {
            return 0;
        }
    }
    /**
     * 交换播放列表中媒体文件的顺序
     *
     * @param object $fid
     * @param object $sid
     * @return
     */
    public function change_playlist_area_media_order($fid, $sid)
    {
        $fpos = 0;
        $spos = 0;

        $this->db->select('position');
        $this->db->from('cat_playlist_area_media p');
        $this->db->where('p.id', $fid);
        $query = $this->db->get();
        if ($query->num_rows()) {
            $fpos = $query->row()->position;
        } else {
            return false;
        }

        $this->db->select('position');
        $this->db->from('cat_playlist_area_media p');
        $this->db->where('p.id', $sid);
        $query = $this->db->get();
        if ($query->num_rows()) {
            $spos = $query->row()->position;
        } else {
            return false;
        }

        $sql = "update cat_playlist_area_media set position = $spos where id = $fid";
        $this->db->query($sql);
        $sql = "update cat_playlist_area_media set position = $fpos where id = $sid";
        $this->db->query($sql);

        return true;
    }

    public function get_playlist_area_media_id($id)
    {
        $this->db->select('media_id');
        $this->db->from('cat_playlist_area_media');
        $this->db->where('id', $id);
        $query = $this->db->get();
        if ($query->num_rows()) {
            return $query->row()->media_id;
        }

        return false;
    }

    /**
     * 获取某个播放列表中某个区域的媒体文件列表信息
     *
     * @param object $playlist_id
     * @param object $area_id
     * @param object $flags [optional]
     * @return
     */
    public function get_playlist_area_media_list($playlist_id, $area_id, $flag = false)
    {
        $result = array();
        $total = 0;
        $data = array();

        $this->db->select('m.*, p.*');
        $this->db->from('cat_playlist_area_media p');
        $this->db->join('cat_media m', 'm.id = p.media_id', 'left');
        $this->db->where('p.playlist_id', $playlist_id);
        $this->db->where('p.area_id', $area_id);

        if ($flag !== false) {
            if (is_array($flag)) {
                $this->db->where_in('flag', $flag);
            } else {
                $this->db->where('flag', $flag);
            }
        }

        $this->db->order_by('p.position', 'asc');



        //$this->db->limit($limit, $offset);
        $query = $this->db->get();
        $total = $query->num_rows();
        if ($total > 0) {
            $data = $query->result();
            $query->free_result();

            //check index order
            if (true /*$data[0]->position == 0 || $data[0]->position > 1*/) {
                //init
                $index = 1;
                for ($i = 0; $i < count($data); $i++) {
                    if ($data[$i]->position != $index) {
                        $data[$i]->position = $index;
                        $sql = "update cat_playlist_area_media set position = " . $index . " where id = " . $data[$i]->id;
                        $this->db->query($sql);
                    }
                    $index++;
                }
            }
        }

        return array('total' => $total, 'data' => $data);
    }


    /**
     * 获取某个播放列表中某个区域的媒体文件列表信息
     *
     * @param object $playlist_id
     * @param object $area_id
     * @param object $flags [optional]
     * @return
     */
    public function get_playlist_area_media_list_noexclude($playlist_id, $area_id, $flag = false, $check_date = false)
    {
        $result = array();
        $total = 0;
        $data = array();

        $this->db->select('p.id,p.media_id,m.name,m.play_time,m.file_size,m.signature,m.date_flag,m.start_date,m.end_date,m.full_path, p.transmode,p.reload,cp.name as playlist_name,cp.start_date as pls_start_date, cp.end_date as pls_end_date');
        $this->db->from('cat_playlist_area_media p');
        $this->db->join('cat_media m', 'm.id = p.media_id', 'left');
        $this->db->join('cat_playlist cp', 'cp.id = p.playlist_id', 'left');

        if (is_array($playlist_id)) {
            $this->db->where_in('p.playlist_id', $playlist_id);
        } else {
            $this->db->where('p.playlist_id', $playlist_id);
        }
        $this->db->where('p.area_id', $area_id);
        $this->db->where('p.status', 0);
        if ($check_date) {
            $today = date('Y-m-d');
            $this->db->group_start();
            $this->db->group_start();
            $this->db->where('m.date_flag', 1);
            $this->db->where('m.end_date>=', $check_date);
            $this->db->where('m.start_date<=', $check_date);
            $this->db->group_end();
            $this->db->or_where('m.date_flag', 0);
            $this->db->group_end();
        }
        /*
        if ($flag !== false) {
            if (is_array($flag)) {
                $this->db->where_in('p.flag', $flag);
            } else {
                $this->db->where('p.flag', $flag);
            }
        } else {
            $this->db->where('p.flag >', $this->config->item('area_media_flag_temp'));
        }
        */
        $this->db->order_by('p.position', 'asc');


        //$this->db->limit($limit, $offset);
        $query = $this->db->get();

        $total = $query->num_rows();
        if ($total > 0) {
            $data = $query->result();
            $query->free_result();
        }

        return array('total' => $total, 'data' => $data);
    }

    /**
     * 获取某个播放列表中某个区域的媒体文件列表信息
     *
     * @param object $playlist_id
     * @param object $area_id
     * @param object $flags [optional]
     * @return
     */
    public function get_playlist_area_media_array($playlist_id, $area_id)
    {
        $data = array();

        $this->db->select('m.*, p.*');
        $this->db->from('cat_playlist_area_media p');
        $this->db->join('cat_media m', 'm.id = p.media_id', 'left');
        $this->db->where('p.playlist_id', $playlist_id);
        $this->db->where('p.area_id', $area_id);

        $this->db->where('flag >', $this->config->item('area_media_flag_temp'));

        $this->db->order_by('p.position', 'asc');


        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $data = $query->result_array();
            $query->free_result();
            return $data;
        }

        return false;
    }


    /**
     * 获取播放列表中最后一个RSS，这个可能是发布了的，也可能是未发布的临时的
     *
     * @param object $playlist_id
     * @param object $area_id
     * @return
     */
    public function get_playlist_area_last_rss($playlist_id, $area_id)
    {
        $this->db->select('m.name, m.type, m.descr,m.url,p.*');
        $this->db->from('cat_playlist_area_media p');
        $this->db->join('cat_rss m', 'm.id = p.media_id', 'left');
        $this->db->where('p.playlist_id', $playlist_id);
        $this->db->where('p.area_id', $area_id);
        $this->db->order_by('p.flag', 'asc');
        $this->db->limit(1);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            //large than 0
            return $query->row();
        } else {
            return false;
        }
    }

    /**
     * 获取当前播放列表下的RSS列表
     *
     * @param object $playlist_id
     * @param object $area_id
     * @param object $flag [optional]
     * @return
     */
    public function get_playlist_area_rss_list($playlist_id, $area_id, $flag = false)
    {
        $data = array();

        $this->db->select('m.name, m.descr,m.url,p.*');
        $this->db->from('cat_playlist_area_media p');
        $this->db->join('cat_rss m', 'm.id = p.media_id', 'left');
        $this->db->where('p.playlist_id', $playlist_id);
        $this->db->where('p.area_id', $area_id);
        if ($flag !== false) {
            if (is_array($flag)) {
                $this->db->where_in('flag', $flag);
            } else {
                $this->db->where('flag', $flag);
            }
        } else {
            $this->db->where('flag >', $this->config->item('area_media_flag_temp'));
        }
        $this->db->order_by('p.flag', 'asc');
        //echo $this->db->get_sql();
        //$this->db->limit($limit, $offset);
        $query = $this->db->get();
        $total = $query->num_rows();
        if ($total > 0) {
            $data = $query->result();
            $query->free_result();
        }
        return array('total' => $total, 'data' => $data);
    }


    /**
     * 删除当前播放列表中临时的媒体文件，每次刷新时执行
     * @param object $playlist_id
     * @param object $flag [optional]
     * @return
     */
    public function delete_playlist_area_media_temp($playlist_id, $flag = 0)
    {
        $this->db->where('playlist_id', $playlist_id);
        //$this->db->where('flag', $this->config->item('area_media_flag_temp'));
        $this->db->where('flag', $flag);
        return $this->db->delete('cat_playlist_area_media');
    }

    /**
     * 获取某个播放列表和区域的设置信息
     *
     * @param object $playlist_id
     * @param object $area_id
     * @return
     */
    public function get_playlist_area_text_setting($playlist_id, $area_id)
    {
        $this->db->where('playlist_id', $playlist_id);
        $this->db->where('area_id', $area_id);

        $query = $this->db->get('cat_area_text_setting');
        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
    }

    /**
     * 获取某个播放列表中静态文本区域的设置信息
     *
     * @param object $playlist_id
     * @param object $area_id
     * @return
     */
    public function get_playlist_area_static_text_setting($playlist_id, $area_id)
    {
        $this->db->where('playlist_id', $playlist_id);
        $this->db->where('area_id', $area_id);

        $query = $this->db->get('cat_area_static_text_setting');
        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
    }

    /**
     * 获取当前配置下的数据
     *
     * @param object $id
     * @return
     */
    public function get_area_text_setting($id)
    {
        $this->db->where('id', $id);
        $query = $this->db->get('cat_area_text_setting');
        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
    }

    /**
     * 获取当前静态文本配置下的数据
     *
     * @param object $id
     * @return
     */
    public function get_area_static_text_setting($id)
    {
        $this->db->where('id', $id);
        $query = $this->db->get('cat_area_static_text_setting');
        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
    }


    //get_area_webpage_setting

    /**
     * 获取某个播放列表中某个区域的媒体文件列表信息
     *
     * @param object $playlist_id
     * @param object $area_id
     * @param object $flags [optional]
     * @return
     */
    public function get_area_webpag_setting($playlist_id, $area_id, $flag = false)
    {
        $result = array();
        $total = 0;
        $data = array();
        $this->db->select('p.*');
        $this->db->from('cat_playlist_area_media p');
        $this->db->where('p.playlist_id', $playlist_id);
        $this->db->where('p.area_id', $area_id);
        if ($flag !== false) {
            if (is_array($flag)) {
                $this->db->where_in('flag', $flag);
            } else {
                $this->db->where('flag', $flag);
            }
        } else {
            $this->db->where('flag >', $this->config->item('area_media_flag_temp'));
        }
        $this->db->order_by('p.position', 'asc');
        //$this->db->limit($limit, $offset);
        $query = $this->db->get();
        $total = $query->num_rows();
        if ($total > 0) {
            $data = $query->result();
            $query->free_result();

            //check index order
            if (true /*$data[0]->position == 0 || $data[0]->position > 1*/) {
                //init
                $index = 1;
                for ($i = 0; $i < count($data); $i++) {
                    if ($data[$i]->position != $index) {
                        $data[$i]->position = $index;
                        $sql = "update cat_playlist_area_media set position = " . $index . " where id = " . $data[$i]->id;
                        $this->db->query($sql);
                    }
                    $index++;
                }
            }
        }

        return array('total' => $total, 'data' => $data);
    }

    /**
     * 获取某个播放列表下的媒体文件
     *
     * @param object $id
     * @return
     */
    public function get_playlist_area_media($id)
    {
        $this->db->where('id', $id);

        $query = $this->db->get('cat_playlist_area_media');
        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
    }

    /**
     * 播放列表中的媒体文件移动
     *
     * @param object $id
     * @param object $index
     * @return
     */
    public function playlist_area_media_move_to($id, $index)
    {
        $m = $this->get_playlist_area_media($id);
        if ($m) {
            //down
            if ($m->position > $index) {
                $sql = "update cat_playlist_area_media set position = position + 1 where playlist_id = $m->playlist_id and area_id = $m->area_id and position between $index and $m->position - 1";
                $this->db->query($sql);
                $sql = "update cat_playlist_area_media set position = $index where id = $id";
                $this->db->query($sql);
            } elseif ($m->position < $index) {
                $sql = "update cat_playlist_area_media set position = position - 1 where playlist_id = $m->playlist_id and area_id = $m->area_id and position between $index and $m->position + 1";
                $this->db->query($sql);
                $sql = "update cat_playlist_area_media set position = $index where id = $id";
                $this->db->query($sql);
            }

            return true;
        } else {
            return false;
        }
    }

    /**
     * 更新播放列表中视频区域的数据选择
     *
     * @param object $id
     * @param object $rotate
     * @return
     */
    public function playlist_area_media_rotate($id, $rotate)
    {
        $this->db->where('id', $id);
        return $this->db->update('cat_playlist_area_media', array('rotate' => $rotate));
    }



    /**
     * 添加某个日程上的播放列表
     *
     * @param object $id
     * @param object $playlist_ids
     * @return
     */
    public function add_schedule_interaction($id, $playlist_ids)
    {
        if ($playlist_ids) {
            $ids = array();
            if (is_array($playlist_ids)) {
                foreach ($playlist_ids as $pid) {
                    if ($this->db->insert('cat_schedule_interaction', array('schedule_id' => $id, 'interaction_playlist_id' => $pid))) {
                        $ids[] = $this->db->insert_id();
                    }
                }
            } else {
                if ($this->db->insert('cat_schedule_interaction', array('schedule_id' => $id, 'interaction_playlist_id' => $playlist_ids))) {
                    $ids = $this->db->insert_id();
                } else {
                    $ids = false;
                }
            }

            return $ids;
        } else {
            return false;
        }
    }

    public function get_schedule_interaction($id)
    {
        $this->db->where('id', $id);
        $query = $this->db->get('cat_schedule_interaction');
        if ($query->num_rows() > 0) {
            $data = $query->row();
            return $data;
        } else {
            return false;
        }
    }

    public function schedule_interaction_move_to($id, $index)
    {
        $m = $this->get_schedule_interaction($id);
        if ($m) {
            //down
            if ($m->position > $index) {
                $sql = "update cat_schedule_interaction set position = position + 1 where schedule_id = $m->schedule_id and position between $index and $m->position - 1";
                $this->db->query($sql);
                $sql = "update cat_schedule_interaction set position = $index where id = $id";
                $this->db->query($sql);
            } elseif ($m->position < $index) {
                $sql = "update cat_schedule_interaction set position = position - 1 where schedule_id = $m->schedule_id and position between $index and $m->position + 1";
                $this->db->query($sql);
                $sql = "update cat_schedule_interaction set position = $index where id = $id";
                $this->db->query($sql);
            }

            return true;
        } else {
            return false;
        }
    }

    /**
     * 更新日程中的播放列表属性的flag信息
     *
     * @param object $data
     * @param object $schedule_id
     * @param object $playlist_id
     * @return
     */
    public function update_schedule_interaction($data, $schedule_id, $playlist_id)
    {
        $this->db->where('schedule_id', $schedule_id);
        $this->db->where('interaction_playlist_id', $playlist_id);

        return $this->db->update('cat_schedule_interaction', $data);
    }

    /**
     * 获取某个日程下的播放列表
     *
     * @param object $id
     * @return
     */
    public function get_schedule_interactions($id)
    {
        $this->db->select('p.name, p.descr, sp.*, u.name as user');
        $this->db->from('cat_interaction_playlist p');
        $this->db->join('cat_schedule_interaction sp', 'p.id = sp.interaction_playlist_id');
        $this->db->join('cat_user u', 'u.id = p.add_user_id');
        $this->db->where('sp.schedule_id', $id);
        $this->db->order_by('sp.position', 'asc');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $result = $query->result();
            if ($result) {
                $idx = 1;
                foreach ($result as $row) {
                    if ($row->position != $idx) {
                        $sql = "update cat_schedule_interaction set position=$idx where id = $row->id";
                        $this->db->query($sql);
                    }
                    $idx++;
                }
            }
            return $result;
        } else {
            return false;
        }
    }

    /**
     * 删除某个日程下的播放列表
     * @param object $id
     * @param object $playlist_id
     * @return
     */
    public function delete_schedule_interaction($id, $playlist_id = false)
    {
        if ($id > 0) {
            $this->db->where('id', $id);
        }

        if ($playlist_id !== false) {
            if (is_array($playlist_id)) {
                $this->db->where_in('interaction_playlist_id', $playlist_id);
            } else {
                $this->db->where('interaction_playlist_id', $playlist_id);
            }
        }

        return $this->db->delete('cat_schedule_interaction');
    }

    /**
     * 添加日程上的组
     * @param object $id
     * @param object $group_id
     * @return
     */
    public function add_schedule_group($id, $group_id)
    {
        if ($group_id) {
            $ids = array();
            if (is_array($group_id)) {
                foreach ($group_id as $gid) {
                    if ($this->db->insert('cat_schedule_group', array('schedule_id' => $id, 'group_id' => $gid))) {
                        $ids[] = $this->db->insert_id();
                    }
                }
            } else {
                if ($this->db->insert('cat_schedule_group', array('schedule_id' => $id, 'group_id' => $group_id))) {
                    $ids = $this->db->insert_id();
                } else {
                    $ids = false;
                }
            }

            return $ids;
        } else {
            return false;
        }
    }

    /**
     * 获取某个日程下的组
     *
     * @param object $id
     * @return
     */
    public function get_schedule_group($id)
    {
        $this->db->select('g.*');
        $this->db->from('cat_group g');
        $this->db->join('cat_schedule_group sg', 'g.id = sg.group_id');
        $this->db->where('sg.schedule_id', $id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }

    public function is_schedule_group_empty($schedule_id)
    {
        $sql = "select count(*) as total from cat_schedule_group where schedule_id = $schedule_id";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            if ($query->row()->total > 0) {
                return false;
            }
        }

        return true;
    }

    public function is_schedule_playlist_empty($schedule_id)
    {
        $sql = "select count(*) as total from cat_schedule_playlist where schedule_id = $schedule_id";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            if ($query->row()->total > 0) {
                return false;
            }
        }

        return true;
    }
    /**
     * 检验一个日程中的多个播放列表是否存在不同的区域
     *
     * @param object $schedule_id
     * @return
     */
    public function check_schedule_playlist_template($schedule_id)
    {
        if ($schedule_id) {
            $sql = "select t.w as tw, t.h as th, a.w as aw, a.h as ah,a.area_type from cat_schedule_playlist sp,cat_template t, cat_template_area a, cat_playlist p where sp.schedule_id=$schedule_id and sp.playlist_id=p.id and p.template_id = t.id and a.template_id=t.id";
            $query = $this->db->query($sql);
            if ($query->num_rows() > 0) {
                $result = $query->result();
                $h = false;
                $v = false;
                foreach ($result as $r) {
                    if ($r->area_type == $this->config->item('area_type_movie') || $r->area_type == $this->config->item('area_type_image')) {
                        if ($r->tw > $r->th) {
                            $h = true;
                        } else {
                            $v = true;
                        }
                        //print_r($r);
                    }
                }
                return $h && $v;
            }
        }
        return false;
    }

    /**
     * 判断是否存在其他日程在当前日程所在的组中
     *
     * @param object $schedule_id
     * @return 不存在返回FALSE，存在返回日程和组信息
     */
    public function check_schedule_group($schedule_id)
    {
        $sql = "select sg.schedule_id, sg.group_id, t.name as schedule_name,t.start_date,t.end_date,t.start_time,t.end_time,t.week  from cat_schedule_group sg, cat_schedule t  where sg.schedule_id = t.id and t.status = 1 and sg.schedule_id <> $schedule_id  and sg.group_id in (select group_id from cat_schedule_group where  schedule_id = $schedule_id)";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $row = $query->row();
            $this->db->select('name');
            $this->db->where('id', $row->group_id);
            $query = $this->db->get('cat_group');
            if ($query->num_rows() > 0) {
                $row->group_name = $query->row()->name;
                return $row;
            }
        }

        return false;
    }

    /**
     * 获取当前时间段的冲突的其它schedule信息
     *
     * @param object $schedule_id
     * @param object $start_date
     * @param object $end_date
     * @param object $week
     * @param object $start_time
     * @param object $end_time
     * @return
     */
    public function get_conflict_schedule_info($schedule_id, $start_date, $end_date, $week, $start_time, $end_time)
    {
        //$sql = "select distinct(s.id) as id, s.name as schedule_name,s.start_date,s.end_date,s.start_time,s.end_time,s.week from cat_schedule s left join cat_schedule_group sg on s.id = sg.schedule_id where s.status = 1 and s.id <> $schedule_id and sg.group_id in (select group_id from cat_schedule_group where schedule_id = $schedule_id)";
        $sql = "select distinct(s.id) as id, s.name as schedule_name,s.start_date,s.end_date,s.start_time,s.end_time,s.week, u.auth from cat_schedule s left join cat_schedule_group sg on s.id = sg.schedule_id left join cat_user u on u.id = s.add_user_id where s.status = 1 and s.id <> $schedule_id and sg.group_id in (select group_id from cat_schedule_group where schedule_id = $schedule_id)";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $sl = $query->result();
            $result = array();
            $s = new StdClass;
            $s->start_date = $start_date;
            $s->end_date = $end_date;
            $s->week = $week;
            $s->start_time = $start_time;
            $s->end_time = $end_time;

            for ($i = 0; $i < count($sl); $i++) {
                if ($this->is_schedule_conflict($s, $sl[$i])) {
                    $result[] = $sl[$i];
                }
            }
            //加载冲突组信息
            if (count($result)) {
                foreach ($result as $s) {
                    $sql = "select g.id, g.name from cat_group g left join cat_schedule_group sg on g.id = sg.group_id where sg.schedule_id <> $schedule_id and sg.group_id in (select group_id from cat_schedule_group where schedule_id = $schedule_id)";
                    $query = $this->db->query($sql);
                    if ($query->num_rows() > 0) {
                        $s->groups = $query->result();
                    } else {
                        $s->groups = array();
                    }
                }

                return $result;
            }
        }

        return false;
    }

    /**
     * 判断两个日程在时间上是否冲突
     * @param object $s1
     * @param object $s2
     * @return
     */
    private function is_schedule_conflict($s1, $s2)
    {
        //2014-10-13 10:14:23  liu add (结束时间默认改成00:00)
        if ($s2->end_time == '00:00') {
            $s2->end_time = '23:59';
        }
        if ($s1->end_time == '00:00') {
            $s1->end_time = '23:59';
        }
        if ($this->is_date_conflict($s1->start_date, $s1->end_date, $s2->start_date, $s2->end_date)) {
            if ($this->is_week_conflict($s1->week, $s2->week)) {
                return $this->is_time_conflict($s1->start_time, $s1->end_time, $s2->start_time, $s2->end_time);
            }
        }

        return false;
    }

    private function is_date_conflict($ds1, $de1, $ds2, $de2)
    {
        if ($ds1 <= $ds2) {
            if ($de1 >= $ds2) {
                return true;
            }
        } elseif ($ds2 <= $ds1) {
            if ($de2 >= $ds1) {
                return true;
            }
        }

        return false;
    }

    private function is_week_conflict($w1, $w2)
    {
        if ($w1 == $w2) {
            return true;
        }

        for ($i = 0; $i < 7; $i++) {
            if (is_week($w1, $i) && is_week($w2, $i)) {
                return true;
            }
        }
        return false;
    }

    private function is_time_conflict($ts1, $te1, $ts2, $te2)
    {
        if ($ts1 <= $ts2) {
            if ($te1 > $ts2) {
                return true;
            }
        } elseif ($ts2 <= $ts1) {
            if ($te2 > $ts1) {
                return true;
            }
        }

        return false;
    }


    public function is_one_touch_playlist($schedule_id)
    {
        $sql = "select s.id as id, s.name as schedule_name, u.auth from cat_schedule s left join cat_schedule_group sg on s.id = sg.schedule_id left join cat_user u on u.id = s.add_user_id where s.status = 1 and s.id <> $schedule_id and sg.group_id in (select group_id from cat_schedule_group where schedule_id = $schedule_id)";
        $query = $this->db->query($sql);

        $touch_one = false;
        $sql_one = "select id from cat_schedule_interaction where schedule_id = $schedule_id";
        $query_one = $this->db->query($sql_one);
        if ($query_one->num_rows() > 0) {
            $touch_one = true;
        }

        if ($query->num_rows() > 0) {
            $array = $query->result();
            foreach ($array as $arr) {
                $sql_two = "select id from cat_schedule_interaction where schedule_id = $arr->id";
                $query_two = $this->db->query($sql_two);
                if ($query_two->num_rows() > 0 && $touch_one) {
                    return true;
                }
            }
        } else {
            return false;
        }
    }

    /**
     * 获取某个Player的日程信息
     *
     * @param object $gid
     * @return
     */
    public function get_schedule_by_player($gid)
    {
        $this->db->select('t.*');
        $this->db->from('cat_schedule t');
        $this->db->join('cat_schedule_group sg', 't.id = sg.schedule_id');
        $this->db->where('sg.group_id', $gid);
        $this->db->where('t.status', 1); //发布状态
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }


    /**
     * 获取某个组下发布的日程信息
     *
     * @param object $gid
     * @return
     */
    public function get_publish_schedule_by_group($gid)
    {
        $this->db->select('t.*');
        $this->db->from('cat_schedule t');
        $this->db->join('cat_schedule_group sg', 't.id = sg.schedule_id');
        $this->db->where('sg.group_id', $gid);
        $this->db->where('t.status', 1); //发布状态
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }

    /**
     * 获取日程安排下的播放列表
     *
     * @param object $schedule_id
     * @return
     */
    public function get_playlist_by_schedule($schedule_id)
    {
        $this->db->select('p.id, p.name,p.file_size,p.signature, p.template_id, sp.position');
        $this->db->from('cat_playlist p');
        $this->db->join('cat_schedule_playlist sp', 'p.id = sp.playlist_id', 'left');
        $this->db->where('sp.schedule_id', $schedule_id);
        $this->db->order_by('position', 'asc');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }

    /**
     * 获取日程安排下的interaction列表
     *
     * @param object $schedule_id
     * @return
     */
    public function get_interaction_by_schedule($schedule_id)
    {
        $this->db->select('p.id, p.name,p.file_size,p.signature, p.interaction_id, sp.position');
        $this->db->from('cat_interaction_playlist p');
        $this->db->join('cat_schedule_interaction sp', 'p.id = sp.interaction_playlist_id', 'left');
        $this->db->where('sp.schedule_id', $schedule_id);
        $this->db->order_by('position', 'asc');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }

    /**
     * 删除日程下的分组
     *
     * @param object $id
     * @param object $group_id
     * @return
     */
    public function delete_schedule_group($id, $group_id)
    {
        $this->db->where('schedule_id', $id);

        if (is_array($group_id)) {
            /*foreach ($group_id as $gid) {
             $this->db->delete('cat_schedule_group', array('schedule_id'=>$id, 'group_id'=>$gid));
         }*/
            $this->db->where_in('group_id', $group_id);
        } else {
            //$this->db->insert('cat_schedule_group', array('schedule_id'=>$id, 'group_id'=>$group_id));
            $this->db->where('group_id', $group_id);
        }

        return $this->db->delete('cat_schedule_group');
    }
    /**
     * 删除与当前日程冲突的其它日程拥有的组,并执行将状态由发布改为非发布状态
     *
     * @param object $schedule_id
     * @return
     */
    public function delete_schedule_conflict_group($schedule_id, $start_date, $end_date, $week, $start_time, $end_time)
    {

        /*$sql = "select schedule_id, group_id  from cat_schedule_group sg, cat_schedule t  where sg.schedule_id = t.id and t.status = 1 and sg.schedule_id <> $schedule_id  and sg.group_id in (select group_id from cat_schedule_group where  schedule_id = $schedule_id)";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $result = $query->result();
            foreach ($result as $row) {

                $sql = "delete from cat_schedule_group where schedule_id = ".$row->schedule_id." and group_id = ".$row->group_id;
                $this->db->query($sql);

                $sql = "update cat_schedule set status=0 where id = ".$row->schedule_id;
                $this->db->query($sql);
            }
        } else {
            return FALSE;
        }*/

        $sl = $this->get_conflict_schedule_info($schedule_id, $start_date, $end_date, $week, $start_time, $end_time);
        foreach ($sl as $s) {
            foreach ($s->groups as $g) {
                $sql = "delete from cat_schedule_group where schedule_id = " . $s->id . " and group_id = " . $g->id;
                $this->db->query($sql);
                $sql = "update cat_schedule set status=" . $this->config->item('schedule.default') . " where id=" . $s->id;
                $this->db->query($sql);
            }
            //$sql = "update cat_schedule set status=0 where id = ".$s->id;
            //$this->db->query($sql);
        }
    }


    //*******************************************
    //Schedule end
    //*******************************************
    //判断该组有无SCH
    public function get_sch_gid($gid)
    {
        $sql = "select schg.group_id as gid from cat_schedule sch, cat_schedule_group schg where sch.id=schg.schedule_id and schg.group_id=$gid and sch.status=1";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }
    //获取播放列表中的媒体url
    public function get_playlist_area_media_by_playlistId($id)
    {
        $sqla = "select publish_url,count(publish_url) as num from cat_playlist_area_media group by publish_url";
        $querya = $this->db->query($sqla);
        $result = $querya->result();
        foreach ($result as $re) {
            if ($re->num > 1) {
                $publish_url[] = $re->publish_url;
            }
        }

        $this->db->select('publish_url');
        $this->db->from('cat_playlist_area_media');
        $this->db->where('playlist_id', $id);
        if (isset($publish_url)) {
            $this->db->where_not_in('publish_url', $publish_url);
        }
        $query = $this->db->get();
        $data = array();
        if ($query->num_rows()) {
            $data = $query->result();
            $query->free_result();
            return $data;
        }
        return false;
        /*
        $this->db->select('publish_url');
        $this->db->from('cat_playlist_area_media');
        $this->db->where('playlist_id', $id);
        $query = $this->db->get();
        $data = array();
        if($query->num_rows()){
            $data = $query->result();
            $query->free_result();
            return $data;
        }
        return FALSE;
        */
    }

    public function update_all_area_media($array, $id, $playlist_id)
    {
        if (empty($array) || $id == 0) {
            return false;
        }
        $this->db->where('area_id', $id);
        $this->db->where('playlist_id', $playlist_id);
        return $this->db->update('cat_playlist_area_media', $array);
    }

    public function edit_startTime($array, $id)
    {
        $this->db->where('id', $id);
        return $this->db->update('cat_playlist_area_media', $array);
    }

    public function edit_endTime($array, $id)
    {
        $this->db->where('id', $id);
        return $this->db->update('cat_playlist_area_media', $array);
    }
    public function interaction_edit_endTime($array, $id)
    {
        $this->db->where('id', $id);
        return $this->db->update('cat_interaction_playlist_area_media', $array);
    }

    public function edit_status($array, $id)
    {
        $sql = 'select status from cat_playlist_area_media where id=' . $id;
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $result = $query->result();
            foreach ($result as $arr) {
                //return $arr->status;
                if ($arr->status == 1) {
                    $status = 0;
                } else {
                    $status = 1;
                }
                $this->db->where('id', $id);
                return $this->db->update('cat_playlist_area_media', array('status' => $status));
            }
        }
    }

    public function edit_reload($array, $id)
    {
        $sql = 'select reload from cat_playlist_area_media where id=' . $id;
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $result = $query->result();
            foreach ($result as $arr) {
                //return $arr->status;
                if ($arr->reload == 1) {
                    $reload = 0;
                } else {
                    $reload = 1;
                }
                $this->db->where('id', $id);
                return $this->db->update('cat_playlist_area_media', array('reload' => $reload));
            }
        }
    }

    public function edit_date($array, $id)
    {
        $this->db->where('id', $id);
        return $this->db->update('cat_playlist_area_media', $array);
    }

    public function edit_areaStatus($array, $playlistId, $areaId)
    {
        $this->db->where('playlist_id', $playlistId);
        $this->db->where('area_id', $areaId);
        return $this->db->update('cat_playlist_area_media', $array);
    }

    /**
     * 获取 互动应用 列表
     */
    public function get_interaction_list($cid, $offset = 0, $limit = 10, $order_item = 'id', $order = 'desc')
    {
        $this->db->select('count(id) as total');
        $this->db->from('cat_interaction');
        $this->db->where('company_id', $cid);
        $query = $this->db->get();
        $total = $query->row()->total;

        $this->db->select('i.*, u.name as user');
        $this->db->from('cat_interaction i');
        $this->db->join('cat_user u', 'i.add_user_id = u.id');
        $this->db->where('i.company_id', $cid);

        $this->db->order_by($order_item, $order);
        $this->db->limit($limit, $offset);
        $query = $this->db->get();
        $array = array();
        if ($query->num_rows()) {
            $array = $query->result();
            $query->free_result();
        }
        return array('total' => $total, 'data' => $array);
    }

    /**
     * 添加 互动应用
     */
    public function add_interaction($array, $cid = false, $uid = false)
    {
        if ($cid) {
            $array['company_id'] = $cid;
        }
        if ($uid) {
            $array['add_user_id'] = $uid;
        }
        if ($this->db->insert('cat_interaction', $array)) {
            $id = $this->db->insert_id();
            $this->user_log($this->OP_TYPE_USER, 'add_template[' . $id . '] ' . json_encode($array));
            return $id;
        } else {
            return false;
        }
    }

    /**
     * 修改 互动应用
     */
    public function update_interaction($array, $id)
    {
        if (empty($array)) {
            return 0;
        }

        $this->db->where('id', $id);
        if ($this->db->update('cat_interaction', $array)) {
            $this->user_log($this->OP_TYPE_USER, 'add_template[' . $id . '] ' . json_encode($array));
            return $id;
        } else {
            return false;
        }
    }

    /**
     * 获得单个互动应用信息
     */
    public function get_interaction($id)
    {
        $this->db->from('cat_interaction');
        $this->db->where('id', $id);
        $query = $this->db->get();
        if ($query->num_rows()) {
            return $query->row();
        } else {
            return false;
        }
    }

    /**
     * 根据模板名称查询 此名称的模板是否存在
     * @param object $id
     * @param object $cid
     * @param object $name
     * @return
     */
    public function get_interaction_by_name($id, $cid, $name)
    {
        if ($id > 0) {
            $sql = "select id from cat_interaction where id != $id and company_id = '$cid' and name = '$name'";
        } else {
            $sql = "select id from cat_interaction where company_id = '$cid' and name = '$name'";
        }
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
    }

    /**
     * 判断名字重复
     */
    public function get_repeated_interaction($name, $id, $cid)
    {
        /*
        $this->db->from('cat_interaction');
        $this->db->where('id', $id);
        $query = $this->db->get();
        if($query->num_rows()){
            return $query->row();
        }else{
            return FALSE;
        }*/
        //$sql = "select id from cat_player where id != $id and company_id = '$cid' and name = '$name'";
        //$sql = "select id from cat_player where company_id = '$cid' and name = '$name'";

        $this->db->from('cat_interaction');
        $this->db->where('name', $name);
        $this->db->where('company_id', $cid);
        if ($id > 0) {
            $this->db->where('id !=', $id);
        }

        $query = $this->db->get();
        if ($query->num_rows()) {
            return $query->row();
        } else {
            return false;
        }
    }

    public function add_static_bg_area($array)
    {
        if ($this->db->insert('cat_playlist_area_media', $array)) {
            $id = $this->db->insert_id();
            return $id;
        } else {
            return false;
        }
    }

    public function get_static_bg_area($pid, $area_id, $media_id)
    {
        $this->db->from('cat_playlist_area_media');
        $this->db->where('playlist_id', $pid);
        $this->db->where('area_id', $area_id);
        $this->db->where('media_id', $media_id);
        $query = $this->db->get();
        $array = array();
        if ($query->num_rows()) {
            $array = $query->result();
            $query->free_result();
            return $array;
        } else {
            return false;
        }
    }

    /**
     * 根据 Playlist_id 更新日程 最后更新时间
     */
    public function update_schedule_by_pid($pid)
    {
        $this->db->select('sch.id');
        $this->db->from('cat_schedule sch');
        $this->db->join('cat_schedule_playlist sp', 'sch.id = sp.schedule_id');
        $this->db->where('sp.playlist_id', $pid);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $result = $query->result();
            if ($result) {
                foreach ($result as $row) {
                    $sql = "update cat_schedule set publish_time='" . date('Y-m-d H:i:s') . "' where id=" . $row->id;
                    $this->db->query($sql);
                }
            }
            return $result;
        } else {
            return false;
        }
    }

    /**
     * 根据 Playlist_id 更新日程 最后更新时间
     */
    public function update_interaction_schedule_by_pid($pid)
    {
        $this->db->select('sch.id');
        $this->db->from('cat_schedule sch');
        $this->db->join('cat_schedule_interaction sp', 'sch.id = sp.schedule_id');
        $this->db->where('sp.interaction_playlist_id', $pid);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $result = $query->result();
            if ($result) {
                foreach ($result as $row) {
                    $sql = "update cat_schedule set publish_time='" . date('Y-m-d H:i:s') . "' where id=" . $row->id;
                    $this->db->query($sql);
                }
            }
            return $result;
        } else {
            return false;
        }
    }

    public function add_cat_fid()
    {
        $array = array('id' => null);
        if ($this->db->insert('cat_fid', $array)) {
            $id = $this->db->insert_id();
            return $id;
        } else {
            return rand(100, 999);
        }
    }

    public function get_media_by_resource_name($name)
    {
        $sql = "select id from cat_media where full_path like '%" . $name . "%'";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            return true;
        } else {
            $sql2 = "select id from cat_playlist_area_media where publish_url like '%" . $name . "%' or preview_url like '%" . $name . "%'";
            $query2 = $this->db->query($sql2);
            if ($query2->num_rows() > 0) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function get_interactionpls_list($cid, $condition = false, $offset = 0, $limit = -1, $order_item = 'id', $order = 'desc')
    {
        /*
        $this->db->select('count(*) as total');
        $this->db->where('company_id', $cid);
        if ($condition) {
            foreach ($condition as $key => $value) {
                if ($key == 'date') {
                    $this->db->where('add_time >=', $value);
                    $this->db->where('add_time <=', $value . ' 23:59:59');
                } elseif ($key == 'name') {
                    $this->db->like($key, $value);
                } else {
                    $this->db->where_in($key, $value);
                }
            }
        }
        $query = $this->db->get('cat_interaction_playlist');
        $total = $query->row()->total;
        $array = array();
        if ($total > 0) {
            $this->db->select('p.*, u.name as user, i.name as interaction_name');
            $this->db->from('cat_interaction_playlist p');
            $this->db->join('cat_user u', 'p.add_user_id = u.id');
            $this->db->join('cat_interaction i', 'p.interaction_id = i.id');
            $this->db->where('p.company_id', $cid);
            if ($condition) {
                if ($condition) {
                    foreach ($condition as $key => $value) {
                        if ($key == 'date') {
                            $this->db->where('p.add_time >=', $value);
                            $this->db->where('p.add_time <=', $value . ' 23:59:59');
                        } elseif ($key == 'name') {
                            $this->db->like('p.' . $key, $value);
                        } else {
                            $this->db->where_in('p.' . $key, $value);
                        }
                    }
                }
            }

            $this->db->order_by('p.' . $order_item, $order);
            //$this->db->order_by('add_time', $order);
            if ($limit > 0) {
                $this->db->limit($limit, $offset);
            }

            $query = $this->db->get();
            if ($query->num_rows() > 0) {
                $array = $query->result();

                $query->free_result();
            }
        }

        return array('total' => $total, 'data' => $array);
        */
    }

    /**
     * 获取全部的 互动应用列表
     */
    public function get_all_interaction_list($cid)
    {
        $this->db->from('cat_interaction');
        $this->db->where('company_id', $cid);
        $this->db->where('save_flag', 1);
        $this->db->order_by('id', 'desc');
        $query = $this->db->get();
        $array = array();

        if ($query->num_rows()) {
            $array = $query->result();
            $query->free_result();
        }

        return $array;
    }

    /**
     * 添加 互动应用列表
     */
    public function add_interactionpls($array, $cid, $uid)
    {
        if (empty($array)) {
            return 0;
        }

        $array['company_id'] = $cid;
        $array['add_user_id'] = $uid;
        $array['update_time'] = date('Y-m-d H:i:s');

        if ($this->db->insert('cat_interaction_playlist', $array)) {
            $id = $this->db->insert_id();
            $this->user_log($this->OP_TYPE_USER, 'add_interactionpls[' . $id . ']' . json_encode($array));
            //init this interactionPlaylist's bg ,weather,date,time
            $this->initInteractionPlaylistAreaDefault($id, $array['interaction_id'], $uid);

            return $id;
        } else {
            return false;
        }
    }

    /**
     * 删除某个日程下的播放列表
     * @param object $id
     * @param object $playlist_id
     * @return
     */
    public function delete_interactoin_schedule_playlist($id, $playlist_id = false)
    {
        if ($id > 0) {
            $this->db->where('id', $id);
        }

        if ($playlist_id !== false) {
            if (is_array($playlist_id)) {
                $this->db->where_in('playlist_id', $playlist_id);
            } else {
                $this->db->where('playlist_id', $playlist_id);
            }
        }

        return $this->db->delete('cat_schedule_playlist');
    }

    /**
     * 获取单个播放列表信息
     *
     * @param object $id
     * @return
     */
    public function get_interaction_playlist($id)
    {
        $this->db->where('id', $id);
        $query = $this->db->get('cat_interaction_playlist');
        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
    }

    /**
     * 分组获取 interaction 某个模板下的区域信息
     *
     * @param object $interaction_id
     * @return
     */
    public function get_interaction_playlist_area_list($interaction_id, $screen_id)
    {
        $this->db->where('interaction_id', $interaction_id);
        $this->db->where('page_id', $screen_id);
        $this->db->from('cat_interaction_area');
        $array = array();

        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $result = $query->result();
            $query->free_result();
            return $result;
        } else {
            return false;
        }
    }

    public function get_interaction_playlist_area_screen_list($interaction_id)
    {
        $sql = "select distinct page_id from cat_interaction_area where interaction_id =" . $interaction_id;
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $result = $query->result();
            $query->free_result();
            return $result;
        } else {
            return false;
        }
    }

    /**
     * 获取某个区域详细信息
     *
     * @param object $id
     * @return
     */
    public function get_one_interaction_area($id)
    {
        $this->db->where('id', $id);
        $query = $this->db->get('cat_interaction_area');
        if ($query->num_rows() > 0) {
            $result = $query->row();
            return $result;
        } else {
            return false;
        }
    }
    /**
     * 初始化播放列表默认参数信息
     *
     * @param object $playlist_id
     * @param object $template_id
     * @param object $uid
     * @return
     */
    private function initInteractionPlaylistAreaDefault($interactionpls_id, $interaction_id, $uid)
    {
        //date
        $a = $this->get_interaction_all_area($interaction_id, $this->config->item('area_type_date'));
        if ($a) {
            $this->add_interaction_area_time_setting(array('interaction_playlist_id' => $interactionpls_id, 'area_id' => $a->id), $uid);
        }
        //weather
        $a = $this->get_interaction_all_area($interaction_id, $this->config->item('area_type_weather'));
        if ($a) {
            $this->add_interaction_area_weather_setting(array('interaction_playlist_id' => $interactionpls_id, 'area_id' => $a->id), $uid);
        }
        //time
        $a = $this->get_interaction_all_area($interaction_id, $this->config->item('area_type_time'));
        if ($a) {
            $this->add_interaction_area_time_setting(array('interaction_playlist_id' => $interactionpls_id, 'area_id' => $a->id), $uid);
        }
        //btn
        $btns = $this->get_interaction_all_area($interaction_id, $this->config->item('area_type_btn'));
        $interaction = $this->get_interaction($interaction_id);
        if ($btns) {
            if (count($btns) > 1) {
                foreach ($btns as $a) {
                    $width = $interaction->width;
                    $height = $interaction->height;
                    $this->add_interaction_area_btn_setting(array('interaction_playlist_id' => $interactionpls_id, 'area_id' => $a->id, 'w' => $width, 'h' => $height), $uid);
                }
            } else {
                $width = $interaction->width;
                $height = $interaction->height;
                $this->add_interaction_area_btn_setting(array('interaction_playlist_id' => $interactionpls_id, 'area_id' => $btns->id, 'w' => $width, 'h' => $height), $uid);
            }
        }
    }

    /**
     * 添加区域设置保存
     *
     * @param object $array
     * @param object $uid
     * @return
     */
    public function add_interaction_area_btn_setting($array, $uid)
    {
        if (empty($array)) {
            return 0;
        }

        $array['add_user_id'] = $uid;

        if ($this->db->insert('cat_interaction_area_btn_setting', $array)) {
            $id = $this->db->insert_id();
            $this->user_log($this->OP_TYPE_USER, 'add_interaction_area_btn_setting' . json_encode($array));
            return $id;
        } else {
            return false;
        }
    }

    /**
     * 获取某个区域的button设置
     *
     * @param object $area_id
     * @return
     */
    public function get_interaction_area_btn_setting($area_id, $playlist_id)
    {
        $this->db->where('area_id', $area_id);
        $this->db->where('interaction_playlist_id', $playlist_id);
        $query = $this->db->get('cat_interaction_area_btn_setting');
        if ($query->num_rows() > 0) {
            $result = $query->row();
            return $result;
        } else {
            return false;
        }
    }

    /**
     * 更新button区域设置
     *
     * @param object $array
     * @param object $area_id
     * @return
     */
    public function update_interaction_area_btn_setting($array, $id)
    {
        if (empty($array)) {
            return 0;
        }

        //$array['add_user_id'] = $uid;
        $this->db->where('id', $id);
        if ($this->db->update('cat_interaction_area_btn_setting', $array)) {
            $this->user_log($this->OP_TYPE_USER, 'update_interaction_btn_time_setting[' . $id . '] ' . json_encode($array));
            return true;
        } else {
            return false;
        }
    }

    /**
     * 更新button区域设置
     *
     * @param object $array
     * @param object $area_id
     * @return
     */
    public function update_interaction_area_btn_show($array, $areaId, $pId)
    {
        if (empty($array)) {
            return 0;
        }
        $this->db->where('area_id', $areaId);
        $this->db->where('interaction_playlist_id', $pId);
        if ($this->db->update('cat_interaction_area_btn_setting', $array)) {
            $this->user_log($this->OP_TYPE_USER, 'update_interaction_btn_btn_setting[' . $areaId . '] ' . json_encode($array));
            return true;
        } else {
            return false;
        }
    }

    /**
     * 添加区域设置保存
     *
     * @param object $array
     * @param object $uid
     * @return
     */
    public function add_interaction_area_time_setting($array, $uid)
    {
        if (empty($array)) {
            return 0;
        }

        $array['add_user_id'] = $uid;

        if ($this->db->insert('cat_interaction_area_time_setting', $array)) {
            $id = $this->db->insert_id();
            $this->user_log($this->OP_TYPE_USER, 'add_interaction_area_time_setting' . json_encode($array));
            return $id;
        } else {
            return false;
        }
    }

    /**
     * 获取某个区域的时间设置
     *
     * @param object $area_id
     * @return
     */
    public function get_interaction_area_time_setting($area_id, $playlist_id)
    {
        $this->db->where('area_id', $area_id);
        $this->db->where('interaction_playlist_id', $playlist_id);
        $query = $this->db->get('cat_interaction_area_time_setting');
        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
    }

    /**
     * 更新时间区域设置
     *
     * @param object $array
     * @param object $area_id
     * @return
     */
    public function update_interaction_area_time_setting($array, $id)
    {
        if (empty($array)) {
            return 0;
        }

        //$array['add_user_id'] = $uid;
        $this->db->where('id', $id);
        if ($this->db->update('cat_interaction_area_time_setting', $array)) {
            $this->user_log($this->OP_TYPE_USER, 'update_interaction_area_time_setting[' . $id . '] ' . json_encode($array));
            return true;
        } else {
            return false;
        }
    }

    /**
     * 获取天气预报的设置
     *
     * @param object $area_id
     * @return
     */
    public function get_interaction_area_weather_setting($area_id, $playlist_id)
    {
        $this->db->where('area_id', $area_id);
        $this->db->where('interaction_playlist_id', $playlist_id);
        $query = $this->db->get('cat_interaction_area_weather_setting');
        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
    }

    /**
     * 添加天气预报设置
     *
     * @param object $array
     * @param object $uid
     * @return
     */
    public function add_interaction_area_weather_setting($array, $uid)
    {
        if (empty($array)) {
            return 0;
        }

        $array['add_user_id'] = $uid;

        if ($this->db->insert('cat_interaction_area_weather_setting', $array)) {
            $id = $this->db->insert_id();
            $this->user_log($this->OP_TYPE_USER, 'add_interaction_area_weather_setting' . json_encode($array));
            return $id;
        } else {
            return false;
        }
    }

    /**
     * 更新天气设置
     *
     * @param object $array
     * @param object $area_id
     * @return
     */
    public function update_interaction_area_weather_setting($array, $id)
    {
        if (empty($array)) {
            return 0;
        }

        $this->db->where('id', $id);
        if ($this->db->update('cat_interaction_area_weather_setting', $array)) {
            $this->user_log($this->OP_TYPE_USER, 'update_interaction_area_weather_setting[' . $id . '] ' . json_encode($array));
            return true;
        } else {
            return false;
        }
    }

    /**
     * 添加文字区域设置
     *
     * @param object $array
     * @param object $uid
     * @return
     */
    public function add_interaction_area_text_setting($array, $uid)
    {
        if (empty($array)) {
            return 0;
        }

        $array['add_user_id'] = $uid;

        if ($this->db->insert('cat_interaction_area_text_setting', $array)) {
            $id = $this->db->insert_id();
            $this->user_log($this->OP_TYPE_USER, 'add_interaction_area_text_setting[' . $id . '] ' . json_encode($array));
            return $id;
        } else {
            return false;
        }
    }

    /**
     * 更新文本区域设置
     *
     * @param object $array
     * @return
     */
    public function update_interaction_area_text_setting($array, $id)
    {
        if (empty($array)) {
            return 0;
        }

        $this->db->where('id', $id);
        if ($this->db->update('cat_interaction_area_text_setting', $array)) {
            $this->user_log($this->OP_TYPE_USER, 'update_interaction_area_text_setting  id[' . $id . '] ' . json_encode($array));
            return true;
        } else {
            return false;
        }
    }

    /**
     * 获取当前配置下的数据
     *
     * @param object $id
     * @return
     */
    public function get_interaction_area_text_setting($id)
    {
        $this->db->where('id', $id);
        $query = $this->db->get('cat_interaction_area_text_setting');
        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
    }

    /**
     * 获取当前配置下的数据
     *
     * @param object $id
     * @return
     */
    public function get_interaction_area_text_setting_p($interactionpls_id, $area_id)
    {
        $this->db->where('interaction_playlist_id', $interactionpls_id);
        $this->db->where('area_id', $area_id);
        $query = $this->db->get('cat_interaction_area_text_setting');
        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
    }

    /**
     * 添加静态文字区域设置
     *
     * @param object $array
     * @param object $uid
     * @return
     */
    public function add_interaction_area_static_text_setting($array, $uid)
    {
        if (empty($array)) {
            return 0;
        }

        $array['add_user_id'] = $uid;

        if ($this->db->insert('cat_interaction_area_static_text_setting', $array)) {
            $id = $this->db->insert_id();
            $this->user_log($this->OP_TYPE_USER, 'add_interaction_area_text_setting[' . $id . '] ' . json_encode($array));
            return $id;
        } else {
            return false;
        }
    }

    /**
     * 更新静态文本区域设置
     *
     * @param object $array
     * @return
     */
    public function update_interaction_area_static_text_setting($array, $id)
    {
        if (empty($array)) {
            return 0;
        }
        $this->db->where('id', $id);
        if ($this->db->update('cat_interaction_area_static_text_setting', $array)) {
            $this->user_log($this->OP_TYPE_USER, 'update_interaction_area_static_text_setting  id[' . $id . '] ' . json_encode($array));
            return true;
        } else {
            return false;
        }
    }

    /**
     * 获取当前静态文本配置下的数据
     *
     * @param object $id
     * @return
     */
    public function get_interaction_area_static_text_setting($id)
    {
        $this->db->where('id', $id);
        $query = $this->db->get('cat_interaction_area_static_text_setting');
        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
    }

    /**
     * 获取某个播放列表和区域的设置信息
     *
     * @param object $playlist_id
     * @param object $area_id
     * @return
     */
    public function get_interaction_playlist_area_text_setting($interactionpls_id, $area_id)
    {
        $this->db->where('interaction_playlist_id', $interactionpls_id);
        $this->db->where('area_id', $area_id);

        $query = $this->db->get('cat_interaction_area_text_setting');
        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
    }

    /**
     * 获取某个播放列表中静态文本区域的设置信息
     *
     * @param object $playlist_id
     * @param object $area_id
     * @return
     */
    public function get_interaction_playlist_area_static_text_setting($interactionpls_id, $area_id)
    {
        $this->db->where('interaction_playlist_id', $interactionpls_id);
        $this->db->where('area_id', $area_id);

        $query = $this->db->get('cat_interaction_area_static_text_setting');
        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
    }

    /**
     * 获取当前播放列表下的RSS列表
     *
     * @param object $playlist_id
     * @param object $area_id
     * @param object $flag [optional]
     * @return
     */
    public function get_interaction_playlist_area_rss_list($playlist_id, $area_id, $flag = false)
    {
        $data = array();

        $this->db->select('m.name, m.descr,m.url,p.*');
        $this->db->from('cat_interaction_playlist_area_media p');
        $this->db->join('cat_rss m', 'm.id = p.media_id', 'left');
        $this->db->where('p.interaction_playlist_id', $playlist_id);
        $this->db->where('p.area_id', $area_id);
        if ($flag !== false) {
            if (is_array($flag)) {
                $this->db->where_in('flag', $flag);
            } else {
                $this->db->where('flag', $flag);
            }
        } else {
            $this->db->where('flag >', $this->config->item('area_media_flag_temp'));
        }
        $this->db->order_by('p.flag', 'asc');
        //echo $this->db->get_sql();
        //$this->db->limit($limit, $offset);
        $query = $this->db->get();
        $total = $query->num_rows();
        if ($total > 0) {
            $data = $query->result();
            $query->free_result();
        }
        return array('total' => $total, 'data' => $data);
    }

    /**
     * 获取播放列表下区域中的媒体数
     *
     * @param object $playlist_id
     * @param object $area_id
     * @param object $flag [optional]
     * @return
     */
    public function get_interaction_playlist_area_media_count($playlist_id, $area_id, $flag = false)
    {
        $this->db->select('count(*) as total');
        $this->db->where('interaction_playlist_id', $playlist_id);
        $this->db->where('area_id', $area_id);
        if ($flag) {
            if (is_array($flag)) {
                $this->db->where_in('flag', $flag);
            } else {
                $this->db->where('flag', $flag);
            }
        } else {
            $this->db->where('flag >', $this->config->item('area_media_flag_temp'));
        }

        $query = $this->db->get('cat_interaction_playlist_area_media');

        return $query->row()->total;
    }

    /**
     * 返回当前区域的类型
     *
     * @param object $area_id
     * @return
     */
    public function get_interaction_area_type($area_id)
    {
        $this->db->select('area_type');
        $this->db->where('id', $area_id);
        $query = $this->db->get('cat_interaction_area');
        if ($query->num_rows() > 0) {
            $result = $query->row();
            return $result->area_type;
        } else {
            return false;
        }
    }

    /**
     * 删除某个播放列表区域中的媒体文件
     *
     * @param object $playlist_id
     * @param object $area_id [optional] 删除播放列表中某个区域下的所有文件
     * @param object $id [optional] 删除某个区域下的某个文件
     * @param object $flag [optional] 删除某个区域下的特定文件
     * @return
     */
    public function delete_interaction_playlist_area_media($playlist_id, $area_id = 0, $id = 0, $flag = false)
    {
        $this->db->where('interaction_playlist_id', $playlist_id);

        if ($area_id > 0) {
            $this->db->where('area_id', $area_id);
        }

        if ($id) {
            if (is_array($id)) {
                $this->db->where_in('id', $id);
            } else {
                $this->db->where('id', $id);
            }
        }

        if ($flag !== false) {
            $this->db->where('flag', $flag);
        }

        return $this->db->delete('cat_interaction_playlist_area_media');
    }

    /**
     * 更新媒体文件的状态
     * @param object $id
     * @param object $flag [optional]
     * @return
     */
    public function update_interaction_media_flag($id, $flag = false)
    {
        if (is_array($id)) {
            $this->db->where_in('id', $id);
        } else {
            $this->db->where('id', $id);
        }
        if ($flag !== false) {
            $this->db->set(array('flag' => $flag));
        } else {
            $this->db->set(array('flag' => $this->config->item('area_media_flag_ok')));
        }

        return $this->db->update('cat_interaction_playlist_area_media');
    }

    /**
     * 获取当前播放列表区域中最大的位置
     * @param object $playlist_id
     * @param object $area_id
     * @return
     */
    public function get_interaction_playlist_area_media_max_position($playlist_id, $area_id)
    {
        $this->db->select('max(position) as position');
        $this->db->from('cat_interaction_playlist_area_media p');
        $this->db->where('p.interaction_playlist_id', $playlist_id);
        $this->db->where('p.area_id', $area_id);
        $query = $this->db->get();
        if ($query->num_rows()) {
            return $query->row()->position;
        } else {
            return 0;
        }
    }

    /**
     * 添加区域数据
     *
     * @param object $array
     * @param object $uid
     * @return
     */
    public function add_interaction_area_media($array, $uid)
    {
        if (empty($array)) {
            return 0;
        }
        $array['add_user_id'] = $uid;

        if ($this->db->insert('cat_interaction_playlist_area_media', $array)) {
            $id = $this->db->insert_id();
            $this->user_log($this->OP_TYPE_USER, 'add_area_media[' . $id . '] ' . json_encode($array));
            return $id;
        } else {
            return false;
        }
    }

    public function update_interaction_area_media($array, $id)
    {
        if (empty($array) || $id == 0) {
            return false;
        }
        $this->db->where('id', $id);

        return $this->db->update('cat_interaction_playlist_area_media', $array);
    }

    /**
     * 获取某个播放列表中某个区域的媒体文件列表信息
     *
     * @param object $playlist_id
     * @param object $area_id
     * @param object $flags [optional]
     * @return
     */
    public function get_interaction_playlist_area_media_list($playlist_id, $area_id, $flag = false)
    {
        $result = array();
        $total = 0;
        $data = array();
        $this->db->select('m.name, m.media_type, m.ext, m.full_path,m.signature, m.file_size,m.preview_status,m.tiny_url,,m.main_url,m.source,m.company_id, p.*');
        $this->db->from('cat_interaction_playlist_area_media p');
        $this->db->join('cat_media m', 'm.id = p.media_id', 'left');
        $this->db->where('p.interaction_playlist_id', $playlist_id);
        $this->db->where('p.area_id', $area_id);
        if ($flag !== false) {
            if (is_array($flag)) {
                $this->db->where_in('flag', $flag);
            } else {
                $this->db->where('flag', $flag);
            }
        } else {
            $this->db->where('flag >', $this->config->item('area_media_flag_temp'));
        }
        $this->db->order_by('p.position', 'asc');
        $query = $this->db->get();
        $total = $query->num_rows();
        if ($total > 0) {
            $data = $query->result();
            $query->free_result();

            //check index order
            if (true /*$data[0]->position == 0 || $data[0]->position > 1*/) {
                //init
                $index = 1;
                for ($i = 0; $i < count($data); $i++) {
                    if ($data[$i]->position != $index) {
                        $data[$i]->position = $index;
                        $sql = "update cat_interaction_playlist_area_media set position = " . $index . " where id = " . $data[$i]->id;
                        $this->db->query($sql);
                    }
                    $index++;
                }
            }
        }

        return array('total' => $total, 'data' => $data);
    }

    /**
     * 获取某个播放列表中某个区域的媒体文件列表信息
     *
     * @param object $playlist_id
     * @param object $area_id
     * @param object $flags [optional]
     * @return
     */
    public function get_interaction_area_webpag_setting($playlist_id, $area_id, $flag = false)
    {
        $result = array();
        $total = 0;
        $data = array();
        $this->db->select('r.name, r.descr, r.url, r.company_id, r.add_user_id, r.add_time, r.type, p.*');
        $this->db->from('cat_interaction_playlist_area_media p');
        $this->db->join('cat_rss r', 'r.id = p.media_id', 'left');
        $this->db->where('p.interaction_playlist_id', $playlist_id);
        $this->db->where('p.area_id', $area_id);
        if ($flag !== false) {
            if (is_array($flag)) {
                $this->db->where_in('flag', $flag);
            } else {
                $this->db->where('flag', $flag);
            }
        } else {
            $this->db->where('flag >', $this->config->item('area_media_flag_temp'));
        }
        $this->db->order_by('p.position', 'asc');
        //$this->db->limit($limit, $offset);
        $query = $this->db->get();
        $total = $query->num_rows();
        if ($total > 0) {
            $data = $query->result();
            $query->free_result();

            //check index order
            if (true /*$data[0]->position == 0 || $data[0]->position > 1*/) {
                //init
                $index = 1;
                for ($i = 0; $i < count($data); $i++) {
                    if ($data[$i]->position != $index) {
                        $data[$i]->position = $index;
                        $sql = "update cat_interaction_playlist_area_media set position = " . $index . " where id = " . $data[$i]->id;
                        $this->db->query($sql);
                    }
                    $index++;
                }
            }
        }

        return array('total' => $total, 'data' => $data);
    }

    /**
     * 删除临时数据，修改所有状态为删除的为ok状态
     * @param object $playlist_id
     * @return
     */
    public function update_interaction_area_media_commit($playlist_id)
    {
        $this->db->where('interaction_playlist_id', $playlist_id);
        $this->db->where('flag', $this->config->item('area_media_flag_temp'));
        $this->db->delete('cat_interaction_playlist_area_media');

        $this->db->where('interaction_playlist_id', $playlist_id);
        $this->db->where('flag', $this->config->item('area_media_flag_delete'));
        $this->db->update('cat_interaction_playlist_area_media', array('flag' => $this->config->item('area_media_flag_ok')));
    }
    /**
     * 删除某个播放列表中的某条记录
     *
     * @param object $id
     * @return
     */
    public function delete_interaction_media($id)
    {
        if (is_array($id)) {
            $this->db->where_in('id', $id);
        } else {
            $this->db->where('id', $id);
        }

        $this->user_log($this->OP_TYPE_USER, 'delete_media cat_interaction_playlist_area_media' . json_encode($id));
        return $this->db->delete('cat_interaction_playlist_area_media');
    }

    /**
     * 删除当前播放列表中临时的媒体文件，每次刷新时执行
     * @param object $playlist_id
     * @param object $flag [optional]
     * @return
     */
    public function delete_interaction_playlist_area_media_temp($playlist_id, $flag = 0)
    {
        $this->db->where('interaction_playlist_id', $playlist_id);
        $this->db->where('flag', $flag);
        return $this->db->delete('cat_interaction_playlist_area_media');
    }

    /**
     * 更新播放列表
     *
     * @param object $array
     * @param object $id
     * @return
     */
    public function update_interaction_playlist($array, $id)
    {
        if (empty($array)) {
            return 0;
        }
        $this->db->where('id', $id);
        if ($this->db->update('cat_interaction_playlist', $array)) {
            $this->user_log($this->OP_TYPE_USER, 'update_interaction_playlist[' . $id . '] ' . json_encode($array));
            return true;
        } else {
            return false;
        }
    }

    /**
     * 获取播放列表中最后一个RSS，这个可能是发布了的，也可能是未发布的临时的
     *
     * @param object $playlist_id
     * @param object $area_id
     * @return
     */
    public function get_interaction_playlist_area_last_rss($playlist_id, $area_id)
    {
        $this->db->select('m.name, m.type, m.descr,m.url,p.*');
        $this->db->from('cat_interaction_playlist_area_media p');
        $this->db->join('cat_rss m', 'm.id = p.media_id', 'left');
        $this->db->where('p.interaction_playlist_id', $playlist_id);
        $this->db->where('p.area_id', $area_id);
        $this->db->order_by('p.flag', 'asc');
        $this->db->limit(1);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            //large than 0
            return $query->row();
        } else {
            return false;
        }
    }

    /**
     * 删除某个播放列表，同时删除与当前播放列相关的媒体记录
     * 删除播放列表中的媒体文件
     * 删除与播放列表相关的设置，文本区域设置
     *
     * @param object $ids
     * @return
     */
    public function delete_interaction_playlist($ids)
    {
        if ($ids) {
            if (is_array($ids)) {
                foreach ($ids as $id) {
                    $this->delete_interaction_playlist_area_media($id);
                    $this->delete_interaction_playlist_area_text($id);
                    $this->delete_interaction_playlist_area_time($id);
                    $this->delete_interaction_playlist_area_weather($id);
                    $this->delete_interaction_playlist_area_static_text($id);
                    $this->delete_interaction_playlist_area_btn($id);
                    $this->db->delete('cat_interaction_playlist', array('id' => $id));
                }
            } else {
                $this->delete_interaction_playlist_area_media($ids);
                $this->delete_interaction_playlist_area_text($ids);
                $this->delete_interaction_playlist_area_time($ids);
                $this->delete_interaction_playlist_area_weather($ids);
                $this->delete_interaction_playlist_area_static_text($ids);
                $this->delete_interaction_playlist_area_btn($ids);
                $this->db->delete('cat_interaction_playlist', array('id' => $ids));
            }
            //$this->delete_schedule_playlist(0, $ids);
            return true;
        } else {
            return false;
        }
    }
    /**
     * 获取某个播放列表下的媒体文件
     *
     * @param object $id
     * @return
     */
    public function get_interaction_playlist_area_media($id)
    {
        $this->db->where('id', $id);

        $query = $this->db->get('cat_interaction_playlist_area_media');
        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
    }
    /**
     * 播放列表中的媒体文件移动
     *
     * @param object $id
     * @param object $index
     * @return
     */
    public function interaction_playlist_area_media_move_to($id, $index)
    {
        $m = $this->get_interaction_playlist_area_media($id);
        if ($m) {
            //down
            if ($m->position > $index) {
                $sql = "update cat_interaction_playlist_area_media set position = position + 1 where interaction_playlist_id = $m->interaction_playlist_id and area_id = $m->area_id and position between $index and $m->position - 1";
                $this->db->query($sql);
                $sql = "update cat_interaction_playlist_area_media set position = $index where id = $id";
                $this->db->query($sql);
            } elseif ($m->position < $index) {
                $sql = "update cat_interaction_playlist_area_media set position = position - 1 where interaction_playlist_id = $m->interaction_playlist_id and area_id = $m->area_id and position between $index and $m->position + 1";
                $this->db->query($sql);
                $sql = "update cat_interaction_playlist_area_media set position = $index where id = $id";
                $this->db->query($sql);
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * 交换播放列表中媒体文件的顺序
     *
     * @param object $fid
     * @param object $sid
     * @return
     */
    public function change_interaction_playlist_area_media_order($fid, $sid)
    {
        $fpos = 0;
        $spos = 0;

        $this->db->select('position');
        $this->db->from('cat_interaction_playlist_area_media p');
        $this->db->where('p.id', $fid);
        $query = $this->db->get();
        if ($query->num_rows()) {
            $fpos = $query->row()->position;
        } else {
            return false;
        }

        $this->db->select('position');
        $this->db->from('cat_interaction_playlist_area_media p');
        $this->db->where('p.id', $sid);
        $query = $this->db->get();
        if ($query->num_rows()) {
            $spos = $query->row()->position;
        } else {
            return false;
        }

        $sql = "update cat_interaction_playlist_area_media set position = $spos where id = $fid";
        $this->db->query($sql);
        $sql = "update cat_interaction_playlist_area_media set position = $fpos where id = $sid";
        $this->db->query($sql);

        return true;
    }

    public function interaction_edit_status($array, $id)
    {
        $sql = 'select status from cat_interaction_playlist_area_media where id=' . $id;
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $result = $query->result();
            foreach ($result as $arr) {
                //return $arr->status;
                if ($arr->status == 1) {
                    $status = 0;
                } else {
                    $status = 1;
                }
                $this->db->where('id', $id);
                return $this->db->update('cat_interaction_playlist_area_media', array('status' => $status));
            }
        }
    }

    /**
     * 获取某个播放列表区域的传输类型，是transmode_type_full or transmode_type_part
     *
     * @param object $id
     * @return
     */
    public function get_interaction_area_transmode_type($id)
    {
        $this->db->select('ta.area_type, ta.w, ta.h, t.w as tw, t.h as th');
        $this->db->from('cat_interaction_area ta');
        $this->db->join('cat_interaction t', 't.id=ta.interaction_id', 'left');
        $this->db->where('ta.id', $id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $result = $query->row();
            if ($result->area_type == $this->config->item('area_type_image')) {
                return $this->config->item('transmode_type_image');
            } elseif ($result->area_type == $this->config->item('area_type_movie')) {
                if ($result->w == $result->tw && $result->h == $result->th) {
                    return $this->config->item('transmode_type_full');
                } else {
                    return $this->config->item('transmode_type_part');
                }
            }
        } else {
            return false;
        }
    }

    public function update_interaction_all_area_media($array, $id)
    {
        if (empty($array) || $id == 0) {
            return false;
        }
        $sql = "select id from cat_interaction_playlist_area_media where area_id=$id";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $result = $query->result();
            foreach ($result as $arr) {
                $ids[] = $arr->id;
            }
            if (count($ids) == 1) {
                $this->db->where('id', $ids[0]);
            } else {
                $this->db->where_in('id', $ids);
            }
            return $this->db->update('cat_interaction_playlist_area_media', $array);
        }
        return 0;
    }

    public function edit_interaction_reload($array, $id)
    {
        $sql = 'select reload from cat_interaction_playlist_area_media where id=' . $id;
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $result = $query->result();
            foreach ($result as $arr) {
                //return $arr->status;
                if ($arr->reload == 1) {
                    $reload = 0;
                } else {
                    $reload = 1;
                }
                $this->db->where('id', $id);
                return $this->db->update('cat_interaction_playlist_area_media', array('reload' => $reload));
            }
        }
    }
    /**
     * 删除某个播放列表区域中的字幕设置
     */
    public function delete_interaction_playlist_area_text($playlist_id)
    {
        $this->db->delete('cat_interaction_area_text_setting', array('interaction_playlist_id' => $playlist_id));
    }
    /**
     * 删除某个播放列表区域中的天气设置
     */
    public function delete_interaction_playlist_area_weather($playlist_id)
    {
        $this->db->delete('cat_interaction_area_weather_setting', array('interaction_playlist_id' => $playlist_id));
    }

    /**
     * 删除某个播放列表区域中的时间、日期设置
     */
    public function delete_interaction_playlist_area_time($playlist_id)
    {
        $this->db->delete('cat_interaction_area_time_setting', array('interaction_playlist_id' => $playlist_id));
    }

    /**
     * 删除某个播放列表区域中的按钮设置
     */
    public function delete_interaction_playlist_area_btn($playlist_id)
    {
        $this->db->delete('cat_interaction_area_btn_setting', array('interaction_playlist_id' => $playlist_id));
    }
    /**
     * 删除某个播放列表区域中的按钮设置
     */
    public function delete_interaction_playlist_area_static_text($playlist_id)
    {
        $this->db->delete('cat_interaction_area_static_text_setting', array('interaction_playlist_id' => $playlist_id));
    }

    /**
     * 获取某个公司下的播放列表
     *
     * @param object $cid
     * @param object $condition 查询条件
     * @param object $offset
     * @param object $limit -1返回全部记录
     * @return
     */
    public function get_interaction_playlist_list($cid, $condition = false, $offset = 0, $limit = -1, $order_item = 'id', $order = 'desc')
    {
        $this->db->select('count(*) as total');
        $this->db->where('company_id', $cid);
        if ($condition) {
            foreach ($condition as $key => $value) {
                if ($key == 'date') {
                    $this->db->where('add_time >=', $value);
                    $this->db->where('add_time <=', $value . ' 23:59:59');
                } elseif ($key == 'name') {
                    $this->db->like($key, $value);
                } else {
                    $this->db->where_in($key, $value);
                }
            }
        }

        $query = $this->db->get('cat_interaction_playlist');
        $total = $query->row()->total;
        $array = array();
        if ($total > 0) {
            $this->db->select('p.*, u.name as user, t.name as template');
            $this->db->from('cat_interaction_playlist p');
            $this->db->join('cat_user u', 'p.add_user_id = u.id');
            $this->db->join('cat_interaction t', 'p.interaction_id = t.id');
            $this->db->where('p.company_id', $cid);
            if ($condition) {
                if ($condition) {
                    foreach ($condition as $key => $value) {
                        if ($key == 'date') {
                            $this->db->where('p.add_time >=', $value);
                            $this->db->where('p.add_time <=', $value . ' 23:59:59');
                        } elseif ($key == 'name') {
                            $this->db->like('p.' . $key, $value);
                        } else {
                            $this->db->where_in('p.' . $key, $value);
                        }
                    }
                }
            }

            $this->db->order_by('p.' . $order_item, $order);
            if ($limit > 0) {
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

    public function is_schedule_interaction_empty($schedule_id)
    {
        $sql = "select count(*) as total from cat_schedule_interaction where schedule_id = $schedule_id";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            if ($query->row()->total > 0) {
                return false;
            }
        }

        return true;
    }

    public function get_interaction_static_bg_area($pid, $area_id, $media_id)
    {
        $this->db->from('cat_interaction_playlist_area_media');
        $this->db->where('interaction_playlist_id', $pid);
        $this->db->where('area_id', $area_id);
        $this->db->where('media_id', $media_id);
        $query = $this->db->get();
        $array = array();
        if ($query->num_rows()) {
            $array = $query->result();
            $query->free_result();
            return $array;
        } else {
            return false;
        }
    }

    public function add_interaction_static_bg_area($array)
    {
        if ($this->db->insert('cat_interaction_playlist_area_media', $array)) {
            $id = $this->db->insert_id();
            return $id;
        } else {
            return false;
        }
    }

    /**
     * 获取播放列表信息通过模板
     *
     * @param object $template_id
     * @return
     */
    public function get_playlist_by_interaction($template_id)
    {
        $this->db->select('id, name');
        $this->db->where('interaction_id', $template_id);
        $query = $this->db->get('cat_interaction_playlist');
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }

    public function delete_interaction($id)
    {
        $this->db->select('id,area_type');
        $this->db->where('interaction_id', $id);
        $query = $this->db->get('cat_interaction_area');
        if ($query->num_rows() > 0) {
            //删除区域信息
            foreach ($query->result() as $row) {
                $this->delete_interaction_area($row->id, $row->area_type);
            }
            //删除当前模板
            $this->db->delete('cat_interaction', array('id' => $id));
            $this->db->delete('cat_interaction_tree', array('interaction_id' => $id));
            $this->db->delete('cat_interaction_area', array('interaction_id' => $id));
            $this->db->delete('cat_interaction_playlist', array('interaction_id' => $id));
        } else {
            //删除当前模板
            $this->db->delete('cat_interaction', array('id' => $id));
            $this->db->delete('cat_interaction_tree', array('interaction_id' => $id));
            $this->db->delete('cat_interaction_area', array('interaction_id' => $id));
            $this->db->delete('cat_interaction_playlist', array('interaction_id' => $id));
        }
        $playlists = $this->get_playlist_by_interaction($id);
        if ($playlists) {
            foreach ($playlists as $p) {
                $this->delete_interaction_playlist($p->id);
            }
        }
        return true;
    }

    public function get_playlist_area_media_name($id)
    {
        $sql = "select pm.id, ta.name, m.name as media_name from cat_playlist_area_media as pm, cat_template_area as ta, cat_media as m where pm.playlist_id = $id and pm.area_id = ta.id and pm.media_id = m.id";
        $query = $this->db->query($sql);
        if ($query->num_rows()) {
            return $query->result();
        }
        return false;
    }

    public function get_playlist_byname($cid, $pname)
    {
        $this->db->select('p.*');
        $this->db->from('cat_playlist p');
        $this->db->where('p.name', $pname);
        $this->db->where('p.company_id', $cid);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }

    public function get_media_by_tags($tags, $exclude = null)
    {
        if (empty($tags)) {
            return false;
        }


        $sql = 'select m.*, t.name as tag_name from cat_media m, cat_tag_media tm, cat_tag t WHERE m.id = tm.media_id AND  t.id = tm.tag_id AND tm.tag_id
      IN(' . $tags . ')';

        if ($exclude) {
            $exstr = ' AND tm.media_id NOT IN( 
          SELECT tm.media_id FROM cat_tag_media tm
          WHERE tm.tag_id IN(' . $exclude . '))';
            $sql = $sql . $exstr;
        }


        $query = $this->db->query($sql);
        if ($query->num_rows()) {
            return $query->result();
        } else {
            return false;
        }



        if (empty($tags)) {
            return false;
        }
        $in_tags = explode(',', $tags);
        $exlude_tags = explode(',', $exclude);


        $this->db->select('m.*');
        $this->db->from('cat_media m');
        $this->db->join('cat_tag_media tm', 'm.id=tm.media_id', 'left');


        if (!empty($in_tags)) {
            $this->db->where_in('tm.tag_id', $in_tags);
        }
        if (!empty($exlude_tags)) {
            $this->db->where_not_in('tm.tag_id', $exlude_tags);
        }

        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }

    /*************
     * 查询campaign包含的player的合集
     */
    public function get_player_by_campaign($camid, $detail = true)
    {
        if ($detail) {
            $this->db->select("p.id,p.name,p.timer_config_id,p.company_id,pe.custom_sn1,pe.custom_sn2,t.offweekdays");
            $this->db->join("cat_player_extra pe", 'pe.player_id = p.id', 'left');
            $this->db->join("cat_timer_config t", 't.id = p.timer_config_id', 'left');
        } else {
            $this->db->select("p.id,p.name");
        }
        $this->db->join('campaign_player cp', 'cp.player_id=p.id', "LEFT");
        $this->db->from("cat_player p");
        $this->db->where("cp.campaign_id", $camid);

        $query = $this->db->get();
        $cnt = $query->num_rows();
        if ($query->num_rows() > 0) {
            return $query->result();
        }
        return false;
    }

    public function get_sel_player_array_by_campaign($camid)
    {
        $this->db->select("player_id");
        $this->db->from("campaign_player");

        $this->db->where("campaign_id", $camid);


        $query = $this->db->get();
        $cnt = $query->num_rows();
        if ($query->num_rows() > 0) {
            return array_column($query->result_array(), 'player_id');
        }
        return false;
    }

    public function get_player_by_criterias($cris, $bind_cris = false, $ex_cris = false, $bind_players = false, $tags = false, $ex_players = false, $bind_cris_or = false)
    {

        $player_ids = $this->get_players_by_condition($cris, $bind_cris, $ex_cris, $bind_players, $tags, $ex_players, $bind_cris_or);

        if (!$player_ids) {
            return false;
        }

        $this->db->select("cp.*,pe.setupdate");
        $this->db->from('cat_player cp');
        //$this->db->join("cat_criteria_player cc", 'cc.player_id = cp.id', 'left');
        $this->db->join("cat_player_extra pe", 'pe.player_id = cp.id', 'left');

        $this->db->where_in('cp.id', $player_ids);


        $this->db->where("pe.setupdate is not null");
        //$this->db->where("UNIX_TIMESTAMP(pe.setupdate) !=", 0);
        $this->db->distinct();

        $query = $this->db->get();

        if ($query->num_rows()) {
            $players =  $query->result();
            return $players;
        } else {
            return false;
        }
    }

    /*************
     * 查询player所属critrias获取campaign的集合
     */
    public function get_published_campaign_by_player($player_id, $day, $priority = 9)
    {
        $this->db->select("campaign_id");
        $this->db->from("campaign_player");
        $this->db->where('player_id', $player_id);
        $query = $this->db->get();
        $cam_ids = null;
        if ($query->num_rows() > 0) {
            $cam_ids = array_column($query->result_array(), 'campaign_id');
        } else {
            return false;
        }

        $this->db->select("p.*, c.pId,c.nxslot,GROUP_CONCAT(ta.tag_id) as tags");
        // $this->db->join('campaign_player pc', "p.id = pc.campaign_id and pc.player_id=$player_id");
        $this->db->from("cat_playlist p");
        if ($this->config->item('with_template')) {
            $this->db->join("taggables ta", "ta.taggable_id=p.id and ta.taggable_type='App\\\Playlist'", "LEFT");
        } else {
            $this->db->join("taggables ta", "ta.taggable_id=p.id and ta.taggable_type='App\\\Campaign'", "LEFT");
        }
        $this->db->join('cat_company c', "c.id=p.company_id", 'LEFT');

        $this->db->where_in('p.id', $cam_ids);

        $this->db->where('p.published', 1);
        if ($priority == 10) {
            $this->db->where("p.priority!=", 3)->where("p.priority!=", 5)->where("p.priority!=", 6);
        } elseif ($priority == 8) {
            $this->db->where("p.priority<", 3);
        } elseif ($priority == 3) {
            $this->db->group_start()->where("p.priority", 3)->or_where("p.priority", 6)->group_end();
        } elseif ($priority != 9) {
            $this->db->where("p.priority", $priority);
        } elseif ($priority == 11) {
            $this->db->where("p.priority!=", 3)->where("p.priority!=", 6);
        }
        if ($day != -1) {
            $this->db->where('p.start_date<=', $day);
            $this->db->where('p.end_date>=', $day);
        }

        $this->db->where('p.deleted_at is null');

        $this->db->group_by("p.id");
        $this->db->order_by('update_time', 'asc');

        $query = $this->db->get();

        if ($query->num_rows()) {
            $cams = $query->result();

            foreach ($cams as $cam) {
                $this->fill_campaign_media_info($cam);
                if ($day != -1 && $cam->media) {
                    $cam->media = array_filter($cam->media, function ($medium) use ($day) {
                        if ($medium['date_flag'] == 0 || ($medium['date_flag'] == 1 && $day >= $medium['start_date'] && $day <= $medium['end_date'])) {
                            return true;
                        }
                        return false;
                    });
                }
            }
            return $cams;
        }
        return false;
    }



    public function get_published_campaign($cid)
    {
        $this->db->select("p.*,c.pId,c.nxslot,MAX(pf.criterion_id) as criterion_id,GROUP_CONCAT(distinct cc.criterion_id) as criterias,GROUP_CONCAT(distinct ca.criterion_id) as and_criterias,
            GROUP_CONCAT(distinct co.criterion_id) as and_criteria_or,GROUP_CONCAT(distinct ce.criterion_id) as ex_criterias,GROUP_CONCAT(distinct tm.tag_id) as tags,
            GROUP_CONCAT(distinct pc.player_id) as players,GROUP_CONCAT(distinct pc_ex.player_id) as ex_players");
        $this->db->from('cat_playlist p');
        if ($this->config->item('with_template')) {
            $this->db->join('criterionables cc', "cc.criterionable_id = p.id and cc.cam_bindtype=0 and (cc.criterionable_type='App\\\Playlist' or cc.criterionable_type='App\\\Campaign')", "LEFT");
            $this->db->join('criterionables ca', "ca.criterionable_id = p.id and ca.cam_bindtype=1 and (ca.criterionable_type='App\\\Playlist' or ca.criterionable_type='App\\\Campaign')", "LEFT");
            $this->db->join('criterionables ce', "ce.criterionable_id = p.id and ce.cam_bindtype=2 and (ce.criterionable_type='App\\\Playlist' or ce.criterionable_type='App\\\Campaign')", "LEFT");
            $this->db->join('criterionables co', "co.criterionable_id = p.id and co.cam_bindtype=3 and (co.criterionable_type='App\\\Playlist' or co.criterionable_type='App\\\Campaign')", "LEFT");
            $this->db->join('taggables tm', "tm.taggable_id=p.id and (tm.taggable_type='App\\\Playlist' or tm.taggable_type='App\\\Campaign')", "LEFT");
        } else {
            $this->db->join('criterionables cc', "cc.criterionable_id = p.id and cc.cam_bindtype=0 and cc.criterionable_type='App\\\Campaign'", "LEFT");
            $this->db->join('criterionables ca', "ca.criterionable_id = p.id and ca.cam_bindtype=1 and ca.criterionable_type='App\\\Campaign'", "LEFT");
            $this->db->join('criterionables ce', "ce.criterionable_id = p.id and ce.cam_bindtype=2 and ce.criterionable_type='App\\\Campaign'", "LEFT");
            $this->db->join('criterionables co', "co.criterionable_id = p.id and co.cam_bindtype=3 and co.criterionable_type='App\\\Campaign'", "LEFT");
            $this->db->join('taggables tm', "tm.taggable_id=p.id and tm.taggable_type='App\\\Campaign'", "LEFT");
        }

        $this->db->join('cat_player_campaign pc', "pc.campaign_id = p.id and pc.type=0", "LEFT");
        $this->db->join('cat_player_campaign pc_ex', "pc_ex.campaign_id = p.id and pc_ex.type=1", "LEFT");

        $this->db->join('cat_company c', 'c.id = p.company_id', "LEFT");
        $this->db->join('cat_parter_fields pf', 'pf.partner_id=p.company_id', 'LEFT');
        $this->db->where('p.published', 1);
        //$this->db->where('p.priority!=', 5);
        if (is_array($cid)) {
            $this->db->where_in('p.company_id', $cid);
        } else {
            $this->db->where('p.company_id', $cid);
        }
        $this->db->where('p.deleted_at is null');
        $this->db->where('p.end_date>', date('Y-m-d'));
        $this->db->distinct();
        $this->db->group_by('p.id');


        $query = $this->db->get();

        $data['total'] = $query->num_rows();
        if ($data['total']) {
            $cams = $query->result();

            foreach ($cams as $cam) {
                $this->fill_campaign_media_info($cam);
            }

            $data['data'] = $cams;
            return $data;
        } else {
            return false;
        }
    }


    /**
     * 获取某个播放列表中video区域的媒体文件的总播放长度
     *
     * @param object $playlist_id
     * @param object $area_id
     * @return
     */
    public function get_playlist_total_time($playlist_id)
    {
        $sql = "SELECT SUM( m.play_time ) AS total_time
      FROM cat_playlist cp, cat_playlist_area_media p, cat_media m 
      WHERE m.id = p.media_id
      AND cp.id = p.playlist_id
      AND p.status = 0 
      AND p.playlist_id =" . $playlist_id;


        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $data = $query->row()->total_time;
            $query->free_result();
            return $data;
        } else {
            return false;
        }
    }

    /**
     * 获取某个播放列表中video区域的媒体文件的总播放长度
     *
     * @param object $playlist_id
     * @param object $area_id
     * @return
     */
    public function has_expired_medias($id)
    {
        $sql = "select * 
        FROM cat_media m,cat_playlist_area_media p 
         WHERE m.id = p.media_id 
         AND p.id in(" . implode(",", $id) . ")
         AND m.date_flag = 1 
         AND m.end_date is not null
         AND m.end_date<CURDATE()";


        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            if ($query->result()) {
                return true;
            }
        }
        return false;
    }

    /**
     *
     * @param array $timeslots
     * @param object $cam
     * @return -1 if no valid playtime
     */

    public function try_allocate_campaign(&$timeslots, $cam)
    {
        $success_flag = 0;
        $fail_flag = 0;
        $idel_count = 0;
        $cam_startH = 0;
        $cam_stopH = 24;
        $valid_slot = 0;


        $slots_total_seconds = 0;
        $slots_used_seconds = 0;
        if (!$cam->time_flag) {
            $cam_startH = $cam->start_timeH;
            $cam_stopH = $cam->end_timeH;


            foreach ($timeslots as $slot) {
                if ($slot->startH >= $cam_startH && $slot->startH < $cam_stopH) {
                    $valid_slot++;
                }
            }
        } else {
            $valid_slot = count($timeslots);
        }


        //Alan 2018/1/30 if no overlapping, just ignore it.
        if ($valid_slot == 0) {
            return array('status' => true, 'day_used' => 0, 'day_booked' => 0);
        }

        $success_flag = 0;

        //For empty Reservation
        if ($cam->priority == 5) {
            if ($cam->media_cnt == 0) {
                $cam->media_cnt = 1;
                $cam->total_time = 10;
            }
        }
        if ($cam->media_cnt == 0) {
            return array('status' => false);
        }

        $cam_total_used = 0;
        $cam_total_booked = 0;
        foreach ($timeslots as $slot) {
            if ($slot->startH >= $cam_startH && $slot->startH < $cam_stopH) {
                $playcount = 1;
                $bookedcount = 1;
                if ($slot->total_time == 0) {
                    continue;
                }

                //Pecentage
                if ($cam->play_cnt_type == 1) {
                    $percentage = $cam->play_weight;
                    if ($cam->is_grouped) {
                        $playcount = round($slot->total_time * ($percentage / 100) / ($cam->total_time));
                        $bookedcount = round($slot->total_time * ($cam->booked / 100) / ($cam->total_time));
                    } else {
                        $playcount = round($slot->total_time * ($percentage / 100) / ($cam->total_time / $cam->media_cnt));
                        $bookedcount = round($slot->total_time * ($cam->booked / 100) / ($cam->total_time));
                    }
                }
                //xlost
                elseif ($cam->play_cnt_type == 9) {
                    $playcount =  floor($slot->total_time * (1 / $cam->nxslot) / ($cam->total_time / $cam->media_cnt));
                    $bookedcount =  $playcount;
                }

                //Total time
                elseif ($cam->play_cnt_type == 2) {
                    //if total time is zero, continue
                    $playcount = floor($cam->play_totalperhour / (3600 / $slot->total_time));

                    $bookedcount = $playcount;
                }
                //Times per hour
                elseif ($cam->play_cnt_type == 0) {
                    $playcount = floor($cam->play_count / (3600 / $slot->total_time));
                    $bookedcount = floor($cam->booked / (3600 / $slot->total_time));
                }



                if ($playcount == 0) {
                    continue;
                }

                //total time or grouped campaign
                if ($cam->play_cnt_type == 2 || $cam->is_grouped) {
                    $bookedcount = $cam->total_time * $bookedcount;
                } else {
                    $bookedcount = $bookedcount * ($cam->total_time / $cam->media_cnt);
                }

                $cam_total_booked += $bookedcount;

                $used = $slot->add_campagin($cam, $playcount);
                if ($used) {
                    $cam_total_used += $used;

                    $success_flag++;
                } else {
                    return array('status' => false);
                }
            }
        }
        //if no timeslot is set
        if ($success_flag == 0) {
            return array('status' => false);
        }

        return array('status' => true, 'day_used' => $cam_total_used, 'day_booked' => $cam_total_booked);
    }

    /**
     *
     * @param unknown $duration
     * @param unknown $startH
     * @param unknown $startM
     * @param unknown $stopH
     * @param unknown $stopM
     * @param unknown $playerid
     * @param timestamp $today
     * @return TimeSlot
     */
    public function new_time_slot($duration, $startH, $startM, $stopH, $stopM)
    {
        $slot = new TimeSlot(array(
            "total_time" => $duration,
            "startH" => $startH,
            "startM" => $startM,
            "stopH" => $stopH,
            "stopM" => $stopM
        ));
        return $slot;
    }



    /**
     * 取出指定日期的Timeslot列表
     * @param string $today, format: Y-m-d
     */
    //FIXME do_get_today_timeslots

    public function do_get_today_timeslots($player, $today, $with_fillin = true, $with_reservation = false, $company_id = false)
    {
        $this->load->helper("chrome_logger");
        set_time_limit(0);

        $this->load->library("TimeSlot");
        $this->load->model('strategy');
        $this->load->model('device');
        $this->load->model('membership');

        $dedicated_timeslots = array();
        $time_slots = array();


        if (!$player) {
            return false;
        }

        if (!isset($player->campaigns)) {
            $this->fill_player_details($player);
        }

        if (!isset($player->nxslot)) {
            $company = $this->membership->get_company($player->company_id);
            $player->nxslot = $company->nxslot;
        }

        $todaystimer = null;
        if ($player->timer_config_id && isset($player->timers['type'])) {
            $ptimer = $player->timers;
            if ($ptimer['type'] != 0) {
                $weekd = date("w", strtotime($today));
                if ($weekd == 0) {
                    $weekd = 7;
                }
                if ($ptimer['offwds'] && in_array($weekd, $ptimer['offwds'])) {

                    return false;
                }
                $todaystimer = $ptimer['data'][$weekd];
            } else {
                $todaystimer = $ptimer['data'][0];
            }
        }

        $quota = 100;

        if ($this->config->item('with_partners') && $company_id) {

            if (isset($player->partners) && isset($player->partners[$company_id])) {
                $quota = $player->partners[$company_id]->quota;
            }

            //skipping the date that player was taken 100% by partners
            if ($quota == 0) {
                return false;
            }
            $time_slots = $this->get_time_slots_byTimer($todaystimer, $quota);
        } else {
            $time_slots = $this->get_time_slots_byTimer($todaystimer);
        }

        if (!$time_slots) {
            return false;
        }


        $todaycampaigns = $player->campaigns;

        $publishedcampaigns = false;
        $trail_campaigns = false;
        $fillin_campaigns = false;
        $extended_campaigns = false;


        if ($todaycampaigns) {
            $publishedcampaigns = array_filter($todaycampaigns, function ($value) use ($today, $with_reservation, $company_id) {
                if (($value->priority == 1 || $value->priority == 2 || ($with_reservation && $value->priority == 5) || $value->priority == 7)) {
                    if ($today > $value->end_date || $today < $value->start_date) {
                        return false;
                    } else {
                        if (isset($value->media)) {
                            $cam_media = array_filter($value->media, function ($medium) use ($today, $value) {
                                if ($medium['date_flag'] == '0' || ($medium['date_flag'] == '1' && $today >= $medium['start_date'] && $today <= $medium['end_date'])) {
                                    return true;
                                }
                                return false;
                            });

                            if (count($cam_media) == 0) {
                                return false;
                            }
                        } elseif ($value->priority == 5) {
                            return true;
                        } else {
                            return false;
                        }


                        if ($company_id && $value->company_id != $company_id) {
                            return false;
                        }
                        return true;
                    }
                }
                return false;
            });
            $extended_campaigns = array_filter($todaycampaigns, function ($value) use ($today, $company_id) {
                if ($value->priority == 8) {
                    if ($today >= $value->start_date && $today <= $value->end_date) {
                        if ($company_id && $value->company_id != $company_id) {
                            return false;
                        }
                        return true;
                    }
                }
                return false;
            });


            if ($publishedcampaigns) {
                foreach ($publishedcampaigns as $cam) {
                    $cur_cam = clone $cam;
                    if ($this->config->item('with_partners')) {
                        if ($cam->play_cnt_type == 1) {
                            if (isset($player->partners) && isset($player->partners[$cam->company_id])) {

                                $cur_cam->play_weight = $cam->play_weight * ($player->partners[$cam->company_id]->quota / 100);
                            }
                        }
                    }
                    //TODO: get extended campaign id;
                    if ($extended_campaigns) {
                        $extended_id_array = array();
                        $has_replace_main = false;
                        foreach ($extended_campaigns as $ext_cam) {
                            if ($ext_cam->main_campaign_id == $cam->id) {
                                $extended_id_array[] = $ext_cam->id;
                                if ($ext_cam->is_replace_main) {
                                    $has_replace_main = true;
                                }
                            }
                        }
                        if (!empty($extended_id_array)) {
                            $cur_cam->extended_campaigns_id = $extended_id_array;
                            $cur_cam->has_replace_main = $has_replace_main;
                        }
                    }
                    $ret = $this->try_allocate_campaign($time_slots, $cur_cam);
                }
            }

            if ($with_fillin) {
                if ($this->config->item('campaign_with_tags')) {
                    $trail_campaigns = array_filter($todaycampaigns, function ($value) use ($today, $company_id) {
                        if ($value->priority == 4) {
                            if ($today >= $value->start_date && $today <= $value->end_date) {
                                if ($company_id && $value->company_id != $company_id) {
                                    return false;
                                }

                                return true;
                            }
                        }
                        return false;
                    });
                }

                $fillin_campaigns = array_filter($todaycampaigns, function ($value) use ($today) {
                    if ($value->priority == 3 || $value->priority == 6) {
                        if ($today >= $value->start_date && $today <= $value->end_date) {
                            return true;
                        }
                    }
                    return false;
                });



                if ($this->config->item('campaign_with_tags') && $trail_campaigns) {
                    foreach ($trail_campaigns as $trail) {
                        if ($this->config->item('xslot_on')) {
                            $trail->nxslot = $player->nxslot;
                        }
                        if ($this->config->item('with_partners')) {
                            if ($trail->play_cnt_type == 1) {
                                if (isset($player->partners) && isset($player->partners[$trail->company_id])) {
                                    //$cam->quota = $player->partners[$trail->company_id]->quota;
                                    $cam->play_weight = $cam->play_weight * ($player->partners[$cam->company_id]->quota / 100);
                                }
                            }
                        }

                        $this->try_allocate_campaign($time_slots, $trail);
                    }
                }


                if ($fillin_campaigns) {
                    $ret = $this->try_fill_slots($time_slots, $fillin_campaigns, $player);
                }
            }
        }


        if (!$with_fillin && $publishedcampaigns == false) {
            return $time_slots;
        }

        $time_slots = array_filter($time_slots, function ($value) {
            $re = true;
            if (!isset($value->campaigns) || empty($value->campaigns) || $value->total_time == 0) {
                $re = false;
            }
            return $re;
        });


        $time_slots = $this->multi_array_sort($time_slots, "startH", SORT_ASC);



        return $time_slots;
    }

    public function multi_array_sort($multi_array, $sort_key, $sort = SORT_ASC)
    {
        $key_array = array();
        if (is_array($multi_array)) {
            foreach ($multi_array as $row_array) {
                if (is_array($row_array)) {
                    $key_array[] = $row_array[$sort_key];
                } elseif (is_object($row_array)) {
                    $key_array[] = $row_array->$sort_key;
                }
            }
        } else {
            return false;
        }
        array_multisort($key_array, $sort, $multi_array);
        return $multi_array;
    }

    /**
     * 取出排序好的Slot里面所有campaign的media file列表

     */

    public function shuffle_assoc($list)
    {
        if (!is_array($list)) {
            return $list;
        }
        $keys = array_keys($list);
        shuffle($keys);
        $random = array();
        foreach ($keys as $key) {
            $random[] = $list[$key];
        }

        return $random;
    }



    public function get_sorted_timeslot_medias($slot, $checkday = false)
    {
        $medias = array();
        $campaign_medias_ary = array();
        $total_count = 0;

        if ($slot->campaigns == null || count($slot->campaigns) == 0) {
            return $medias;
        }


        $slot->used_time = 0;

        $cam_media_ary = array();
        $fillin_medias = array();

        $fill_in_count = 0;


        //取出Xslot的档案列表并安装A1B1C1..A2B2C2排列好.
        //计算有几个x slot 从第一个位置一直到第x个位置, 按照x步长排列好
        //

        if ($checkday) {
            $today = $checkday;
        } else {
            $today = date("Y-m-d");
        }

        foreach ($slot->campaigns as $key => $cam) {
            $playllist_id_array = array();
            if (isset($cam['extended_campaigns_id'])) {
                $playllist_id_array = $cam['extended_campaigns_id'];
                //if extended campaign has replace main, remove media from the main campaign
                if (!$cam['has_replace_main']) {
                    array_push($playllist_id_array, $cam['compaign_id']);
                }
            }

            $cam_medias = $this->get_playlist_area_media_list_noexclude($playllist_id_array ? $playllist_id_array : $cam['compaign_id'], $this->config->item('area_video'), false, $today);



            if ($cam_medias['total'] == 0) {
                unset($slot->campaigns[$key]);
                continue;
            }


            //Total
            if ($cam['playcnt_type'] == 2) {
                $total_count += $cam['count'] * count($cam_medias['data']);
            } else {
                if ($cam['is_grouped']) {
                    $total_count += $cam['count'] * count($cam_medias['data']);
                } else {
                    $total_count += $cam['count'];
                }
            }

            foreach ($cam_medias['data'] as $medium) {
                $medium->replacable = ($cam['priority'] == 3 || $cam['priority'] == 6 || $cam['priority'] == 7) ? 1 : 0;
            }

            /*
            if ($cam['playcnt_type']==3) {
                $fill_in_count += $cam['count'];
                $fill_media=array();
                if ($cam_medias['total']>$cam['count']) {
                    $fill_media = array_slice($cam_medias['data'], 0, $cam['count']);
                } else {
                    $fill_media = $cam_medias['data'];
                }
                $fillin_medias = array_merge($fillin_medias, $fill_media);
                unset($slot->campaigns[$key]);
            } else
            */ {
                $cam_media_ary[$cam['compaign_id']] =  $cam_medias['data'];
            }
        }



        $temp_ary =  array();
        $xcam_index = 0;
        $prev_xcam_index = 0;
        $pos = 0;

        $xslot_cams = array_filter($slot->campaigns, function ($cam) {
            $re = true;
            if ($cam['playcnt_type'] != 9) {
                return false;
            }
            return true;
        });


        if ($this->config->item('campaign_with_tags')) {
            if ($xslot_cams) {
                $arr = array_values($xslot_cams);
                $cnt = count($arr);

                for ($i = 0; $i < $cnt - 1; $i++) {
                    $insertions = array_intersect($arr[$i]['tags'], $arr[$i + 1]['tags']);
                    if (!empty($insertions)) {
                        for ($j = $cnt - 1; $j > $i; $j--) {
                            $insertions = array_intersect($arr[$i]['tags'], $arr[$j]['tags']);
                            if (empty($insertions)) {
                                $tmp = $arr[$j];
                                $arr[$j] = $arr[$i + 1];
                                $arr[$i + 1] = $tmp;
                                break;
                            }
                        }
                    }
                }
                $xslot_cams = $arr;
            }
        }


        foreach ($xslot_cams as $slot_cam) {
            $count = 0;
            $pos = $xcam_index;
            while ($count < $slot_cam['count']) {
                $temp_ary[$pos] = $slot_cam;
                $pos += $slot_cam['xslot'];
                $count++;
            }
            $xcam_index++;
        }



        $i = 0;

        foreach ($slot->campaigns as $campaign) {
            if ($campaign['playcnt_type'] == 9) {
                continue;
            }
            $pos = 0;
            $count = 0;


            while ($count < $campaign['count']) {
                while (array_key_exists($pos, $temp_ary)) {
                    $pos++;
                }

                $temp_ary[$pos] = $campaign;

                $count++;
                $pos = intval(($total_count / $campaign['count']) * $count);
            }
        }



        ksort($temp_ary);
        foreach ($temp_ary  as $item) {
            $type = $item['playcnt_type'];
            $index = $item['compaign_id'];
            $grouped = $item['is_grouped'];

            if ($type == 2 || $grouped) {
                $media_ary = $cam_media_ary[$index];
                $medias = array_merge($medias, $media_ary);
            } else {
                $medias[] = current($cam_media_ary[$index]);

                if (next($cam_media_ary[$index]) == false) {
                    reset($cam_media_ary[$index]);
                }
            }
        }

        return $medias;
    }


    public function get_playlist_media_playtime($playlist_id, $time_to_check = false)
    {
        $sql = "SELECT m.play_time 
       FROM cat_playlist cp, cat_playlist_area_media p, cat_media m
       WHERE m.id = p.media_id
       AND cp.id = p.playlist_id
       AND p.status =0
       AND p.playlist_id = $playlist_id 
       order BY p.position asc";


        $query = $this->db->query($sql);
        $numrows = $query->num_rows();
        if ($numrows) {
            $data = array_column($query->result_array(), 'play_time');
            $query->free_result();


            if ($time_to_check) {
                if (count(array_unique($data)) > 1) {
                    return false;
                } else {
                    if ($data[0] != $time_to_check) {
                        return false;
                    }
                }
            }
            return $data;
        }
        return false;
    }


    public function get_campaign_user_players($uid)
    {
        /*
        $sql = "SELECT GROUP_CONCAT(DISTINCT(p.criterias)) as ids
       FROM cat_playlist p,cat_user_campaign uc
       WHERE p.id=uc.campaign_id
       AND   uc.user_id=$uid 
       GROUP BY p.criterias";
*/
        $this->db->select('pc.player_id as id');
        $this->db->from("cat_player_campaign pc");
        $this->db->join('cat_user_campaign uc', "uc.campaign_id = pc.campaign_id", "LEFT");
        $this->db->where('uc.user_id', $uid);

        $query = $this->db->get();

        if ($query->num_rows()) {
            $data = $query->result_array();
            return array_column($data, 'id');
        }
        return false;
    }



    public function update_campaigns_with_reload()
    {
        $now = date("Y-m-d H:i:s");
        $sql = "update cat_playlist set update_time ='$now' where id in ( SELECT DISTINCT playlist_id FROM cat_playlist_area_media WHERE reload=1)";
        $query = $this->db->query($sql);
    }


    public function get_players_capcity($players, $startDate = false, $endDate = false, $cam_startH = false, $cam_stopH = false, $company_id = false)
    {
        //$this->load->helper("chrome_logger");
        $runtime_start = microtime(true);
        $this->load->model('strategy');

        $this->load->library("TimeSlot");

        $day7_capcity = 0.00;
        $day15_capcity = 0.00;
        $result = array("day7_capcity" => 0, 'day15_capcity' => 0, 'total_capcity' => 0, 'nextmon_capacity' => 0, 'next6mon_capacity' => 0);
        $is_date_set = false;

        if ($startDate && $endDate) {
            // $startStamp = strtotime($startDate);
            //$endStamp = strtotime($endDate);
        } else {
            if (!$startDate) {

                $startDate = date('Y-m-d', strtotime('tomorrow'));
            }
            $next_7day_date = date(strtotime($startDate . '+7 days'));

            $next_mon_first_date = date("Y-m-d", strtotime($startDate . 'first day of next month'));
            $next_mon_end_date = date("Y-m-d", strtotime($startDate . 'last day of next month'));
            $next_6mon_end_date =  date('Y-m-d', strtotime($startDate . ' +6 months'));

            $endDate = $next_6mon_end_date;
            $is_date_set = true;
        }

        //$at_date = $startStamp;

        $begin = new DateTime($startDate);
        $end = new DateTime($endDate);

        $end->modify('+1 day');
        //$end->setTime(0, 0, 1);
        $interval = DateInterval::createFromDateString('1 day');
        $date_range = new DatePeriod($begin, $interval, $end);


        $data = array();


        foreach ($players as $key => $player) {
            if (!$player->setupdate || $player->setupdate == '0000-00-00') {
                continue;
            }
            $least_free = -1;

            //$checkday = $startStamp;
            $daycount = 0;
            $total_secs = 0;
            $used_secs = 0;
            $quota = 100;

            $next_month_total_offset = 0;
            $next_month_used_offset = 0;
            $this->fill_player_details($player);




            $quota = 100;
            if ($this->config->item('with_partners') && $company_id) {
                if (isset($player->partners) && isset($player->partners[$company_id])) {
                    $quota = $player->partners[$company_id]->quota;
                }

                //skipping the player was taken 100% by partners
                if ($quota == 0) {
                    $data[$player->id] = array('id' => $player->id, 'name' => $player->name, 'quota' => $quota, 'least_free' => 0, 'total_free' => 0, 'total_capcity' => 0, 'total_secs' => 0, "day7_capcity" => 100, 'day15_capcity' => 100, 'nextmon_capacity' => 100, 'next6mon_capacity' => 100);
                    continue;
                }
            }

            foreach ($date_range as $dt) {
                $checkday = $dt->format("Y-m-d");

                if ($is_date_set && $checkday == $next_mon_first_date) {
                    $next_month_total_offset = $total_secs;
                    $next_month_used_offset = $used_secs;
                }

                $timeslots = $this->do_get_today_timeslots($player, $checkday, false, true, $company_id);

                if ($timeslots && is_array($timeslots)) {
                    foreach ($timeslots as $slot) {
                        $slot_total = $slot->total_time;
                        if ($company_id) {
                            $slot_total = $slot_total * ($quota / 100);
                        }
                        if ($cam_startH !== false && $cam_stopH !== false) {
                            if ($slot->startH >= $cam_startH && $slot->startH < $cam_stopH) {
                                $total_secs += $slot_total;
                                $used_secs += $slot->used_time;
                            }
                        } else {
                            $total_secs += $slot_total;
                            $used_secs += $slot->used_time;
                        }
                        //$this->load->helper('chrome_logger');
                        if ($slot->total_time == 3600 && ($least_free == -1 || ($slot_total - $slot->used_time) < $least_free)) {

                            if ($slot->quota != 100) {
                                $least_free = round($slot->total_time * ($slot->quota / 100)) - $slot->used_time;
                            } else {
                                $least_free =  $slot_total - $slot->used_time;
                            }

                            $quota = $quota;
                        }
                    }
                }

                if ($total_secs > 0 && $is_date_set) {
                    if ($checkday == $next_7day_date) {
                        $result['day7_capcity'] = ceil($used_secs * 100 / $total_secs);
                    } elseif ($checkday == $next_mon_end_date) {
                        if ($total_secs - $next_month_total_offset == 0) {
                            $result['nextmon_capacity'] = 0;
                        } else {
                            $result['nextmon_capacity'] = ceil(($used_secs - $next_month_used_offset) * 100 / ($total_secs - $next_month_total_offset));
                        }
                    } elseif ($checkday == $next_6mon_end_date) {
                        if ($total_secs - $next_month_total_offset == 0) {
                            $result['next6mon_capacity'] = 0;
                        } else {
                            $result['next6mon_capacity'] = ceil(($used_secs - $next_month_used_offset) * 100 / ($total_secs - $next_month_total_offset));
                        }
                    }
                }

                //$checkday = strtotime("+1 days", $checkday);
            }
            if ($total_secs > 0) {
                $result['least_free'] = round($least_free, 2);
                $result['total_free'] = $total_secs - $used_secs;
                $result['total_capcity'] = ceil($used_secs * 100 / $total_secs);
                $result['total_secs'] = $total_secs;
                $result['quota'] = $quota;
            } else {
                $result['least_free'] = 0;
                $result['total_free'] = 0;
                $result['total_capcity'] = 0;
                $result['total_secs'] = 0;
                $result['quota'] = 100;
            }
            $result['id'] = $player->id;
            $result['name'] = $player->name;
            $result['sn'] = $player->sn;
            $result['date'] =  $startDate;

            $data[$player->id] = $result;
        }
        // $runtime_stop = microtime(true);
        // chrome_log("<!-- Processed in ".round($runtime_stop-$runtime_start, 6)." second(s) -->");

        return $data;
    }

    private function is_ssl()
    {
        if (isset($_SERVER['HTTPS']) && ('1' == $_SERVER['HTTPS'] || 'on' == strtolower($_SERVER['HTTPS']))) {

            return true;
        } else if (isset($_SERVER['SERVER_PORT']) && ('443' == $_SERVER['SERVER_PORT'])) {

            return true;
        }

        return false;
    }

    public function get_playlist_media_cnt($playlist_id)
    {
        $sql = "SELECT COUNT( p.id ) AS media_cnt
        FROM cat_playlist_area_media p 
        LEFT JOIN cat_playlist cp ON cp.id = p.playlist_id 
        WHERE cp.id = p.playlist_id
        AND p.playlist_id =" . $playlist_id;

        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $data = $query->row();
            $query->free_result();
            return $data->media_cnt;
        } else {
            return false;
        }
    }
    public function sync_players($id, $playerstr, $type = 0)
    {
        $this->detach_players($id, $type);
        $this->attach_players($id, $playerstr, $type);
    }
    public function attach_players($id, $inplayers, $type = 0)
    {
        $players = is_array($inplayers) ? $inplayers : explode(",", $inplayers);

        if (empty($players)) {
            return;
        }
        $data = array();
        $this->db->trans_start();
        foreach ($players as $pid) {
            if (!empty($pid)) {
                $item = array('player_id' => $pid, 'campaign_id' => $id, 'type' => $type);
                $this->db->insert('cat_player_campaign', $item);
            }
        }
        $this->db->trans_complete();
    }
    public function detach_players($id, $type = 0)
    {
        $this->db->where('campaign_id', $id);
        $this->db->where('type', $type);
        $this->db->delete('cat_player_campaign');
    }


    public function sync_campaign_player($id, $data)
    {
        $this->db->insert('campaign_player', $data);
    }

    public function saveManyCampaignPlayer($data)
    {
        if (empty($data)) {
            return;
        }
        $this->db->trans_start();
        foreach ($data as $item) {
            $this->db->insert('campaign_player', $item);
        }
        $this->db->trans_complete();
    }


    public function detach_campaign_player($cam_id)
    {
        if (is_array($cam_id)) {
            $this->db->where_in('campaign_id', $cam_id);
        } else {
            $this->db->where('campaign_id', $cam_id);
        }
        $this->db->delete('campaign_player');
    }

    public function detach_campaign_player_byPlayerId($pid)
    {
        $this->db->where('player_id', $pid);
        $this->db->delete('campaign_player');
    }


    public function update_reserved_campaigns()
    {
        $this->db->where("published", 1);
        $this->db->where('priority', 5);
        $this->db->where('start_date', date('Y-m-d'));
        $this->db->select('*');
        $this->db->from('cat_playlist');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $rezerved_cams =  $query->result();
            foreach ($rezerved_cams as $cam) {
                $this->reset_player_least_while_update_campaign($cam);
            }

            $this->db->where("published", 1);
            $this->db->where('priority', 5);
            $this->db->where('start_date', date('Y-m-d'));
            $this->db->update('cat_playlist', array('published' => 0));
        }
    }

    public function do_publish_time_slot($playlist, $players = null)
    {
        $this->load->helper('chrome_logger');

        set_time_limit(0);
        $msg = '';
        $this->lang->load('campaign');

        $this->load->library("TimeSlot");

        $this->load->helper('file');
        $this->load->helper('media');
        $runtime_start = microtime(true);
        $this->load->model('membership');
        $this->load->model('device');

        $this->load->model('strategy');

        if (!$playlist) {
            $msg = $this->lang->line("playlist.error.not.exist");
            return array('code' => 1, 'msg' => $msg);
        }


        $company = $this->membership->get_company($playlist->company_id);

        if (!$players) {
            $players = $this->get_player_by_campaign($playlist->id);
        }


        if (!$players) {
            $msg = $this->lang->line('campaign.error.not.assigned');
            $msg = $msg . "<p>&nbsp</p>";

            return array('code' => 1, 'msg' => $msg);
        }

        if ($this->config->item("with_template")) {
            $msg = $this->lang->line('playlist.publish.success');
            return array('code' => 0, 'msg' => $msg);
        }


        if ($playlist->media_cnt == 0 && $playlist->priority != 5) {
            $msg = $playlist->name . "<br>" . $this->lang->line("warn.publish.empty.media");
            return array('code' => 1, 'msg' => $msg);
        }

        if ($playlist->priority == 3 || $playlist->priority == 6 || $playlist->priority == 8) {
            $msg = $this->lang->line('playlist.publish.success');
            return array('code' => 0, 'msg' => $msg);
        }

        foreach ($players as $player) {
            $this->fill_player_details($player);
        }

        $start_date = $playlist->start_date;
        $end_date = $playlist->end_date;


        $today = date('Y-m-d');
        if ($start_date < $today) {
            $start_date = $today;
        }

        if ($playlist->end_date < $today) {
            $msg = $this->lang->line("campaign.expired.date");
            return  array('code' => 1, 'msg' => $msg);
        }

        $begin = new DateTime($start_date);
        $end = new DateTime($playlist->end_date);
        $end = $end->modify('+1 day');
        $interval = new DateInterval('P1D');
        $date_range = new DatePeriod($begin, $interval, $end);

        $this->fill_campaign_media_info($playlist);


        if ($playlist->priority != 3 && $playlist->priority != 6 && $playlist->play_cnt_type == 2) {
            $totalmin = $this->get_total_minutes($playlist, $players);

            if ($totalmin == 0) {
                $playcountperhour = 0;
                $msg = sprintf($this->lang->line('campaign.ob.no.intersection'), $playlist->name, "whole day off");
                $msg = $msg . "<p>&nbsp</p>";
                return array('code' => 1, 'msg' => $msg);
            } else {
                $playcountperhour = ($playlist->play_total) / ($totalmin / 60);
                $playcountperhour = ceil($playcountperhour);
            }


            if ($playcountperhour == 0) {
                $msg = sprintf($this->lang->line("campaign.total.too.small"), $playlist->name, ceil($totalmin / 60));
                $msg = $msg . "<p>&nbsp</p>";
                return array('code' => 1, 'msg' => $msg);
            }


            if (($playcountperhour * 10) > 3600) {
                $max = floor($totalmin / 60) * 360;
                $msg = sprintf($this->lang->line("campaign.ob.tv.too.big"), $playlist->name, $max);
                return array('code' => 1, 'msg' => $msg);
            }


            $playlist->play_totalperhour = $playcountperhour;

            $this->update_playlist(array('play_totalperhour' => $playcountperhour), $playlist->id);
        }



        $extra_code = 0;
        $extra_msg = '';

        if ($playlist->play_cnt_type == 1) {
            $original_percentage =  $playlist->play_weight;
        }


        //TODO: delete planed recored for the campign&player on that day
        $least_arrays = array();
        $media_planed_array = array();


        foreach ($players as $player) {
            $runtime_start1 = microtime(true);
            $today = $start_date;
            $quota = 100;

            if ($this->config->item('with_partners')) {
                if (isset($player->partners[$playlist->company_id])) {
                    $quota = $player->partners[$playlist->company_id]->quota;
                    if ($playlist->play_cnt_type == 1 && $quota <= 100) {
                        $playlist->play_weight = $original_percentage * ($quota / 100);
                    }
                }
                /*
                $partners = $this->get_partners_by_player($player->id, $player->company_id);


                if (isset($partners[$playlist->company_id])) {
                    $quota = $partners[$playlist->company_id]->quota;
                    //skipping player that was taken 100% by partners
                    if ($quota == 0) {
                        continue;
                    }
                    if ($playlist->play_cnt_type == 1 && $quota <= 100) {
                        $playlist->play_weight = $original_percentage * ($quota / 100);
                    }
                }
                */
                $partners = $player->partners;
                if (isset($partners[$playlist->company_id])) {
                    $quota = $partners[$playlist->company_id]->quota;
                    //skipping player that was taken 100% by partners
                    if ($quota == 0) {
                        continue;
                    }
                    if ($playlist->play_cnt_type == 1 && $quota <= 100) {
                        $playlist->play_weight = $original_percentage * ($quota / 100);
                    }
                }
            }


            if ($playlist->is_grouped && $playlist->play_cnt_type == 1 && $playlist->total_time > 3600 * ($playlist->play_weight / 100)) {
                $msg = sprintf($this->lang->line('campaign.percentage.too.small'));

                $msg = $msg . "<p>&nbsp</p>";
                return array('code' => 1, 'msg' => $msg);
            }


            //$ptimer = $this->strategy->get_timer_details($player->timer_config_id);
            //$publishedcampaigns =  $this->get_published_campaign_by_player($player->id, -1, 9);
            $ptimer = $player->timers;
            $publishedcampaigns =  $player->campaigns;



            $company_id = $playlist->company_id;
            //get campiangs belongs to the publiing playlist's company
            if ($publishedcampaigns) {
                $publishedcampaigns =  array_filter($publishedcampaigns, function ($value) use ($company_id, $playlist) {
                    if ($value->priority == 1 || $value->priority == 2 || $value->priority == 5 || $value->priority == 7) {
                        if ($value->id == $playlist->id) {
                            return false;
                        }
                        if ($value->company_id == $company_id && $value->media_cnt) {
                            return true;
                        }
                    }
                    return false;
                });
            }

            if ($this->config->item('campaign_with_tags') && $publishedcampaigns) {
                if (($playlist->priority != 3) && $playlist->tags) {
                    $same_tag_campaigns = array_filter($publishedcampaigns, function ($value) use ($playlist) {
                        if (
                            (strtotime($value->start_date) < strtotime($playlist->end_date) && strtotime($value->start_date) >= strtotime($playlist->start_date)) && $value->tags
                        ) {
                            //both have time range
                            if (!$playlist->time_flag && !$value->time_flag) {
                                if (($value->start_timeH >= $playlist->end_timeH || $value->end_timeH <= $playlist->start_timeH)) {
                                    return false;
                                }
                            }
                            return true;
                        }
                        return false;
                    });


                    foreach ($same_tag_campaigns as $cam) {
                        if (isset($cam->tags) && $cam->tags) {
                            if ($cam->tag_options == 2 || $playlist->tag_options == 2) {
                                $intersets = array_intersect(explode(',', $playlist->tags), explode(',', $cam->tags));
                                if (!empty($intersets)) {
                                    $intersetstags =  $this->material->get_tagname_byids($intersets);

                                    $msg = sprintf($this->lang->line('campaign.exclusive.intersection'), $cam->name, $intersetstags);
                                    return array('code' => 1, 'msg' => $msg);
                                }
                            }
                        }
                    }
                }
            }
            $weekd = 0;




            foreach ($date_range as $checkday) {

                $today = $checkday->format("Y-m-d");

                $todaystimer = null;

                if ($ptimer) {
                    if ($ptimer['type'] != 0) {
                        //$weekd = date("w", $today);
                        $weekd = $checkday->format('w');
                        if ($weekd == 0) {
                            $weekd = 7;
                        }

                        if ($ptimer['offwds'] && in_array($weekd, $ptimer['offwds'])) {
                            continue;
                        }
                        $todaystimer = $ptimer['data'][$weekd];
                    } else {
                        $todaystimer = $ptimer['data'][0];
                    }
                }
                $time_slots = array();

                $today_campaigns = null;



                if ($publishedcampaigns && !empty($publishedcampaigns)) {
                    $today_campaigns = array_filter($publishedcampaigns, function ($value) use ($today) {
                        if ($today >= $value->start_date && $today <= $value->end_date) {
                            return true;
                        }
                        return false;
                    });
                }

                $time_slots = $this->get_time_slots_byTimer($todaystimer, $quota);


                if (!$time_slots || empty($time_slots)) {
                    //OVERBOOKING, no time slots left
                    $msg = sprintf($this->lang->line('campaign.ob.no.intersection'), $player->name);
                    return array('code' => 1, 'msg' => $msg);
                }


                if ($today_campaigns != false) {
                    foreach ($today_campaigns as $cam) {

                        if ($this->config->item('xslot_on')) {
                            $cam->nxslot = $company->nxslot;
                        }

                        $minfo = $this->get_campaign_media_info_by_date($cam,  $today);

                        if ($minfo['media_cnt']) {
                            $cam->media_cnt = $minfo['media_cnt'];
                            $cam->total_time = $minfo['total_time'];
                        } else {
                            //no media on that day
                            continue;
                        }

                        $cur_cam = clone $cam;

                        if ($this->config->item('with_partners')) {

                            if (isset($partners[$cam->company_id])) {
                                $quota = $partners[$cam->company_id]->quota;
                                //skipping player that was taken 100% by partners
                                if ($quota == 0) {
                                    continue;
                                }
                                if ($cam->play_cnt_type == 1 && $quota <= 100) {
                                    $cur_cam->play_weight = $cam->play_weight * ($quota / 100);
                                }
                            }
                        }


                        $ret = $this->try_allocate_campaign($time_slots, $cur_cam);
                        if ($ret['status'] == false) {
                            $msg = sprintf($this->lang->line('campaign.ob.comon'),  $today, $cam->name, $player->name);
                            $msg = $msg . $this->lang->line('campaign.ob.opiton1') . $this->lang->line('campaign.ob.opiton2') . $this->lang->line('campaign.ob.opiton3');
                            $msg = $msg . "<p>&nbsp</p>";
                            return array('code' => 1, 'msg' => $msg);
                        }
                    }
                }


                if ($playlist->priority == 1 || $playlist->priority == 2 || $playlist->priority == 4 || $playlist->priority == 5 || $playlist->priority == 7) {
                    if ($this->config->item('xslot_on')) {
                        $playlist->nxslot = $company->nxslot;
                    }
                    $playlist_minfo = $this->get_campaign_media_info_by_date($playlist, $today);



                    if ($playlist_minfo['media_cnt']) {
                        $playlist->media_cnt = $playlist_minfo['media_cnt'];
                        $playlist->total_time = $playlist_minfo['total_time'];
                    } else {
                        //no media on that day
                        //$today = strtotime("+1 days", $today);
                        continue;
                    }

                    $ret = $this->try_allocate_campaign($time_slots, $playlist);

                    if ($ret['status'] == false) {

                        $msg = sprintf($this->lang->line('campaign.ob.comon'),  $today, $playlist->name, $player->name, $today);
                        $msg = $msg . $this->lang->line('campaign.ob.opiton1') . $this->lang->line('campaign.ob.opiton2') . $this->lang->line('campaign.ob.opiton3');
                        $msg = $msg . "<p>&nbsp</p>";
                        return array('code' => 1, 'msg' => $msg);
                    } else {

                        //-----planed records--------------
                        $today_media = false;

                        $media = $playlist->media;


                        if ($media) {
                            $today_media = array_filter($media, function ($medium) use ($today) {
                                if ($medium['date_flag'] == 0 || ($medium['date_flag'] == 1 && $today >= $medium['start_date'] && $today <= $medium['end_date'])) {
                                    return true;
                                }
                                return false;
                            });
                            if ($today_media) {


                                $media_cnt = count($today_media);
                                //$media_planed_array = [];

                                $planed_cnt = $ret['day_used'];
                                $booked_cnt = $ret['day_booked'];

                                foreach ($today_media as $medium) {
                                    //if campaign is grouped or total(type=2), media of campiang will play $ret times
                                    //otherwise media will play $ret/total media times

                                    $medium_planed = 0;
                                    if ($playlist->play_cnt_type == 2 || $playlist->is_grouped) {
                                        $medium_planed =  $planed_cnt;
                                        $medium_booked =  $booked_cnt;
                                    } else {
                                        $medium_planed = ceil($planed_cnt / $media_cnt);
                                        $medium_booked = ceil($booked_cnt / $media_cnt);
                                    }
                                    $medium_planed = array(
                                        'player_id' => $player->id,
                                        'campaign_id' => $playlist->id,
                                        'medium_id' => $medium['area_media_id'],
                                        'date' =>  $today,
                                        'planed_times' => $medium_planed,
                                        'booked_times' => $medium_booked
                                    );

                                    $media_planed_array[] = $medium_planed;
                                }
                                //$this->db->insert_batch('cat_player_campaign_planed', $media_planed_array);
                            }
                        }
                    }

                    if (!$company->pId) {
                        //chrome_log($leastfree);
                        $least_arrays[] = $this->get_least_from_timeslot($player->id,  $today, $time_slots);
                    }
                }


                $time_slots = null;


                //$today = strtotime("+1 days", $today);
            } //Date

            //chrome_log("<!-- Processed player in " . round(microtime(true) - $runtime_start1, 6) . " second(s) -->");
        } //Player
        $this->delete_planed_records($playlist->id,  $start_date);
        if (!empty($media_planed_array)) {
            $this->device->saveMany_Planed($media_planed_array);
        }

        if (!empty($least_arrays)) {
            $this->db->trans_start();
            foreach ($players as $player) {

                $this->device->delete_player_least_free($player->id, $start_date, $end_date);
            }
            $this->db->trans_complete();
            $this->device->saveMany_least_free($least_arrays);
        }

        //chrome_log("<!-- Processed in " . round(microtime(true) - $runtime_start, 6) . " second(s) -->");

        $msg = $this->lang->line('playlist.publish.success');
        return array('code' => 0, 'msg' => $msg, 'extra_code' => $extra_code, 'extra_msg' => $extra_msg);
    }



    public function get_total_minutes($playlist, $players)
    {
        $totalmin  = 0;
        if ($playlist->priority != 3 && $playlist->play_cnt_type == 2) {

            $this->load->model('strategy');

            $player_count = count($players);
            if (!$playlist->time_flag) {
                $start_hour = $playlist->start_timeH;
                $end_hour = $playlist->end_timeH;
            }

            foreach ($players as $p) {
                $ptimer = $this->strategy->get_timer_details($p->timer_config_id);

                $daytocheck = strtotime($playlist->start_date);
                $enddate = strtotime($playlist->end_date);

                while ($daytocheck < $enddate) {
                    $daytimer = null;
                    $yestodaytimer = null;

                    if ($ptimer) {
                        $weekd = 0;
                        //if it's not dialy timer
                        if ($ptimer['type'] != 0) {
                            $weekd = date("w", $daytocheck);
                            if ($weekd == 0) {
                                $weekd = 7;
                            }

                            if ($ptimer['offwds'] && in_array($weekd, $ptimer['offwds'])) {
                                $daytocheck = strtotime("+1 days", $daytocheck);
                                continue;
                            }
                            $daytimer = $ptimer['data'][$weekd];

                            $previousweekd =  date("w", strtotime("-1 days", $daytocheck));
                            $yestodaytimer = $ptimer['data'][$previousweekd];
                        } else {
                            $daytimer = $ptimer['data'][0];
                            $yestodaytimer = $daytimer;
                        }


                        if ($daytimer[0]['startH'] != 0 && $daytimer[0]['endH'] != 24) {
                            foreach ($yestodaytimer as $timer) {
                                $startH = $timer['startH'];
                                $endH = $timer['endH'];
                                $startM = $timer['startM'];
                                $endM = $timer['endM'];
                                if (($endH != 0 && $endH < $startH) || ($endH == $startH && $endM < $startM)) {
                                    $startH = 0;
                                    $startM = 0;

                                    //if playlist has time range
                                    if (!$playlist->time_flag) {

                                        if ($start_hour >= $endH || $end_hour <= $startH) {
                                            break;
                                        } else {
                                            $left = 0;
                                            if ($start_hour > $startH) {
                                                $startH = $start_hour;
                                                $startM = 0;
                                            }
                                            if ($end_hour < $endH) {
                                                $endH = $end_hour;
                                                $endM = 0;
                                            }

                                            $todaymins = ($endH - $startH) * 60 - $startM + $endM;
                                            $totalmin += $todaymins;
                                        }
                                    } else {
                                        $totalmin += ($endH - $startH) * 60 - $startM + $endM;
                                    }
                                }
                            }
                        }


                        foreach ($daytimer as $timer) {
                            $startH = $timer['startH'];
                            $endH = $timer['endH'];
                            $startM = $timer['startM'];
                            $endM = $timer['endM'];

                            if ($endH < $startH || ($endH == $startH && $endM < $startM) || ($endH == 0 && $endM == 0)) {
                                $endH = 24;
                                $endM = 0;
                            }

                            if (!$playlist->time_flag) {
                                if ($start_hour >= $endH || $end_hour <= $startH) {
                                    continue;
                                } else {
                                    $left = 0;
                                    if ($start_hour > $startH) {
                                        $startH = $start_hour;
                                        $startM = 0;
                                    }
                                    if ($end_hour < $endH) {
                                        $endH = $end_hour;
                                        $endM = 0;
                                    }

                                    $todaymins = ($endH - $startH) * 60 - $startM + $endM;

                                    $totalmin += $todaymins;
                                }
                            } else {
                                $totalmin += ($endH - $startH) * 60 - $startM + $endM;
                            }
                        }
                    } else {
                        if (!$playlist->time_flag) {
                            $totalmin += ($end_hour - $start_hour) * 60;
                        } else {
                            $totalmin += 24 * 60;
                        }
                    }

                    $daytocheck = strtotime("+1 days", $daytocheck);
                } //while
            }
        }
        return $totalmin;
    }


    public function get_time_slots_byTimer($todaystimer, $quota = 100)
    {
        $time_slots = null;
        $duration = 3600;

        if (!$todaystimer) {
            for ($hour = 0; $hour < 24; $hour++) {
                $slot = $this->new_time_slot($duration, $hour, 0, $hour + 1, 0);
                $slot->quota = $quota;

                $time_slots[] = $slot;
            }
        } else {
            foreach ($todaystimer as $timer) {
                $startH = $timer['startH'];
                $endH = $timer['endH'];
                $startM = $timer['startM'];
                $endM = $timer['endM'];

                if ($endH < $startH || ($endH == $startH && $endM < $startM) || ($endH == 0 && $endM == 0)) {
                    $endH = 24;
                    $endM = 0;
                }

                if ($startH == $endH) {
                    $duration = ($endM - $startM) * 60;
                    $slot = $this->new_time_slot($duration, $startH, $startM, $endH, $endM);
                    $slot->quota = $quota;

                    if ($slot->total_time >= 300) {
                        $time_slots[] = $slot;
                    }
                } else {
                    for ($hour = $startH; $hour <= $endH; $hour++) {
                        if ($hour == $startH && $startM != 0) {
                            $duration = (60 - $startM) * 60;
                            $slot = $this->new_time_slot($duration, $hour, $startM, $hour + 1, 0);
                        } elseif ($hour == $endH) {
                            if ($endM != 0) {
                                $duration = $endM * 60;
                                $slot = $this->new_time_slot($duration, $hour, 0, $hour, $endM);
                            } else {
                                break;
                            }
                        } else {
                            $duration = 3600;
                            $slot = $this->new_time_slot($duration, $hour, 0, $hour + 1, 0);
                        }
                        if ($slot->total_time >= 300) {
                            $slot->quota = $quota;

                            $time_slots[] = $slot;
                        }
                    }
                }
                //ignore slot < 5 mintues
            }
        }
        return $time_slots;
    }

    public function get_partners_by_player($player_id, $parent_id)
    {
        /*
        //get indivisual player settings

        $partner_settings = null;
        $this->db->select("p.partner_id,p.player_quota as quota,p.shareblock");
        $this->db->from('cat_parter_fields p');
        $this->db->join('cat_company c', 'c.id = p.partner_id and c.flag=0');
        $this->db->join('cat_partner_players pp', 'pp.partner_id=p.partner_id', "LEFT");
        $this->db->where('pp.player_id', $player_id);
        $this->db->distinct();
        $query = $this->db->get();
        if ($query->num_rows()) {
            $partner_settings = $query->result();
        } else {
            //get by critia
            $this->db->select("p.partner_id,p.quota,p.shareblock");
            $this->db->from('cat_parter_fields p');
            $this->db->join('cat_company c', 'c.id = p.partner_id and c.flag=0');
            $this->db->join('cat_criteria_player cp', "cp.criteria_id = p.criterion_id ", 'LEFT');
            $this->db->where('cp.player_id', $player_id);

            $this->db->distinct();

            $query = $this->db->get();

            if ($query->num_rows()) {
                $partner_settings =  $query->result();
            }
        }
        */

        $partner_settings = null;
        //get indivisual player settings
        $individual_settings = null;
        $this->db->select("p.partner_id,p.player_quota as quota,p.shareblock");
        $this->db->from('cat_parter_fields p');
        $this->db->join('cat_company c', 'c.id = p.partner_id and c.flag=0');
        $this->db->join('cat_partner_players pp', 'pp.partner_id=p.partner_id', "LEFT");
        $this->db->where('pp.player_id', $player_id);
        $this->db->distinct();
        $query = $this->db->get();
        if ($query->num_rows()) {
            $individual_settings = $query->result();
        }

        //get by critia
        $this->db->select("p.partner_id,p.quota,p.shareblock");
        $this->db->from('cat_parter_fields p');
        $this->db->join('cat_company c', 'c.id = p.partner_id and c.flag=0');
        $this->db->join('cat_criteria_player cp', "cp.criteria_id = p.criterion_id ", 'LEFT');
        $this->db->where('cp.player_id', $player_id);

        $this->db->distinct();

        $query = $this->db->get();

        if ($query->num_rows()) {
            $criteria_partner_settings =  $query->result();
            if ($individual_settings) {
                //merge individual settings and criteria settings into a new array, and if there are same partner_id, use individual settings. if they don't have same partner_id, add individual settings to the end of array
                foreach ($criteria_partner_settings as $setting) {
                    $found = false;
                    foreach ($individual_settings as $key => $item) {
                        if ($setting->partner_id == $item->partner_id) {
                            $partner_settings[] = $item;
                            unset($individual_settings[$key]);
                            $found = true;
                            break;
                        }
                    }
                    if (!$found) {
                        $partner_settings[] = $setting;
                    }
                }
                $partner_settings = array_merge($partner_settings, $individual_settings);
            } else {
                $partner_settings = $criteria_partner_settings;
            }
        } else {
            $partner_settings = $individual_settings;
        }

        if ($partner_settings) {
            $partners = null;
            if ($partner_settings) {
                $parter_used = 0;
                foreach ($partner_settings as $setting) {
                    //$parter_used += (($setting->quota / 3600) * 100);
                    $item = new StdClass();
                    $item->partner_id = $setting->partner_id;
                    $item->quota = ($setting->quota / 3600) * 100;
                    $parter_used += $item->quota;
                    $item->shareblock = $setting->shareblock;
                    $partners[$item->partner_id] = $item;
                }
                $parent = new StdClass();
                $parent->partner_id = $parent_id;
                $parent->quota = 100 - $parter_used;
                $parent->shareblock = 1;
                $partners[$parent->partner_id] = $parent;
            } else {
                $parent = new StdClass();
                $parent->partner_id = $parent_id;
                $parent->quota = 100;
                $parent->shareblock = 1;
                $partners[$parent->partner_id] = $parent;
            }
        } else {
            $parent = new StdClass();
            $parent->partner_id = $parent_id;
            $parent->quota = 100;
            $parent->shareblock = 1;
            $partners[$parent->partner_id] = $parent;
        }

        return $partners;
    }

    //TODO try fill empty slots base on simple or fill-in campaigns
    public function try_fill_slots(&$time_slots, $fill_cams, $player)
    {
        if (isset($player->partners) && $player->partners) {
            foreach ($player->partners as $partner_id => $partner) {
                //skip parent company,最后才排parent
                if ($partner_id == $player->company_id) {
                    continue;
                }
                $simple_campaigns = array_filter($fill_cams, function ($value) use ($partner_id) {
                    if ($value->company_id == $partner_id && $value->priority == 6) {
                        return true;
                    }
                    return false;
                });

                if ($partner->shareblock) {
                    $fillin_campaigns = array_filter($fill_cams, function ($value) use ($partner_id) {
                        if ($value->company_id == $partner_id && $value->priority == 3) {
                            return true;
                        }
                        return false;
                    });
                } else {
                    $fillin_campaigns = null;
                }

                if (!$simple_campaigns && !$fillin_campaigns) {
                    continue;
                }


                if ($simple_campaigns) {
                    foreach ($time_slots as $slot) {
                        $slot->fill_with_campaigns($simple_campaigns, $partner);
                    }
                }
                if ($fillin_campaigns) {
                    foreach ($time_slots as $slot) {
                        foreach ($time_slots as $slot) {
                            $slot->fill_with_campaigns($fillin_campaigns, $partner);
                        }
                    }
                }
            }

            //Parent company
            if (isset($player->partners[$player->company_id])) {
                $partner = $player->partners[$player->company_id];
                $partner_id = $player->company_id;

                $simple_campaigns = array_filter($fill_cams, function ($value) use ($partner_id) {
                    if ($value->company_id == $partner_id && $value->priority == 6) {
                        return true;
                    }
                    return false;
                });


                $fillin_campaigns = array_filter($fill_cams, function ($value) use ($partner_id) {
                    if ($value->company_id == $partner_id && $value->priority == 3) {
                        return true;
                    }
                    return false;
                });

                if (!$simple_campaigns && !$fillin_campaigns) {
                    return;
                }


                if ($simple_campaigns) {
                    foreach ($time_slots as $slot) {
                        $slot->fill_with_campaigns($simple_campaigns, $partner);
                    }
                }
                //to fill up empty slots with parent company's fill-in
                if ($fillin_campaigns) {
                    foreach ($time_slots as $slot) {
                        //chrome_log("=-============startFill:" . $slot->startH);
                        $slot->fill_with_campaigns($fillin_campaigns);
                    }
                }
            }
        }
    }


    public function fill_player_details(&$player)
    {
        $player->partners = $this->get_partners_by_player($player->id, $player->company_id);

        if ($player->timer_config_id) {
            $this->load->model('strategy');
            $player->timers = $this->strategy->get_timer_details($player->timer_config_id);
        } else {
            $player->timers = null;
        }


        $player->campaigns = $this->get_published_campaign_by_player($player->id, -1, 9);
    }

    public function fill_campaign_media_info($cam)
    {
        if ($this->config->item('with_template')) {
            return;
        }
        $this->db->select("m.id,m.play_time,m.date_flag,m.start_date,m.end_date, pm.id as area_media_id");
        $this->db->from('cat_media m');
        $this->db->join("cat_playlist_area_media pm", "pm.media_id = m.id");
        $this->db->where('pm.playlist_id', $cam->id);
        $query = $this->db->get();

        if ($query->num_rows()) {
            $media = $query->result_array();
            $cam->media = $media;
            $cam->media_cnt = count($media);
            $cam->total_time = array_sum(array_column($media, 'play_time'));
        } else {
            $cam->media = null;
            if ($cam->priority == 5) {
                $cam->media_cnt = 1;
                $cam->total_time = 10;
            } else {
                $cam->media_cnt = 0;
                $cam->total_time = 0;
            }
        }
    }
    public function get_campaign_media_info_by_date($cam, $today)
    {
        if (isset($cam->media) && $cam->media) {
            $today_media = false;
            $media_cnt = 0;
            $total_time = 0;
            $media = $cam->media;
            if ($media) {
                $today_media = array_filter($media, function ($medium) use ($today) {
                    if ($medium['date_flag'] == 0 || ($medium['date_flag'] == 1 && $today >= $medium['start_date'] && $today <= $medium['end_date'])) {
                        return true;
                    }
                    return false;
                });
                if ($today_media) {
                    $media_cnt = count($today_media);
                    $total_time = array_sum(array_column($today_media, 'play_time'));
                }
            }
        } else {
            if ($cam->priority == 5) {
                $media_cnt = 1;
                $total_time = 10;
            } else {
                $media_cnt = 0;
                $total_time = 0;
            }
        }
        return array('media_cnt' => $media_cnt, 'total_time' => $total_time);
    }

    /*************
     * get campaign's excluded players
     */
    public function get_excluded_player_by_campaign($camid)
    {
        $this->db->select("p.id,p.name,p.timer_config_id,p.company_id,pe.custom_sn1,pe.custom_sn2");
        $this->db->from("cat_player p");
        $this->db->join('cat_player_campaign pc_ex', "pc_ex.player_id = p.id and pc_ex.type=1");
        $this->db->join("cat_player_extra pe", 'pe.player_id = p.id', 'left');
        $this->db->where("pc_ex.campaign_id", $camid);

        $query = $this->db->get();
        $cnt = $query->num_rows();
        if ($query->num_rows() > 0) {
            return $query->result();
        }
        return false;
    }

    private function get_least_from_timeslot($player_id, $date, $timeslots)
    {
        $maxused = 0;
        $least_data =  array('player_id' => $player_id, 'at_date' => $date, 'least_free' => 3600, 'partner_used' => 0, 'h0' => null, 'h1' => null, 'h2' => null, 'h3' => null, 'h4' => null, 'h5' => null, 'h6' => null, 'h7' => null, 'h8' => null, 'h9' => null, 'h10' => null, 'h11' => null, 'h12' => null, 'h13' => null, 'h14' => null, 'h15' => null, 'h16' => null, 'h17' => null, 'h18' => null, 'h19' => null, 'h20' => null, 'h21' => null, 'h22' => null, 'h23' => null);
        $rezerved = 0;
        foreach ($timeslots as $slot) {
            if ($slot->total_time == 3600) {
                if ($slot->quota < 100 && !$rezerved) {
                    $rezerved = round(3600 * ((100 - $slot->quota) / 100));
                }
                if ($maxused < $slot->used_time) {
                    //$maxused = $slot->used_time + $rezerved;
                    $maxused = $slot->used_time;
                }
                //$least_data["h" . $slot->startH] = 3600 - $slot->used_time - $rezerved;
                $least_data["h" . $slot->startH] = 3600 - $slot->used_time;
            }
        }

        $least_data['partner_used'] = $rezerved;
        $least_data['least_free'] = 3600 - $maxused;
        return $least_data;
        // return array('player_id' => $player_id, 'at_date' => $date, 'least_free' => 3600 - $maxused);
    }



    //重新发布的时候如果daterange有变化也需要调用这个

    //换天的时候,从发布状态变成未发布的时候.
    public function reset_player_least_while_update_campaign($playlist)
    {
        if (!$playlist) {
            return 0;
        }
        if ($playlist->published != 1) {
            return 0;
        }
        if ($playlist->priority == 3 || $playlist->priority == 6) {
            return 0;
        }


        $players = $this->get_player_by_campaign($playlist->id);
        if (!$players) {
            return -1;
        }

        foreach ($players as $player) {
            $player->partners = $this->get_partners_by_player($player->id, $player->company_id);
        }

        $this->load->model('membership');
        if ($this->config->item('xslot_on')) {
            $company = $this->membership->get_company($playlist->company_id);
            $playlist->nxslot = $company->nxslot;
        }

        $this->load->model('device');

        $company_id = $playlist->company_id;

        $start_date = strtotime($playlist->start_date);
        $pl_start_date = $start_date;
        $end_date = strtotime($playlist->end_date);

        $today = strtotime(date('Y-m-d'));

        if ($start_date < $today) {
            $start_date = $today;
        }

        $checkday = $start_date;

        while ($checkday <= $end_date) {
            $playlist_minfo = $this->get_campaign_media_info_by_date($playlist, date('Y-m-d', $checkday));

            if ($playlist_minfo['media_cnt']) {
                $playlist->media_cnt = $playlist_minfo['media_cnt'];
                $playlist->total_time = $playlist_minfo['total_time'];
            } else {
                //no media on that day
                $checkday = strtotime("+1 days", $checkday);
                continue;
            }

            $pl_used = 0;
            if ($playlist->play_cnt_type == 1) {
                $pl_used = round(3600 * ($playlist->play_weight / 100));
            }
            //xlost
            elseif ($playlist->play_cnt_type == 9) {
                $pl_used =  round(3600 * (1 / $playlist->nxslot));
            }
            //Total time
            elseif ($playlist->play_cnt_type == 2) {
                //if total time is zero, continue
                $pl_used = round($playlist->play_totalperhour * $playlist->total_time);
            }
            //Times per hour
            elseif ($playlist->play_cnt_type == 0) {
                if ($playlist->is_grouped) {
                    $pl_used = round($playlist->play_count * $playlist->total_time);
                } else {
                    $pl_used = round($playlist->play_count * ($playlist->total_time / $playlist->media_cnt));
                }
            }


            $this->db->trans_start();
            foreach ($players as $player) {
                //don't update parter compaigns;
                if ($player->company_id != $playlist->company_id) {
                    continue;
                }
                $real_used = $pl_used;
                if ($playlist->play_cnt_type == 1 && isset($player->partners) && isset($player->partners[$company_id])) {
                    $real_used = round($pl_used * ($player->partners[$company_id]->quota / 100));
                    if (!$real_used) {
                        continue;
                    }
                }
                if (!$playlist->time_flag) {
                    $start_hour = $playlist->start_timeH;
                    $end_hour = $playlist->end_timeH;
                } else {
                    $start_hour = 0;
                    $end_hour = 24;
                }

                $current_data = $this->get_player_lest_free($player->id, date("Y-m-d", $checkday));
                if ($current_data) {
                    $free_value = $current_data['least_free'] + $real_used;
                    if ($free_value > 3600) {
                        $free_value = 3600;
                    }
                    $least = $free_value;

                    for ($i =  $start_hour; $i < $end_hour; $i++) {

                        if ($current_data['h' . $i] !== null) {

                            $current_data['h' . $i] = $current_data['h' . $i] + $real_used;
                            if ($current_data['h' . $i] > 3600) {
                                $current_data['h' . $i] = 3600;
                            }
                            if ($least > $current_data['h' . $i]) {
                                $least = $current_data['h' . $i];
                            }
                        }
                    }


                    $current_data['least_free'] = $least;
                    $this->db->where('id', $current_data['id']);
                    $this->db->update('cat_player_leastfree', $current_data);
                }


                //$this->update_player_leat_free($player->id, date("Y-m-d", $checkday), $real_used);
            }
            $this->db->trans_complete();
            $checkday = strtotime("+1 days", $checkday);
        }
    }

    public function get_player_lest_free($player_id, $date)
    {
        $this->db->select("*");
        $this->db->from("cat_player_leastfree");
        $this->db->where('player_id', $player_id);
        $this->db->where("at_date", $date);

        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->row_array();
        }
        return false;
    }

    public function update_player_leat_free($pid, $date, $pl_free)
    {
        $sql = "update cat_player_leastfree set least_free=(case when least_free+$pl_free<3600 then least_free+$pl_free else 3600 end) where player_id=$pid AND at_date=" . '"' . $date . '"';
        return $this->db->query($sql);
        /*
        $this->db->where('player_id', $pid);
        $this->db->where('at_date', $date);
        $this->db->update('least_free', $pl_free);
        */
    }
    public function delete_player_least_free($player_id)
    {
        $this->db->where('player_id', $player_id);
        $this->db->delete('cat_player_leastfree');
    }


    public function getPlaylistAreaMedia($playlist_id, $area_id, $flag = -1)
    {
        $data = array();

        $this->db->select('m.*, p.status, p.playlist_id, p.media_id,p.reload,p.transmode, p.id as area_media_id, f.name as folder_name,u.name as author');
        $this->db->from('cat_playlist_area_media p');
        $this->db->join('cat_media m', 'm.id = p.media_id', 'left');
        $this->db->join('cat_media_folder f', 'f.id=m.folder_id', 'left');
        $this->db->join('cat_user u', 'u.id=m.add_user_id', 'left');
        $this->db->where('p.playlist_id', $playlist_id);
        $this->db->where('p.area_id', $area_id);

        if ($flag >= 0) {
            $this->db->where('p.flag', $flag);
        }
        $this->db->order_by('p.position', 'asc');


        $query = $this->db->get();
        $total = $query->num_rows();
        if ($total > 0) {
            $data = $query->result();
            $query->free_result();
        }

        return array('total' => $total, 'data' => $data);
    }

    public function get_campaigns_count($cid, $uid = FALSE)
    {
        $this->db->select('id,published');
        $this->db->from('cat_playlist');
        $this->db->where('company_id', $cid);
        if ($uid) {
            $this->db->where('add_user_id', $uid);
        }
        $this->db->where('deleted_at is null');
        $query = $this->db->get();
        $total = $query->num_rows();
        $data = array();
        if ($total > 0) {
            $data = $query->result();
            $query->free_result();
        }
        return array('total' => $total, 'data' => $data);
    }

    public function get_campaign_players_count($camId)
    {
        $this->db->select('player_id');
        $this->db->from('campaign_player');
        $this->db->where("campaign_id", $camId);
        $query = $this->db->get();
        return $query->num_rows();
    }
    public function get_campaign_ex_players_count($camId)
    {
        $this->db->select('player_id');
        $this->db->from('cat_player_campaign');
        $this->db->where("campaign_id", $camId);
        $this->db->where("type", 1);
        $query = $this->db->get();
        return $query->num_rows();
    }



    public function update_playlist_text($array, $playlist_id)
    {
        $this->db->where('playlist_id', $playlist_id);
        $this->db->delete("cat_playlist_text");


        if (empty($array)) {
            return 0;
        }

        if ($this->db->insert('cat_playlist_text', $array)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function get_playlist_text($id)
    {
        $this->db->where('playlist_id', $id);
        $query = $this->db->get('cat_playlist_text');

        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return FALSE;
        }
    }


    public function save_campaign_xml($cam_id)
    {
        $id = $cam_id;

        $this->load->model('template');
        $this->load->helper('media');
        $this->load->helper('xml');

        $transmodemapping = $this->config->item('media.transmode.mapping');
        $playlist = $this->get_playlist($cam_id);

        $hasMoive = false;
        if ($playlist) {
            $template = $this->template->get_template($playlist->template_id);
            $areas = $this->template->get_area_list($playlist->template_id);
            $screen_attr['count'] = count($areas);
            if (isset($playlist->master_area_id) && $playlist->master_area_id) {
                $screen_attr['anchor'] = $playlist->master_area_id;
            }
            $array = [
                "Templatename" => $template->name,
                'Programme' => [
                    '_attributes' => ["id" => $playlist->id, "name" => $playlist->name, "playtime" => "00:00:00"],
                    'ScreenType' => [
                        '_attributes' => ["height" => $template->height, "width" => $template->width, "rotation" => "0"],
                    ],

                    'Screen' => [
                        '_attributes' => $screen_attr,
                    ],
                ],


            ];

            $areaLists = array();
            $tickerList = null;
            $transmodemapping = $this->config->item('media.transmode.mapping');
            $root = [
                'rootElementName' => 'SignwayPoster',
                "_attributes" => ['type' => "playlist", "version" => "1.0.1"],
            ];
            foreach ($areas as $area) {
                if ($area->area_type == $this->config->item('area_type_id')) {
                    continue;
                }
                $areaList = array();
                $left = number_format(($area->x / $template->w) * 100, 2) . '%';
                $top =  number_format(($area->y / $template->h) * 100, 2) . '%';
                $width =  number_format(($area->w / $template->w) * 100, 2) . '%';
                $height = number_format(($area->h / $template->h) * 100, 2) . '%';

                $attributes = [
                    "id" => $area->id,
                    "name" =>  $area->name,
                    "model" => $area->area_type,
                    "left" => $left,
                    "top" => $top,
                    "width" => $width,
                    "height" => $height,
                    "zindex" => $area->zindex
                ];
                if (
                    $area->area_type == $this->config->item('area_type_date')
                    || $area->area_type == $this->config->item('area_type_time')
                    || $area->area_type == $this->config->item('area_type_weather')
                ) {

                    $settings = $this->template->get_area_extra_setting($area->id);
                    //$xml .= sprintf('< bold="%d" color="%s" bgcolor="%s" family="%s" size="%spx" style="%d" bgmix="%d%%" type="%d" lang="%d"/>', $area->id, $area->name, $area->area_type, ($area->x / $template->w) * 100, ($area->y / $template->h) * 100, ($area->w / $template->w) * 100, ($area->h / $template->h) * 100, $area->zindex, $setting->bold, $setting->color, $setting->bg_color, $setting->font_family, $setting->font_size, $setting->style, $setting->transparent, $setting->format, $setting->language) . $this->sep;
                    if ($settings) {
                        if ($area->area_type == $this->config->item('area_type_weather')) {
                            $setting = [
                                'bold' => 0,
                                'color' => $settings->color,
                                'bgcolor' => $settings->bg_color,
                                'family' => $settings->font_family,
                                'size' => $settings->font_size . 'px',
                                'style' => $settings->style,
                                "bgmix" => $settings->transparent . '%',
                                'type' => 0,
                            ];
                        } else {
                            $setting = [
                                'bold' => 0,
                                'color' => $settings->color,
                                'bgcolor' => $settings->bg_color,
                                'family' => $settings->font_family,
                                'size' => $settings->font_size . 'px',
                                'style' => $settings->style,
                                "bgmix" => $settings->transparent . '%',
                                'type' => 0,
                                'lang' => 0,
                            ];
                        }
                        $attributes = array_merge($attributes, $setting);
                    }
                }
                if (
                    $area->area_type == $this->config->item('area_type_movie')
                    || $area->area_type == $this->config->item('area_type_image')
                    || $area->area_type == $this->config->item('area_type_logo')
                    || $area->area_type == $this->config->item('area_type_mask')
                ) {
                    $hasMoive = true;
                    $medias = $this->get_playlist_area_media_list($id, $area->id, 0);

                    if ($medias['total'] > 0) {
                        $areaList = ['_attributes' => ["id" => $area->id, "playtime" => "00:00:10"]];
                        foreach ($medias['data'] as $medium) {
                            //exclude status is not checked
                            if (!$medium->status) {

                                $resource = [
                                    "_attributes" => [
                                        "id" => $medium->id,
                                        "name" => $medium->name,
                                        "fid" => $medium->id,
                                        "sw5159Size" => $medium->file_size,
                                        "signature" => $medium->signature,
                                        "sw5159Signature" => $medium->signature,
                                        "transmode" => $medium->transmode >= 0 ? $transmodemapping[$medium->transmode] : 0,
                                        "transittime" => "0.5",
                                        "mode" => $area->area_type,
                                        "fillmode" => 1,
                                        "startdate" => $medium->date_flag ? $medium->start_date : "",
                                        "enddate" => $medium->date_flag ? $medium->end_date : "",
                                        "cleardate" => "",
                                        "duration" => gmdate("H:i:s", (int)$medium->play_time), //"00:" . $medium->duration,
                                        'reload' => 0,
                                    ],
                                    'URL' => $medium->full_path,
                                ];
                                $areaList['Resource'][] = $resource;
                            }
                        }
                        $areaLists[] = $areaList;
                    }
                } else if ($area->area_type == $this->config->item('area_type_text')) {
                    $tickerList = ['_attributes' => ["id" => $area->id, "playtime" => "00:00:10"]];
                    $settings = $this->template->get_area_extra_setting($area->id);
                    $text = $this->get_playlist_text($cam_id);

                    if ($settings) {

                        $setting = [
                            'face' => $settings->font_family,
                            'size' => $settings->font_size,
                            'color' => $settings->color,
                            'bgcolor' => $settings->bg_color,
                            "bgmix" => $settings->transparent . '%',
                            'direction' => $settings->direction,
                            'speed' => $settings->style,
                            'align' => "0",
                            'valign' => "0",
                            'duration' => "",
                        ];
                        // $attributes = array_merge($attributes, $setting);
                        $ticker = [
                            'Text' => [
                                "_attributes" => $setting,
                                "@cdata" => $text ? ' ' . $text->text : ""
                            ]
                        ];
                        $tickerList['Ticker'] = $ticker;
                    }
                } else if ($area->area_type == $this->config->item('area_type_webpage')) {
                    //$webpages = $this->get_playlist_webpage_list($cam_id);
                    $webpages = $this->get_playlist_mce_list($cam_id);
                    if ($webpages['total'] > 0) {
                        $areaList = ['_attributes' => ["id" => $area->id, "playtime" => "00:00:10"]];
                        foreach ($webpages['data'] as $webpage) {

                            $cid = $playlist->company_id;
                            $targetDir =  './resources/' . $cid . "/webpage";
                            $destHtml = $targetDir . '/' . $webpage->mid . ".html";

                            if (!file_exists($targetDir)) {
                                if (!mkdir($targetDir, 0744, true)) {
                                    $result = array('code' => 1, 'msg' => "can not create webapage folder");
                                    echo json_encode($result);
                                    return false;
                                }
                            }


                            $html = HtmlDomParser::str_get_html($webpage->html);

                            if (!$html) {
                                return false;
                            }


                            if ($webpage->text && $webpage->text != '') {
                                $body = $html->find('body', 0);
                                $span =  $body->find('span', 0);
                                if ($span) {
                                    $span->innertext = $webpage->text;
                                } else {
                                    $p =  $body->firstChild();

                                    if ($p->tag == "p" || $p->tag == "h1" || $p->tag == "h2" || $p->tag == "h3" || $p->tag == "h4" || $p->tag == "h5" || $p->tag == "h6" || $p->tag == "pre") {
                                        $p->innertext = $webpage->text;
                                    }
                                }
                            }

                            $html->save($destHtml);

                            $url = ($this->is_ssl() ? "https:" : "http:") . '//' . $_SERVER['HTTP_HOST'] . '/' . substr($destHtml, 1);

                            $resource = [
                                "_attributes" => [
                                    "id" => $webpage->id,
                                    "name" =>  $url,
                                    "fid" => $webpage->id,
                                    "mode" => $area->area_type,
                                    "sw5159Size" => 0,
                                    "startdate" =>  "",
                                    "enddate" => "",
                                    "cleardate" => "",
                                    "duration" => $webpage->play_time,
                                    "refreshtime" => "12:00:00",
                                ],

                                'URL' => $url,
                            ];


                            $areaList['Resource'][] = $resource;
                        }
                    }
                    $areaLists[] = $areaList;
                } else if ($area->area_type == $this->config->item('area_type_bg')) {
                    $this->load->model('template');
                    $medium = $this->template->get_area_image_setting($area->id);

                    if ($medium) {
                        $areaList = ['_attributes' => ["id" => $area->id, "playtime" => "00:00:10"]];
                        $resource = [
                            "_attributes" => [
                                "id" => 'bg_' . $medium->id,
                                "name" => $medium->name,
                                "fid" => 'bg_' . $medium->id,
                                "sw5159Size" => $medium->file_size,
                                "signature" => $medium->signature,
                                "sw5159Signature" => $medium->signature,
                                "transmode" =>  0,
                                "transittime" => "0.5",
                                "mode" => $area->area_type,
                                "fillmode" => 1,
                                "startdate" => "",
                                "enddate" =>  "",
                                "cleardate" => "",
                                "duration" => "23:00:00",
                                'reload' => 0,
                            ],
                            'URL' => $medium->full_path,
                        ];
                        $areaList['Resource'][] = $resource;
                        $areaLists[] = $areaList;
                    }
                }
                $array['Programme']['Screen']["Area"][] = ["_attributes" => $attributes];
            }


            //if ($this->config->item('with_id_zone')) {
            $id_areas = array_filter($areas, function ($area) {
                if ($area->area_type == $this->config->item('area_type_id')) {
                    return true;
                }
                return false;
            });

            if ($id_areas) {
                $areaList = ['_attributes' => ["id" => $area->id, "playtime" => "00:00:10"]];
                $attributes = [
                    "id" => $area->id,
                    "name" =>  "ID",
                    "model" => 7,
                    "left" => 0,
                    "top" => 0,
                    "width" => $template->width,
                    "height" => $template->height,
                    "zindex" => 100
                ];
                $array['Programme']['Screen']["Area"][] = ["_attributes" => $attributes];
                $url = ($this->is_ssl() ? "https:" : "http:") . '//' . $_SERVER['HTTP_HOST'] . '/assets/template.html?playlist_id=' . $cam_id . "&time=" . time();


                $id_duration = $hasMoive ? "00:01:00" : "00:00:10";


                $resource = [
                    "_attributes" => [
                        "id" => $area->id,
                        "name" =>  $url,
                        "fid" => $area->id,
                        "mode" => 7,
                        "sw5159Size" => 0,
                        "startdate" =>  "",
                        "enddate" => "",
                        "cleardate" => "",
                        "duration" => $id_duration,
                        "refreshtime" => "24:00:00",
                    ],

                    'URL' => $url,
                ];

                $areaList['Resource'][] = $resource;
                $areaLists[] = $areaList;
            }
            //}

            if ($tickerList) {
                $areaLists[] = $tickerList;
            }
            $array['Programme']["AreaList"] = $areaLists;

            $arrayToXml = new ArrayToXml($array, $root, false, "UTF-8");


            $xml = $arrayToXml->prettify()->toXml();


            $playlist_path = $this->config->item('playlist_publish_path') . $playlist->company_id;
            if (!file_exists($playlist_path)) {
                mkdir($playlist_path, 0777, TRUE);
            }

            $this->load->helper('file');
            $playlist_path .= '/' . $id . '.PLS';
            saveFile($playlist_path, $xml);

            //update file_size&signature
            $file_size = filesize($playlist_path);
            $signature = md5_file($playlist_path);

            //FIXME
            $this->program->update_affected_players($id);
            //if ($result['code'] == 0) {
            $updates = array('update_time' => date("Y-m-d H:i:s"), 'signature' => $signature, 'file_size' => $file_size, 'published' => $this->config->item('playlist.status.published'));
            $this->update_playlist($updates, $id);
            return true;
        }

        return false;
    }

    public function update_affected_players($cam_id)
    {
        $players = $this->get_playerIds_of_camapaign($cam_id);
        if ($players) {
            $this->db->where_in('id', $players);
            $this->db->update('cat_player', array('campaign_update_time' => date("Y-m-d H:i:s")));
        }
    }

    public function get_playerIds_of_camapaign($id)
    {
        $this->db->select('player_id');
        $this->db->from('campaign_player');
        $this->db->where('campaign_id', $id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $ret =  $query->result_array();
            return array_column($query->result_array(), 'player_id');
        }
        return FALSE;
    }

    public function update_playlist_webpage_batch($array, $playlist_id)
    {
        $this->db->where('playlist_id', $playlist_id);
        $this->db->delete("cat_playlist_webpage");

        if (empty($array)) {
            return 0;
        }

        if ($this->db->insert_batch('cat_playlist_webpage', $array)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    public function update_playlist_mce_batch($array, $playlist_id)
    {
        $this->db->where('playlist_id', $playlist_id);
        $this->db->delete("cat_playlist_mce");

        if (empty($array)) {
            return 0;
        }

        if ($this->db->insert_batch('cat_playlist_mce', $array)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }


    public function get_playlist_webpage_list($playlist_id)
    {
        $this->db->select("*");
        $this->db->from('cat_playlist_webpage');
        $this->db->where('playlist_id', $playlist_id);
        $this->db->order_by('position', 'asc');
        $query = $this->db->get();
        $total = $query->num_rows();
        $res = [];
        if ($total) {
            $res = $query->result();
        }
        return array('total' => $total, 'data' => $res);
    }
    public function get_playlist_mce_list($playlist_id)
    {
        $this->db->select("w.*,m.text,m.id as mid");
        $this->db->from('cat_webpage w');
        $this->db->join('cat_playlist_mce m', "m.mce_id=w.id", "RIGHT");
        $this->db->where('m.playlist_id', $playlist_id);
        $this->db->order_by('position', 'asc');
        $query = $this->db->get();
        $total = $query->num_rows();
        $res = [];
        if ($total) {
            $res = $query->result();
        }
        return array('total' => $total, 'data' => $res);
    }

    public function get_campaign($id, $withDetails = FALSE)
    {
        $this->db->where('id', $id);
        $query = $this->db->get('cat_playlist');
        if ($query->num_rows() > 0) {
            $campaign =  $query->row();
            if ($withDetails) {
                $campaign->criteria = $this->get_campaign_criteria($id, 0);
                $campaign->and_criteria = $this->get_campaign_criteria($id, 1);
                $campaign->and_criteria_or = $this->get_campaign_criteria($id, 3);
                $campaign->ex_criteria = $this->get_campaign_criteria($id, 2);
                $campaign->tags =  $this->get_campaign_tags($id);
                $campaign->players = $this->get_campaign_players($id, 0);
                $campaign->ex_players = $this->get_campaign_players($id, 1);
            }
            return $campaign;
        }
        return FALSE;
    }

    public function get_campaign_criteria($id, $type)
    {
        $this->db->select('criterion_id');
        $this->db->from('criterionables');
        $this->db->where('criterionable_id', $id);
        $this->db->where('cam_bindtype', $type);
        if ($this->config->item('with_template')) {
            $this->db->group_start();
            $this->db->where('criterionable_type', 'App\Playlist');
            $this->db->or_where('criterionable_type', 'App\Campaign');
            $this->db->group_end();
        } else {
            $this->db->where('criterionable_type', 'App\Campaign');
        }

        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $ret =  $query->result_array();
            return array_column($query->result_array(), 'criterion_id');
        }
        return FALSE;
    }

    public function get_campaign_tags($id)
    {
        $this->db->select('tag_id');
        $this->db->from('taggables');
        $this->db->where('taggable_id', $id);
        if ($this->config->item('with_template')) {
            $this->db->where('taggable_type', 'App\Playlist');
        } else {
            $this->db->where('taggable_type', 'App\Campaign');
        }

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $ret =  $query->result_array();
            return array_column($query->result_array(), 'tag_id');
        }
        return FALSE;
    }

    public function get_campaign_players($id, $type)
    {
        $this->db->select('player_id');
        $this->db->from('cat_player_campaign');
        $this->db->where('campaign_id', $id);
        $this->db->where('type', $type);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $ret =  $query->result_array();
            return array_column($query->result_array(), 'player_id');
        }
        return FALSE;
    }

    public function get_published_playlist_by_player($player_id, $day = -1)
    {

        $this->db->select("p.*");
        $this->db->join('campaign_player pc', "p.id = pc.campaign_id", 'right');
        $this->db->from("cat_playlist p");


        $this->db->where('p.published', 1);
        $this->db->where('pc.player_id', $player_id);
        if ($day != -1) {
            //$today = date("Y-m-d", $day);
            //$this->db->where('p.start_date<=', $today);
            $this->db->where('p.start_date<=', $day);
            $this->db->where('p.end_date>=', $day);
        }

        $this->db->where('p.deleted_at is null');
        $this->db->order_by('update_time', 'asc');


        $query = $this->db->get();

        if ($query->num_rows()) {
            $cams = $query->result();

            return $cams;
        }
        return false;
    }

    public function refresh_published_playlist($mce_id)
    {

        $this->db->select('p.id');
        $this->db->from('cat_playlist p');
        $this->db->join('cat_playlist_mce pm', 'pm.playlist_id=p.id', 'right');
        $this->db->where('pm.mce_id', $mce_id);
        $this->db->where('p.published', 1);

        $query = $this->db->get();

        if ($query->num_rows()) {
            $cams = $query->result();
            foreach ($cams as $cam) {
                $this->save_campaign_xml($cam->id);
                $this->program->update_playlist(array('update_time' => date('Y-m-d H:i:s')), $cam->id);
            }
        }
    }

    public function delete_planed_records($campaign_id = FALSE, $start_date = FALSE, $player_id = FALSE)
    {
        if ($campaign_id === FALSE &&  $player_id === FALSE) {
            return FALSE;
        }
        if ($campaign_id) {
            $this->db->where('campaign_id', $campaign_id);
        }
        if ($player_id) {
            $this->db->where('player_id', $player_id);
        }
        if ($start_date) {
            $this->db->where('date>=', $start_date);
        }
        $this->db->delete('cat_player_campaign_planed');
    }

    public function add_area_id($array)
    {
        if (empty($array)) {
            return 0;
        }


        if ($this->db->insert('cat_playlist_area_id', $array)) {
            $id = $this->db->insert_id();
            return $id;
        } else {
            return false;
        }

        return false;
    }

    public function update_area_id($array, $id)
    {
        if (empty($array) || $id == 0) {
            return false;
        }
        $this->db->where('id', $id);

        return $this->db->update('cat_playlist_area_id', $array);
    }

    public function getPlaylistAreaID($playlist_id, $area_id = false)
    {
        $data = array();

        //$this->db->select('p.id,p.name,p.area_id,p.id_number,p.descr,s.style as type');
        $this->db->select('p.*');
        $this->db->from('cat_playlist_area_id p');
        //$this->db->join('cat_area_extra_setting s', 's.area_id=p.area_id', 'left');
        $this->db->where('p.playlist_id', $playlist_id);
        if ($area_id !== false) {
            $this->db->where('p.area_id', $area_id);
        }

        $query = $this->db->get();
        $total = $query->num_rows();
        if ($total > 0) {
            if ($area_id === false) {
                $data = $query->result();
            } else {
                $data = $query->row();
            }
            $query->free_result();
            return $data;
        }

        return false;
    }

    public function get_playlist_id_areas($playlist_id)
    {
        $this->load->model('charger_status');
        $template = $this->get_template_of_playlist($playlist_id);
        if ($template) {
            $rate = $template->width / $template->w;
            $this->db->select("i.id_number,i.name,ta.x as left,ta.y as top ,ta.w as width, ta.h as height,s.color,s.font_size,s.transparent,s.bg_color,s.style,s.font_family,s.charger_setting_id, i.type");
            $this->db->from('cat_playlist_area_id i');
            $this->db->join('cat_template_area ta', 'ta.id=i.area_id', 'left');
            $this->db->join('cat_area_extra_setting s', 's.area_id=i.area_id', 'left');
            $this->db->where('i.playlist_id', $playlist_id);

            $query = $this->db->get();
            $total = $query->num_rows();
            if ($total > 0) {
                $zones =  $query->result_array();

                foreach ($zones as &$zone) {
                    if ($zone['type'] == 0) {
                        $zone['settings'] = $this->charger_status->get_status_list($zone['charger_setting_id']);
                    }
                }

                $data['width'] = $template->width;
                $data['height'] = $template->height;
                $data['rate'] = $rate;
                $data['zones'] = $zones;
                return $data;
            }
        }
        return false;
    }

    public function get_template_of_playlist($playlist_id)
    {
        $this->db->select('t.*');
        $this->db->from('cat_playlist p');
        $this->db->join('cat_template t', 't.id=p.template_id', 'left');
        $this->db->where('p.id', $playlist_id);

        $query = $this->db->get();
        $total = $query->num_rows();
        if ($total > 0) {
            return $query->row();
        }
        return false;
    }

    public function delete_playlist_area_media_not_in_list($playlist_id, $area_id, $medium_list)
    {
        $ret = true;

        $this->db->where('playlist_id', $playlist_id);
        if ($area_id) {
            $this->db->where('area_id', $area_id);
        }
        if (is_array($medium_list) && count($medium_list) > 0) {
            $this->db->where_not_in("id", $medium_list);
        }
        $ret = $this->db->delete('cat_playlist_area_media');
        return $ret;
    }

    public function get_players_by_condition($cris, $bind_cris = false, $ex_cris = false, $bind_players = false, $tags = false, $ex_players = false, $bind_cris_or = false)
    {
        if (!$cris && !$bind_cris && !$bind_players) {
            return false;
        }
        $player_ids = false;

        if ($cris) {
            $cris = is_array($cris) ? $cris : explode(',', $cris);
            $this->db->select("cp.id");
            $this->db->from("cat_player cp");
            $this->db->join("cat_criteria_player cc", 'cc.player_id = cp.id', 'left');
            $this->db->where_in('cc.criteria_id', $cris);
            $query = $this->db->get();
            if ($query->num_rows()) {
                $result = $query->result_array();
                $player_ids = array_column($result, 'id');
                $query->free_result();
            }
        }

        //bindCrtieria|bind_criteria_or
        $and_id = null;
        if ($bind_cris) {
            $bindcams = is_array($bind_cris) ? $bind_cris : explode(',', $bind_cris);

            $count = count($bindcams);
            $sql = "SELECT cc.player_id as id FROM cat_criteria_player cc ";
            for ($i = 1; $i < $count; $i++) {
                $sql .= "INNER JOIN cat_criteria_player c$i on cc.player_id=c$i.player_id ";
            }
            $sql .= "WHERE cc.criteria_id = $bindcams[0] ";
            for ($i = 1; $i < $count; $i++) {
                $sql .= "AND c$i.criteria_id = $bindcams[$i] ";
            }
            $query = $this->db->query($sql);
            if ($query->num_rows()) {
                $result = $query->result_array();
                $and_id = array_column($result, 'id');
                $query->free_result();
            }
        }
        if ($bind_cris_or) {
            $bindcams_or = is_array($bind_cris_or) ? $bind_cris_or : explode(',', $bind_cris_or);

            $this->db->select("cp.id");
            $this->db->from("cat_player cp");
            $this->db->join('cat_criteria_player cc', "cc.player_id=cp.id", "LEFT");
            $this->db->where_in("cc.criteria_id", $bindcams_or);
            $query = $this->db->get();
            if ($query->num_rows()) {
                $result = $query->result_array();
                $and_ids_or = array_column($result, 'id');
                if ($and_id) {
                    //$and_id = array_intersect($and_id, $and_ids_or);
                    $and_id = array_unique(array_merge($and_id, $and_ids_or));
                } else {
                    $and_id = $and_ids_or;
                }
                $query->free_result();
            }
        }

        //Criteria&BindCriteria
        if ($and_id) {
            if ($player_ids) {
                $player_ids = array_intersect($player_ids, $and_id);
            }
        }

        //destinated players
        if ($bind_players) {
            $bind_players = is_array($bind_players) ? $bind_players : explode(',', $bind_players);
            if ($player_ids) {
                $player_ids = array_merge($player_ids, $bind_players);
            } else {
                $player_ids = $bind_players;
            }
        }

        //excluded players
        $exclude_id = null;
        if ($ex_cris) {
            $ex_cris = is_array($ex_cris) ? $ex_cris : explode(',', $ex_cris);


            $this->db->select("cp.id");
            $this->db->from("cat_player cp");
            $this->db->join('cat_criteria_player cc', "cc.player_id=cp.id", "LEFT");
            $this->db->where_in("cc.criteria_id", $ex_cris);
            $query = $this->db->get();

            if ($query->num_rows()) {
                $result = $query->result_array();
                $exclude_id = array_column($result, 'id');
                $query->free_result();
            }
        }

        if ($ex_players) {
            $ex_players = is_array($ex_players) ? $ex_players : explode(',', $ex_players);
            if ($exclude_id) {
                $exclude_id = array_merge($exclude_id, $ex_players);
            } else {
                $exclude_id = $ex_players;
            }
        }
        if ($tags) {
            $tags = is_array($tags) ? $tags : explode(',', $tags);

            $this->db->select("taggable_id");
            $this->db->from("taggables");
            $this->db->where("taggable_type", "App\Player");
            $this->db->where_in('tag_id', $tags);
            $this->db->distinct();

            $query = $this->db->get();
            if ($query->num_rows()) {
                $result = $query->result_array();
                $ex_players_withSameTag = array_column($result, 'taggable_id');
                if ($exclude_id) {
                    $exclude_id = array_merge($exclude_id, $ex_players_withSameTag);
                } else {
                    $exclude_id = $ex_players_withSameTag;
                }
            }
        }

        if ($exclude_id && $player_ids) {
            $player_ids = array_diff($player_ids, $exclude_id);
        }


        return $player_ids;
    }

    public function refresh_campaigns_by_player($company_id, $player_id)
    {
        $this->load->helper('chrome_logger');
        $campaigns = $this->program->get_published_campaign($company_id);
        $this->load->model('device');
        $this->detach_campaign_player_byPlayerId($player_id);

        $succeed_camapigns = [];
        $failed_campaigns = [];
        if ($campaigns && $campaigns['total'] > 0) {
            $today = date("Y-m-d");
            foreach ($campaigns['data'] as $campaign) {
                if ($campaign->end_date < $today) {
                    continue;
                }

                //skipping locked campaigns
                if (isset($campaign->is_locked) && $campaign->is_locked) {

                    continue;
                }

                $bind_criteria = $campaign->and_criterias;

                if ($campaign->criterion_id) {
                    if ($bind_criteria) {
                        $bind_criteria = $bind_criteria . "," . $campaign->criterion_id;
                    } else {
                        $bind_criteria = $campaign->criterion_id;
                    }
                }


                $player_ids = $this->get_players_by_condition($campaign->criterias, $bind_criteria, $campaign->ex_criterias, $campaign->players, $campaign->tags, $campaign->ex_players, $campaign->and_criteria_or);

                if (!$player_ids || !in_array(intval($player_id), $player_ids)) {
                    continue;
                }

                $campaign_names[] = $campaign->name;
                $this->program->detach_campaign_player($campaign->id);

                $items = array();
                foreach ($player_ids as $pid) {
                    $items[] = $item = array('player_id' => $pid, 'campaign_id' => $campaign->id);
                }
                $this->program->saveManyCampaignPlayer($items);


                $players = $this->device->get_player_byIDs($player_id);
                $ret = $this->do_publish_time_slot($campaign, $players);
                if ($ret['code'] == 0) {
                    $succeed_camapigns[] = $campaign->name;
                } else {
                    $failed_campaigns[] = $campaign->name;
                }
            }
        }
        return  array('refreshed_list' => $succeed_camapigns, 'failed_list' => $failed_campaigns);
    }

    public function saveMany_Planed($arrays)
    {
        if (empty($arrays)) {
            return false;
        }
        $this->db->trans_start();
        foreach ($arrays as $plan) {
            $this->db->insert('cat_player_campaign_planed',  $plan);
        }
        $this->db->trans_complete();
    }
    public function check_if_criteria_inuse($criteria_id)
    {
        $this->db->select("p.id,p.name");
        $this->db->from('criterionables cm');

        $this->db->join('cat_playlist p', "cm.criterionable_id = p.id", "left");
        $this->db->join('cat_criteria_player cp', "cp.criteria_id = cm.criterion_id", "left");
        $this->db->where('cm.cam_bindtype<', 2);
        if (is_array($criteria_id)) {
            $this->db->where_in('cm.criterion_id', $criteria_id);
        } else {
            $this->db->where('cm.criterion_id', $criteria_id);
        }
        $this->db->where('p.end_date>=', date('Y-m-d'));
        $this->db->where('cp.player_id is not null');

        $query = $this->db->get();
        if ($query->num_rows()) {
            return true;
        }
        return false;
    }
    public function get_main_campaigns($company_id)
    {
        $this->db->select('id,name');
        $this->db->from('cat_playlist');
        $this->db->where('priority!=', 8);
        $this->db->where("deleted_at is null");
        $this->db->where('company_id', $company_id);
        $this->db->where('end_date>=', date('Y-m-d'));
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result();
        }
        return false;
    }


    public function get_selected_campaign($cam_id)
    {
        $this->db->select('p.*');
        $this->db->from('cat_playlist p');
        $this->db->where('p.id', $cam_id);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $campaign =  $query->row();
            $this->db->select("p.id,p.name");
            $this->db->from('campaign_player pc');
            $this->db->join('cat_player p', 'p.id=pc.player_id', 'left');
            $this->db->where('pc.campaign_id', $cam_id);
            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                $campaign->players = $query->result();
            }
            return $campaign;
        }
        return false;
    }

    public function get_extended_campaigns($main_id)
    {
        $this->db->select('*');
        $this->db->from('cat_playlist');
        $this->db->where('priority', 8);
        $this->db->where('main_campaign_id', $main_id);
        $this->db->where('deleted_at is null');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result();
        }
        return false;
    }

    public function get_extended_campaigns_count($main_id)
    {
        $this->db->select('id,is_replace_main');
        $this->db->from('cat_playlist');
        $this->db->where('priority', 8);
        $this->db->where('main_campaign_id', $main_id);
        $this->db->where('deleted_at is null');
        $query = $this->db->get();

        $total = $query->num_rows();
        $replace_media_cnt = 0;
        if ($total > 0) {
            foreach ($query->result() as $row) {
                if ($row->is_replace_main) {
                    $replace_media_cnt++;
                }
            }
        }
        return array('total' => $total, 'replaced_extended_cnt' => $replace_media_cnt);
    }
}
