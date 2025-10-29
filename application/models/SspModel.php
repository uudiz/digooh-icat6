<?php

class SspModel extends CI_Model
{
    public function getHourlyData()
    {
        $result = array();
        $this->db->select('*');
        $this->db->from('ssp_hourly');
        $this->db->order_by('id', 'DESC');
        $this->db->limit(12);

        $query = $this->db->get();
        $total = $query->num_rows();
        if ($total > 0) {
            $result =  $query->result();
        }
        return array('total' => $total, 'data' => $result);
    }

    public function getDailyData()
    {
        $result = array();
        $this->db->select('*');
        $this->db->from('ssp_dialy');
        $this->db->order_by('id', 'DESC');
        $this->db->where('request_at>=', date("Y-m-d", strtotime("-7 days")));

        $query = $this->db->get();
        $total = $query->num_rows();
        if ($total > 0) {
            $result =  $query->result();
        }
        return array('total' => $total, 'data' => $result);
    }

    public function getPlayerLog($filter = array(), $offset = 0, $limit = -1)
    {
        $result = array();
        $this->db->select('pm.created_at, p.name as player, p.sn as sn, c.campaign_name as campaign,m.media_id as media');
        $this->db->from('ssp_player_media pm');
        $this->db->join('cat_player p', 'p.id = pm.player_id', 'left');
        $this->db->join('ssp_campaign_media cm', 'cm.media_id = pm.media_id', 'left');
        $this->db->join('ssp_media m', 'm.id = pm.media_id', 'left');
        $this->db->join('ssp_campaign c', 'c.id = cm.campaign_id', 'left');
        $this->db->order_by('pm.id', 'DESC');

        foreach ($filter as $key => $value) {
            if ($key == "player_filter") {
                $this->db->group_start();
                $this->db->like('p.name', $value);
                $this->db->or_like('p.sn', $value);
                $this->db->group_end();
            } else if ($key == "camapign_filter") {
                $this->db->like('c.campaign_name', $value);
            } else if ($key == "media_filter") {
                $this->db->like('m.media_id', $value);
            }
        }

        $this->db->limit(50);

        $query = $this->db->get();
        $total = $query->num_rows();
        if ($total > 0) {
            $result =  $query->result();
        }
        return array('total' => $total, 'data' => $result);
    }
}
