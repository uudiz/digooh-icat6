<?php
class Product extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_all($offset = 0, $limit = -1, $order_item = 'name', $order = 'asc', $filter_array = array())
    {
        $array = array();

        $this->db->select('cr_products.*');
        $this->db->from("cr_products");
        if (!empty($filter_array)) {
            foreach ($filter_array as $key => $value) {
                if ($key == 'name') {
                    $this->db->group_start();
                    $this->db->like('name', $value);
                    $this->db->or_like('ean_code', $value);
                    $this->db->or_like('plu_code', $value);
                    $this->db->group_end();
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
        $query = $this->db->get('cr_products');
        return $query->row();
    }
    public function get_by_filter($filter_array)
    {
        foreach ($filter_array as $key => $value) {
            $this->db->where($key, $value);
        }
        $query = $this->db->get('cr_products');
        return $query->row();
    }
    public function insert($data)
    {
        $this->db->insert('cr_products', $data);
        return $this->db->insert_id();
    }

    public function update($id, $data)
    {
        if (!isset($data['updated_at'])) {
            $data['updated_at'] = date('Y-m-d H:i:s');
        }
        $this->db->where('id', $id);
        return $this->db->update('cr_products', $data);
    }

    public function delete($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('cr_products');
    }
    public function delete_not_in_product_ids($product_ids)
    {
        if (!$product_ids || empty($product_ids)) {
            return false;
        }
        $this->db->where_not_in('product_id', $product_ids);
        return $this->db->delete('cr_products');
    }

    public function delete_discounts($id)
    {
        $this->db->where('product_id', $id);
        return $this->db->delete('cr_discounts');
    }
    public function insert_discounts_batch($data)
    {
        return $this->db->insert_batch('cr_discounts', $data);
    }
    public function get_price($product_id)
    {
        $this->db->select('price');
        $this->db->from('cr_products');
        $this->db->where('id', $product_id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $row = $query->row();
            $this->db->where('product_id', $product_id);
            $query = $this->db->get('cr_discounts');
            if ($query->num_rows() > 0) {
                $row->discounts  = $query->result();
            }
            return $row;
        }
    }
}
