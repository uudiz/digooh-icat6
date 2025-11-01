<?php

class Device extends MY_Model
{

    /**
     * ï¿½ï¿½È¡Ä³ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½Ã»ï¿½ï¿½Âµï¿½ï¿½ï¿½ï¿½ï¿½Èºï¿½ï¿½
     *
     * @param object $uid
     * @return
     */
    public function get_group_ids($uid)
    {
        $result = array();
        $this->db->select("group_id");
        $query = $this->db->get_where("cat_user_group", array('user_id' => $uid));
        if ($query->num_rows() > 0) {
            //$result = $query->result_array();
            foreach ($query->result() as $row) {
                $result[] = $row->group_id;
            }
        }

        return $result;
    }


    /**
     * ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½
     *
     * @param object $groups
     * @param object $uid
     * @return
     */
    public function assign_group($groups, $uid)
    {
        if (empty($groups)) {
            return false;
        }

        foreach ($groups as $group) {
            $sql = "insert ignore into cat_user_group(user_id, group_id) values($uid, $group)";
            $this->db->query($sql);
        }

        //É¾ï¿½ï¿½ï¿½ï¿½ï¿½Ú·ï¿½ï¿½ï¿½ï¿½Ðµï¿½ï¿½ï¿½
        $sql = "delete from cat_user_group where user_id=$uid and group_id not in(" . implode(',', $groups) . ")";
        $this->db->query($sql);

        return true;
    }

    /**
     * É¾ï¿½ï¿½ï¿½ï¿½ï¿½Ã»ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½Ð±ï¿½
     *
     * @param object $uid
     * @return
     */
    public function delete_assign_group($uid)
    {
        $this->db->where('user_id', $uid);

        return $this->db->delete('cat_user_group');
    }

    /**
     * ï¿½ï¿½È¡ï¿½ï¿½Ë¾ï¿½ï¿½ï¿½ï¿½ï¿½Ðµï¿½ï¿½ï¿½ID
     *
     * @param object $cid
     * @return
     */
    public function get_company_group_ids($cid)
    {
        $result = array();
        $this->db->select("id");
        $query = $this->db->get_where("cat_group", array('company_id' => $cid));
        if ($query->num_rows() > 0) {
            //$result = $query->result_array();
            foreach ($query->result() as $row) {
                $result[] = $row->id;
            }
        }

        return $result;
    }

    /**
     * ï¿½ï¿½È¡È«ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½Ð±ï¿½
     *
     * @param object $cid
     * @return
     */
    public function get_all_group_list($cid, $gids = 0)
    {
        if ($gids !== 0) {
            if (is_array($gids)) {
                if (count($gids) > 0) {
                    $this->db->where_in('id', $gids);
                } else {
                    //ï¿½ï¿½ï¿½ï¿½Ö±ï¿½Ó·ï¿½ï¿½ï¿½Îªï¿½ï¿½
                    return array('total' => 0, 'data' => array());
                }
            } else {
                $this->db->where('id', $gids);
            }
        }
        $this->db->from('cat_group');
        //$this->db->where('company_id', $cid);
        if ($cid !== 0) {
            $this->db->where('company_id', $cid);
        }
        $this->db->order_by('type', 'desc');
        $query = $this->db->get();
        $array = array();

        if ($query->num_rows()) {
            $array = $query->result();
            $query->free_result();
        }

        return $array;
    }





    /**
     * ï¿½ï¿½È¡ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½Ï¢
     *
     * @param object $id
     * @return
     */
    public function get_group($id)
    {
        $this->db->where('id', $id);
        $query = $this->db->get('cat_group');
        if ($query->num_rows()) {
            return $query->row();
        } else {
            return false;
        }
    }
    /**
     * ï¿½ï¿½ï¿½ï¿½ ï¿½ï¿½ï¿½ï¿½ ï¿½ï¿½È¡ï¿½ï¿½ï¿½ï¿½Ï¢
     * 2013-9-17 9:01:10
     * @param object $id
     * @param object $cid
     * @param object $name
     * @return
     */
    public function get_group_byname($id, $cid, $name)
    {
        if ($id > 0) {
            $sql = "select id from cat_group where id != $id and company_id = '$cid' and name = '$name'";
        } else {
            $sql = "select id from cat_group where company_id = '$cid' and name = '$name'";
        }
        $query = $this->db->query($sql);

        if ($query->num_rows()) {
            return $query->row();
        } else {
            return false;
        }
    }

    /**
     * ï¿½ï¿½ï¿½Ó·ï¿½ï¿½ï¿½
     *
     * @param object $array
     * @param object $uid ï¿½ï¿½Ç°ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½
     * @return ï¿½É¹ï¿½ï¿½ï¿½ï¿½ï¿½TRUE
     */
    public function add_group($array, $uid)
    {
        if (empty($array)) {
            return 0;
        }

        $array['add_user_id'] = $uid;

        if ($this->db->insert('cat_group', $array)) {
            $id = $this->db->insert_id();
            $this->user_log($this->OP_TYPE_USER, 'add_group[' . $id . '] name[' . json_encode($array) . ']');
            return $id;
        } else {
            return false;
        }
    }

    /**
     * ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½Ï¢
     *
     * @param object $array
     * @param object $id
     * @return
     */
    public function update_group($array, $id)
    {
        if (empty($array)) {
            return 0;
        }

        $this->db->where('id', $id);
        if ($this->db->update('cat_group', $array)) {
            $this->user_log($this->OP_TYPE_USER, 'update_group[' . $id . '] name[' . json_encode($array) . ']');
            return true;
        } else {
            return false;
        }
    }

