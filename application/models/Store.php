<?php
class Store extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_all($offset = 0, $limit = -1, $order_item = 'name', $order = 'asc', $filter_array = array())
    {
        $array = array();

        $this->db->select('*');
        $this->db->from("cr_stores");

        if (!empty($filter_array)) {
            foreach ($filter_array as $key => $value) {
                if ($key == 'name') {
                    $this->db->like('name', $value);
                } else {
                    $this->db->where($key, $value);
                }
            }
        }
        if ($limit > 0) {
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
        } else {
            $this->db->order_by($order_item, $order);
            $query = $this->db->get();
            $total = $query->num_rows();
            if ($total > 0) {
                $array = $query->result();
                $query->free_result();
            }
        }
        return array('total' => $total, 'data' => $array);
    }

    public function get_by_id($id)
    {
        $this->db->where('id', $id);
        $query = $this->db->get('cr_stores');
        return $query->row();
    }
    public function get_by_store_id($id)
    {
        $this->db->where('store_id', $id);
        $query = $this->db->get('cr_stores');
        return $query->row();
    }
    public function insert($data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->insert('cr_stores', $data);
        return $this->db->insert_id();
    }

    public function update($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->where('id', $id);
        return $this->db->update('cr_stores', $data);
    }

    public function delete($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('cr_stores');
    }
    public function delete_not_in_store_ids($store_ids)
    {
        $this->db->where_not_in('store_id', $store_ids);
        return $this->db->delete('cr_stores');
    }
    public function get_stores_by_user($user_id)
    {

        $this->db->select("s.id,s.name");
        $this->db->from('cr_user_stores us');
        $this->db->join('cr_stores s', 's.id=us.store_id', 'left');
        $this->db->where('us.user_id', $user_id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $result =  $query->result();
            return $result;
        }
        return false;
    }
}
