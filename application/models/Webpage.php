<?php

class Webpage extends MY_Model
{
    public function get_list($cid, $offset = 0, $limit = -1, $order_item = 'name', $order = 'asc', $filter_array = array())
    {
        $array = array();

        $this->db->select('*');
        $this->db->from('cat_webpage');

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

    public function get_item($id)
    {
        $this->db->select("w.*,m.full_path as full_path");
        $this->db->from('cat_webpage w');
        $this->db->join('cat_media m', 'm.id=w.bg_id', 'left');
        $this->db->where('w.id', $id);


        $query = $this->db->get();


        if ($query->num_rows()) {
            $result = $query->row();
            return $result;
        } else {
            return false;
        }
    }

    public function get_item_byName($id, $cid, $name)
    {
        $name = $this->db->escape_str($name);

        if ($id > 0) {
            $sql = "select id from cat_webpage where id != $id and company_id = '$cid' and name = '$name'";
        } else {
            $sql = "select id from cat_webpage where company_id = '$cid' and name = '$name'";
        }
        $query = $this->db->query($sql);

        if ($query->num_rows()) {
            return $query->row();
        } else {
            return false;
        }
    }

    public function add_item($array)
    {
        if (empty($array)) {
            return 0;
        }
        if ($this->db->insert('cat_webpage', $array)) {
            $id = $this->db->insert_id();
            return $id;
        } else {
            return false;
        }
    }


    public function update_item($array, $id)
    {
        if (empty($array)) {
            return 0;
        }

        $this->db->where('id', $id);
        if ($this->db->update('cat_webpage', $array)) {
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
    public function delete_item($id)
    {
        $this->db->trans_begin();

        if (is_array($id)) {
            $this->db->where_in('id', $id);
        } else {
            $this->db->where('id', $id);
        }
        $this->db->delete("cat_webpage");

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();

            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }
}
