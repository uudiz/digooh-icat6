<?php
class Xmlnews extends MY_Model {
	
	public function get_news_settings(){
		$this->db->select('*');
		$this->db->from('cat_news_settings');
		
		$array = array();
		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			$array = $query->row();
			
			$query->free_result();
		}
		
		return $array;
	}
	
	public function update_news_settings($array){
		if ( empty($array)) {
			return FALSE;
		}
		
		$this->db->where('id', 0);
		if ($this->db->update('cat_news_settings', $array)) {
			return TRUE;
		}
		return FALSE;
	}
	
	
	public function update_news_item($id,$array){
		if ( empty($array)) {
			return FALSE;
		}
		
		$this->db->where('id', $id);
		if ($this->db->update('cat_news_item', $array)) {
			return $id;
		}
		return FALSE;
	}
	
	public function add_news_item($array){
		if ( empty($array)) {
			return FALSE;
		}
		
		if ($this->db->insert('cat_news_item', $array)) {
			return TRUE;
		}
		
		return FALSE;
	}
	
	public function delete_news_item($id){
		
		$this->db->where('id',$id);
		return $this->db->delete('cat_news_item');
		
	}
	
	public function insert_or_update_item($id,$array){
		$this->db->select('id');
		$this->db->from('cat_news_item');
		$this->db->where('id',$id);
		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			$this->update_news_item($id, $array);	
		}else{
			$array['id'] = $id;
			$this->add_news_item($array);
		}
	}
	
	public function get_news_item($id){
		$this->db->select('*');
		$this->db->from('cat_news_item');
		$this->db->where('id',$id);
		
		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			return $query->row();
		}
		
		return FALSE;
	}
	
	public function get_news_item_list($order_item = 'id', $order = 'desc'){
		$this->db->select('*');
		$this->db->from('cat_news_item');
		$this->db->order_by($order_item, $order);
		
		$array = array();
		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			$array = $query->result();
			
			$query->free_result();
		}
		
		return $array;
	}
}