    /**
     * É¾ï¿½ï¿½Ò»ï¿½ï¿½ï¿½ï¿½ï¿½é£¬É¾ï¿½ï¿½ï¿½ëµ±Ç°ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½Ð¹ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½
     *
     * 1.É¾ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½
     * 2.É¾ï¿½ï¿½ï¿½Õ¶Ë»ï¿½
     * 3.É¾ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½Ã»ï¿½
     *
     * @param object $group_id
     * @return
     */
    public function delete_group($group_id)
    {
        $this->db->trans_begin();
        $this->db->query('delete from cat_group where id = ' . $group_id);
        $this->db->query('delete from cat_player where group_id = ' . $group_id);
        $this->db->query('delete from cat_user_group where group_id = ' . $group_id);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $this->user_log($this->OP_TYPE_USER, 'delete_group[' . $group_id . ']', $this->OP_STATUS_FAIL);
            return false;
        } else {
            $this->db->trans_commit();
            $this->user_log($this->OP_TYPE_USER, 'delete_group[' . $group_id . ']');
            return true;
        }
    }

    /***********************tag************************************/

    /**
     * ï¿½ï¿½È¡Ä³ï¿½ï¿½ï¿½ï¿½Ë¾ï¿½Âµï¿½tagï¿½Ð±ï¿½
     *
     * @param object $cid

     * @param object $offset [optional]
     * @param object $limit [optional]
     * @return
     */
    public function get_tag_list($cid, $uid = false, $offset = 0, $limit = -1, $order_item = 'name', $order = 'ASC', $name = '')
    {
        //FIXME
        /*
        $tagary = array();
        if ($uid) {
            $this->db->select('GROUP_CONCAT(m.tags) as tagstr');
            $this->db->from('cat_media_folder m');
            $this->db->join('cat_user_folder u', 'm.id=u.folder_id', 'left');
            $this->db->where('u.user_id', $uid);
            $query = $this->db->get();

            if ($query->num_rows()) {
                $data = $query->row();
                $tagary = explode(",", $data->tagstr);
            }
        }
        */

        $this->db->select('count(id) as total');
        $this->db->from("cat_tag");
        if ($cid != 0) {
            $this->db->where('company_id', $cid);
        }

        if ($name != '') {
            $this->db->like('name', $name);
        }

        if ($uid && !empty($tagary)) {
            $this->db->where_in('id', $tagary);
        }

        //echo $this->db->get_sql();die();
        $query = $this->db->get();
        $total = $query->row()->total;

        $this->db->select('g.*,COUNT(p.taggable_id) as player_cnt');
        $this->db->from('cat_tag g');
        $this->db->join('taggables p', "p.tag_id=g.id AND p.taggable_type='App\\\Player'", 'left');
        if ($cid != 0) {
            $this->db->where('g.company_id', $cid);
        }
        $this->db->group_by('g.id');

        if ($name != '') {
            $this->db->like('g.name', $name);
        }


        if ($uid && !empty($tagary)) {
            $this->db->where_in('id', $tagary);
        }

        $this->db->order_by($order_item, $order);
        if ($limit != -1) {
            $this->db->limit($limit, $offset);
        }
        //echo $this->db->get_sql();die();
        $query = $this->db->get();
        $array = array();

        if ($query->num_rows()) {
            $array = $query->result();
            $query->free_result();
        }
        return array('total' => $total, 'data' => $array);
    }


    /**
     * ï¿½ï¿½È¡ï¿½ï¿½ï¿½ï¿½tagï¿½ï¿½Ï¢
     *
     * @param object $id
     * @return
     */
    public function get_tag($id)
    {
        $this->db->select("t.*");
        $this->db->from('cat_tag t');
        $this->db->where('t.id', $id);

        $query = $this->db->get();

        if ($query->num_rows()) {
            $result = $query->row();
            $affectedplayers = $this->get_tag_playerids($id);
            if ($affectedplayers) {
                $result->players = $affectedplayers;
            }
            return $query->row();
        } else {
            return false;
        }
    }


    public function get_tag_playerids($tag_id)
    {
        $this->db->select("taggable_id");
        $this->db->from('taggables');
        $this->db->where('tag_id', $tag_id);
        $this->db->where('taggable_type', "App\Player");

        $query = $this->db->get();

        if ($query->num_rows()) {
            $result = $query->result_array();
            return array_column($result, 'taggable_id');
        } else {
            return false;
        }
    }

    /**
     *
     * @param object $id
     * @param object $cid
     * @param object $name
     * @return
     */
    public function get_tag_byname($id, $cid, $name)
    {
        $name = $this->db->escape_str($name);
        if ($id > 0) {
            $sql = "select id from cat_tag where id != $id and company_id = '$cid' and name = '$name'";
        } else {
            $sql = "select id from cat_tag where company_id = '$cid' and name = '$name'";
        }
        $query = $this->db->query($sql);

        if ($query->num_rows()) {
            return $query->row();
        } else {
            return false;
        }
    }

    /**
     * Create new tag
     *
     * @param object $array
     * @param object $uid
     * @return
     */
    public function add_tag($array, $uid)
    {
        if (empty($array)) {
            return 0;
        }

        $array['add_user_id'] = $uid;

        if ($this->db->insert('cat_tag', $array)) {
            $id = $this->db->insert_id();
            $this->user_log($this->OP_TYPE_USER, 'add_tag[' . $id . '] name[' . json_encode($array) . ']');
            return $id;
        } else {
            return false;
        }
    }

    /**
     * ï¿½ï¿½ï¿½ï¿½tagï¿½ï¿½Ï¢
     *
     * @param object $array
     * @param object $id
     * @return
     */
    public function update_tag($array, $id)
    {
        if (empty($array)) {
            return 0;
        }

        $this->db->where('id', $id);
        if ($this->db->update('cat_tag', $array)) {
            $this->user_log($this->OP_TYPE_USER, 'update_tag[' . $id . '] name[' . json_encode($array) . ']');
            return true;
        } else {
            return false;
        }
    }

    public function create_or_get_tag($name, $cid, $uid)
    {
        $tag = $this->get_tag_byname(0, $cid, $name);
        if ($tag) {
            return $tag->id;
        } else {
            $tag_id = $this->add_tag(array('name' => $name, 'company_id' => $cid), $uid);
            return $tag_id;
        }
    }

    /**
     * É¾ï¿½ï¿½Ò»ï¿½ï¿½tagï¿½ï¿½É¾ï¿½ï¿½ï¿½ëµ±Ç°ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½Ð¹ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½
     *
     * 1.É¾ï¿½ï¿½tag
     *
     * @param object $group_id
     * @return
     */
    public function delete_tag($tag_id)
    {
        $this->db->trans_begin();
        $this->db->query('delete from cat_tag where id = ' . $tag_id);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $this->user_log($this->OP_TYPE_USER, 'delete_tag[' . $tag_id . ']', $this->OP_STATUS_FAIL);
            return false;
        } else {
            /*
            $sql = "SELECT id,tags FROM `cat_media_folder` WHERE find_in_set($tag_id,tags)";
            $query = $this->db->query($sql);

            if ($query->num_rows() > 0) {
                $folder_array = $query->result();
                foreach ($folder_array as $folder) {
                    $this->db->where('id', $folder->id);
                    $tag_ary = explode(',', $folder->tags);

                    $key = array_search($tag_id, $tag_ary);
                    if ($key !== false) {
                        array_splice($tag_ary, $key, 1);
                        $this->db->update('cat_media_folder', array('tags' => implode(",", $tag_ary)));
                    }
                }
            }
            */
            $this->db->trans_commit();

            $this->user_log($this->OP_TYPE_USER, 'delete_tag[' . $tag_id . ']');
            return true;
        }
    }

    /***************************************************************/
    //ï¿½ï¿½È¡ï¿½Ã»ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½Ã»ï¿½ 2013-12-17
    public function get_all_user_by_usergroup($uid)
    {
        $sql = "select distinct user_id from cat_user_group where group_id in (select group_id from cat_user_group where user_id = $uid)";
        $query = $this->db->query($sql);
        $array = array();
        if ($query->num_rows() > 0) {
            $array = $query->result();
            $query->free_result();
            return $array;
        } else {
            return false;
        }
    }
    //ï¿½ï¿½È¡ï¿½Ã»ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½Ã»ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ÆºÍ±ï¿½ï¿½ 2013-12-17
    public function get_all_user_name_by_usergroup($uid)
    {
        $sql = "select distinct g.user_id as id, u.name as name from cat_user_group g, cat_user u where u.id=g.user_id and g.group_id in (select group_id from cat_user_group where user_id = $uid)";
        $query = $this->db->query($sql);
        $array = array();
        if ($query->num_rows() > 0) {
            $array = $query->result();
            $query->free_result();
            return $array;
        } else {
            return false;
        }
    }

    public function get_group_player_count($cids)
    {
        $company = $this->membership->get_company($cids);

        if ($company->pId) {
            $this->db->join('cat_criteria_player c2', 'c2.player_id= p.id', "RIGHT");
            $this->db->where('c2.criteria_id', $company->criterion_id);
            $this->db->where('p.company_id', $company->pId);
        } else {
            $this->db->where('p.company_id', $cids);
        }

        $this->db->select('count(*) as total');
        $this->db->from('cat_player p ');



        $query = $this->db->get();
        return $query->row()->total;
    }

    public function get_all_player_list($cid, $gids = 0)
    {
        $this->db->select('id, name');
        $this->db->from('cat_player p ');
        $array = array();
        if ($gids != 0) {
            if (is_array($gids)) {
                if (count($gids) > 0) {
                    $this->db->where_in('p.group_id', $gids);
                } else {
                    return $array;
                }
            } else {
                $this->db->where('p.group_id', $gids);
            }
        }
        $this->db->where('p.company_id', $cid);


        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $array = $query->result();
        }

        return $array;
    }

    public function get_all_online_player_list($cid, $gids = 0, $order_item = 'id', $order = 'desc', $type = 10, $core = 10)
    {
        $this->db->select('p.id, p.sn, p.name, p.version, p.upgrade_version, p.player_type, p.mpeg_core');
        $this->db->from('cat_player p ');
        $this->db->where('p.status >', '1');
        $array = array();

        if ($type <= 1) {
            $this->db->where('p.player_type', $type);
        }
        if ($core <= 3) {
            $this->db->where('p.mpeg_core', $core);
        }

        $this->db->where('p.company_id', $cid);
        $this->db->order_by($order_item, $order);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $array = $query->result();
        }

        return $array;
    }

    /**
     *
     * @param object $ids
     * @param object $version
     * @return
     */
    public function update_upgrade_version($ids, $version)
    {


        if (is_array(($ids))) {
            $this->db->where_in('id', $ids);
        } else {
            $this->db->where('id', $ids);
        }
        $this->db->where('version!=', $version);

        if ($this->db->update('cat_player', array('upgrade_version' => $version))) {
            return $this->db->affected_rows();
        }
        return false;
    }

    public function update_upgrade_firmware_version($ids, $version)
    {

        $this->db->select('id,firmver,upgrade_firmware_version');
        $this->db->from('cat_player');
        if (is_array(($ids))) {
            $this->db->where_in('id', $ids);
        } else {
            $this->db->where('id', $ids);
        }
        $this->db->where('firmver!=', $version);
        $query = $this->db->get();
        if ($query->num_rows()) {
            $players = array_filter($query->result(), function ($player) use ($version) {
                if ($player->firmver == '' || $player->firmver == null || !preg_match('/^[0-9]{4}[0-9]{2}[0-9]{2}$/', $player->firmver)) {
                    return true;
                } else if ($player->firmver != $version) {
                    return true;
                }
                return false;
            });
            if (count($players)) {
                $this->db->where_in("id", array_column($players, 'id'));
                if ($this->db->update('cat_player', array('upgrade_firmware_version' => $version))) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * 
     *
     * @param string $sn
     * @return
     */
    public function get_upgrade_version($sn)
    {
        $sql = "select upgrade_version from cat_player where sn = '$sn'";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            return $query->row()->upgrade_version;
        }

        return false;
    }

    /**
     *
     *
     * @param object $sn
     * @return
     */
    public function remove_upgrade_version($sn)
    {
        $sql = "update cat_player set upgrade_version = null where sn = '$sn'";
        $this->db->query($sql);
        return true;
    }

    public function get_upgrade_firmware_version($sn)
    {
        $sql = "select upgrade_firmware_version from cat_player where sn = '$sn'";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            return $query->row()->upgrade_firmware_version;
        }

        return false;
    }

    public function remove_firmware_upgrade_version($sn)
    {
        $sql = "update cat_player set upgrade_firmware_version = null where sn = '$sn'";
        $this->db->query($sql);
        return true;
    }

    /**
     * get_player_list
     *
     * @param object $cid company ID
     * @param object $tid [optional] tagID
     * @param  $with_detail
     * @return
     */
    public function get_player_list($cid, $filter_array = false, $with_detail = false, $offset = 0, $limit = -1, $order_item = 'name', $order = 'asc')
    {
        $array = array();

        $and_id = null;
        $exclude_id = null;
        //$this->load->helper("chrome_logger");
        //$runtime_start = microtime(true);


        if (isset($filter_array['and_criteria']) && $filter_array['and_criteria']) {
            $bind_cris = $filter_array['and_criteria'];

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
            $ands = array();
            $query = $this->db->query($sql);
            if ($query->num_rows()) {
                $result = $query->result_array();
                $and_id = array_column($result, 'id');
            }
        }
        if (isset($filter_array['and_criteria_or']) && $filter_array['and_criteria_or']) {
            $bindcams_or = is_array($filter_array['and_criteria_or']) ? $filter_array['and_criteria_or'] : explode(',', $filter_array['and_criteria_or']);

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
            }
        }
        if ((isset($filter_array['and_criteria_or']) || isset($filter_array['and_criteria'])) && !$and_id) {
            return array('total' => 0, 'data' => $array);
        }

        if (isset($filter_array['main_campaign_id']) && $filter_array['main_campaign_id']) {
            $this->db->select('player_id');
            $this->db->from('campaign_player');
            $this->db->where('campaign_id', $filter_array['main_campaign_id']);
            $query = $this->db->get();
            if ($query->num_rows()) {
                $result = $query->result_array();
                $players_in_main_campaign = array_column($result, 'player_id');
                if ($and_id) {
                    $and_id = array_merge($and_id, $players_in_main_campaign);
                } else {
                    $and_id = $players_in_main_campaign;
                }
            }
        }

        if (isset($filter_array['ex_criteria']) && $filter_array['ex_criteria']) {
            $ex_cris = $filter_array['ex_criteria'];
            $ex_cris = is_array($ex_cris) ? $ex_cris : explode(',', $ex_cris);
            $this->db->select("cp.id");
            $this->db->from("cat_player cp");
            $this->db->join('cat_criteria_player cc', "cc.player_id=cp.id", "LEFT");
            $this->db->where_in("cc.criteria_id", $ex_cris);
            $query = $this->db->get();

            if ($query->num_rows()) {
                $result = $query->result_array();
                $exclude_id = array_column($result, 'id');
                $this->db->where_not_in('p.id', $exclude_id);
            }
        }

        if ($with_detail) {
            $this->db->select('p.*,pe.barcode,pe.conname,pe.conphone,pe.conemail,pe.conaddr,pe.conzipcode,pe.contown,pe.simno,pe.simvolume,pe.itemnum,pe.modelname,
                         pe.screensize,pe.sided,pe.partnerid,pe.locationid,pe.geox,pe.geoy,pe.setupdate,pe.viewdirection,pe.pps,pe.visitors,pe.displaynum,pe.state,
                         pe.country,pe.custom_sn1,pe.custom_sn2, pe.street_num, pe.last_maintenance,tc.name as timecfg');

            $this->db->join('cat_player_extra pe', 'pe.player_id=p.id', "LEFT");
            $this->db->join('cat_timer_config tc', 'p.timer_config_id = tc.id', 'left');
            $this->db->join('cat_criteria_player c2', 'c2.player_id= p.id', "LEFT");

            if ($cid == 0) {
                $this->db->select('c.name as company_name');
                $this->db->join('cat_company c', 'p.company_id=c.id', 'left');
            }

            //$this->db->join("cat_tag t", "t.id = tg.tag_id", "LEFT");
        } else {
            $this->db->select('p.id,p.name,pe.setupdate,p.status');
            $this->db->join('cat_criteria_player c2', 'c2.player_id= p.id', "LEFT");
            $this->db->join('cat_player_extra pe', 'pe.player_id=p.id', "LEFT");
        }
        $this->db->from('cat_player p');


        if ($cid != 0) {
            $this->db->where('p.company_id', $cid);
        }

        $this->db->distinct();


        ////////////////////////////

        if (!empty($filter_array)) {
            if (isset($filter_array['filter_type']) && $filter_array['filter']) {
                if ($filter_array['filter_type'] == 'name') {
                    $this->db->like('p.name', $filter_array['filter']);
                } elseif ($filter_array['filter_type'] == 'sn') {
                    $this->db->like('p.sn', $filter_array['filter']);
                } elseif ($filter_array['filter_type'] == 'fourfields') {
                    if (preg_match("/(^[0-9]{3}\-[0-9]{3}\-[0-9]{4}$)/", $filter_array['filter'])) {
                        $filter_array['filter'] = trim(str_replace('-', '', $filter_array['filter']));
                    }
                    $this->db->group_start();
                    $this->db->like('p.name', $filter_array['filter']);
                    $this->db->or_like('p.sn', $filter_array['filter']);
                    $this->db->or_like('pe.contown', $filter_array['filter']);
                    $this->db->or_like('pe.conzipcode', $filter_array['filter']);
                    $this->db->or_like('pe.conaddr', $filter_array['filter']);
                    $this->db->or_like('pe.custom_sn1', $filter_array['filter']);
                    $this->db->or_like('pe.custom_sn2', $filter_array['filter']);
                    $this->db->or_like('pe.partnerid', $filter_array['filter']);
                    $this->db->or_like('pe.barcode', $filter_array['filter']);
                    $this->db->or_like('pe.itemnum', $filter_array['filter']);
                    $this->db->or_like('pe.displaynum', $filter_array['filter']);
                    $this->db->or_like('p.descr', $filter_array['filter']);
                    $this->db->or_like('pe.viewdirection', $filter_array['filter']);
                    $this->db->or_like('pe.simno', $filter_array['filter']);


                    $this->db->group_end();
                }
            }

            if (isset($filter_array['name']) && $filter_array['name']) {
                $this->db->like('p.name', $filter_array['name']);
            }
            if (isset($filter_array['online']) && $filter_array['online']) {

                $this->db->where('status >', 1);
            }
            if (isset($filter_array['status']) && $filter_array['status'] >= 0) {
                if ($filter_array['status'] == 12) {
                    $this->db->where_in('status', [12, 127]);
                } else {
                    $this->db->where('status', $filter_array['status']);
                }
            }
            if (isset($filter_array['mpeg_core']) && $filter_array['mpeg_core']) {
                $this->db->where('p.mpeg_core', $filter_array['mpeg_core']);
            }

            if (isset($filter_array['batch_reg_status']) && $filter_array['batch_reg_status']) {
                $this->db->where('batch_reg_status', 1);
            }
            if (isset($filter_array['batch_registration']) && $filter_array['batch_registration']) {
                $this->db->where('batch_registration', 1);
            }
            if (isset($filter_array['criteria']) && $filter_array['criteria']) {
                if (is_array($filter_array['criteria'])) {
                    $this->db->where_in('c2.criteria_id', $filter_array['criteria']);
                } else {
                    $this->db->where('c2.criteria_id', $filter_array['criteria']);
                }
            }
            if (isset($filter_array['tag']) && $filter_array['tag']) {
                $this->db->join("taggables tg", "tg.taggable_id= p.id and tg.taggable_type='App\\\Player'", "LEFT");

                if (is_array($filter_array['tag'])) {
                    $this->db->where_in('tg.tag_id', $filter_array['tag']);
                } else {
                    $this->db->where('tg.tag_id', $filter_array['tag']);
                }
            }
            if (isset($filter_array['setupdate']) && $filter_array['setupdate']) {
                $this->db->where("UNIX_TIMESTAMP(pe.setupdate) !=", 0);
                $this->db->where('pe.setupdate<=', $filter_array['setupdate']);
            }
            if (isset($filter_array['pids']) && $filter_array['pids']) {
                $this->db->where_in('p.id', $filter_array['pids']);
            }
            if (isset($filter_array['criteria_array']) && $filter_array['criteria_array']) {
                $this->db->where_in('c2.criteria_id', $filter_array['criteria_array']);
            }
            if (isset($filter_array['exclude_players']) && $filter_array['exclude_players']) {
                $this->db->where_not_in('p.id', $filter_array['exclude_players']);
            }

            if (isset($filter_array['sdawids']) && $filter_array['sdawids']) {
                $this->db->where_in('pe.custom_sn1', $filter_array['sdawids']);
            }


            if ($and_id) {
                $this->db->where_in('p.id', $and_id);
            }

            if ($exclude_id) {
                $this->db->where_not_in('p.id', $exclude_id);
            }

            if (isset($filter_array['minpps']) && $filter_array['minpps']) {
                $this->db->where('pe.pps>=', $filter_array['minpps']);
            }
            if (isset($filter_array['healthy_status'])) {
                $this->db->where('p.healthy_status', 0);
            }
        }


        $total = $this->db->count_all_results('', false);

        if ($total > 0) {

            if ($order_item == 'name') {
                $this->db->order_by('p.name', $order);
            } else {
                if ($order_item == 'id') {
                    $this->db->order_by('last_connect', 'desc');
                    $this->db->order_by('status', 'desc');
                } else {
                    $this->db->order_by($order_item, $order);
                    $this->db->order_by('status', 'desc');
                    $this->db->order_by('last_connect', 'desc');
                }
            }
            if ($limit > 0) {
                $this->db->limit($limit, $offset);
            }
            $query = $this->db->get();


            if ($query->num_rows() > 0) {
                $array = $query->result();
                if ($with_detail) {
                    foreach ($array as $player) {
                        /*/* GROUP BY tp.player_id*/

                        $this->db->select("(select GROUP_CONCAT(DISTINCT t.name ORDER BY t.name ASC SEPARATOR '/' ) from cat_criteria t, cat_criteria_player tp where tp.player_id=$player->id and tp.criteria_id=t.id ) as criteria_name");
                        if ($with_detail == 2) {
                            $this->db->select("(select GROUP_CONCAT(DISTINCT tag.name ORDER BY tag.name ASC SEPARATOR '/' ) from cat_tag tag, taggables tb where tb.taggable_id= $player->id and tb.taggable_type='App\\\Player' and tb.tag_id=tag.id ) as tags");
                        }
                        $extra_query = $this->db->get();
                        if ($extra_query->num_rows() > 0) {
                            $extra = $extra_query->row();
                            $player->criteria_name = $extra->criteria_name;
                            if ($with_detail == 2) {
                                $player->tags = $extra->tags;
                            }
                        }
                    }
                }

                $query->free_result();
            }
        }
        $this->db->reset_query();
        //$runtime_stop = microtime(true);
        //chrome_log("<!-- Processed in ".round($runtime_stop-$runtime_start, 6)." second(s) -->");

        return array('total' => $total, 'data' => $array);
    }

    public function check_sn_exist($sn)
    {
        $this->db->select("id");
        $this->db->from('cat_player');

        $this->db->where("sn", $sn);
        $query = $this->db->get();

        if ($query->num_rows()) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * Generate company code
     *
     * @param object $cid
     * 
     */
    public function get_player_new_code($cid)
    {
        $query = $this->db->query('select code from cat_company where id=' . $cid);
        if ($query->num_rows() > 0) {
            $row = $query->row();
            $this->load->helper('serial');
            do {
                $sn =  generate_player_code($row->code, false);
            } while ($this->check_sn_exist($sn));
            return $sn;
        } else {
            return false;
        }
    }

    /**
     * ï¿½Â½ï¿½Ò»ï¿½ï¿½ï¿½Í»ï¿½ï¿½ï¿½
     *
     * @param object $array
     * @param object $uid
     * @return
     */
    public function add_player($array, $uid, $cris = false, $tagstr = false)
    {
        if (empty($array)) {
            return 0;
        }

        $array['add_user_id'] = $uid;
        if ($this->db->insert('cat_player', $array)) {
            $id = $this->db->insert_id();
            $this->user_log($this->OP_TYPE_USER, 'add_player[' . $id . '] name[' . $array['name'] . ']');
            if ($cris && !empty($cris)) {
                if (!is_array($cris)) {
                    $cris = explode(",", $cris);
                }
                foreach ($cris as $cri) {
                    $data = array('criteria_id' => $cri, 'player_id' => $id);
                    $this->db->insert('cat_criteria_player', $data);
                }
            }
            if ($tagstr && !empty($tagstr)) {
                $this->attach_tags($id, $tagstr, "App\Player");
            }

            return $id;
        } else {
            $this->user_log($this->OP_TYPE_USER, 'add_player name[' . $array['name'] . ']', $this->OP_STATUS_FAIL);
            return false;
        }
    }

    /**
     * ï¿½ï¿½ï¿½Â¿Í»ï¿½ï¿½ï¿½
     *
     * @param object $array
     * @param object $id
     * @return
     */
    public function update_player_wCriteria($array, $id, $cristr = '', $tagstr = '')
    {
        if (empty($array)) {
            return 0;
        }

        $this->update_player($array, $id);

        $result = $this->db->query("delete from cat_criteria_player where player_id=" . $id);
        if (!empty($cristr)) {
            $criteria = explode(",", $cristr);
            foreach ($criteria as $criid) {
                if (!empty($criid)) {
                    $data = array('criteria_id' => $criid, 'player_id' => $id);
                    $this->db->insert('cat_criteria_player', $data);
                }
            }
        }

        $this->sync_tags($id, $tagstr, "App\Player");
        /*
        $this->detach_tags($id,'App\\\Player');
        //$this->db->query("delete from taggables where taggable_id=".$id);
        if(!empty($tagstr)){
                $tags = explode(",",$tagstr);
                foreach($tags as $tagid){
                    if(!empty($tagid)){
                        $data = array('tag_id'=>$tagid,'taggable_id'=>$id, 'taggable_type'=>'App\Player');
                        $this->db->insert('taggables',$data);
                    }
                }

        }
        */
        return $result;
    }

    /**
     * ï¿½ï¿½ï¿½Â¿Í»ï¿½ï¿½ï¿½
     *
     * @param object $array
     * @param object $id
     * @return
     */
    public function update_player($array, $id)
    {
        if (empty($array)) {
            return 0;
        }

        $this->db->where('id', $id);
        $array['update_at'] = date("Y-m-d H:i:s");
        if ($this->db->update('cat_player', $array)) {
            //$this->user_log($this->OP_TYPE_USER, 'update_player['.$id.'] name['.json_encode($array).']');
            return true;
        } else {
            return false;
        }
    }

    /**
     * ï¿½ï¿½ï¿½Â¿Í»ï¿½ï¿½ï¿½ï¿½Þ¸ï¿½Ê±ï¿½ï¿½
     *
     * @param object $array
     * @param object $id
     * @param object $cid
     * @return
     */
    public function update_player_add_time($array, $id, $cid)
    {
        if (empty($array)) {
            return 0;
        }

        $this->db->where('timer_config_id', $id);
        $this->db->where('company_id', $cid);
        if ($this->db->update('cat_player', $array)) {
            $this->user_log($this->OP_TYPE_USER, 'update_player[' . $id . '] name[' . json_encode($array) . ']');
            return true;
        } else {
            return false;
        }
    }

    /**
     * É¾ï¿½ï¿½ï¿½Í»ï¿½ï¿½ï¿½
     *
     * @param object $id
     * @param object $uid
     * @return
     */
    public function delete_player($id, $uid)
    {
        $this->db->query('delete from cat_player where id=' . $id);
        $this->user_log($this->OP_TYPE_USER, 'delete_player[' . $id . ']');
        $this->db->where('player_id', $id);
        $this->db->delete("cat_player_extra");
        return true;
    }

    /**
     * ï¿½ï¿½È¡ï¿½Í»ï¿½ï¿½ï¿½ï¿½ï¿½Ï¸ï¿½ï¿½Ï¢
     *
     * @param object $id
     * @return
     */
    public function get_player($id, $withdetails = false)
    {
        $this->db->select('p.*,c.nxslot');
        $this->db->from('cat_player p');
        $this->db->join('cat_company c', 'c.id = p.company_id', "LEFT");
        $this->db->where("p.id", $id);

        /*
        if ($this->config->item('ssp_feature')) {
            $this->db->select("amc.mon,amc.tue,amc.wed,amc.thu,amc.fri,amc.sat,amc.sun");
            $this->db->join('ssp_amc amc', 'p.id = amc.player_id', "LEFT");
        }
        */


        $query = $this->db->get();

        if ($query->num_rows()) {
            $player =  $query->row();
            if ($withdetails) {
                $this->load->model('program');
                $this->program->fill_player_details($player);
            }
            return $player;
        } else {
            return false;
        }
    }

    /**
     *
     * @param int $id
     * @return
     */
    public function get_criteria_by_player($playerid)
    {
        /* $query = $this->db->query('			
					SELECT id,name FROM cat_criteria  WHERE 
					id in (SELECT criteria_id
					FROM cat_criteria_player 
					WHERE player_id =' . $playerid . ')');
        */
        $this->db->select('criteria_id');
        $this->db->from('cat_criteria_player');
        $this->db->where("player_id", $playerid);

        $query = $this->db->get();
        if ($query->num_rows()) {
            return array_column($query->result_array(), 'criteria_id');
        } else {
            return false;
        }
    }

    /**
     * ï¿½ï¿½È¡Ö¸ï¿½ï¿½meidaï¿½ï¿½tagï¿½ï¿½Ï¢
     *
     * @param object $id
     * @return
     */
    public function get_tags_id_by_media($mid)
    {
        $sqlstr =     "SELECT GROUP_CONCAT( t.id ) as tagids
			FROM cat_tag t, cat_tag_media tm
			WHERE tm.tag_id = t.id
			AND tm.media_id='$mid'
			GROUP BY tm.media_id";

        $query = $this->db->query($sqlstr);
        if ($query->num_rows()) {
            return $query->row()->tagids;
        } else {
            return false;
        }
    }

    /**
     * ï¿½ï¿½ï¿½Ø¿Í»ï¿½ï¿½ï¿½ï¿½ï¿½Ï¢
     *
     * @param object $sn
     * @return
     */
    public function get_player_by_sn($sn)
    {
        $this->db->select("p.*,c.device_setup,c.weather_format,c.offline_email_flag,c.name as company_name,c.com_interval as comm_intval,c.email");
        $this->db->from('cat_player p');
        $this->db->join('cat_company c', 'c.id=p.company_id', 'left');
        $this->db->where("p.sn", $sn);
        $query = $this->db->get();

        if ($query->num_rows()) {
            return $query->row();
        } else {
            return false;
        }
    }
    /**
     * ï¿½ï¿½ï¿½ï¿½ ï¿½Í»ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ ï¿½ï¿½È¡ï¿½Í»ï¿½ï¿½ï¿½ï¿½ï¿½Ï¢
     * 2013-9-17 9:01:10
     * @param object $id
     * @param object $gid
     * @param object $cid
     * @param object $name
     * @return
     */
    public function get_player_by_name($id, $cid, $name)
    {
        if ($id > 0) {
            $sql = "select id from cat_player where id != $id and company_id = '$cid' and name = '$name'";
        } else {
            $sql = "select id from cat_player where company_id = '$cid' and name = '$name'";
        }
        $query = $this->db->query($sql);

        if ($query->num_rows()) {
            return $query->row();
        } else {
            return false;
        }
    }

    /**
     * ï¿½Ð¶Ïµï¿½Ç°snï¿½Ç·ï¿½ï¿½ï¿½Ú£ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ò·µ»ï¿½FALSEï¿½ï¿½ï¿½ò·µ»ï¿½id
     * @param object $sn
     * @return
     */
    public function get_player_id($sn)
    {
        $sql = "select id from cat_player where sn = '" . $sn . "'";
        $query = $this->db->query($sql);
        if ($query->num_rows()) {
            $id = $query->row()->id;
            return $id;
        } else {
            return false;
        }
    }

    /**
     * Ö´ï¿½Ð¿Í»ï¿½ï¿½ï¿½ï¿½ï¿½Â¼ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½Ý¿â¶¯ï¿½ï¿½
     *
     * @param object $sn
     * @param object $model
     * @param object $mac
     * @param object $ver
     * @return
     */
    public function do_login($sn, $model, $mac, $ver, $vol, $spa, $gmt)
    {
        if ($spa) {
            $info = explode(',', $spa);
            if (count($info) == 2) {
                $data['disk_free'] = $info[0];
                $data['disk_total'] = $info[1];
            }
        }
        $data  = array('model' => $model, 'mac' => $mac, 'version' => $ver, 'storage' => $vol, 'space' => $spa, 'time_zone' => $gmt, 'last_connect' => date('Y-m-d H:i:s'));
        if ($spa) {
            $info = explode(',', $spa);
            if (count($info) == 2) {
                $data['disk_free'] = $info[0];
                $data['disk_total'] = $info[1];
            }
        }

        $this->db->where('sn', $sn);
        $this->db->update('cat_player', $data);
    }

    /**
     * ï¿½ï¿½ï¿½Â¿Í»ï¿½ï¿½ï¿½×´Ì¬ï¿½ï¿½ï¿½ï¿½,ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½×´Ì¬ï¿½ë£¬ï¿½ï¿½ï¿½ï¿½Îªï¿½Ç¶ï¿½Ê±ï¿½ï¿½ï¿½ï¿½Ö´ï¿½Ð£ï¿½ï¿½ï¿½ï¿½ï¿½Òªï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½Óµï¿½Ê±ï¿½ï¿½
     *
     * @param object $sn
     * @param object $status
     * @param object $voltage [optional]
     * @param object $elec [optional]
     * @param object $fan [optional]
     * @param object $diskspace [optional]
     * @param object $wetless [optional]
     * @param object $temp [optional]
     * @return
     */
    public function update_status($sn, $status, $voltage = false, $elec = false, $model = false, $diskspace = false, $wetless = false, $temp = false)
    {
        $data  = array('status' => $status);
        //, 'voltage'=>$voltage, 'electric'=>$elec,'fan'=>$fan,'disk_total'=>$diskspace, 'humidity'=>$wetless,'temperature'=>$temp
        if ($voltage !== false) {
            $data['voltage'] = $voltage;
        }

        if ($elec !== false) {
            $data['electric'] = $elec;
        }

        if ($model !== false) {
            $data['model'] = $model;
        }

        if ($diskspace !== false) {
            $data['disk_total'] = $diskspace;
        }

        if ($wetless !== false) {
            $data['humidity'] = $wetless;
        }


        if ($temp !== false) {
            $data['temperature'] = $temp;
        }

        if (count($data) >= 1) {
            //,'last_connect'=>'now()'
            $data['last_connect'] = date('Y-m-d H:i:s');
        }

        $this->db->where('sn', $sn);
        $this->db->update('cat_player', $data);
    }
    /**
     *
     * @param string $campasion_time
     */
    public function update_status_offline($campasion_time)
    {
        $sql = "select id, name,sn, company_id, last_connect from cat_player 
			 where ( status > 1 and last_connect <= '" . $campasion_time . "') ";

        $query = $this->db->query($sql);

        if ($query->num_rows()) {
            $this->load->helper('date');
            $result = $query->result();


            $players = array();
            foreach ($result as $p) {
                $players[] = $p->id;
            }
            $this->db->where_in('id', $players);
            $this->db->update('cat_player', array('status' => 1));

            return $result;
        }

        return false;
    }


    /**
     *
     * @param object $player_id
     * @param object $event_type
     * @param string $detail
     * @return
     */
    public function add_player_log($player_id, $event_type, $detail)
    {
        //return to save cpu time

        if ($event_type == 5) {
            $start = strpos($detail, '[');
            $end = strrpos($detail, ']');

            if ($start !== null && $end !== null) {
                $substr = substr($detail, $start + 1, $end - $start - 1);
                $matchs = explode("_", $substr);
                if (count($matchs) == 3) {
                    $detail = substr($detail, 0, $start) . $matchs[1] . ":" . $matchs[2];
                }
            }
        }
        $array = array('player_id' => $player_id, 'event_type' => $event_type, 'detail' => $detail, 'add_time' => date('Y-m-d H:i:s'));
        $this->db->insert('cat_player_log', $array);
    }

    /**
     * ï¿½ï¿½È¡ï¿½ï¿½ï¿½Ò»ï¿½ï¿½ï¿½Í»ï¿½ï¿½ï¿½ï¿½ï¿½Ö¾ï¿½ï¿½Â¼
     *
     * @param object $player_id
     * @param object $event_type
     * @return
     */
    public function get_last_player_log($player_id, $event_type)
    {
        $this->db->from('cat_player_log');
        $this->db->where('player_id', $player_id);
        $this->db->where('event_type', $event_type);

        $this->db->order_by('id', 'desc');
        $this->db->limit(1);
        $query = $this->db->get();
        if ($query->num_rows()) {
            return $query->row();
        }

        return false;
    }


    public function get_player_logList($player_id, $offset, $limit = -1)
    {
        $this->db->select('id');
        $this->db->from('cat_player_log');
        $this->db->where('player_id', $player_id);
        $total = $this->db->count_all_results();

        $array = array();
        if ($total > 0) {
            $this->db->select('*');
            $this->db->from('cat_player_log');
            $this->db->where('player_id', $player_id);

            if ($limit > 0) {
                $this->db->limit($limit, $offset);
            }
            $this->db->order_by('add_time', 'desc');

            $query = $this->db->get();
            if ($query->num_rows()) {
                $array = $query->result();
            }
        }
        return array('total' => $total, 'data' => $array);
    }

    public function get_player_log_list($player_id, $after_time, $limit = 1000)
    {
        $this->db->from('cat_player_log');
        $this->db->where('player_id', $player_id);
        $this->db->where('add_time >=', $after_time);
        $this->db->order_by('id', 'desc');
        $this->db->limit($limit);
        $query = $this->db->get();
        $array = array();

        if ($query->num_rows()) {
            $result = $query->result();
            for ($i = 0; $i < count($result); $i++) {
                if ($i != count($result) - 1) {
                    $time_nex = strtotime($result[$i + 1]->add_time);
                    $time_now = strtotime($result[$i]->add_time);
                    $detail_nex = $result[$i + 1]->detail;
                    $detail_now = $result[$i]->detail;
                    if (!($result[$i]->event_type == 3 && $detail_now == $detail_nex && $time_now - $time_nex < 10)) {
                        $array[] = $result[$i];
                    }
                } else {
                    $array[] = $result[$i];
                }
            }
            $query->free_result();
        }
        return $array;
    }
    /**
     * ï¿½ï¿½È¡ï¿½Í»ï¿½ï¿½ï¿½Ê±ï¿½ï¿½
     * @return
     */
    public function get_player_timezone($id)
    {
        $timezone = 0; //Ä¬ï¿½ï¿½UTC
        $sql = "select time_zone from cat_player where id = $id";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $timezone = $query->row()->time_zone;
        }
        return $timezone;
    }
    /**
     * liu 2013-10-10 ï¿½ï¿½È¡ï¿½Í»ï¿½ï¿½ï¿½reboot_flagï¿½ï¿½format_flagï¿½ï¿½Ö¾Îª1ï¿½ï¿½ï¿½ï¿½Ï¢
     * @param $sn ,$flag
     * @return
     */
    public function get_flag($sn, $flag)
    {
        $sql = "select * from cat_player where sn = $sn and $flag = 1";
        $query = $this->db->query($sql);
        if ($query->num_rows()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * liu 2013-10-10 ï¿½ï¿½ï¿½Â±ï¿½ï¿½ï¿½cat_player; ï¿½ï¿½reboot_flagï¿½ï¿½format_flag Îª 1
     * @param $sn $flag
     * @return
     */
    public function update_flag($id, $flag)
    {
        $sql = "update cat_player set $flag = '1' where id";
        if (is_array($id)) {
            $sql .= " in (" . implode(',', $id) . ")";
        } else {
            $sql .= " = $id";
        }
        $this->db->query($sql);
        return $this->db->affected_rows();
    }

    /**
     * liu 2013-10-10 ï¿½ï¿½ï¿½Â±ï¿½ï¿½ï¿½cat_player; ï¿½Ö¸ï¿½reboot_flagï¿½ï¿½format_flag Îª 0
     * @param $sn  $flag
     * @return
     */
    public function restore_flag($sn, $flag)
    {
        $sql = "update cat_player set $flag = '0' where sn = $sn";
        $this->db->query($sql);
        return $this->db->affected_rows();
    }

    public function get_player_control_by_ids($ids)
    {
        $this->db->from('cat_player');
        if (is_array($ids)) {
            $this->db->where_in('id', $ids);
        } else {
            $this->db->where('id', $ids);
        }
        $query = $this->db->get();
        $array = array();
        if ($query->num_rows()) {
            $array = $query->result();
            $query->free_result();
        }
        return $array;
    }


    ////////////////////configxmlï¿½ï¿½ï¿½ï¿½////////////////////////////////////
    public function update_config_player($config_id, $player_ids, $dailyRestartTime = false)
    {

        if (is_array($player_ids)) {
            $this->db->where_in('id', $player_ids);
        } else {
            $this->db->where('id', $player_ids);
        }
        $array = array('config' => $config_id, 'config_update_time' => date("Y-m-d H:i:s"), 'update_flag' => 1);
        if ($dailyRestartTime) {
            $array['daily_restart'] = $dailyRestartTime;
        }
        if ($this->db->update('cat_player', $array)) {
            return true;
        } else {
            return false;
        }

        /*

        if ($updateTime) {
            $sql = "UPDATE cat_player SET update_flag =1, config_update_time = '" . $updateTime . "'";
        } else {
            $sql = "UPDATE cat_player SET update_flag =1";
        }
        //$sql = "UPDATE cat_player SET update_flag =1";
        if (is_array($ids)) {
            $sql .= " WHERE id in (" . implode(',', $ids) . ")";
        } else {
            $sql .= " WHERE id =$ids";
        }
        $query = $this->db->query($sql);
        if ($query) {
            return true;
        } else {
            return false;
        }
        */
    }

    public function delete_config_player($sn)
    {
        $sql = "UPDATE cat_player SET update_flag =0 WHERE sn =$sn";
        $query = $this->db->query($sql);
        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    public function re_config_xml($sn)
    {
        $sql = "UPDATE cat_player SET update_flag =0 WHERE sn =$sn";
        $query = $this->db->query($sql);
        if ($query) {
            return true;
        } else {
            return false;
        }
    }
    public function get_config_update($sn)
    {
        $sql = "SELECT update_flag
				FROM cat_player
				WHERE sn = $sn
				";
        $query = $this->db->query($sql);
        if ($query) {
            return $query->row()->update_flag;
        } else {
            return false;
        }
    }

    public function get_config_setting_by_sn($sn)
    {
        $sql = "SELECT cs . * FROM cat_device_config AS cs, cat_player AS p WHERE cs.id = p.config AND p.sn = $sn and p.update_flag=1";
        $query = $this->db->query($sql);
        $array = array();
        if ($query->num_rows() > 0) {
            $array = $query->row();
        }

        return $array;
    }

    //////////////////configxmlï¿½ï¿½ï¿½Ö½ï¿½ï¿½ï¿½//////////////////////////////////////
    //ï¿½ï¿½ï¿½ï¿½ï¿½Õ¶ï¿½id ï¿½ï¿½È¡ï¿½Õ¶Ëµï¿½ï¿½Ð±ï¿½
    public function get_playlist_by_playerId($id)
    {
        $sql = "select sp.playlist_id, p.id, g.name as groupname, pl.name as playlistname, p.name as playername, pl.template_id, temp.w, temp.h from cat_group g, cat_player p, cat_template temp, cat_playlist pl ,cat_schedule_group sg, cat_schedule_playlist sp, cat_schedule sch where p.id=$id and sg.group_id=p.group_id and g.id=p.group_id and sg.schedule_id=sp.schedule_id and sp.playlist_id=pl.id and pl.template_id=temp.id and sch.end_date>='" . date("Y-m-d") . "' and sch.status=1 and sg.schedule_id=sch.id order by sch.status desc";
        $query = $this->db->query($sql);
        $array = array();
        if ($query->num_rows() > 0) {
            $array = $query->result();
        }
        return $array;
    }

    //ï¿½ï¿½ï¿½ï¿½group_id ï¿½ï¿½È¡ï¿½Ð±ï¿½ï¿½Ä¼ï¿½ï¿½ï¿½ï¿½Ç·ï¿½ï¿½ï¿½HTTPï¿½Ä¼ï¿½
    public function get_playlist_by_sn($sn)
    {
        $sql = "select distinct pl.id as playlist_id, sp.schedule_id, t.width, t.height from cat_template as t, cat_player as p, cat_schedule_group as sg, cat_schedule_playlist as sp, cat_playlist as pl, cat_playlist_area_media as pm, cat_media as m where p.sn = $sn and p.group_id = sg.group_id and sg.schedule_id = sp.schedule_id and sp.playlist_id = pl.id  and pl.id = pm.playlist_id and m.id = pm.media_id and m.source = 2 and pl.published = 1 and pl.template_id=t.id and pm.status=0";
        $query = $this->db->query($sql);
        $array = array();
        if ($query->num_rows() > 0) {
            $array = $query->result();
        }
        return $array;
    }

    //ï¿½ï¿½ï¿½ï¿½ playlist_id ï¿½ï¿½È¡ï¿½ï¿½Ó¦ï¿½ï¿½HTTPï¿½ï¿½ï¿½Íµï¿½ï¿½Ä¼ï¿½id
    public function get_HttpMedia_by_pid($pid)
    {
        $sql = "select distinct m.id, m.full_path from cat_playlist as pl, cat_playlist_area_media as pm, cat_media as m where pl.id=$pid and pl.id=pm.playlist_id and pm.media_id=m.id and m.source=2";
        $query = $this->db->query($sql);
        $array = array();
        if ($query->num_rows() > 0) {
            $array = $query->result();
        }
        return $array;
    }

    //xmlï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½Ö·ï¿½ï¿½æ»»
    public function xmlencode($tag)
    {
        $tag = str_replace("&", "&amp;", $tag);
        $tag = str_replace("<", "&lt;", $tag);
        $tag = str_replace(">", "&gt;", $tag);
        $tag = str_replace("'", "&apos;", $tag);
        $tag = str_replace("\"", '&quot;', $tag);
        return $tag;
    }
    public function rename_media_name($file_name, $media_type, $area_type, $rotate, $file_id)
    {
        $result = $file_name;
        if ($area_type == $this->config->item('area_type_movie')) {
            if ($rotate) {
                if ($media_type == $this->config->item('media_type_video')) {
                    $result = $this->rename($file_name, 'P');
                } else {
                    $result = $this->rename($file_name, $file_id);
                }

                $ext = strtolower(substr($result, -4));
                if ($ext == 'mpeg' || $ext == 'divx') {
                    $result = substr($result, 0, strlen($result) - 4) . 'mkv';
                } else {
                    $ext = strtolower(substr($result, -3));
                    if ($ext == 'mkv' || $ext == 'mp4' || $ext == 'mpg' || $ext == 'flv' || $ext == 'avi' || $ext == 'mov' || $ext == 'wmv') {
                        $result = substr($result, 0, strlen($result) - 3) . 'mkv';
                    }
                }
            } else {
                $result = $file_name;
                //if mp4 or wmv or flv or mov will convert to be avi
                $ext = strtolower(substr($result, -3));
                if ($ext == 'mp4' || $ext == 'wmv' || $ext == 'mov' || $ext == 'flv' || $ext == 'mov') {
                    $result = substr($result, 0, strlen($result) - 3) . 'mkv';
                }
            }
        } else {
            if ($area_type == $this->config->item('area_type_bg')) {
                $result = $file_name;
            } else {
                $result = $this->rename($file_name, $file_id);
            }
        }

        return $result;
    }

    public function rename($file_name, $file_id)
    {
        $tmp = explode('.', $file_name);
        $dest = '';
        for ($i = 0; $i < count($tmp) - 1; $i++) {
            $dest .= $tmp[$i];
            if ($i < count($tmp) - 2) {
                $dest .= '.';
            }
        }
        $dest .= '[' . $file_id . '].' . $tmp[count($tmp) - 1];
        return $dest;
    }

    /**
     * ï¿½ï¿½È¡ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½Ï¢
     */
    public function get_software($id)
    {
        $this->db->where('id', $id);
        $query = $this->db->get('cat_software');
        if ($query->num_rows()) {
            return $query->row();
        } else {
            return false;
        }
    }

    /**
     * ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½Ï¢
     *
     * @param object $array
     * @param object $id
     * @return
     */
    public function update_software($array, $id)
    {
        if (empty($array)) {
            return 0;
        }

        $this->db->where('id', $id);
        if ($this->db->update('cat_software', $array)) {
            $this->user_log($this->OP_TYPE_USER, 'update_softeware[' . $id . '] name[' . json_encode($array) . ']');
            return true;
        } else {
            return false;
        }
    }

    /**
     * ï¿½ï¿½È¡ï¿½Ð±ï¿½ï¿½Ðµï¿½Ã½ï¿½ï¿½ï¿½Ä¼ï¿½ï¿½ï¿½Ï¢
     *
     * @param object $ids
     * @return
     */
    public function get_pls_media($ids)
    {
        $sql = "select m.id as mid, pm.id as pid, m.name, m.media_type, m.source, m.ext, m.signature, pm.area_id, ta.area_type, pm.publish_url, pm.img_fitORfill, t.h, t.w from cat_playlist_area_media pm, cat_template_area ta, cat_media m, cat_template t where pm.playlist_id in(" . $ids . ") and pm.area_id = ta.id and m.id = pm.media_id and ta.template_id = t.id and pm.status=0";
        $query = $this->db->query($sql);
        $array = array();
        if ($query->num_rows() > 0) {
            $array = $query->result();
        }
        return $array;
    }


    /**
     *
     *
     * @param object $uid
     * @return
     */
    public function get_folder_ids($uid)
    {
        $result = array();
        $this->db->select("folder_id");
        $query = $this->db->get_where("cat_user_folder", array('user_id' => $uid));
        if ($query->num_rows() > 0) {
            //$result = $query->result_array();
            foreach ($query->result() as $row) {
                $result[] = $row->folder_id;
            }
        }

        return $result;
    }

    public function get_user_folderID($uid)
    {
        $result = array();
        $this->db->select("folder_id");
        $query = $this->db->get_where("cat_user_folder", array('user_id' => $uid));
        if ($query->num_rows() > 0) {
            $row = $query->row()->folder_id;
            return $row;
        }

        return false;
    }
    /**
     *
     * @param object $uid
     * @return
     */
    public function get_rootFolder_id($uid)
    {
        $result = array();
        $this->db->select("*");
        $query = $this->db->get_where("cat_user_folder", array('user_id' => $uid, 'folder_id' => 0));
        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * ï¿½ï¿½ï¿½ï¿½ï¿½Ä¼ï¿½ï¿½ï¿½
     *
     * @param object $groups
     * @param object $uid
     * @return
     */
    public function assign_folder($folders, $uid)
    {
        if (empty($folders)) {
            return false;
        }

        foreach ($folders as $folder) {
            $sql = "insert ignore into cat_user_folder(user_id, folder_id) values($uid, $folder)";
            $this->db->query($sql);
        }

        $sql = "delete from cat_user_folder where user_id=$uid and folder_id not in(" . implode(',', $folders) . ")";
        $this->db->query($sql);

        return true;
    }

    /**
     * É¾ï¿½ï¿½ï¿½ï¿½ï¿½Ã»ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½Ä¼ï¿½ï¿½ï¿½ï¿½Ð±ï¿½
     *
     * @param object $uid
     * @return
     */
    public function delete_assign_folder($uid)
    {
        $this->db->where('user_id', $uid);

        return $this->db->delete('cat_user_folder');
    }

    //ï¿½ï¿½ï¿½ï¿½ï¿½Õ¶ï¿½ï¿½ï¿½ï¿½Ðºï¿½  ï¿½ï¿½È¡weacherï¿½ï¿½Ê¾ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½
    public function get_weather_lang_by_sn($sn)
    {
        $sql = "select aws.language from cat_player as p, cat_schedule_group as sg, cat_schedule_playlist as sp, cat_template_area as ta, cat_area_weather_setting as aws where p.sn = '$sn' and p.group_id = sg.group_id and sg.schedule_id = sp.schedule_id and sp.playlist_id = aws.playlist_id and aws.area_id = ta.id and ta.name = 'Weather'";
        $query = $this->db->query($sql);
        $array = array();
        if ($query->num_rows() > 0) {
            $array = $query->result();
        }
        return $array;
    }

    public function get_all_reboot_flag()
    {
        $this->db->from('cat_player');
        $this->db->where('player_type', 1);
        $this->db->where('reboot_flag', 1);
        $query = $this->db->get();
        $array = array();

        if ($query->num_rows()) {
            $array = $query->result();
            $query->free_result();
        }

        return $array;
    }

    public function delete_data_everyWeek()
    {
        $sql = "delete from cat_player_log where add_time <='" . date('Y-m-d H:i:s', strtotime('-2 weeks')) . "'";
        $this->db->query($sql);
    }

    /**
     * ï¿½ï¿½Ñ¯Ä³ï¿½ï¿½ï¿½ï¿½Ë¾ï¿½ï¿½Ö¸ï¿½ï¿½ï¿½Ä¼ï¿½Â¼ï¿½ï¿½Ï¢
     *
     * @param object $company_id
     * @param object $group_ids [optional]
     * @param object $player_id [optional]
     * @param object $start_date [optional]
     * @param object $end_date [optional]
     * @param object $offset [optional]
     * @param object $limit [optional]
     * @return
     */
    public function get_player_bandwidth($company_id, $group_ids = 0, $player_id = 0, $start_date = false, $end_date = false, $offset = 0, $limit = 10, $order_item = 'id', $order = 'desc')
    {
        $this->db->select("count(*) as total");
        if ($group_ids != 0) {
            if (is_array($group_ids)) {
                if (count($group_ids) > 0) {
                    $this->db->where_in('g.id', $group_ids);
                }
            } else {
                $this->db->where('g.id', $group_ids);
            }
        }
        $this->db->from('cat_player_bandwidth pb');
        $this->db->join('cat_player p', 'pb.player_id = p.id', 'left');
        $this->db->join('cat_group g', 'p.group_id = g.id', 'left');
        if (!empty($start_date)) {
            $this->db->where('pb.recode_date >=', $start_date);
        }
        if (!empty($end_date)) {
            $this->db->where('pb.recode_date <= ', $end_date);
        }
        if ($player_id > 0) {
            $this->db->where('pb.player_id', $player_id);
        }
        $this->db->where('p.company_id', $company_id);
        //echo $this->db->get_sql();
        $query = $this->db->get();
        $total = $query->row()->total;
        $array = array();
        if ($total > 0) {
            $this->db->select("pb.*, p.name as player_name, p.sn, p.player_type as type, p.id as player_id, g.id as group_id, g.name as group_name");
            if ($group_ids != 0) {
                if (is_array($group_ids)) {
                    if (count($group_ids) > 0) {
                        $this->db->where_in('g.id', $group_ids);
                    }
                } else {
                    $this->db->where('g.id', $group_ids);
                }
            }
            $this->db->from('cat_player_bandwidth pb');
            $this->db->join('cat_player p', 'pb.player_id = p.id', 'left');
            $this->db->join('cat_group g', 'p.group_id = g.id', 'left');
            if (!empty($start_date)) {
                $this->db->where('pb.recode_date >= ', $start_date);
            }
            if (!empty($end_date)) {
                $this->db->where('pb.recode_date <= ', $end_date);
            }
            if ($player_id > 0) {
                $this->db->where('pb.player_id', $player_id);
            }
            $this->db->where('p.company_id', $company_id);
            if ($order_item == 'recode_date') {
                $this->db->order_by('pb.' . $order_item, $order);
                $this->db->order_by('p.id', $order);
            } elseif ($order_item == 'used_bandwidth') {
                $this->db->order_by('pb.' . $order_item, $order);
                $this->db->order_by('p.id', $order);
            } else {
                $this->db->order_by('p.' . $order_item, $order);
                $this->db->order_by('pb.recode_date', 'desc');
            }
            $this->db->limit($limit, $offset);
            //echo $this->db->get_sql();
            $query = $this->db->get();
            if ($query->num_rows() > 0) {
                $array = $query->result();
            }
        }
        return array(
            'total' => $total,
            'data' => $array
        );
    }

    public function get_all_player_bandwidth($company_id, $group_ids = 0, $player_id = 0, $start_date = false, $end_date = false)
    {
        $this->db->select("pb.*, p.name as player_name, p.sn, p.player_type as type, p.id as player_id, g.id as group_id, g.name as group_name");
        if ($group_ids != 0) {
            if (is_array($group_ids)) {
                if (count($group_ids) > 0) {
                    $this->db->where_in('g.id', $group_ids);
                }
            } else {
                $this->db->where('g.id', $group_ids);
            }
        }
        $this->db->from('cat_player_bandwidth pb');
        $this->db->join('cat_player p', 'pb.player_id = p.id', 'left');
        $this->db->join('cat_group g', 'p.group_id = g.id', 'left');
        if (!empty($start_date)) {
            $this->db->where('pb.recode_date >= ', $start_date);
        }
        if (!empty($end_date)) {
            $this->db->where('pb.recode_date <= ', $end_date);
        }
        if ($player_id > 0) {
            $this->db->where('pb.player_id', $player_id);
        }
        $this->db->where('p.company_id', $company_id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }





    /**
     * 
     *
     * @param object $cid
     * @param object $offset [optional]
     * @param object $limit [optional]
     * @return
     */
    public function get_criteria_list($cid, $offset = 0, $limit = -1, $order_item = 'name', $order = 'asc', $filter_array = array())
    {
        $array = array();

        $this->db->select('r.*,(select count(p.player_id) from cat_criteria_player p where p.criteria_id=r.id) as player_count');
        $this->db->from('cat_criteria r');

        if ($cid != 0) {
            $this->db->where('r.company_id', $cid);
        }

        if (!empty($filter_array)) {
            if (isset($filter_array['name']) && $filter_array['name']) {
                $this->db->like('r.name', $filter_array['name']);
            }
            if (isset($filter_array['exclude']) && $filter_array['exclude']) {
                $this->db->where_not_in('r.id', $filter_array['exclude']);
            }
        }

        $total = $this->db->count_all_results('', false);

        if ($total > 0) {
            //$this->db = $db;
            $this->db->order_by($order_item, $order);

            if ($limit != -1) {
                $this->db->limit($limit, $offset);
            }
            $query = $this->db->get();
            if ($query->num_rows()) {
                $array = $query->result();
                $query->free_result();
            }
        }
        $this->db->reset_query();
        return array('total' => $total, 'data' => $array);
    }

    /**
     *
     * @param object $id
     * @return
     */
    public function get_criteria($id, $with_players = false)
    {
        // $this->db->where('id', $id);
        // $query = $this->db->get('cat_criteria');
        $this->db->select("c.*");
        $this->db->from('cat_criteria c');
        $this->db->where('id', $id);

        $query = $this->db->get();


        if ($query->num_rows()) {
            $result = $query->row();
            if ($with_players) {
                $attached_players = $this->get_criteron_playerids($id);
                if ($attached_players) {
                    $result->players = $attached_players;
                }
            }
            return $result;
        } else {
            return false;
        }
    }
    public function get_criteron_playerids($criterion_id)
    {
        $this->db->select("player_id");
        $this->db->from('cat_criteria_player');
        $this->db->where('criteria_id', $criterion_id);

        $query = $this->db->get();


        if ($query->num_rows()) {
            $result = $query->result_array();
            return array_column($result, 'player_id');
        } else {
            return false;
        }
    }
    /**
     * get_criteria_byname
     * 2013-9-17 9:01:10
     * @param object $id
     * @param object $cid
     * @param object $name
     * @return
     */
    public function get_criteria_byname($id, $cid, $name)
    {
        $name = $this->db->escape_str($name);

        if ($id > 0) {
            $sql = "select id from cat_criteria where id != $id and company_id = '$cid' and name = '$name'";
        } else {
            $sql = "select id from cat_criteria where company_id = '$cid' and name = '$name'";
        }
        $query = $this->db->query($sql);

        if ($query->num_rows()) {
            return $query->row();
        } else {
            return false;
        }
    }
    /**
     * ï¿½ï¿½ï¿½ï¿½criteria
     *
     * @param object $array
     * @return
     */
    public function add_criteria($array)
    {
        if (empty($array)) {
            return 0;
        }
        if ($this->db->insert('cat_criteria', $array)) {
            $id = $this->db->insert_id();
            $this->user_log($this->OP_TYPE_USER, 'add_criteria[' . $id . '] name[' . json_encode($array) . ']');
            return $id;
        } else {
            return false;
        }
    }

    /**
     * ï¿½ï¿½ï¿½ï¿½criteriaï¿½ï¿½Ï¢
     *
     * @param object $array
     * @param object $id
     * @return
     */
    public function update_criteria($array, $id)
    {
        if (empty($array)) {
            return 0;
        }

        $this->db->where('id', $id);
        if ($this->db->update('cat_criteria', $array)) {
            $this->user_log($this->OP_TYPE_USER, 'update_criteria[' . $id . '] name[' . json_encode($array) . ']');
            return true;
        } else {
            return false;
        }
    }

    /**
     * criteria
     *
     * @return
     */
    public function delete_criteria($id)
    {
        $this->db->trans_begin();

        if (is_array($id)) {
            $this->db->where_in('id', $id);
        } else {
            $this->db->where('id', $id);
        }
        $this->db->delete("cat_criteria");

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();

            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }

    /**
     * get user's criteria
     *
     * @param object $uid
     * @return
     */
    public function get_user_criterias($uid)
    {
        $result = array();
        $this->db->select("criteria_id");
        $query = $this->db->get_where("cat_user_criteria", array('user_id' => $uid));
        if ($query->num_rows() > 0) {
            $result =  array_column($query->result_array(), 'criteria_id');
        }

        return $result;
    }

    public function assign_user_criteria($cris, $uid)
    {
        if (empty($cris)) {
            return false;
        }
        $this->delete_user_criteria($uid);

        foreach ($cris as $cri) {
            $sql = "insert ignore into cat_user_criteria(user_id, criteria_id) values($uid, $cri)";
            $this->db->query($sql);
        }


        return true;
    }

    /**
     * É¾ï¿½ï¿½ï¿½ï¿½ï¿½Ã»ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½Ä¼ï¿½ï¿½ï¿½ï¿½Ð±ï¿½
     *
     * @param object $uid
     * @return
     */
    public function delete_user_criteria($uid)
    {
        $this->db->where('user_id', $uid);

        return $this->db->delete('cat_user_criteria');
    }



    /**
     * ï¿½ï¿½È¡Ä³ï¿½ï¿½ï¿½ï¿½Ë¾ï¿½Âµï¿½criteriaï¿½Ð±ï¿½
     *
     * @param object $cid
     * @param object $offset [optional]
     * @param object $limit [optional]
     * @return
     */
    public function get_user_criteria_list($uid)
    {
        $this->db->select('r.*');
        $this->db->from('cat_criteria r');
        $this->db->join('cat_user_criteria uc', 'r.id=uc.criteria_id', 'left');
        $this->db->where('uc.user_id', $uid);
        $this->db->order_by('r.name', "ASC");

        $query = $this->db->get();

        if ($query->num_rows()) {
            return $query->result();
        }
        return false;
    }

    /**
     * ï¿½ï¿½ï¿½ï¿½campaign
     *
     * @param object $groups
     * @param object $uid
     * @return
     */
    public function assign_campaign($campaigns, $uid)
    {
        if (empty($campaigns) || !$uid) {
            return false;
        }

        $this->delete_assign_campaign($uid);

        $dataary = array();
        foreach ($campaigns as $cam) {
            $dataary[] = array('user_id' => $uid, "campaign_id" => $cam);
        }
        return $this->db->insert_batch('cat_user_campaign', $dataary);
    }

    /**
     * É¾ï¿½ï¿½ï¿½ï¿½ï¿½Ã»ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½campaigns
     *
     * @param object $uid
     * @return
     */
    public function delete_assign_campaign($uid)
    {
        $this->db->where('user_id', $uid);

        return $this->db->delete('cat_user_campaign');
    }

    /**
     * ï¿½ï¿½ï¿½ï¿½×´Ì¬Îªï¿½ï¿½ï¿½ßµï¿½ï¿½ï¿½Ä³ï¿½ï¿½Ê±ï¿½ï¿½Ö®Ç°
     *
     * @param object $before_time
     * @return ï¿½ï¿½ï¿½Ø¸ï¿½ï¿½ï¿½ï¿½Ëµï¿½Playerï¿½ï¿½Ï¢
     */
    public function check_offline_email_players($cid, $start_time, $end_time, $is_dston)
    {
        $sql = "select id, name,sn,last_connect,time_zone
			 from cat_player p
			 where p.company_id = $cid
			 and p.status =1 
			 and p.last_connect between '$start_time' and '$end_time'";


        $query = $this->db->query($sql);



        if ($query->num_rows()) {
            $this->load->helper('date');
            $result = $query->result();

            foreach ($result as $item) {
                $item->last_connect = server_to_local_by_zonenum($item->last_connect, $item->time_zone, $is_dston);
            }




            return $result;
        }

        return false;
    }

    public function get_player_extra($id)
    {
        $this->db->where('player_id', $id);
        $query = $this->db->get('cat_player_extra');
        if ($query->num_rows()) {
            return $query->row();
        } else {
            return false;
        }
    }


    public function get_player_power_exption_count($pid, $date = FALSE)
    {
        $this->db->select('*');
        $this->db->from("cat_power_record");
        $this->db->where('player_id', $pid);
        if ($date) {
            $this->db->where("DATE(off_at)", $date);
        }
        $query = $this->db->get();

        if ($query->num_rows()) {
            return $query->num_rows();
        } else {
            return 0;
        }
    }

    public function add_player_extra($array)
    {
        if (empty($array)) {
            return 0;
        }
        if ($this->db->insert('cat_player_extra', $array)) {
            return true;
        }
        return false;
    }

    public function update_player_extra($array, $id)
    {
        if (empty($array)) {
            return 0;
        }
        if ($this->get_player_extra($id)) {
            $this->db->where("player_id", $id);
            if ($this->db->update("cat_player_extra", $array)) {
                return true;
            }
        } else {
            $array['player_id'] = $id;
            return $this->add_player_extra($array);
        }
        return false;
    }

    public function get_player_pics($pid)
    {
        $this->db->where('player_id', $pid);
        $query = $this->db->get('cat_player_picture');
        if ($query->num_rows()) {
            return $query->result();
        }
        return false;
    }

    public function add_player_pic($array)
    {
        if (empty($array)) {
            return 0;
        }

        if ($this->db->insert('cat_player_picture', $array)) {
            $id = $this->db->insert_id();
            return $id;
        } else {
            return false;
        }
    }

    public function delete_player_pic($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('cat_player_picture');
    }

    public function delete_player_pics($pid)
    {
        $this->db->where('player_id', $pid);
        return $this->db->delete('cat_player_picture');
    }

    public function get_player_pic_byId($pid)
    {
        $this->db->where('id', $pid);
        $query = $this->db->get('cat_player_picture');
        if ($query->num_rows()) {
            return $query->row();
        }
        return false;
    }

    public function get_players_byCompany($cid)
    {
        $this->db->select("id,name");
        $this->db->from('cat_player');
        $this->db->where('company_id', $cid);
        $this->db->order_by('name', 'asc');
        $query = $this->db->get();

        if ($query->num_rows()) {
            return $query->result();
        }
        return false;
    }


    public function get_all_zipcode($cid)
    {
        $this->db->select('distinct(pe.conzipcode) as zipcode');
        $this->db->from("cat_player_extra pe");
        $this->db->join('cat_player p', 'pe.player_id=p.id', "LEFT");
        $this->db->where('p.company_id', $cid);
        $this->db->where('pe.conzipcode is not null');
        $this->db->where('pe.conzipcode!=', '');



        $query = $this->db->get();
        $array = array();

        if ($query->num_rows()) {
            $array = $query->result();
            $query->free_result();
            return $array;
        }

        return false;
    }
    public function get_player_list_with_extra($cid, $filter_array = array())
    {
        $filter_criterion_players = null;
        if (isset($filter_array['criteria']) && $filter_array['criteria']) {
            $this->db->select("player_id");
            $this->db->from('cat_criteria_player');
            $this->db->where('criteria_id', $filter_array['criteria']);
            $query = $this->db->get();
            if ($query->num_rows()) {
                $result = $query->result_array();
                $filter_criterion_players = array_column($result, 'player_id');

                $query->free_result();
            }
        }
        $this->db->select('p.*, pe.player_id,pe.barcode,pe.conname,pe.conphone,pe.conemail,pe.conaddr,pe.conzipcode,pe.contown,pe.simno,pe.simvolume,pe.itemnum,pe.modelname,
                         pe.screensize,pe.sided,pe.partnerid,pe.locationid,pe.geox,pe.geoy,pe.setupdate,pe.viewdirection,pe.pps,pe.visitors,pe.displaynum,pe.state,pe.country,pe.custom_sn1,pe.custom_sn2,
                         tc.name as timecfg,
				(select GROUP_CONCAT(DISTINCT t.name ORDER BY t.name ASC SEPARATOR "/" ) from cat_criteria t, cat_criteria_player tp where p.id=tp.player_id and tp.criteria_id=t.id GROUP BY p.id) as criteria_name');
        $this->db->from('cat_player p');
        $this->db->join('cat_timer_config tc', 'p.timer_config_id = tc.id', 'left');
        $this->db->join('cat_player_extra pe', 'pe.player_id = p.id', 'left');
        $this->db->join('cat_criteria_player c2', 'c2.player_id= p.id', "LEFT");



        if (!empty($filter_array)) {
            if (isset($filter_array['filter_type']) && $filter_array['filter'] && $filter_array['filter_type'] && $filter_array['filter']) {
                if ($filter_array['filter_type'] == 'name') {
                    $this->db->like('p.name', $filter_array['filter']);
                } elseif ($filter_array['filter_type'] == 'sn') {
                    $this->db->like('p.sn', $filter_array['filter']);
                }
            }
            if (isset($filter_array['online']) && $filter_array['online']) {
                $this->db->where('p.status >', 1);
            }

            if (isset($filter_array['criteria']) && $filter_array['criteria']) {
                if (is_array($filter_array['criteria'])) {
                    $this->db->where_in('c2.criteria_id', $filter_array['criteria']);
                } else {
                    $this->db->where('c2.criteria_id', $filter_array['criteria']);
                }
            }

            if ($filter_criterion_players) {
                $this->db->where_in("p.id", $filter_criterion_players);
            }
        }

        $this->db->where('p.company_id', $cid);
        $this->db->where('pe.setupdate is not null');
        $this->db->where('pe.setupdate<=', date("Y-m-d"));
        $this->db->order_by('pe.setupdate', "asc");

        $query = $this->db->get();
        if ($query->num_rows()) {
            $array = $query->result();
            $query->free_result();
            return $array;
        }

        return false;
    }

    /**
     *
     *
     * @param object $uid
     * @return
     */
    public function get_user_player($uid)
    {
        $result = array();
        $this->db->select("player_id");
        $query = $this->db->get_where("cat_user_player", array('user_id' => $uid));
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $result[] = $row->player_id;
            }
            return $result;
        }
        return false;
    }

    public function assign_user_player($cris, $uid)
    {
        if (empty($cris)) {
            return false;
        }
        $this->delete_user_player($uid);

        foreach ($cris as $cri) {
            $sql = "insert ignore into cat_user_player(user_id, player_id) values($uid, $cri)";
            $this->db->query($sql);
        }


        return true;
    }

    /**
     * É¾ï¿½ï¿½ï¿½ï¿½ï¿½Ã»ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½Ä¼ï¿½ï¿½ï¿½ï¿½Ð±ï¿½
     *
     * @param object $uid
     * @return
     */
    public function delete_user_player($uid)
    {
        $this->db->where('user_id', $uid);

        return $this->db->delete('cat_user_player');
    }


    public function get_ctiteria_player_by_user($uid)
    {
        $sql = "select player_id from cat_criteria_player where criteria_id in (select criteria_id from cat_user_criteria where user_id=$uid)";
        $query = $this->db->query($sql);

        $result = $query->result_array();
        return array_column($result, 'player_id');
    }

    public function get_tags_by_player($playerid)
    {
        $query = $this->db->query('			
						SELECT id,name FROM cat_tag  WHERE 
						id in (SELECT tag_id
						FROM taggables 
						WHERE taggable_type="App\\\Player" and taggable_id =' . $playerid . ')');
        if ($query->num_rows()) {
            return $query->result_array();
        } else {
            return false;
        }
    }

    public function get_disconnected_players($cid, $start_time, $end_time)
    {
        $this->db->select("id,name,sn,company_id,last_connect");
        $this->db->from('cat_player');
        $this->db->where('company_id', $cid);
        $this->db->where('status', 1);
        $this->db->where('last_connect>', $start_time);
        $this->db->where('last_connect<=', $end_time);

        $query = $this->db->get();

        if ($query->num_rows()) {
            $result = $query->result();
            return $result;
        }

        return false;
    }

    public function calDistance($player_lat, $player_lng, $lat, $lng)
    {
        $radLat1 = deg2rad(floatval($lat));
        $radLat2 = deg2rad(floatval($player_lat));

        $radLng1 = deg2rad(floatval($lng));
        $radLng2 = deg2rad(floatval($player_lng));
        $a = $radLat1 - $radLat2;
        $b = $radLng1 - $radLng2;
        $s = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2))) * 6378.137 * 1000;
        $distance = round($s, 4);
        return $distance;
    }

    public function getNearbyDevices($cid, $lat, $lng, $radius, $addr, $city, $zipcode, $parter_criterion = false)
    {

        $this->db->select('p.name,p.sn,p.id,pe.conaddr,pe.conzipcode,pe.contown,pe.viewdirection,pe.geox,pe.geoy');
        $this->db->from('cat_player_extra pe');
        $this->db->join('cat_player p', 'p.id=pe.player_id', "LEFT");
        $this->db->where('company_id', $cid);

        $this->db->where('pe.setupdate!=', null);
        // $this->db->where('pe.conaddr!=', '');
        $this->db->where('pe.setupdate<=', date("Y-m-d"));
        if ($zipcode && $city) {
            $this->db->group_start();
            $this->db->where('pe.conzipcode', $zipcode);
            $this->db->or_like('pe.contown', $city);


            $this->db->group_end();
        } elseif ($city) {
            $this->db->where('pe.contown', $city);
        } elseif ($zipcode) {
            $this->db->where('pe.conzipcode', $zipcode);
        }

        if (!$city && !$zipcode && $addr) {
            $this->db->group_start();
            $this->db->where('pe.conzipcode', $addr);
            $this->db->or_like('pe.contown', $addr);
            $this->db->or_like('pe.viewdirection', $addr);

            $this->db->group_end();
        }
        if ($parter_criterion) {
            $this->db->join('cat_criteria_player cp', 'cp.player_id= p.id', "LEFT");
            $this->db->where('cp.criteria_id', $parter_criterion);
        }

        $query = $this->db->get();

        if ($query->num_rows()) {
            $result = $query->result();

            if ($lat && $lng) {
                foreach ($result as &$player) {
                    $player->distance = $this->calDistance($player->geox, $player->geoy, $lat, $lng);
                }

                $radius = $radius * 1000;
                $players = array_filter($result, function ($value) use ($radius) {
                    if (isset($value->distance) && $value->distance <= $radius) {
                        return true;
                    }
                    return false;
                });
            } else {
                $players = $result;
            }
            return $players;
        }
        return false;
    }
    public function get_paterner_criterias($partner_id)
    {
        if ($partner_id <= 0) {
            return false;
        }

        $sql = "SELECT cp.player_id from cat_criteria_player cp
                LEFT JOIN cat_parter_fields pf on pf.criterion_id = cp.criteria_id
                WHERE pf.partner_id = $partner_id";
        $query = $this->db->query($sql);
        $array = array();
        $total = 0;

        $player_array = array();
        if ($query->num_rows()) {
            $array = $query->result_array();
            $player_array = array_column($array, 'player_id');

            $this->db->select('c.id,c.name');
            $this->db->from('cat_criteria c');
            $this->db->join("cat_criteria_player cp", "cp.criteria_id=c.id", "LEFT");
            $this->db->where_in('cp.player_id', $player_array);
            $this->db->distinct();
            $query = $this->db->get();
            $total = $query->num_rows();
            $array = array();
            if ($total) {
                $array =  $query->result();
            }
        }
        return array('total' => $total, 'data' => $array, 'player_array' => $player_array);
    }
    /**
     * Sync player's by criteria id
     *
     * @param object $array
     * @param object $id
     * @return
     */
    public function sync_criteria_players($players, $criteria_id)
    {
        $this->db->where('criteria_id', $criteria_id);
        $this->db->delete('cat_criteria_player');

        if (empty($players)) {
            return;
        }

        if (!is_array($players)) {
            $players = explode(",", $players);
        }
        $data = array();
        $this->db->trans_start();
        foreach ($players as $pid) {
            $this->db->insert('cat_criteria_player',  array('player_id' => $pid, 'criteria_id' => $criteria_id));
        }
        $this->db->trans_complete();
    }


    /**
     * get_player_by_criterion
     *
     * @param object $id
     * @return
     */
    public function get_player_by_criterion($criid)
    {
        $this->db->select('p.id,p.name');
        $this->db->from('cat_player p');
        $this->db->join('cat_criteria_player cp', 'cp.player_id=p.id', "LEFT");
        $this->db->where('cp.criteria_id', $criid);

        $query = $this->db->get();

        $result = array();
        if ($query->num_rows()) {
            $result = $query->result();
        }
        return $result;
    }

    public function get_last_append_num_of_player_name($cid, $name)
    {
        $this->db->select("name");
        $this->db->from('cat_player');
        $this->db->where('company_id', $cid);
        $this->db->like('name', $name, 'after');
        $this->db->order_by('name', 'desc');
        $this->db->limit(1);


        $query = $this->db->get();

        if ($query->num_rows()) {
            $name = $query->row()->name;

            $start = strripos($name, '(');
            $end = strripos($name, ')');

            if ($start === false || $end === false) {
                return 0;
            }
            $start += 1;
            if ($end > $start) {
                $numchar = substr($name, $start, $end - $start);
                if (is_numeric($numchar)) {
                    return intval($numchar);
                }
            }
        }
        return false;
    }

    public function get_criteria_id_byName($cid, $nameAry)
    {
        if (empty($nameAry)) {
            return false;
        }
        if (!is_array($nameAry)) {
            if (strstr($nameAry, '/')) {
                $nameAry = explode("/", $nameAry);
            } else {
                $nameAry = explode(",", $nameAry);
            }
        }

        for ($i = 0; $i < count($nameAry); $i++) {
            $nameAry[$i] = trim($nameAry[$i]);
        }

        $this->db->select("id");
        $this->db->from("cat_criteria");
        $this->db->where("company_id", $cid);
        $this->db->where_in('name', $nameAry);
        $query = $this->db->get();

        if ($query->num_rows()) {
            return array_column($query->result_array(), 'id');
        }
        return false;
    }

    /**
     * check_player_exist_for_importing
     *
     * @param [type] $sadwid by sdaw id
     * @return true if player is exist; false if player is not exist.
     */
    public function check_player_exist_for_importing($sadwid)
    {
        if (empty($sadwid)) {
            return false;
        }
        $this->db->select("id");
        $this->db->from("cat_player_extra");
        $this->db->where('custom_sn1', $sadwid);

        $query = $this->db->get();
        if ($query->num_rows()) {
            return true;
        }
        return false;
    }

    public function detach_tag_players($tagid, $type = 'App\Player')
    {
        $this->db->where("tag_id", $tagid);
        $this->db->where("taggable_type", $type);
        $this->db->delete("taggables");
    }

    //Type: 'App\Campaign','App\Player'
    public function sync_tag_players($tag_id, $players)
    {
        $type = 'App\Player';
        $this->detach_tag_players($tag_id, $type);
        $this->attach_tag_players($tag_id, $players, $type);
    }
    public function attach_tag_players($tagid, $players, $type)
    {
        if (!$players) {
            return;
        }
        $players = is_array($players) ? $players : explode(",", $players);

        if (empty($players)) {
            return;
        }
        $data = array();
        foreach ($players as $playerid) {
            if (!empty($playerid)) {
                $item = array('tag_id' => $tagid, 'taggable_id' => $playerid, 'taggable_type' => $type);
                $data[] = $item;
            }
        }
        if (!empty($data)) {
            $this->db->insert_batch('taggables', $data);
        }
    }
    public function get_ssp_media_list($pid)
    {
        $sql = "SELECT sm.* from ssp_media sm,player_sspmedium pm WHERE pm.medium_id =sm.id and player_id = $pid";
        $query = $this->db->query($sql);
        if ($query->num_rows()) {
            return $query->result();
        }
        return false;
    }


    public function send_command($ids, $type, $value)
    {
        $sn = '';
        $num = 0;
        $sn_length = 0;
        $length_dechex = 0;

        $players = $this->get_player_control_by_ids($ids);

        $socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);

        // Send the message to the server

        if (is_array($players)) {
            $host = $this->config->item("socket_server") ? $this->config->item("socket_server") : "127.0.0.1";

            $port = $this->config->item("tcp_port") ?: 4702;
            $socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
            foreach ($players as $player) {
                $item = array("command" => 0x3, "sn" => $player->sn, 'fd' => $player->socket_fd, 'type' => $type, "value" => $value);
                $str = json_encode($item);
                $ret = socket_sendto($socket, $str, strlen($str), 0, $host, $port);
            }
            socket_close($socket);
            /*
            $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP) or die("Could not create  socket\n");  // åˆ›å»ºä¸€ä¸ªSocket
            $connection = socket_connect($socket, $host, $port) or die("Could not connet server\n");    //  è¿žæŽ¥
            $this->load->library('Utils');
            $utils = new Utils();
            $c_type = $type;
            foreach ($players as $player) {
                $sn_length = strlen($player->sn);
                $fd_length = strlen($player->socket_fd);
                $data = pack('Ca4CCa' . $sn_length . 'a' . $fd_length . 'CCC', 0x00, '1234', $sn_length, $fd_length, $player->sn, $player->socket_fd, $c_type, $value, 0x3);


                //blowfish åŠ å¯†DATAæ•°æ®
                $encdata = $utils->blowfish_enc($data);
                $length = strlen($encdata);

                $header = pack('C4', 0xec, 0xeb, 0x0f, $length);

                //CRCæ ¡éªŒ
                $msg = $header . $encdata;
                $crc = $utils->crc16($msg);
                //æ‹¼è£…æ•°æ®åŒ…
                $loginmsg = $msg . pack('C2', (($crc & 0xff00) >> 8), ($crc & 0xff));


                socket_write($socket, $loginmsg) or die("Write failed\n"); // æ•°æ®ä¼ é€ å‘æœåŠ¡å™¨å‘é€æ¶ˆæ¯
            }
          
            socket_close($socket);
              */
        }
    }
    public function get_sspcriteria_list($cid, $offset = 0, $limit = -1, $order_item = 'name', $order = 'asc', $filter_array = array())
    {
        $array = array();

        $this->db->select('r.*,(select count(p.player_id) from ssp_criteria_player p where p.criteria_id=r.id) as player_count, t.name as type_name');
        $this->db->from('ssp_criteria r');
        $this->db->where('r.company_id', $cid);
        $this->db->join('ssp_code_types t', 't.id=r.type', 'left');

        if (!empty($filter_array)) {
            if (isset($filter_array['name']) && $filter_array['name']) {
                $this->db->like('r.name', $filter_array['name']);
                $this->db->or_like('r.code', $filter_array['name']);
            }
            if (isset($filter_array['type']) && $filter_array['type'] != -1) {
                $this->db->where('r.type', $filter_array['type']);
            }
        }

        $total = $this->db->count_all_results('', false);

        if ($total > 0) {
            //$this->db = $db;
            $this->db->order_by($order_item, $order);

            if ($limit != -1) {
                $this->db->limit($limit, $offset);
            }
            $query = $this->db->get();
            if ($query->num_rows()) {
                $array = $query->result();
                $query->free_result();
            }
        }
        $this->db->reset_query();
        return array('total' => $total, 'data' => $array);
    }


    /**
     * Retrieve SSP code types from the database.
     *
     * This method selects the 'id' and 'name' columns from the 'ssp_code_types' table
     * and returns the result as an array of objects.
     *
     * @return array An array of objects containing 'id' and 'name' of SSP code types.
     *
     */
    public function get_ssp_code_types()
    {
        $this->db->select('id, name');
        $this->db->from('ssp_code_types');
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * 
     *
     * @param object $id
     * @return
     */
    public function get_sspcriteria($id, $with_players = false)
    {
        // $this->db->where('id', $id);
        // $query = $this->db->get('cat_criteria');
        $this->db->select("c.*,GROUP_CONCAT(p.player_id ) as players");
        $this->db->from('ssp_criteria c');
        $this->db->join('ssp_criteria_player p', 'p.criteria_id=c.id', "LEFT");
        $this->db->where('id', $id);

        $query = $this->db->get();


        if ($query->num_rows()) {
            $result = $query->row();
            if ($with_players) {
                $attached_players = $this->get_sspcriteron_playerids($id);
                if ($attached_players) {
                    $result->players = $attached_players;
                }
            }
            return $result;
        } else {
            return false;
        }
    }

    public function get_sspcriteron_playerids($criterion_id)
    {
        $this->db->select("player_id");
        $this->db->from('ssp_criteria_player');
        $this->db->where('criteria_id', $criterion_id);

        $query = $this->db->get();


        if ($query->num_rows()) {
            $result = $query->result_array();
            return array_column($result, 'player_id');
        } else {
            return false;
        }
    }

    /**
     * get_criteria_byname
     * 2013-9-17 9:01:10
     * @param object $id
     * @param object $cid
     * @param object $name
     * @return
     */
    public function get_sspcriteria_byname($id, $cid, $name, $code, $type)
    {
        $name = $this->db->escape_str($name);

        if ($id > 0) {
            $sql = "select id from ssp_criteria where id != $id and company_id = '$cid' and type='$type' and (name = '$name')";
        } else {
            $sql = "select id from ssp_criteria where company_id = '$cid' and type='$type' and (name = '$name')";
        }
        $query = $this->db->query($sql);

        if ($query->num_rows()) {
            return $query->row();
        } else {
            return false;
        }
    }
    /**
     * ï¿½ï¿½ï¿½ï¿½criteria
     *
     * @param object $array
     * @return
     */
    public function add_sspcriteria($array)
    {
        if (empty($array)) {
            return 0;
        }
        if ($this->db->insert('ssp_criteria', $array)) {
            $id = $this->db->insert_id();
            //$this->user_log($this->OP_TYPE_USER, 'add_criteria['.$id.'] name['.json_encode($array).']');
            return $id;
        } else {
            return false;
        }
    }

    /**
     * ï¿½ï¿½ï¿½ï¿½criteriaï¿½ï¿½Ï¢
     *
     * @param object $array
     * @param object $id
     * @return
     */
    public function update_sspcriteria($array, $id)
    {
        if (empty($array)) {
            return 0;
        }

        $this->db->where('id', $id);
        if ($this->db->update('ssp_criteria', $array)) {
            // $this->user_log($this->OP_TYPE_USER, 'update_criteria['.$id.'] name['.json_encode($array).']');
            return true;
        } else {
            return false;
        }
    }

    /**
     * criteria
     *
     * @return
     */
    public function delete_sspcriteria($id)
    {
        $this->db->trans_begin();
        $this->db->query('delete from ssp_criteria where id = ' . $id);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            // $this->user_log($this->OP_TYPE_USER, 'delete_criteria['.$id.']', $this->OP_STATUS_FAIL);
            return false;
        } else {
            $this->db->trans_commit();
            //$this->user_log($this->OP_TYPE_USER, 'delete_criteria['.$id.']');
            return true;
        }
    }

    public function sync_sspcriteria_players($players, $criteria_id)
    {
        $this->db->where('criteria_id', $criteria_id);
        $this->db->delete('ssp_criteria_player');

        if (empty($players)) {
            return;
        }

        if (!is_array($players)) {
            $players = explode(",", $players);
        }
        $data = array();
        foreach ($players as $pid) {
            $data[]  = array('player_id' => $pid, 'criteria_id' => $criteria_id);
        }
        $this->db->insert_batch('ssp_criteria_player', $data);
    }

    public function save_power_record($array)
    {
        $this->db->select("id");
        $this->db->from('cat_power_record');
        $this->db->where('player_id', $array['player_id']);
        $this->db->where('off_at', $array['off_at']);
        $query = $this->db->get();
        if ($query->num_rows()) {
            return false;
        }
        $this->db->insert('cat_power_record', $array);
    }

    public function get_ssptag_list($cid, $uid = false, $offset = 0, $limit = -1, $order_item = 'name', $order = 'ASC', $name = '')
    {
        //FIXME
        $tagary = array();

        $this->db->select('count(id) as total');
        $this->db->from("ssp_tags");
        $this->db->where('company_id', $cid);

        if ($name != '') {
            $this->db->like('name', $name);
        }

        if ($uid && !empty($tagary)) {
            $this->db->where_in('id', $tagary);
        }

        //echo $this->db->get_sql();die();
        $query = $this->db->get();
        $total = $query->row()->total;
        $this->db->select('t.*,COUNT(p.player_id) as player_cnt');
        $this->db->from('ssp_tags t');
        $this->db->join('ssp_tag_player p', 'p.tag_id=t.id', "LEFT");
        $this->db->where('t.company_id', $cid);

        if ($name != '') {
            $this->db->like('t.name', $name);
        }

        $this->db->group_by('t.id');

        if ($uid && !empty($tagary)) {
            $this->db->where_in('id', $tagary);
        }

        $this->db->order_by($order_item, $order);
        if ($limit != -1) {
            $this->db->limit($limit, $offset);
        }
        //echo $this->db->get_sql();die();
        $query = $this->db->get();
        $array = array();

        if ($query->num_rows()) {
            $array = $query->result();
            $query->free_result();
        }
        return array('total' => $total, 'data' => $array);
    }


    /**
     * ï¿½ï¿½È¡ï¿½ï¿½ï¿½ï¿½tagï¿½ï¿½Ï¢
     *
     * @param object $id
     * @return
     */
    public function get_ssptag($id)
    {
        $this->db->select("t.*,GROUP_CONCAT(p.player_id ) as players");
        $this->db->from('ssp_tags t');
        $this->db->join('ssp_tag_player p', 'p.tag_id=t.id', "LEFT");
        $this->db->where('t.id', $id);

        $query = $this->db->get();

        if ($query->num_rows()) {
            $result = $query->row();

            $attached_players = $this->get_ssptag_playerids($id);
            if ($attached_players) {
                $result->players = $attached_players;
            }

            return $result;
        } else {
            return false;
        }
    }


    public function get_ssptag_playerids($tag_id)
    {
        $this->db->select("player_id");
        $this->db->from('ssp_tag_player');
        $this->db->where('tag_id', $tag_id);

        $query = $this->db->get();


        if ($query->num_rows()) {
            $result = $query->result_array();
            return array_column($result, 'player_id');
        } else {
            return false;
        }
    }
    /**
     *
     * @param object $id
     * @param object $cid
     * @param object $name
     * @return
     */
    public function get_ssptag_byname($id, $cid, $name)
    {
        $name = $this->db->escape_str($name);
        if ($id > 0) {
            $sql = "select id from ssp_tags where id != $id and company_id = '$cid' and name = '$name'";
        } else {
            $sql = "select id from ssp_tags where company_id = '$cid' and name = '$name'";
        }
        $query = $this->db->query($sql);

        if ($query->num_rows()) {
            return $query->row();
        } else {
            return false;
        }
    }

    /**
     * Create new tag
     *
     * @param object $array
     * @param object $uid
     * @return
     */
    public function add_ssptag($array, $uid)
    {
        if (empty($array)) {
            return 0;
        }


        if ($this->db->insert('ssp_tags', $array)) {
            $id = $this->db->insert_id();
            return $id;
        } else {
            return false;
        }
    }

    /**
     * ï¿½ï¿½ï¿½ï¿½tagï¿½ï¿½Ï¢
     *
     * @param object $array
     * @param object $id
     * @return
     */
    public function update_ssptag($array, $id)
    {
        if (empty($array)) {
            return 0;
        }

        $this->db->where('id', $id);
        if ($this->db->update('ssp_tags', $array)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * É¾ï¿½ï¿½Ò»ï¿½ï¿½tagï¿½ï¿½É¾ï¿½ï¿½ï¿½ëµ±Ç°ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½Ð¹ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½
     *
     * 1.É¾ï¿½ï¿½tag
     *
     * @param object $group_id
     * @return
     */
    public function delete_ssptag($tag_id)
    {
        $this->db->trans_begin();
        $this->db->query('delete from ssp_tags where id = ' . $tag_id);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }

    public function sync_player_sspcriteria($player_id, $criteria)
    {
        $this->db->where('player_id', $player_id);
        $this->db->delete('ssp_criteria_player');

        if (empty($criteria)) {
            return;
        }

        if (!is_array($criteria)) {
            $criteria = explode(",", $criteria);
        }
        $data = array();
        foreach ($criteria as $criterion) {
            $data[]  = array('player_id' => $player_id, 'criteria_id' => $criterion);
        }
        $this->db->insert_batch('ssp_criteria_player', $data);
    }

    public function sync_player_ssptag($player_id, $tags)
    {
        $this->db->where('player_id', $player_id);
        $this->db->delete('ssp_tag_player');

        if (empty($tags)) {
            return;
        }

        if (!is_array($tags)) {
            $tags = explode(",", $tags);
        }
        $data = array();
        foreach ($tags as $tag) {
            $data[]  = array('player_id' => $player_id, 'tag_id' => $tag);
        }
        $this->db->insert_batch('ssp_tag_player', $data);
    }

    public function sync_ssptag_players($players, $tag_id)
    {
        $this->db->where('tag_id', $tag_id);
        $this->db->delete('ssp_tag_player');

        if (empty($players)) {
            return;
        }

        if (!is_array($players)) {
            $players = explode(",", $players);
        }
        $data = array();
        foreach ($players as $pid) {
            $data[]  = array('player_id' => $pid, 'tag_id' => $tag_id);
        }
        $this->db->insert_batch('ssp_tag_player', $data);
    }

    public function get_sspcriteria_ids_by_player($playerid, $type = 0)
    {
        if ($type == 0) {
            $this->db->select('GROUP_CONCAT(c.id) as cristr');
        } else {
            $this->db->select('GROUP_CONCAT(c.name) as cristr');
        }
        $this->db->from('ssp_criteria c');
        $this->db->join('ssp_criteria_player p', 'c.id=p.criteria_id', 'left');
        $this->db->where('p.player_id', $playerid);
        $query = $this->db->get();

        $this->db->last_query();
        if ($query->num_rows()) {
            return $query->row()->cristr;
        } else {
            return false;
        }
    }
    public function get_ssptags_ids_by_player($playerid, $type = 0)
    {
        if ($type == 0) {
            $this->db->select('GROUP_CONCAT(t.id) as tagstr');
        } else {
            $this->db->select('GROUP_CONCAT(t.name) as tagstr');
        }

        $this->db->from('ssp_tags t');
        $this->db->join('ssp_tag_player p', 't.id=p.tag_id', 'left');
        $this->db->where('p.player_id', $playerid);
        $query = $this->db->get();


        if ($query->num_rows()) {
            return $query->row()->tagstr;
        } else {
            return false;
        }
    }

    public function get_player_least_free_byDate($player_id, $date = false)
    {
        if (!$player_id && !$date) {
            return false;
        }
        $this->db->select("*");
        $this->db->from('cat_player_leastfree');
        if (is_array($player_id)) {
            $this->db->where_in('player_id', $player_id);
        } else {
            $this->db->where('player_id', $player_id);
        }

        if ($date) {
            $this->db->where('at_date', $date);
        }

        $query = $this->db->get();

        if ($query->num_rows()) {
            return $query->result();
        }
        return false;
    }



    public function delete_player_least_free($player_id, $start_date = false, $end_date = false)
    {
        if (!$player_id && !$end_date) {
            return false;
        }
        if ($player_id) {
            $this->db->where('player_id', $player_id);
        }
        if ($start_date && $end_date && $start_date == $end_date) {
            $this->db->where('at_date', $start_date);
        } else {
            if ($start_date) {
                $this->db->where('at_date>=', $start_date);
            }
            if ($end_date) {
                $this->db->where('at_date<=', $end_date);
            }
        }

        return $this->db->delete('cat_player_leastfree');
    }

    public function saveMany_Planed($arrays)
    {
        if (empty($arrays)) {
            return false;
        }

        $this->db->insert_batch('cat_player_campaign_planed',  $arrays);
        /*

        $this->db->trans_start();
        foreach ($arrays as $plan) {
            $this->db->insert('cat_player_campaign_planed',  $plan);
        }
        $this->db->trans_complete();
        */
    }

    public function saveMany_least_free($arrays)
    {
        if (empty($arrays)) {
            return false;
        }
        $this->db->insert_batch('cat_player_leastfree', $arrays);
        /*
        $this->db->trans_start();
        foreach ($arrays as $leastFree) {
            $this->db->insert('cat_player_leastfree', $leastFree);
        }
        $this->db->trans_complete();
        */
    }


    public function get_amc_from_timer($timer_id)
    {
        $result = new stdClass();
        $this->load->model('strategy');
        $ptimer = $this->strategy->get_timer_details($timer_id);
        $result = new stdClass();
        if ($ptimer) {
            for ($weekd = 1; $weekd <= 7; $weekd++) {
                $todaystimer = false;
                if ($ptimer['type'] != 0) {
                    if ($ptimer['offwds'] && in_array($weekd, $ptimer['offwds'])) {
                    } else {
                        $todaystimer = $ptimer['data'][$weekd];
                    }
                } else {
                    $todaystimer = $ptimer['data'][0];
                }
                $amc = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);

                if ($todaystimer) {
                    foreach ($todaystimer as $timerItem) {
                        if ($timerItem['endH'] == 0 && $timerItem['endM'] == 0) {
                            $timerItem['endH'] = 24;
                        }
                        for ($hour = 0; $hour <= 24; $hour++) {
                            if ($hour >= $timerItem['startH'] && $hour < $timerItem['endH']) {
                                $amc[$hour] = 1;
                            }
                        }
                    }
                }
                switch ($weekd) {
                    case 1:
                        $result->mon = implode(",", $amc);
                        break;
                    case 2:
                        $result->tue = implode(",", $amc);

                        break;
                    case 3:
                        $result->wed = implode(",", $amc);

                        break;
                    case 4:
                        $result->thu = implode(",", $amc);

                        break;
                    case 5:
                        $result->fri = implode(",", $amc);

                        break;
                    case 6:
                        $result->sat = implode(",", $amc);

                        break;
                    case 7:
                        $result->sun = implode(",", $amc);

                        break;
                }
            }
            return $result;
        } else {
            $amc = array(1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1);

            $result->mon = implode(",", $amc);
            $result->tue = implode(",", $amc);
            $result->wed = implode(",", $amc);
            $result->thu = implode(",", $amc);
            $result->fri = implode(",", $amc);
            $result->sat = implode(",", $amc);
            $result->sun = implode(",", $amc);
        }
    }
    public function get_player_amc($player)
    {
        if (!$player) {
            return false;
        }
        $this->db->select("mon,tue,wed,thu,fri,sat,sun");
        $this->db->where('player_id', $player->id);
        $this->db->from('ssp_amc');
        $query = $this->db->get();

        if ($query->num_rows()) {
            return $query->row();
        } else {
            return $this->get_amc_from_timer($player->timer_config_id);
        }
        return false;
    }

    public function update_player_amc($player_id, $amc)
    {
        if (empty($amc)) {
            return;
        }
        $this->db->where('player_id', $player_id);
        $this->db->delete('ssp_amc');
        $amc['player_id'] = $player_id;
        $this->db->insert('ssp_amc', $amc);
    }

    public function update_macs()
    {
        $this->db->select("p.id,p.timer_config_id");
        $this->db->from('cat_player p');
        $this->db->join('ssp_criteria_player sp', 'sp.player_id=p.id', "RIGHT");
        $query = $this->db->get();


        if ($query->num_rows()) {
            $players = $query->result();
            foreach ($players as $player) {
                $amc = (array)$this->get_amc_from_timer($player->timer_config_id);

                $this->update_player_amc($player->id, $amc);
            }
        }
        return false;
    }

    public function get_company_players_count($cid, $pid = null)
    {
        $ret = array();

        $pids = false;
        if ($pid) {
            $cris = $this->get_paterner_criterias($cid);
            $pids = $cris['player_array'];
        }
        $this->db->select("id,status");
        $this->db->from('cat_player');
        $this->db->where('company_id', $pid ?: $cid);
        if ($pids) {
            $this->db->where_in("id", $pids);
        }
        $query = $this->db->get();

        $ret['players_cnt'] = $query->num_rows();
        $ret['online_cnt'] = 0;
        if ($query->num_rows() > 0) {

            $result = $query->result_array();
            $onlines = array_filter($result, function ($player) {
                if ($player['status'] > 1) {
                    return true;
                }
                return false;
            });
            if ($onlines) {
                $ret['online_cnt'] = count($onlines);
            }
        }

        return $ret;
    }

    public function send_command_new($ids, $params)
    {
        if (empty($ids)) {
            return;
        }
        $players = $this->get_player_control_by_ids($ids);


        if (is_array($players)) {
            $host = $this->config->item("socket_server") ?: "127.0.0.1";

            $port = $this->config->item("tcp_port") ?: 4702;
            $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP) or die("Could not create  socket\n");  // åˆ›å»ºä¸€ä¸ªSocket
            //$connection = socket_connect($socket, $host, $port);

            $connection = socket_connect($socket, $host, $port);
            if (!$connection) {
                return;
            }

            $this->load->model('device');

            $this->load->library('Utils');
            $utils = new Utils();

            foreach ($players as $player) {
                $sn_length = strlen($player->sn);
                $fd_length = strlen($player->socket_fd);
                //  $data = pack('Ca4CCa' . $sn_length . 'a' . $fd_length . 'CCC', 0x00, '1234', $sn_length, $fd_length, $player->sn, $player->socket_fd, $c_type, $value, 0x3);
                //$data = pack('Ca' . $fd_length . 'a12', $fd_length, $player->socket_fd,  $params->command);
                $settings = json_encode(array('baudrate' => $params->baudrate, 'parity' => $params->parity, 'stop_bits' => $params->stop_bits, 'data_bits' => $params->data_bits));
                $setings_length = strlen($settings);

                $command = str_replace(' ', '', $params->command);
                $commandLen = strlen($command);
                /*if ($commandLen < 12) {
                    $command = str_pad($command, 12, ' ', STR_PAD_RIGHT);
                }
                */

                $data = pack('NCCa' . $commandLen . 'Ca' . $setings_length, $player->socket_fd, $params->address, $commandLen, $command, $setings_length, $settings);
                //blowfish åŠ å¯†DATAæ•°æ®
                $encdata = $utils->blowfish_enc($data);
                $length = strlen($encdata);

                $header = pack('C4', 0xec, 0xeb, 0x0ff, $length);

                //CRCæ ¡éªŒ
                $msg = $header . $encdata;
                $crc = $utils->crc16($msg);
                //æ‹¼è£…æ•°æ®åŒ…
                $loginmsg = $msg . pack('C2', (($crc & 0xff00) >> 8), ($crc & 0xff));
                $this->device->add_player_log($player->id, 5, "Send command to $params->name: $params->command");

                socket_write($socket, $loginmsg) or die("Write failed\n"); // æ•°æ®ä¼ é€ å‘æœåŠ¡å™¨å‘é€æ¶ˆæ¯
            }

            socket_close($socket);
        }
    }

    public function get_player_byIDs($player_id)
    {
        $this->db->select("p.id,p.name,p.timer_config_id,p.company_id,pe.custom_sn1,pe.custom_sn2,t.offweekdays");
        $this->db->from("cat_player p");
        $this->db->join("cat_player_extra pe", 'pe.player_id = p.id', 'left');
        $this->db->join("cat_timer_config t", 't.id = p.timer_config_id', 'left');

        if (is_array($player_id)) {
            $this->db->where_in("p.id", $player_id);
        } else {
            $this->db->where("p.id", $player_id);
        }

        $query = $this->db->get();
        $cnt = $query->num_rows();
        if ($query->num_rows() > 0) {
            return $query->result();
        }
        return false;
    }

    public function saveMany_programmatic_bookings($arrays)
    {
        if (empty($arrays)) {
            return false;
        }
        $this->db->insert_batch('cat_player_programmatic_booking', $arrays);
    }

    public function delete_player_programmatic_bookings($player_id, $start_date = false, $end_date = false)
    {
        if (!$player_id && !$end_date) {
            return false;
        }
        if ($player_id) {
            $this->db->where('player_id', $player_id);
        }
        if ($start_date && $end_date && $start_date == $end_date) {
            $this->db->where('at_date', $start_date);
        } else {
            if ($start_date) {
                $this->db->where('at_date>=', $start_date);
            }
            if ($end_date) {
                $this->db->where('at_date<=', $end_date);
            }
        }

        return $this->db->delete('cat_player_programmatic_booking');
    }
}
