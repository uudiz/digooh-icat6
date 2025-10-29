<?php

class Activation extends MY_Model
{
    public function get_list($offset = 0, $limit = -1, $order_item = 'name', $order = 'asc', $filter_array = array())
    {
        $array = array();

        $this->db->select('p.*');
        $this->db->from("cat_player_activation p");


        if (!empty($filter_array)) {
            foreach ($filter_array as $key => $value) {
                if ($key == 'name') {
                    $this->db->like('name', $value);
                } else {
                    $this->db->where($key, $value);
                }
            }
        }

        $db = clone ($this->db);
        $total = $this->db->count_all_results('', false);

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
        $this->db->reset_query();
        return array('total' => $total, 'data' => $array);
    }
    public function get_item($id)
    {
        $this->db->select("*");
        $this->db->from("cat_player_activation");
        $this->db->where('id', $id);

        $query = $this->db->get();


        if ($query->num_rows()) {
            $result = $query->row();
            return $result;
        }
        return false;
    }

    public function get_list_byId($id)
    {
        $array = array();

        $this->db->select("*");
        $this->db->from("cat_player_activation");
        if (is_array($id)) {
            $this->db->where_in('id', $id);
        } else {
            $this->db->where('id', $id);
        }

        $query = $this->db->get();

        $total = $query->num_rows();

        if ($total > 0) {
            $array = $query->result();
            $query->free_result();
            return $array;
        }
        return false;
    }
    public function add_item($array)
    {
        if (empty($array)) {
            return 0;
        }
        if ($this->db->insert('cat_player_activation', $array)) {
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
        if ($this->db->update('cat_player_activation', $array)) {
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
        $this->db->delete("cat_player_activation");

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();

            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }


    public function get_item_by_mac($id, $name)
    {
        $this->db->select('*');
        $this->db->from("cat_player_activation");

        if ($id > 0) {
            $this->db->where('id!=', $id);
        }
        $this->db->where('mac', $name);

        $query = $this->db->get();

        if ($query->num_rows()) {
            return $query->row();
        } else {
            return false;
        }
    }
}
