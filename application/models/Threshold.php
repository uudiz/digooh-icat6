<?php

class Threshold extends MY_Model
{
    public $table = 'extremum_settings'; // you MUST mention the table name
    public function get_list($cid, $offset = 0, $limit = -1, $order_item = 'name', $order = 'asc', $filter_array = array())
    {
        $array = array();

        $this->db->select('p.*');
        $this->db->from("thresholds p");
        if ($cid) {
            $this->db->where('p.company_id', $cid);
        }


        if (!empty($filter_array)) {
            foreach ($filter_array as $key => $value) {
                if ($key == 'name') {
                    $this->db->like('name', $value);
                } else {
                    $this->db->where("p." . $key, $value);
                }
            }
        }

        //$db = clone ($this->db);
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
    public function get_item($id)
    {
        $this->db->select("*");
        $this->db->from("thresholds");
        $this->db->where('id', $id);

        $query = $this->db->get();


        if ($query->num_rows()) {
            $result = $query->row();
            return $result;
        }
        return false;
    }
    public function add_item($array)
    {
        if (empty($array)) {
            return 0;
        }
        if ($this->db->insert('thresholds', $array)) {
            $id = $this->db->insert_id();
            //$this->user_log($this->OP_TYPE_USER, 'add_peripheral[' . $id . '] name[' . json_encode($array) . ']');
            return $id;
        } else {
            return false;
        }
    }

    /**
     *
     * @param object $array
     * @param object $id
     * @return
     */
    public function update_item($array, $id)
    {
        if (empty($array)) {
            return 0;
        }
        $this->db->where('id', $id);
        if ($this->db->update('thresholds', $array)) {
            return true;
        } else {
            return false;
        }
    }

    public function delete_item($id)
    {
        $this->db->trans_begin();

        if (is_array($id)) {
            $this->db->where_in('id', $id);
        } else {
            $this->db->where('id', $id);
        }
        $this->db->delete("thresholds");

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();

            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }

    public function get_item_by_name($id, $cid, $name)
    {
        $this->db->select('id');
        $this->db->from("thresholds");
        if ($cid) {
            $this->db->where("company_id", $cid);
        }
        if ($id > 0) {
            $this->db->where('id!=', $id);
        }
        $this->db->where('name', $name);

        $query = $this->db->get();

        if ($query->num_rows()) {
            return $query->row();
        } else {
            return false;
        }
    }

    public function sync_threshold_player($players, $threshold_id)
    {
        $this->db->where('threshold_id', $threshold_id);
        $this->db->update('cat_player', array('threshold_id' => null));

        if (empty($players)) {
            return;
        }

        if (!is_array($players)) {
            $players = explode(",", $players);
        }


        $this->db->where_in('id', $players);
        $this->db->update('cat_player', array('threshold_id' => $threshold_id));
    }



    public function get_player_ids_by_threshold($threshold_id)
    {
        $this->db->select("id");
        $this->db->from('cat_player');
        $this->db->where('threshold_id', $threshold_id);

        $query = $this->db->get();


        if ($query->num_rows()) {
            $result = $query->result_array();
            return array_column($result, 'id');
        }
        return false;
    }
}
