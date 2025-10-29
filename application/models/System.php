<?php 
class System extends MY_Model {

    /**
     * 获取屏幕类型列表
     * @return
     */
    public function get_screen_info_list() {
        $query = $this->db->get('cat_screen_info');
        if ($query->num_rows() > 0) {
            $result = $query->result();
            $query->free_result();
            return $result;
        } else {
            return FALSE;
        }
    }
    /**
     * 通过宽度和高度查询当前屏幕类型
     *
     * @param object $width
     * @param object $height
     * @return
     */
    public function get_screen_type($width, $height) {
        $this->db->where('width', $width);
        $this->db->where('height', $height);
        $this->db->limit(1);
        $query = $this->db->get('cat_screen_info');
        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return FALSE;
        }
    }
    
    /**
     * 添加屏幕设置
     *
     * @param object $array
     * @param object $uid
     * @return
     */
    public function add_screen_info($array, $uid) {
        if ( empty($array)) {
            return 0;
        }
        
        $array['add_user_id'] = $uid;
        
        if ($this->db->insert('cat_screen_info', $array)) {
            $id = $this->db->insert_id();
            $this->user_log($this->OP_TYPE_USER, 'cat_screen_info['.$id.'] ');
            return $id;
        } else {
            return FALSE;
        }
    }
    
    /**
     * 删除屏幕设置信息
     *
     * @param object $id
     * @return
     */
    public function delete_screen_info($id) {
    
        $this->db->delete('cat_screen_info', array('id'=>$id));
    }

    
    
    
}
