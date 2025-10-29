<?php

class Mylog extends MY_Model
{

	/**
	 * 按条件查询所有用户日志
	 * 
	 * @param object $filter
	 * @param object $offset
	 * @param object $limit
	 * @param object $order_item [optional]
	 * @param object $order [optional]
	 * @return 
	 */
	public function get_log_list($filter, $offset, $limit, $order_item = 'id', $order = 'desc')
	{
		$this->db->select('count(*) as total');
		$this->db->from('cat_user_log');
		if (is_array($filter)) {
			foreach ($filter as $key => $value) {
				if ($key == 'start_date') {
					$this->db->where('add_time >=', $value . " 00:00:00");
				} elseif ($key == 'end_date') {
					$this->db->where('add_time <=', $value . " 23:59:59");
				} else {
					$this->db->where($key, $value);
				}
			}
		}
		$query = $this->db->get();
		$total = 0;
		if ($query->num_rows()) {
			$total = $query->row()->total;
		}
		$array = array();
		if ($total) {
			$this->db->select('l.*, u.name as user_name, c.name as company_name');
			$this->db->from('cat_user_log l');
			$this->db->join('cat_user u', 'u.id=l.user_id', 'left');
			$this->db->join('cat_company c', 'c.id=l.company_id', 'left');
			if (is_array($filter)) {
				foreach ($filter as $key => $value) {
					if ($key == 'start_date') {
						$this->db->where('l.add_time >=', $value . " 00:00:00");
					} elseif ($key == 'end_date') {
						$this->db->where('l.add_time <=', $value . " 23:59:59");
					} else {
						$this->db->where('l.' . $key, $value);
					}
				}
			}
			if ($limit > 0) {
				$this->db->limit($limit, $offset);
			}
			$this->db->order_by($order_item, $order);
			$query = $this->db->get();
			if ($query->num_rows()) {
				$array = $query->result();
			}
		}

		return array('total' => $total, 'data' => $array);
	}
	/**
	 * 按条件查询所有用户日志 用于xls导出
	 * 
	 * @param object $filter
	 * @return 
	 */
	public function get_all_log($filter)
	{
		$this->db->select('count(*) as total');
		$this->db->from('cat_user_log l');
		//----
		$this->db->join('cat_user u', 'u.id=l.user_id', 'left');
		//----
		$total = 0;
		$array = array();
		if (is_array($filter)) {
			foreach ($filter as $key => $value) {
				if ($key == 'start_date') {
					$this->db->where('l.add_time >=', $value . " 00:00:00");
				} elseif ($key == 'end_date') {
					$this->db->where('l.add_time <=', $value . " 23:59:59");
				} else {
					//$this->db->where($key, $value);
					$this->db->where('l.company_id', $value);
				}
			}
		}
		$query = $this->db->get();
		if ($query->num_rows()) {
			$total = $query->row()->total;
		}

		if ($total) {
			$this->db->select('l.*, u.name as user_name, c.name as company_name');
			$this->db->from('cat_user_log l');
			$this->db->join('cat_user u', 'u.id=l.user_id', 'left');
			$this->db->join('cat_company c', 'c.id=l.company_id', 'left');

			if (is_array($filter)) {
				foreach ($filter as $key => $value) {
					if ($key == 'start_date') {
						$this->db->where('l.add_time >=', $value . " 00:00:00");
					} elseif ($key == 'end_date') {
						$this->db->where('l.add_time <=', $value . " 23:59:59");
					} else {
						$this->db->where('l.' . $key, $value);
					}
				}
			}
			$query = $this->db->get();
			if ($query->num_rows()) {
				return $query->result();
			}
		}
	}
}
