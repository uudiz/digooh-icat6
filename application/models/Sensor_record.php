<?php

class Sensor_record extends MY_Model
{
    private $table_name = 'sensor_reports'; // you MUST mention the table name
    public function get_list($cid, $offset = 0, $limit = -1, $order_item = 'name', $order = 'asc', $filter_array = array())
    {
        $array = array();

        $this->db->select("$this->table_name.*,p.name as player_name");
        $this->db->join('cat_player p', "p.id=$this->table_name.player_id", "LEFT");
        $this->db->from($this->table_name);
        if ($cid) {
            $this->db->where("$this->table_name.company_id", $cid);
        }


        if (!empty($filter_array)) {
            foreach ($filter_array as $key => $value) {
                if ($key == 'name') {
                    $this->db->like('name', $value);
                } else if ($key == 'start_date') {
                    $this->db->where('date>=', $value);
                } else if ($key == 'end_date') {
                    $this->db->where('date<=', $value);
                } else if ($key == 'notified_only') {
                    $this->db->where('notify_at is not null');
                } else {
                    $this->db->where($key, $value);
                }
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
    public function get_item($id)
    {
        $this->db->select("*");
        $this->db->from($this->table_name);
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
        if ($this->db->insert($this->table_name, $array)) {
            $id = $this->db->insert_id();
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
        if ($this->db->update($this->table_name, $array)) {
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
        $this->db->delete($this->table_name);

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
        $this->db->from($this->table_name);
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

    public function get_chart_list($cid)
    {
        $array = array();

        $this->db->select("date, COUNT(id) as count");
        $this->db->from("sensor_reports");
        $this->db->where('date>=', date('Y-m-d', strtotime('-1 months')));
        $this->db->group_by('date');

        $this->db->where('notify_at is not null');
        if ($cid) {
            $this->db->where("$this->table_name.company_id", $cid);
        }
        $query = $this->db->get();
        if ($query->num_rows()) {
            $array = $query->result();

            $query->free_result();
            return $array;
        }
        return false;
    }
}
