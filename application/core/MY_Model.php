<?php

/**
 * Model基类，定义用户行为日志
 */
class MY_Model extends CI_Model
{

    //系统类型
    public $OP_TYPE_SYSTEM = 1;
    //与用户行为有关类型
    public $OP_TYPE_USER   = 2;

    public $OP_STATUS_SUCCESS = 0;

    public $OP_STATUS_FAIL    = 1;
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        log_message('debug', "MY_Model Class Initialized");
    }

    /**
     * 写入用户行为日志
     *
     * @param object $type
     * @param object $detail
     * @param object $uid [optional]
     * @param object $cid [optional]
     * @param object $status [optional]
     * @return
     */
    public function user_log($type, $detail, $uid = 0, $cid = -1, $status = 0)
    {
        $uid ? $uid : $this->session->userdata('uid');
        if ($uid) {
            $data = array(
                'user_id' => $uid,
                'ip' => $this->input->ip_address(),
                'company_id' => $cid >= 0 ? $cid : $this->session->userdata('cid'),
                'op_type'    => $type,
                'detail'     => $detail,
                'status'     => $status
            );

            $this->db->insert('cat_user_log', $data);
        }
    }

    //Type: 'App\Campaign','App\Player'
    public function sync_tags($id, $tagstr, $type)
    {
        $this->detach_tags($id, $type);
        $this->attach_tags($id, $tagstr, $type);
    }
    public function attach_tags($id, $tagstr, $type)
    {
        if (!$tagstr) {
            return;
        }
        $tags = is_array($tagstr) ? $tagstr : explode(",", $tagstr);

        if (empty($tags)) {
            return;
        }
        $data = array();
        foreach ($tags as $tagid) {
            if (!empty($tagid)) {
                $item = array('tag_id' => $tagid, 'taggable_id' => $id, 'taggable_type' => $type);
                $data[] = $item;
            }
        }
        if (!empty($data)) {
            $this->db->insert_batch('taggables', $data);
        }
    }
    public function detach_tags($id, $type = 'App\Campaign')
    {
        $this->db->where("taggable_id", $id);
        $this->db->where("taggable_type", $type);
        $this->db->delete("taggables");
    }

    public function sync_criteria($id, $tagstr, $type, $bindtype = 0)
    {
        $this->detach_criteria($id, $type, $bindtype);
        $this->attach_criteria($id, $tagstr, $type, $bindtype);
    }
    public function attach_criteria($id, $tagstr, $type, $bindtype = 0)
    {
        if (!$tagstr) {
            return;
        }

        if (!is_array($tagstr)) {
            $tags = explode(",", $tagstr);
        } else {
            $tags =  $tagstr;
        }
        $data = array();
        foreach ($tags as $tagid) {
            if (!empty($tagid)) {
                $item = array('criterion_id' => $tagid, 'criterionable_id' => $id, 'criterionable_type' => $type, 'cam_bindtype' => $bindtype);
                $data[] = $item;
            }
        }
        $this->db->insert_batch('criterionables', $data);
    }
    public function detach_criteria($id, $type = "App\\\Campaign", $bindtype = false)
    {
        $this->db->where("criterionable_id", $id);
        $this->db->where('criterionable_type', $type);
        if ($bindtype !== false) {
            $this->db->where("cam_bindtype", $bindtype);
        }

        $this->db->delete('criterionables');
    }
}
