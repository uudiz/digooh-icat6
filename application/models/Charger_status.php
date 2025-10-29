<?php

class Charger_status extends MY_Model
{
    public function get_list($offset = 0, $limit = -1, $order_item = 'name', $order = 'asc', $filter_array = array())
    {
        $array = array();

        $this->db->select('p.*');
        $this->db->from("charger_settings p");


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
        $this->db->from("charger_settings");
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
        if ($this->db->insert('charger_settings', $array)) {
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
        if ($this->db->update('charger_settings', $array)) {
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
        $this->db->delete("charger_settings");



        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();

            return false;
        } else {
            $this->db->trans_commit();
            if (is_array($id)) {
                $this->db->where_in('charger_setting_id', $id);
            } else {
                $this->db->where('charger_setting_id', $id);
            }
            $this->db->update('cat_area_extra_setting', array('charger_setting_id' => 0));
            return true;
        }
    }


    public function get_item_by_name($id, $name)
    {
        $this->db->select('id');
        $this->db->from("charger_settings");

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


    public function get_default_status_list()
    {
        $this->db->select('name, id as api_status_id, translation, bg_color, font_color');
        $this->db->from('charger_api_status');
        $query = $this->db->get();
        if ($query->num_rows()) {
            return $query->result();
        }
        return false;
    }

    public function get_status_list($charger_setting_id)
    {
        $array = array();
        if (!$charger_setting_id || $charger_setting_id == 0) {
            return $this->get_default_status_list();
        }

        $this->db->select('p.name,s.*');
        $this->db->from("status_settings s");
        $this->db->join('charger_api_status p', 's.api_status_id = p.id', 'left');
        $this->db->where("s.charger_setting_id", $charger_setting_id);

        $this->db->order_by("name", "asc");

        $query = $this->db->get();
        if ($query->num_rows()) {
            $array = $query->result();
            $query->free_result();
        } else {
            $array = $this->get_default_status_list();
        }
        return $array;
    }

    public function update_status($array, $charger_setting_id)
    {
        if (empty($array)) {
            return 0;
        }
        $this->db->where('charger_setting_id', $charger_setting_id);
        $this->db->delete('status_settings');

        foreach ($array as $item) {
            $item['charger_setting_id'] = $charger_setting_id;
            $this->db->insert('status_settings', $item);
        }
    }
}
