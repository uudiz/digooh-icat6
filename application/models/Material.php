<?php
class Material extends MY_Model
{

    /**
     * 获取媒体文件总数
     *
     * @param object $cid
     * @return
     */
    public function get_company_media_count($cid)
    {
        $sql = 'select count(*) as total from cat_media where company_id=' . $cid;
        $query = $this->db->query($sql);
        return $query->row()->total;
    }


    public function get_tag_media_count($cid, $tagid, $meida_type = -1)
    {
        $sql = "select count('m.id') as total from cat_media m, cat_tag_media tm 
				where m.company_id=$cid and m.id=tm.media_id and m.deleted_at is null 
				and tm.tag_id = $tagid";

        if ($meida_type != -1) {
            $sql .= " and m.media_type = $meida_type";
        }

        $query = $this->db->query($sql);
        return $query->row()->total;
    }

    /**
     * 获取公司下所有的媒体目录
     *
     * @param object $cid
     * @return
     */
    public function get_all_folder_list($cid, $folder_ids = array(), $add_user_id = false)
    {
        if ($this->config->item('with_sub_folders')) {
            $this->db->select('id, name,pId,name as text');
        } else {
            $this->db->select('id, name');
        }
        $this->db->from('cat_media_folder');
        if ($cid) {
            $this->db->where('company_id', $cid);
        }
        if (!empty($folder_ids)) {
            $this->db->where_in('id', $folder_ids);
        }

        if ($add_user_id) {
            $this->db->where('add_user_id', $add_user_id);
        }

        $array = array();
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            if ($this->config->item('with_sub_folders')) {
                $array = $query->result_array();
            } else {
                $array = $query->result();
            }
            $query->free_result();
        }

