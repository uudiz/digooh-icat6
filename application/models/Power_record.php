<?php
class Power_record extends MY_Model
{
    public function get_OnOff_list($cid, $offset = 0, $limit = -1, $order_item = 'on_at', $order = 'desc', $filter_array = array())
    {
        $array = array();

        $this->db->select('r.*, p.name');
        $this->db->from('cat_power_record r');
        $this->db->join('cat_player p', 'p.id=r.player_id', 'left');
        if ($cid != 0) {
            $this->db->where('p.company_id', $cid);
        }
        if (!empty($filter_array)) {
            if (isset($filter_array['name']) && $filter_array['name']) {
                $this->db->like('p.name', $filter_array['name']);
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
}
