<?php 
/**
 * 节目相关DB业务
 */
class Weathers extends MY_Model {
	   //*******************************************
    //Template start
    //*******************************************
    
    public function get_company_weather_template_count($cid) {
        $this->db->select("count(*) as total");
        $this->db->where('company_id', $cid);
        $query = $this->db->get('cat_weather_template');
        
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
    public function get_staff_template_list($cid, $offset, $limit) {
        $this->db->select("count(*) as total");

        $this->db->where('company_id', $cid);
        
        $query = $this->db->get('cat_weather_template');
        $total = $query->row()->total;
        $data = array();
        if ($total > 0) {
            $this->db->where('company_id', $cid);       
            $this->db->order_by('id', 'desc');
            $this->db->limit($limit, $offset);
            
            $query = $this->db->get('cat_weather_template');
            
            if ($query->num_rows() > 0) {
                $data = $query->result();
                $query->free_result();
            }
        }
        
        return array('total'=>$total, 'data'=>$data);
    }


        /**
     * 获取某个模板下的区域信息
     *
     * @param object $template_id
     * @return
     */
    public function get_staff_area_list($template_id) {
        $this->db->where('template_id', $template_id);
        $this->db->from('cat_weather_template_area');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $result = $query->result();
            $query->free_result();
            return $result;
        } else {
            return FALSE;
        }
    }


    
 
    
    /**
     * 获取某个模板详情
     *
     * @param object $id
     * @return
     */
    public function get_template($id) {
        $this->db->where('id', $id);
        $query = $this->db->get('cat_weather_template');
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
    public function get_template_by_name($id, $cid, $name) {
        if($id > 0) {
        	$sql = "select id from cat_weather_template where id != $id and company_id = '$cid' and name = '$name'";
        }else {
        	$sql = "select id from cat_weather_template where company_id = '$cid' and name = '$name'";
        }
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return FALSE;
        }
    }
    
    /**
     * 当前模板是否正在被播放列表使用
     *
     * @param object $id
     * @return 正在被用返回true
     */
    public function is_template_using($id) {
            return FALSE;
    }
    

	
	/**
	 * 播放列表的模板是否为竖屏
	 * 
	 * @param object $playlist_id
	 * @return 
	 */
	public function is_portrait_template_playlist($playlist_id){
		$this->db->select('t.width, t.height');
		$this->db->from('cat_weather_template t');
        $this->db->join('cat_playlist p', 't.id=p.template_id', 'left');
        $this->db->where('p.id', $playlist_id);
		$query = $this->db->get();
		
		if ($query->num_rows() > 0) {
            $row = $query->row();
			return $row->width < $row->height;
        } else {
            return FALSE;
        }
	}
    
    /**
     * 创建一个模板
     *
     * @param object $array
     * @param object $cid 用户所在的公司
     * @param object $uid 创建者
     * @param object $init_video_area 是否初始化视频区域，默认不初始化
     * @return
     */
    public function add_template($array, $cid, $uid, $init_video_area=FALSE) {
        if ( empty($array)) {
            return 0;
        }
        
        $array['company_id'] = $cid;
        $array['add_user_id'] = $uid;
        
        if ($this->db->insert('cat_weather_template', $array)) {
            $id = $this->db->insert_id();
			$h=($array['w']>$array['h']);

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
    public function update_template($array, $id) {
        if ( empty($array)) {
            return 0;
        }
        
        $this->db->where('id', $id);
        if ($this->db->update('cat_weather_template', $array)) {
         
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
    public function delete_template($id) {
  
            //删除当前模板
        return $this->db->delete('cat_weather_template', array('id'=>$id));
    }

    
    /**
     * 添加区域信息
     *
     * @param object $array
     * @param object $template_id
     * @return
     */
    public function add_template_area($array, $template_id) {
        if ( empty($array)) {
            return 0;
        }
        
        $array['template_id'] = $template_id;
        
        if ($this->db->insert('cat_weather_template_area', $array)) {
            $id = $this->db->insert_id();
            //$this->user_log($this->OP_TYPE_USER, 'add_template_area['.$id.'] '.json_encode($array));
            return $id;
        } else {
            return FALSE;
        }
    }
  
    
    /**
     * 更新某个区域设置
     *
     * @param object $array
     * @param object $area_id
     * @return
     */
    public function update_template_area($array, $area_id) {
        if ( empty($array) || $area_id <= 0) {
            return 0;
        }
        
        $this->db->where('id', $area_id);
        if ($this->db->update('cat_weather_template_area', $array)) {
            $this->user_log($this->OP_TYPE_USER, 'update_template_area['.$area_id.'] '.json_encode($array));
            return TRUE;
        } else {
            $this->user_log($this->OP_TYPE_USER, 'update_template_area['.$area_id.'] '.json_encode($array), $this->OP_STATUS_FAIL);
            return FALSE;
        }
    }

    /**
     * 删除某个区域中的字幕设置
     *
     * @param object $area_id
     * @return
     */
     public function delete_area_text($area_id) {
        $this->db->delete('cat_area_text_setting', array('area_id'=>$area_id));
     }

        /**
     * 删除模板中的某个区域
     * @param object $id
     * @param object $area_type [optional]
     * @return
     */
    public function delete_template_area($id, $area_type = 0, $media_id = 0) {
        $this->db->delete('cat_weather_template_area', array('id'=>$id));
    }

    public function delete_all_staff_area_byTid($tid) {
        $this->db->delete('cat_weather_template_area', array('template_id'=>$tid));
    }

    public function get_template_bg_area($template_id){
		$area_type = $this->config->item('area_type_bg');
        $this->db->where('area_type',$area_type);
        $this->db->where('template_id', $template_id);
        $this->db->from('cat_weather_template_area');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->row();
        }
        return FALSE;

     //   return $this->get_template_area($template_id, $area_type);
	}

    public function get_template_area($template_id, $area_type = FALSE){

		$this->db->where('template_id', $template_id);
        $this->db->from('cat_weather_template_area');
		$query = $this->db->get();
        if ($query->num_rows() > 0) {
        	if($query->num_rows() == 1){
        		return $query->row();
			}else{
				return $query->result();
			}
        }
		
		return FALSE;
	}

	public function set_active($id,$cid,$active=1){


		if($active){
            $this->db->where('company_id',$cid);
		 	$this->db->update('cat_weather_template', array('flag'=>0));
		}
		$array = array('flag' => $active);
		$this->db->where('id', $id);
     	$this->db->update('cat_weather_template', $array);

	}


	    /**
     * 获取某个模板详情
     *
     * @param object $id
     * @return
     */
    public function get_active_template($cid) {
        $this->db->where('company_id', $cid);
        $this->db->where('flag',1);
        $query = $this->db->get('cat_weather_template');
        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return FALSE;
        }
    }



}