        return $array;
    }

    /**
     *
     * @param object $cid
     * @param object $offset
     * @param object $limit
     * @param object $order_item [optional]
     * @param object $order [optional]
     * @return
     */
    public function get_folder_list($cid, $offset, $limit, $order_item = 'id', $order = 'desc', $filter_array = array())
    {
        $this->db->select("count(*) as total");
        $this->db->where('company_id', $cid);
        /*
        if ($add_user_id) {
            $this->db->where('add_user_id', $add_user_id);
        }
        */
        $query = $this->db->get('cat_media_folder');
        $total = $query->row()->total;
        $array = array();
        if ($total > 0) {
            $this->db->select("mf.*,u.name as add_user,
				 (SELECT GROUP_CONCAT(distinct t1.name) FROM cat_tag t1 RIGHT JOIN taggables t2 ON t2.tag_id=t1.id where t2.taggable_id = mf.id and t2.taggable_type='App\\\Folder') as tag_name ");
            $this->db->where('mf.company_id', $cid);

            if ($limit > 0) {
                $this->db->limit($limit, $offset);
            }
            $this->db->from('cat_media_folder mf');
            $this->db->join('cat_user u', 'u.id = mf.add_user_id', 'left');
            if (isset($filter_array['ids'])) {
                $this->db->where_in('mf.id', $filter_array['ids']);
            }
            if ($order_item != 'tag_name') {
                $this->db->order_by('mf.' . $order_item, $order);
            } else {
                $this->db->order_by($order_item, $order);
            }

            $query = $this->db->get();
            if ($query->num_rows() > 0) {
                $array = $query->result_array();
                $query->free_result();
            }
        }

        return array('total' => $total, 'data' => $array);
    }


    public function get_folder_array($cid)
    {
        $this->db->select('id,name,pId');
        $this->db->where('company_id', $cid);
        $this->db->from('cat_media_folder');

        $this->db->order_by('name', 'asc');
        //$result_ary[] =  array('id'=>0,'pId'=>-1,'name'=>"Root");

        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            //$result_ary = array_merge($result_ary,$query->result_array());
            $result_ary = $query->result_array();
            $query->free_result();
            return $result_ary;
        }


        return false;
    }

    /**
     * 获取单个目录
     *
     * @param object $id
     * @return
     */
    public function get_folder($id)
    {
        if ($id) {
            $this->db->select("f.*,GROUP_CONCAT(distinct t.tag_id) as tags");
            $this->db->from("cat_media_folder f");
            $this->db->join('taggables t', "t.taggable_id=f.id and t.taggable_type='App\\\Folder'", "LEFT");
            $this->db->where('f.id', $id);
            $this->db->group_by("f.id");
            $query = $this->db->get('cat_media_folder');
            if ($query->num_rows() > 0) {
                return $query->row();
            }
        }
        return false;
    }

    /**
     * 获取目录下的媒体文件数
     *
     * @param object $id
     * @return
     */
    public function get_folder_media_count($id)
    {
        $this->db->select('count(*) as total');
        $this->db->where('deleted_at is null');
        $this->db->where('folder_id', $id);
        $query = $this->db->get('cat_media');
        return $query->row()->total;
    }

    /**
     * 获取媒体文件的ID
     *
     * @param object $id
     * @return
     */
    public function get_folder_media_id($id)
    {
        $this->db->select('id');
        $this->db->where('folder_id', $id);
        $query = $this->db->get('cat_media');
        if ($query->num_rows()) {
            foreach ($query->result() as $row) {
                $media_id[] = $row->id;
            }
            //return $query->result();
            return $media_id;
        } else {
            return false;
        }
    }

    /**
     * 更新目录
     *
     * @param object $array
     * @param object $id
     * @return
     */
    //FIXME UPDATEFOLDER
    public function update_folder($array, $id)
    {
        if (empty($array)) {
            return 0;
        }

        $tags = null;
        if (isset($array['tags'])) {
            if ($array['tags'] && $array['tags'] !== 'null') {
                $tags = is_array($array['tags']) ? $array['tags'] : explode(',', $array['tags']);
            }
            unset($array['tags']);
        }


        $this->db->where('id', $id);
        if ($this->db->update('cat_media_folder', $array)) {
            // $this->user_log($this->OP_TYPE_USER, 'update_media_folder['.$id.'] data['.json_encode($array).']');

            //update tags info
            if ($tags) {
                $sql = "select id from cat_media where folder_id = '$id'";
                $query = $this->db->query($sql);
                if ($query->num_rows() > 0) {
                    $mediaary = $query->result_array();
                    foreach ($mediaary as $mid) {
                        //先清空此media相关的tag
                        $this->db->query("delete from cat_tag_media where media_id=" . $mid['id']);

                        foreach ($tags as $tag) {
                            $tmpary = array('tag_id' => $tag, 'media_id' => $mid['id']);
                            $this->db->insert('cat_tag_media', $tmpary);
                        }
                    }
                }

                $this->sync_tags($id, $tags, 'App\Folder');
            } else {
                $this->detach_tags($id, 'App\Folder');
            }



            //todo: update media's property under this folder
            if (isset($array['date_flag'])) {
                $propery = array();
                $propery['date_flag'] = $array['date_flag'];
                if ($propery['date_flag'] == '1') {
                    $propery['start_date'] = $array['start_date'];
                    $propery['end_date'] = $array['end_date'];
                }


                $this->db->where('folder_id', $id);
                $this->db->where('media_type', "2");


                $this->db->update('cat_media', $propery);


                if (isset($array['play_time'])) {
                    $propery['play_time'] = $array['play_time'];
                }

                $this->db->where('folder_id', $id);
                $this->db->where('media_type', "1");

                $this->db->update('cat_media', $propery);
            }
            return $id;
        } else {
            return false;
        }
    }
    /**
     * 删除目录
     *
     * @param object $cid
     * @param object $id
     * @return
     */
    public function delete_folder($cid, $id)
    {
        $this->db->where('id', $id);
        $this->db->where('company_id', $cid);

        if ($this->db->delete('cat_media_folder')) {
            $this->user_log($this->OP_TYPE_USER, 'delete_media_folder[' . $id . ']');
            $this->detach_tags($id, 'App\Folder');

            $media_ids = $this->get_folder_media_id($id);
            if ($media_ids) {
                $this->delete_media($media_ids);
            }
            return true;
        } else {
            $this->user_log($this->OP_TYPE_USER, 'delete_media_folder[' . $id . ']', $this->OP_STATUS_FAIL);
            return false;
        }
    }


    /**
     *
     * @param object $cid
     * @param object $offset
     * @param object $limit
     * @param object $order_item [optional]
     * @param object $order [optional]
     * @return
     */
    public function get_ftp_list($cid, $filter_array = array(), $offset = 0, $limit = -1, $order_item = 'id', $order = 'desc')
    {
        $this->db->select("count(*) as total");
        $this->db->where('company_id', $cid);
        if (!empty($filter_array)) {
            if (isset($filter_array['name']) && $filter_array['name']) {
                $this->db->like('profile', $filter_array['name']);
            }
        }
        $query = $this->db->get('cat_ftp_config');
        $total = $query->row()->total;
        $array = array();
        if ($total > 0) {
            $this->db->select('fc.*, u.name as add_user');
            $this->db->where('fc.company_id', $cid);
            if (!empty($filter_array)) {
                if (isset($filter_array['name']) && $filter_array['name']) {
                    $this->db->like('fc.profile', $filter_array['name']);
                }
            }
            if ($limit > 0) {
                $this->db->limit($limit, $offset);
            }
            $this->db->from('cat_ftp_config fc');
            $this->db->join('cat_user u', 'u.id = fc.add_user_id', 'left');
            $this->db->order_by('fc.' . $order_item, $order);
            $query = $this->db->get();
            if ($query->num_rows() > 0) {
                $array = $query->result();
                $query->free_result();
            }
        }

        return array('total' => $total, 'data' => $array);
    }

    /**
     * 获取单个FTP
     *
     * @param object $id
     * @return
     */
    public function get_ftp($id)
    {
        if ($id) {
            $this->db->where('id', $id);
            $query = $this->db->get('cat_ftp_config');
            if ($query->num_rows() > 0) {
                return $query->row();
            }
        }
        return false;
    }
    /**
     * 通过FTP名称  获取Ftp的信息
     * @param object $id
     * @param object $cid
     * @param object $name
     * @return
     */
    public function get_ftp_by_name($id, $cid, $name)
    {
        if ($id > 0) {
            $sql = "select id from cat_ftp_config where id != $id and company_id = '$cid' and profile = '$name'";
        } else {
            $sql = "select id from cat_ftp_config where company_id = '$cid' and profile = '$name'";
        }
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
    }
    /**
     * 删除FTP
     *
     * @param object $cid
     * @param object $id
     * @return
     */
    public function delete_ftp($cid, $id)
    {
        $this->db->where('id', $id);
        $this->db->where('company_id', $cid);
        if ($this->db->delete('cat_ftp_config')) {
            $this->user_log($this->OP_TYPE_USER, 'delete_ftp_config[' . $id . ']');
            return true;
        } else {
            $this->user_log($this->OP_TYPE_USER, 'delete_ftp_config[' . $id . ']', $this->OP_STATUS_FAIL);
            return false;
        }
    }


    /**
     * 获取指定类型的媒体文件
     *
     * @param object $cid 公司ID
     * @param object $media_type 媒体文件类型
     * @param object $offset 偏移量
     * @param object $limit 长度限制
     * @param object $order_item [optional]
     * @param object $order [optional]
     * @param object $filter_array [optional]
     * @param object $folder_id [optional]
     * @return
     */
    public function get_media_list($cid, $offset, $limit, $order_item = 'id', $order = 'desc', $filter_array = array())
    {
        $pids = array();


        $array = array();

        $this->db->select(
            array(
                "m.*", "u.name as author", "IFNULL(f.name,'Root') as folder_name",
                '(select GROUP_CONCAT(t.name ORDER BY t.name ASC SEPARATOR "/") from cat_tag t, cat_tag_media tm where m.id=tm.media_id and tm.tag_id=t.id GROUP BY m.id) as tag_name'
            )
        );
        $this->db->from('cat_media m');
        $this->db->join('cat_user u', 'u.id = m.add_user_id', 'left');
        $this->db->join('cat_media_folder f', 'f.id = m.folder_id', 'left');
        if ($cid) {
            $this->db->where('m.company_id', $cid);
        }
        $this->db->where('deleted_at is null');


        //filter
        if (!empty($filter_array)) {
            foreach ($filter_array as $key => $value) {
                switch ($key) {
                    case 'name':
                        $this->db->like('m.name', $value);
                        break;
                    case 'add_time':
                        $this->db->where('m.add_time between \'' . $value . ' 00:00:00\' and \'' . $value . ' 23:59:59\'');
                        break;
                    case 'add_user_id':
                    case 'folder_id':
                        if (empty($value)) {
                            break;
                        }
                        if (is_array($value)) {
                            $this->db->where_in($key, $value);
                        } elseif ($value >= 0) {
                            $this->db->where($key, $value);
                        }
                        break;

                    case 'tag_name':

                        $query = $this->db->query("select media_id from cat_tag_media tm,cat_tag t
										where tm.tag_id = t.id
										and  t.name like '%$value%' ");
                        $result = $query->result_array();

                        //FIXME
                        if ($result == false) {
                            return array('total' => 0, 'data' => array());
                        }
                        foreach ($result as $p) {
                            $pids[] = $p['media_id'];
                        }
                        $query->free_result();

                        $this->db->where_in('m.id', $pids);
                        // no break
                    case 'tag_id':
                        //	$this->db->where($key, $value);
                        $this->db->where_in('m.id', $pids);
                        break;
                    case 'media_type':
                        if ($value >= 0) {
                            $this->db->where('media_type', $value);
                        }
                        break;

                    case 'approved':
                        if ($value > 0) {
                            $this->db->where('approved>=', $value);
                        } else {
                            $this->db->where('approved', 0);
                        }
                        break;
                    case 'picture_type':
                        if ($filter_array['media_type'] == 1) {
                            if ($value == 'bg' ||  $value == 'notbmp') {
                                $this->db->where_not_in('ext', 'bmp');
                                $this->db->where_not_in('ext', 'png');
                                $this->db->where_in('source', 0);
                            }

                            if ($value == 'logo') {
                                $this->db->where_in('source', 0);
                            }
                        }
                        break;
                }
            }
        }

        $db = clone ($this->db);
        $total = $this->db->count_all_results();

        if ($total > 0) {
            $this->db = $db;
            if ($order_item == "folder_id") {
                $this->db->order_by('folder_name', $order);
            } elseif ($order_item != "tag_name" && $order_item != "folder_name") {
                $this->db->order_by('m.' . $order_item, $order);
            } else {
                $this->db->order_by($order_item, $order);
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


    /**
     * 获取当前媒体文件详细信息
     * @param object $id
     * @return 成功返回媒体文件详细，否则返回FALSE
     */
    public function get_media($id)
    {
        $this->db->select('m.*, u.name as author');
        $this->db->from('cat_media m');
        $this->db->join('cat_user u', 'u.id = m.add_user_id', 'left');
        $this->db->where('m.id', $id);

        $query = $this->db->get(); //_where('cat_media', array('id'=>$id));
        if ($query->num_rows() > 0) {
            $result = $query->row();
            if ($result->folder_id > 0) {
                $result->folder = $this->get_folder($result->folder_id);
            }

            return $result;
        } else {
            return false;
        }
    }

    /**
     * 该查询只用在媒体文件删除的时候
     * 根据用户选择的获取当前媒体文件,获取文件的预览图，预览视频
     * @param object $id
     * @return 成功返回媒体文件详细，否则返回FALSE
     */
    public function get_media_del($id)
    {
        $this->db->select('id, full_path, media_type, company_id, tiny_url, main_url, source, signature');
        $this->db->from('cat_media');
        if (is_array($id)) {
            $this->db->where_in('id', $id);
        } else {
            $this->db->where('id', $id);
        }
        $result = array();
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $result = $query->result();
            return $result;
        } else {
            return false;
        }
    }


    /**
     * 获取区域的媒体
     *
     * @param object $area_media_id
     * @return
     */
    public function get_area_media($area_media_id)
    {
        $this->db->select('m.*, pam.publish_url');
        $this->db->from('cat_media m');
        $this->db->join('cat_playlist_area_media pam', 'pam.media_id = m.id', 'left');
        $this->db->where('pam.id', $area_media_id);

        $query = $this->db->get(); //_where('cat_media', array('id'=>$id));
        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
    }

    /**
     * http类型的文件采用 获取区域的媒体 根据url
     *
     * @param object $area_media_url
     * @return
     */
    public function get_area_media_by_url($area_media_url)
    {
        $this->db->select('*');
        $this->db->from('cat_media');
        $this->db->where('full_path', $area_media_url);

        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
    }

    /**
     * 获取当前文件名称
     *
     * @param object $file_name
     * @param object $company_id
     * @return
     */
    public function get_next_media_name($file_name, $company_id)
    {
        $raw = $this->get_rawname($file_name);
        $this->db->select('name');
        $this->db->from('cat_media');
        $this->db->where('company_id', $company_id);
        $this->db->like('name', $raw, 'after');
        $this->db->order_by('id', 'desc');
        $this->db->limit(1);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $db_name = $query->row()->name;
            if ($db_name == $file_name) {
                return $this->rename_file_name($file_name, 1);
            } else {
                if ($this->is_same_file_name($file_name, $db_name)) {
                    return $this->rename_file_name($file_name, $this->get_rawname_index($this->get_rawname($db_name)) + 1);
                }
            }
        }

        return $file_name;
    }

    private function get_rawname($file_name)
    {
        $tmp = explode(".", $file_name);
        if (count($tmp) > 1) {
            return substr($file_name, 0, strlen($file_name) - 1 - strlen($tmp[count($tmp) - 1]));
        } else {
            return $file_name;
        }
    }

    private function get_rawname_index($rawname)
    {
        if (preg_match('/.*\(([0-9]+)\)$/', $rawname, $matches)) {
            return $matches[1];
        } else {
            return false;
        }
    }

    /**
     * 匹配两个文件名是否相同
     *
     * @param object $af aa.jpg
     * @param object $bf aa(1).jpg
     * @return
     */
    private function is_same_file_name($af, $bf)
    {
        if ($af == $bf) {
            return true;
        }
        $at = explode(".", $af);
        $bt = explode(".", $bf);
        if (count($at) == count($bt) && count($at) > 1) {
            $aw = substr($af, 0, strlen($af) - 1 - strlen($at[count($at) - 1]));
            $bw = substr($bf, 0, strlen($bf) - 1 - strlen($bt[count($bt) - 1]));
            return preg_match("/$aw\(([0-9]+)\)$/", $bw);
        }

        return false;
    }

    private function rename_file_name($file_name, $index)
    {
        $tmp = explode(".", $file_name);
        if ($tmp) {
            return substr($file_name, 0, strlen($file_name) - 1 - strlen($tmp[count($tmp) - 1])) . "($index)." . $tmp[count($tmp) - 1];
        } else {
            return $file_name;
        }
    }

    /**
     * 获取媒体文件类型
     *
     * @param object $id
     * @return
     */
    public function get_media_type($id)
    {
        $this->db->select('media_type');
        $this->db->from('cat_media');
        $this->db->where('id', $id);

        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->row()->media_type;
        } else {
            return false;
        }
    }

    /**
     * 获取某个该类型的下一个媒体文件
     * @param object $id
     * @param object $media_type
     * @return
     */
    public function get_next_media($id, $media_type)
    {
        $this->db->where('media_type', $media_type);
        $this->db->where('id >', $id);
        $this->db->order_by('id', 'desc');
        $this->db->limit(1);

        $query = $this->db->get('cat_media');
        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
    }

    /**
     * 获取某个该类型的上一个媒体文件
     *
     * @param object $id
     * @param object $media_type
     * @return
     */
    public function get_prev_media($id, $media_type)
    {
        $this->db->where('media_type', $media_type);
        $this->db->where('id <', $id);
        $this->db->order_by('id', 'desc');
        $this->db->limit(1);

        $query = $this->db->get('cat_media');
        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
    }

    /**
     * 上传的文件是否达到上限
     *
     * @param object $cid
     * @return 超过限制返回最大的使用值，都在返回FALSE
     */
    public function is_storage_limited($cid, $file_size)
    {
        $sql = "select c.total_disk, sum(m.file_size) as media_total_size from cat_company c left join cat_media m on c.id = m.company_id where c.id = $cid and m.source = 0";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $row = $query->row();
            if (empty($row->media_total_size)) {
                return false;
            } elseif (($row->media_total_size + $file_size) > $row->total_disk) {
                return $row->total_disk;
            }
        }

        return false;
    }

    /**
     * 获取当前公司的磁盘使用情况
     *
     * @param object $cid
     * @return
     */
    public function get_used_storage($cid)
    {
        $sql = "select sum(m.file_size) as media_total_size from cat_media m where m.company_id = $cid AND source = 0";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $row = $query->row();
            return $row->media_total_size;
        }

        return false;
    }

    /**
     * 保存媒体文件记录
     *
     * @param object $array
     * @param object $cid
     * @param object $uid
     * @return
     */
    public function add_media($array, $cid, $uid, $tags = '')
    {
        if (empty($array)) {
            return 0;
        }

        $array['company_id'] = $cid;
        $array['add_user_id'] = $uid;
        if ($this->db->insert('cat_media', $array)) {
            $id = $this->db->insert_id();
            $this->user_log($this->OP_TYPE_USER, 'add_media[' . $id . '] data[' . json_encode($array) . ']');

            if ($tags && $tags !== 'null' && !empty($tags)) {
                if (!is_array($tags)) {
                    $tags = explode(",", $tags);
                }
                foreach ($tags as $tag) {
                    if (!empty($tag)) {
                        $data1 = array('tag_id' => $tag, 'media_id' => $id);
                        $this->db->insert('cat_tag_media', $data1);
                    }
                }
            }

            return $id;
        } else {
            return false;
        }
    }

    public function add_folder($array, $cid, $uid)
    {
        if (empty($array)) {
            return false;
        }

        $array['company_id'] = $cid;
        $array['add_user_id'] = $uid;
        $tags = false;
        if (isset($array['tags'])) {
            if ($array['tags'] !== 'null') {
                $tags = $array['tags'];
            }
            unset($array['tags']);
        }

        if ($this->db->insert('cat_media_folder', $array)) {
            $id = $this->db->insert_id();
            if ($tags) {
                $this->attach_tags($id, $tags, 'App\Folder');
            }
            //$this->user_log($this->OP_TYPE_USER, 'add_media_folder['.$id.'] data['.json_encode($array).']');
            return $id;
        } else {
            return false;
        }
    }

    /**
     * 获取cid下的满足条件的folder
     *
     * @param object $cid 公司ID
     * @param object $name [optional]
     * @return 不存在返回FALSE
     */
    public function get_company_folder($cid, $name = false, $cur_folder_id = false)
    {
        $this->db->where('company_id', $cid);

        if ($name != false && strlen($name) > 0) {
            $this->db->where('name', $name);
        }

        if ($cur_folder_id != false && $cur_folder_id > 0) {
            $this->db->where('id !=', $cur_folder_id);
        }

        $query = $this->db->get('cat_media_folder');
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }

    /**
     * 移动媒体文件的目录
     *
     * @param object $ids
     * @param object $folder_id
     * @return
     */
    public function move_to_folder($ids, $folder_id)
    {
        if (empty($ids)) {
            return false;
        }


        if ($folder_id == 0) {
            $this->db->where_in('id', $ids);

            $data = array('folder_id' => 0);
            //$data['date_flag'] = 0;

            return $this->db->update('cat_media', $data);
        }


        $folder = $this->get_folder($folder_id);

        //FIXME

        $this->db->where_in("media_id", $ids);
        $this->db->delete("cat_tag_media");

        $data = array('folder_id' => $folder_id);

        if ($folder->tags) {
            $tids = explode(',', $folder->tags);

            if (!empty($tids)) {
                foreach ($ids as $mid) {
                    foreach ($tids as $tag) {
                        $tmpary = array('tag_id' => $tag, 'media_id' => $mid);
                        $this->db->insert('cat_tag_media', $tmpary);
                    }
                }
            }
        }
        $data['date_flag'] = $folder->date_flag;
        if ($folder->date_flag) {
            $data['start_date'] = $folder->start_date;
            $data['end_date'] = $folder->end_date;
        }

        /*
        if ($media_type == 1 && isset($folder->play_time)) {
            $data['play_time'] = $folder->play_time;
        }
        */

        $this->db->where_in('id', $ids);
        return $this->db->update('cat_media', $data);
    }

    public function update_media($array, $id, $tags = '')
    {
        if (!$id || empty($array)) {
            return 0;
        }


        if (is_array($id)) {
            $this->db->where_in('id', $id);
        } else {
            $this->db->where('id', $id);
        }

        if ($this->db->update('cat_media', $array)) {
            if (is_array($id)) {
                $this->db->where_in('media_id', $id);
            } else {
                $this->db->where('media_id', $id);
            }
            $this->db->delete('cat_tag_media');
            //  $this->db->query("delete from cat_tag_media where media_id=" . $id);
            if (!empty($tags)) {
                if (!is_array($tags)) {
                    $tags = explode(",", $tags);
                }
                foreach ($tags as $tag) {
                    $data1 = array('tag_id' => $tag, 'media_id' => $id);
                    $this->db->insert('cat_tag_media', $data1);
                }
            }

            return $id;
        } else {
            return false;
        }
    }
    public function update_medium($array, $id)
    {
        if (empty($array)) {
            return 0;
        }
        if (is_array($id)) {
            $this->db->where_in('id', $id);
        } else {
            $this->db->where('id', $id);
        }
        if ($this->db->update('cat_media', $array)) {
            return $id;
        } else {
            return false;
        }
    }
    /**
     * 添加FTP文件
     *
     * @param Array $media [media[i][file], media[i][size]]
     * @param object $media_type
     * @param object $ftp_config_id
     * @param object $cid
     * @param object $uid
     * @return
     */
    public function add_ftp_media($media, $ftp_config_id, $cid, $uid, $folder_id = 0)
    {
        if (empty($media)) {
            return false;
        }
        $ftp = $this->get_ftp_config($ftp_config_id);
        $ftp_partten = "ftp://%s:%s@%s:%d%s";

        if ($ftp) {
            $allowed_pics = $this->config->item('allowed_image_types');
            $allowed_movs = $this->config->item('allowed_video_types'); //["avi", "mp4", "divx", 'mpeg', 'mpg'];
            $epassword = $ftp->password;
            foreach ($media as $m) {

                $fileInfo = pathinfo($m['file']);
                $fileExt = strtolower($fileInfo['extension']);


                if (in_array($fileExt, $allowed_pics)) {
                    $media_type = 1;
                } else if (in_array($fileExt, $allowed_movs)) {
                    $media_type = 2;
                } else {
                    return false;
                }

                $data = array('media_type' => $media_type, 'source' => $this->config->item('media_source_ftp'), 'company_id' => $cid, 'add_user_id' => $uid);
                $data['file_size'] = $m['size'];
                $name = $this->get_file_name($m['file']);
                $data['ext']  = $this->get_file_ext($name);
                $data['name'] = $name;
                $data['orig_name'] = $name;
                $data['play_time'] = 10.00;
                $data['folder_id'] = $folder_id;
                if (strstr($ftp->server, 'ftp://')) {
                    $ftp->server = substr($ftp->server, 6);
                }

                $data['full_path'] = sprintf($ftp_partten, $ftp->account, $epassword, $ftp->server, $ftp->port, $m['file']);

                if ($this->db->insert('cat_media', $data)) {
                    $id = $this->db->insert_id();
                    $this->user_log($this->OP_TYPE_USER, 'add_media[' . $id . '] data[' . json_encode($data) . ']');
                    $m['id'] = $id;
                }
            }

            return $media;
        } else {
            return false;
        }
    }
    /**
     * 获取媒体文件名称
     *
     * @param object $path
     * @return
     */
    public function get_file_name($path)
    {
        $array = preg_split("/[\\/]/", $path);
        //$array = split("[\\/]", $path);
        return $array[count($array) - 1];
    }

    /**
     * 获取文件扩展名
     *
     * @param object $file_name
     * @return
     */
    public function get_file_ext($file_name)
    {
        $array = preg_split("/[.]/", $file_name);
        //$array = split("[.]", $file_name);
        return $array[count($array) - 1];
    }

    /**
     * 获取远程文件大小
     * 暂时支持http方式
     *
     * @param object $url
     * @return
     */
    public function get_remote_file_size($url)
    {
        /*
         * scheme - e.g. http
            host
            port
            user
            pass
            path
            query - after the question mark ?
            fragment - after the hashmark #

         */
        $array = @parse_url($url);
        if ($array) {
            $host = $array['host'];
            $port = 80;
            if (isset($array['port'])) {
                $port = $array['port'];
            }
            $path = $array['path'];

            if (false) {
                $ip = $host;
                if (!preg_match("/[0-9]*\.[0-9]*\.[0-9]*\.[0-9]*/", $host)) {
                    // a domain name was given, not an IP
                    $ip = gethostbyname($host);
                    if (!preg_match("/[0-9]*\.[0-9]*\.[0-9]*\.[0-9]*/", $ip)) {
                        //domain could not be resolved
                        return false;
                    }
                }
            }

            $fp = @fsockopen($host, $port, $errno, $errstr, 20);
            if ($fp) {
                $out = "HEAD " . $path . " HTTP/1.1\r\n";
                $out .= "Host: " . $host . "\r\n";
                $out .= "Connection: Close\r\n\r\n";
                fwrite($fp, $out);
                $content = "";
                while (!feof($fp)) {
                    $content .= fgets($fp, 2048);
                }
                fclose($fp);
                if (preg_match("/^HTTP\/\d.\d 200 OK/is", $content)) {
                    preg_match("/Content-Length:(.*?)\r\n/is", $content, $length);
                    return trim($length[1]);
                }
            }
        }
        return false;
    }

    /**
     * 执行删除某个媒体文件
     *
     * @param object $id
     * @return
     */
    public function delete_media($id)
    {
        $delete_at = array('deleted_at' => date("Y-m-d H:i:s"));

        /*
        if (is_array($id)) {
            if (count($id) == 0) {
                return false;
            }
            $in_id = implode(',', $id);
            $this->db->query('delete from cat_playlist_area_media where media_id in (' . $in_id . ')');
        } else {
            $this->db->query('delete from cat_playlist_area_media where media_id = ' . $id);
        }
        */
        if ($this->update_media($delete_at, $id)) {
            return true;
        }
        return false;
        /*
        if (is_array($id)) {
            if (count($id) == 0) {
                return false;
            }

            $in_id = implode(',', $id);
            $this->db->query('delete from cat_media where id in (' . $in_id . ')');
            $this->db->query('delete from cat_playback where media_id in (' . $in_id . ')');
            $this->db->query('delete from cat_playlist_area_media where media_id in (' . $in_id . ')');
            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                $this->user_log($this->OP_TYPE_USER, 'delete_media[' . $in_id . ']', $this->OP_STATUS_FAIL);
                return false;
            } else {
                $this->db->trans_commit();
                $this->user_log($this->OP_TYPE_USER, 'delete_media[' . $in_id . ']');
                return true;
            }
        } else {
            $this->db->trans_begin();
            $this->db->query('delete from cat_media where id = ' . $id);
            $this->db->query('delete from cat_playback where media_id = ' . $id);
            $this->db->query('delete from cat_playlist_area_media where media_id = ' . $id);
            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                $this->user_log($this->OP_TYPE_USER, 'delete_media[' . $id . ']', $this->OP_STATUS_FAIL);
                return false;
            } else {
                $this->db->trans_commit();
                $this->user_log($this->OP_TYPE_USER, 'delete_media[' . $id . ']');
                return true;
            }
        }
        */
    }

    /**
     * 保存FTP配置信息
     * @param object $array
     * @param object $cid
     * @param object $uid
     * @return
     */
    public function add_ftp_config($array, $cid, $uid)
    {
        if (empty($array)) {
            return 0;
        }

        $array['company_id'] = $cid;
        $array['add_user_id'] = $uid;

        if ($this->db->insert('cat_ftp_config', $array)) {
            $id = $this->db->insert_id();
            $this->user_log($this->OP_TYPE_USER, 'add_ftp_config[' . $id . '] data[' . json_encode($array) . ']');
            return $id;
        } else {
            return false;
        }
    }

    /**
     * 更新FTP配置信息
     *
     * @param object $array
     * @param object $id
     * @return
     */
    public function update_ftp_config($array, $id)
    {
        if (empty($array)) {
            return 0;
        }

        $this->db->where('id', $id);

        if ($this->db->update('cat_ftp_config', $array)) {
            $this->user_log($this->OP_TYPE_USER, 'update_ftp_config[' . $id . '] data[' . json_encode($array) . ']');
            return $id;
        } else {
            return false;
        }
    }

    /**
     * 获取FTP配置列表
     *
     * @param object $cid
     * @return
     */
    public function get_ftp_config_list($cid)
    {
        $this->db->where('company_id', $cid);
        $query = $this->db->get('cat_ftp_config');

        if ($query->num_rows()) {
            return  $query->result();
        }
        return false;
    }

    /**
     * 获取某个FTP配置
     * @param object $id
     * @return
     */
    public function get_ftp_config($id)
    {
        $this->db->where('id', $id);
        $query = $this->db->get('cat_ftp_config');
        if ($query->num_rows()) {
            return $query->row();
        } else {
            return false;
        }
    }

    /**
     * 删除某个FTP配置
     *
     * @param object $id
     * @return
     */
    public function delete_ftp_config($id)
    {
        $this->db->where('id', $id);
        $this->user_log($this->OP_TYPE_USER, 'delete_ftp_config[' . $id . ']');
        return $this->db->delete('cat_ftp_config');
    }

    public function get_company_rss_count($cid)
    {
        $sql = 'select count(*) as total from cat_rss where company_id=' . $cid;
        $query = $this->db->query($sql);
        return $query->row()->total;
    }

    /**
     * 获取公司下的所有RSS列表信息
     *
     * @param object $cid
     * @return
     */
    public function get_all_rss_list($cid)
    {
        $array = array();
        $this->db->where('company_id', $cid);
        $this->db->where('type !=', 2);
        $this->db->order_by('id', 'desc');

        $query = $this->db->get('cat_rss');
        if ($query->num_rows() > 0) {
            $array = $query->result();

            $query->free_result();
        }

        return $array;
    }

    /**
     * 获取公司下的所有webpage列表信息
     *
     * @param object $cid
     * @return
     */
    public function get_all_webpage_list($cid)
    {
        $array = array();
        $this->db->where('company_id', $cid);
        $this->db->where('type', 2);
        $this->db->order_by('id', 'desc');

        $query = $this->db->get('cat_rss');
        if ($query->num_rows() > 0) {
            $array = $query->result();

            $query->free_result();
        }

        return $array;
    }


    /**
     * 获取当前需要更新的RSS
     * @return
     */
    public function get_all_update_rss_list()
    {
        $array = array();

        $sql = 'select id, url from cat_rss where UNIX_TIMESTAMP(now()) - UNIX_TIMESTAMP(last_update) >= `interval` * 60 or  isnull(last_update) order by id desc';

        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $array = $query->result();

            $query->free_result();
        }

        return $array;
    }

    /**
     * 更新RSS最后的时间
     * @param object $id
     * @return
     */
    public function update_rss_last($id)
    {
        $sql = 'update cat_rss set last_update = now() where id = ' . $id;
        $this->db->query($sql);
    }

    public function get_rss($rss_ids)
    {
        if (is_array($rss_ids)) {
            $this->db->where_in('id', $rss_ids);
        } else {
            $this->db->where('id', $rss_ids);
        }
        $query = $this->db->get('cat_rss');
        if ($query->num_rows() > 0) {
            if (is_array($rss_ids)) {
                $array = $query->result();
                $query->free_result();

                return $array;
            } else {
                return $query->row();
            }
        }

        return false;
    }

    public function get_rss_type($rss_ids)
    {
        if (is_array($rss_ids)) {
            $this->db->where_in('id', $rss_ids);
            $this->db->where_in('type', 0);
        } else {
            $this->db->where('id', $rss_ids);
            $this->db->where('type', 0);
        }
        $query = $this->db->get('cat_rss');
        if ($query->num_rows() > 0) {
            if (is_array($rss_ids)) {
                $array = $query->result();
                $query->free_result();

                return $array;
            } else {
                return $query->row();
            }
        }

        return false;
    }

    /**
     * 通过RSS名称 判断信息是否存在
     * @param object $id
     * @param object $cid
     * @param object $name
     * @return
     */
    public function get_rss_by_name($id, $cid, $name)
    {
        if ($id > 0) {
            $sql = "select id from cat_rss where id != $id and company_id = '$cid' and name = '$name'";
        } else {
            $sql = "select id from cat_rss where company_id = '$cid' and name = '$name'";
        }

        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
    }

    /**
     * 通过RSS编号 获取RSS的内容
     * @param object $id
     * @return
     */
    public function get_rss_content($id)
    {
        $rss = $this->material->get_rss($id);
        $rss_content = '';
        if ($rss) {
            $this->load->library('rssparser');
            $rssObj = $this->rssparser->Get($rss->url, true, true);
            if ($rssObj) {
                $items = $rssObj['items'];
                for ($i = 0; $i < count($items); $i++) {
                    $rss_content .= $items[$i]['title'];
                    $rss_content .= '<==>';
                    if (isset($items[$i]['description'])) {
                        $rss_content .= $items[$i]['description'];
                    }
                    if ($i < count($items) - 1) {
                        $rss_content .= '<=!=>';
                    }
                }
            }
        }
        $data['id'] = $rss->id;
        $data['name']  = $rss->name;
        $data['descr'] = $rss->descr;
        $data['url'] = $rss->url;
        $data['content'] = $rss_content;

        return $data;
    }


    /**
     * 获取某个公司下的RSS列表
     *
     * @param object $cid
     * @param object $offset [optional]
     * @param object $limit [optional]
     * @return
     */
    public function get_rss_list($cid, $offset = 0, $limit = 10, $add_user_id = false)
    {
        $sql = "select count(*) as total from cat_rss where company_id=" . $cid . " and type !=2";
        if ($add_user_id) {
            $sql .= " and add_user_id=.$add_user_id";
        }
        $query = $this->db->query($sql);
        $total = $query->row()->total;
        $array = array();
        if ($total > 0) {
            $this->db->where('company_id', $cid);
            $this->db->where('type !=', 2);
            if ($add_user_id) {
                $this->db->where('add_user_id', $add_user_id);
            }
            $this->db->order_by('id', 'desc');
            $this->db->limit($limit, $offset);

            $query = $this->db->get('cat_rss');
            if ($query->num_rows() > 0) {
                $array = $query->result();

                $query->free_result();
            }
        }

        return array('total' => $total, 'data' => $array);
    }

    /**
     * 获取某个公司下的RSS列表
     *
     * @param object $cid
     * @param object $offset [optional]
     * @param object $limit [optional]
     * @return
     */
    public function get_webpage_list($cid, $offset = 0, $limit = 10, $add_user_id = false)
    {
        $sql = "select count(*) as total from cat_rss where company_id=" . $cid . " and type =2";
        if ($add_user_id) {
            $sql .= " and add_user_id=.$add_user_id";
        }
        $query = $this->db->query($sql);
        $total = $query->row()->total;
        $array = array();
        if ($total > 0) {
            $this->db->where('company_id', $cid);
            $this->db->where('type', 2);
            if ($add_user_id) {
                $this->db->where('add_user_id', $add_user_id);
            }
            $this->db->order_by('id', 'desc');
            $this->db->limit($limit, $offset);

            $query = $this->db->get('cat_rss');
            if ($query->num_rows() > 0) {
                $array = $query->result();

                $query->free_result();
            }
        }

        return array('total' => $total, 'data' => $array);
    }

    /* 添加webpage记录
    *
    * @param object $array
    * @param object $cid
    * @param object $uid
    * @return
    */
    public function add_webpage($array, $cid, $uid)
    {
        if (empty($array)) {
            return 0;
        }

        $array['company_id'] = $cid;
        $array['add_user_id'] = $uid;

        if ($this->db->insert('cat_rss', $array)) {
            $id = $this->db->insert_id();
            $this->user_log($this->OP_TYPE_USER, 'add_rss[' . $id . '] data[' . json_encode($array) . ']');
            return $id;
        } else {
            return false;
        }
    }

    /**
     * 更新webpage记录
     *
     * @param object $array
     * @param object $id
     * @return
     */
    public function update_webpage($array, $id)
    {
        if (empty($array)) {
            return 0;
        }

        $this->db->where('id', $id);
        if ($this->db->update('cat_rss', $array)) {
            $this->user_log($this->OP_TYPE_USER, 'update_rss[' . $id . '] data[' . json_encode($array) . ']');
            return $id;
        } else {
            return false;
        }
    }

    /**
     * 获取RSS对象
     *
     * @return
     */
    /*public function get_rss($id){
        $this->db->where('id',$id);
        $query = $this->db->get('cat_rss');
        if($query->num_rows()){
            return $query->row();
        }else{
            return FALSE;
        }
    }*/

    /**
     * 添加RSS记录
     *
     * @param object $array
     * @param object $cid
     * @param object $uid
     * @return
     */
    public function add_rss($array, $cid, $uid)
    {
        if (empty($array)) {
            return 0;
        }

        $array['company_id'] = $cid;
        $array['add_user_id'] = $uid;

        if ($this->db->insert('cat_rss', $array)) {
            $id = $this->db->insert_id();
            $this->user_log($this->OP_TYPE_USER, 'add_rss[' . $id . '] data[' . json_encode($array) . ']');
            return $id;
        } else {
            return false;
        }
    }

    /**
     * 更新RSS记录
     *
     * @param object $array
     * @param object $id
     * @return
     */
    public function update_rss($array, $id)
    {
        if (empty($array)) {
            return 0;
        }

        $this->db->where('id', $id);
        if ($this->db->update('cat_rss', $array)) {
            $this->user_log($this->OP_TYPE_USER, 'update_rss[' . $id . '] data[' . json_encode($array) . ']');
            return $id;
        } else {
            return false;
        }
    }


    /**
     * 删除RSS数据
     *
     * @param object $id
     * @return
     */
    public function delete_rss($id)
    {
        if ($id > 0) {
            $this->db->where('id', $id);
            if ($this->db->delete('cat_rss')) {
                $this->user_log($this->OP_TYPE_USER, 'delete_rss[' . $id . ']');
                return true;
            } else {
                $this->user_log($this->OP_TYPE_USER, 'delete_rss[' . $id . ']', $this->OP_STATUS_FAIL);
                return false;
            }
        } else {
            return false;
        }
    }

    public function add_software($array, $cid, $uid)
    {
        if (empty($array)) {
            return 0;
        }

        $array['company_id'] = $cid;
        $array['add_user_id'] = $uid;

        if ($this->db->insert('cat_software', $array)) {
            $id = $this->db->insert_id();
            $this->user_log($this->OP_TYPE_USER, 'cat_software[' . $id . '] data[' . json_encode($array) . ']');
            return $id;
        } else {
            return false;
        }
    }


    /**
     * 获取比当前更高的版本
     *
     * @param object $ver 当前版本信息
     * @return
     */
    public function get_lastest_software($cid, $ver)
    {
        //$this->db->where('company_id', $cid);
        $this->db->where('version > ', $ver);
        $this->db->order_by('version', 'desc');
        $this->db->limit(1);
        $query = $this->db->get('cat_software');
        if ($query->num_rows()) {
            return $query->row();
        } else {
            return false;
        }
    }

    /**
     * 获取指定版本号
     *
     * @param object $version
     * @return
     */
    public function get_version_software($version)
    {
        $this->db->from('cat_software');
        $this->db->where('version', $version);
        $this->db->limit(1);
        $query = $this->db->get();
        if ($query->num_rows()) {
            return $query->row();
        } else {
            return false;
        }
    }

    /**
     * 获取软件列表信息
     *
     * @param object $cid
     * @param object $offset [optional]
     * @param object $limit [optional]
     * @return
     */
    public function get_software_list($cid, $offset = 0, $limit = 10, $order_item = 'id', $order = 'desc')
    {
        $this->db->select('count(id) as total');
        $this->db->from("cat_software");
        //$this->db->or_where('company_id', 0);
        //$this->db->or_where('company_id', $cid);

        $query = $this->db->get();
        $total = $query->row()->total;
        $array = array();
        if ($total > 0) {
            //$this->db->where('company_id', $cid);
            //$this->db->order_by('id', 'desc');
            $this->db->order_by($order_item, $order);
            $this->db->limit($limit, $offset);
            $query = $this->db->get('cat_software');

            if ($query->num_rows()) {
                $array = $query->result();
                $query->free_result();
            }
        }

        return array('total' => $total, 'data' => $array);
    }

    public function get_software($id)
    {
        $this->db->where('id', $id);
        $query = $this->db->get('cat_software');

        if ($query->num_rows()) {
            return $query->row();
        }

        return false;
    }


    public function delete_software($id)
    {
        if (is_array($id)) {
            $this->db->where_in('id', $id);
        } else {
            $this->db->where('id', $id);
        }
        $query = $this->db->get('cat_software');
        if ($query->num_rows()) {
            $items = $query->result();
            foreach ($items as $s) {
                $this->db->where('id', $s->id);
                if ($this->db->delete('cat_software')) {
                    $path = $this->config->item('system_media_path');
                    @unlink($path . $s->location);
                }
            }
            return true;
        }
        return false;
    }

    //根据rss_id和sn获取   播放列表id、rss所在区域id
    public function get_text_setting($id, $sn)
    {
        $sql1 = "select sp.playlist_id from cat_player p, cat_schedule_group sg, cat_schedule_playlist sp where p.sn = $sn and p.group_id = sg.group_id and sg.schedule_id = sp.schedule_id";
        $query = $this->db->query($sql1);
        $result1 = array();
        $result2 = array();

        foreach ($query->result() as $qu) {
            $result1[] = $qu->playlist_id;
        }

        for ($i = 0; $i < count($result1); $i++) {
            $sql2 = "select area_id, playlist_id from cat_playlist_area_media where media_id = $id and playlist_id = $result1[$i]";
            $query2 = $this->db->query($sql2);
            if ($query2->num_rows() > 0) {
                foreach ($query2->result() as $qu) {
                    $result2[] = $qu->area_id;
                    $result2[] = $qu->playlist_id;
                }
            }
        }

        return $result2;
    }

    //查询媒体文件所在的playlist的数量
    public function get_pb_flag($id)
    {
        $sql = "select count(pl.id) as num from cat_playlist pl, cat_playlist_area_media pl_m where pl_m.media_id = $id and pl_m.playlist_id = pl.id and pl.published = 1";
        $query = $this->db->query($sql);
        if ($query->num_rows()) {
            return $query->row();
        }

        return false;
    }
    //查询媒体文件所在的playlist的编号
    public function get_pb_id($id)
    {
        if (is_array($id)) {
            $id = implode(',', $id);
        }
        $sql = "select distinct pl.id from cat_playlist pl, cat_playlist_area_media pl_m where pl_m.media_id in ($id) and pl_m.playlist_id = pl.id and pl.published = 1";
        $query = $this->db->query($sql);
        if ($query->num_rows()) {
            return $query->result();
        }

        return false;
    }
    //修改palylsit为  未发布状态
    public function update_pl($ids)
    {
        $sql = "update cat_playlist set published=0 where id in ($ids)";
        $query = $this->db->query($sql);
        return $query;
    }

    /**
     * 获取相同文件名的信息
     *
     * @param object $file_name
     * @param object $company_id
     * @return
     */
    public function get_same_media_name($file_name, $company_id, $id = -1)
    {

        $this->db->select("id, name, source");
        $this->db->from('cat_media');
        $this->db->where('company_id', $company_id);
        $this->db->where("name", $file_name);

        if ($id != -1) {
            $this->db->where("id!=", $id);
        }


        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result();
        }
        return false;
    }
    //根据rss_id和sn获取   播放列表id
    public function get_playlistId_by_sn($id, $sn)
    {
        $time = date('Y-m-d');
        $sql1 = "select sp.playlist_id from cat_player p, cat_schedule_group sg, cat_schedule_playlist sp, cat_playlist_area_media pam, cat_schedule s where p.sn = $sn and p.group_id = sg.group_id and sg.schedule_id = sp.schedule_id and sp.playlist_id=pam.playlist_id and pam.media_id = $id and s.id = sp.schedule_id and s.end_date >'" . $time . "'";
        $query = $this->db->query($sql1);
        $result1 = array();

        foreach ($query->result() as $qu) {
            $result1[] = $qu->playlist_id;
        }

        return $result1;
    }

    /**
     * interaction获取区域的媒体
     *
     * @param object $area_media_id
     * @return
     */
    public function get_interaction_area_media($area_media_id)
    {
        $this->db->select('m.*, pam.publish_url');
        $this->db->from('cat_media m');
        $this->db->join('cat_interaction_playlist_area_media pam', 'pam.media_id = m.id', 'left');
        $this->db->where('pam.id', $area_media_id);

        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
    }

    public function get_media_by_name($cid, $name)
    {
        $this->db->from('cat_media');
        $this->db->where('name', $name);
        $this->db->where('company_id', $cid);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
    }


    /**
     * 获取某个tag中媒体文件列表信息
     *
     * @param object $tag_id
     * @return
     */
    public function get_tag_byid($tag_id)
    {
        $this->db->where('id', $tag_id);
        $query = $this->db->get('cat_tag');


        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
    }

    public function get_folder_by_name($fname, $cid)
    {
        $this->db->where('company_id', $cid);
        $this->db->where('name', $fname);
        $query = $this->db->get('cat_media_folder');
        if ($query->num_rows() > 0) {
            return $query->row();
        }

        return false;
    }
    public function get_medias_byPid($pid, $flag = false)
    {
        if (!$pid) {
            return false;
        }
        $this->db->select('m.media_id as id, GROUP_CONCAT(tm.tag_id) as tags');
        $this->db->from('cat_playlist_area_media m');
        $this->db->join('cat_tag_media tm', "m.media_id=tm.media_id", "LEFT");
        $this->db->where('m.playlist_id', $pid);
        if ($flag) {
            $this->db->where_in('m.flag', $flag);
        }
        $this->db->group_by("m.media_id");

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->result();
        }
        return false;
    }

    public function get_medias_byId($ids)
    {
        if (!$ids) {
            return false;
        }
        $this->db->select('m.id as id, GROUP_CONCAT(tm.tag_id) as tags');
        $this->db->from('cat_media m');
        $this->db->join('cat_tag_media tm', "m.id=tm.media_id", "LEFT");
        $this->db->where_in('m.id', $ids);

        $this->db->group_by("m.id");

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->result();
        }
        return false;
    }
    public function get_tagname_byids($ids)
    {
        $this->db->where_in('id', $ids);
        $this->db->select('GROUP_CONCAT(name) as tags');
        $query = $this->db->get('cat_tag');


        if ($query->num_rows() > 0) {
            return $query->row()->tags;
        } else {
            return false;
        }
    }

    public function sync_folder_tags()
    {
        $this->db->where('taggable_type', 'App\Folder');
        $this->db->delete('taggables');

        $this->db->select("*");
        $this->db->where('tags is not null');
        $query = $this->db->get('cat_media_folder');
        if ($query->num_rows() > 0) {
            $folders = $query->result();
            foreach ($folders as $folder) {
                if ($folder->tags && strlen($folder->tags) > 0) {
                    $this->sync_tags($folder->id, $folder->tags, 'App\Folder');
                }
            }
        }
    }

    public function get_partner_rootFolder($partner_id)
    {
        $this->db->select("root_folder_id");
        $this->db->from('cat_parter_fields');
        $this->db->where('partner_id', $partner_id);
        $query = $this->db->get();
        $array = array();
        $total = $query->num_rows();
        if ($total > 0) {
            return $query->row()->root_folder_id;
        }
        return false;
    }

    public function get_unapproved_media_cnt($cid)
    {
        $this->db->select('id');
        $this->db->from('cat_media');
        $this->db->where('company_id', $cid);
        $this->db->where('approved', 0);
        $this->db->where('deleted_at is null');
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function get_text_list($cid, $offset = 0, $limit = -1, $order_item = 'name', $order = 'asc', $filter_array = array())
    {
        $array = array();

        $this->db->select('id,name,url,type,interval,last_update');
        $this->db->from('cat_rss');

        if ($cid != 0) {
            $this->db->where('company_id', $cid);
        }

        if (!empty($filter_array)) {
            if (isset($filter_array['name']) && $filter_array['name']) {
                $this->db->like('name', $filter_array['name']);
            }
        }

        $db = clone ($this->db);
        $total = $this->db->count_all_results();

        if ($total > 0) {
            $this->db = $db;
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
        return array('total' => $total, 'data' => $array);
    }

    public function get_affected_published_campaigns_id($medium_id)
    {
        $this->db->select('cp.id');
        $this->db->from('cat_playlist cp');
        $this->db->join('cat_playlist_area_media cm', 'cp.id = cm.playlist_id');
        $this->db->where('cp.published', 1);
        $this->db->where("cp.end_date>=DATE_FORMAT(NOW(), '%Y-%m-%d')");
        $this->db->where('cm.media_id', $medium_id);

        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return array_column($query->result_array(), "id");
        }
        return false;
    }
}
