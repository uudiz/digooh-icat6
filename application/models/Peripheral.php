<?php

class Peripheral extends MY_Model
{
    public function get_player_peripherals($player_id, $offset = 0, $limit = -1, $order_item = 'name', $order = 'asc', $filter_array = array())
    {
        $this->db->select('p.*');
        $this->db->from("cat_peripherals p");
        $this->db->join('player_peripheral pp', 'pp.peripheral_id = p.id', 'left');
        $this->db->join('cat_player player', 'player.id=pp.player_id', 'left');
        $this->db->where('player.id', $player_id);

        $db = clone ($this->db);
        $total = $this->db->count_all_results('', false);
        $array = array();
        if ($total > 0) {
            $this->db = $db;
            $this->db->order_by($order_item, $order);
            if ($limit != -1) {
                $this->db->limit($limit, $offset);
            }
            $query = $this->db->get();
            if ($query->num_rows()) {
                $array = $query->result();
            }
        }
        $this->db->reset_query();
        return array('total' => $total, 'data' => $array);
    }


    public function get_list($cid, $offset = 0, $limit = -1, $order_item = 'name', $order = 'asc', $filter_array = array())
    {
        $array = array();

        $this->db->select('p.*');
        $this->db->from("cat_peripherals p");
        if ($cid) {
            $this->db->where('company_id', $cid);
        }


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
        $this->db->from("cat_peripherals");
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
        if ($this->db->insert('cat_peripherals', $array)) {
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
        if ($this->db->update('cat_peripherals', $array)) {
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
        $this->db->delete("cat_peripherals");

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
        $this->db->from("cat_peripherals");
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

    public function sync_peripheral_player($players, $peripheral_id)
    {
        $this->db->where('peripheral_id', $peripheral_id);
        $this->db->delete('player_peripheral');

        if (empty($players)) {
            return;
        }

        if (!is_array($players)) {
            $players = explode(",", $players);
        }
        $data = array();
        foreach ($players as $pid) {
            $data[]  = array('player_id' => $pid, 'peripheral_id' => $peripheral_id);
        }
        $this->db->insert_batch('player_peripheral', $data);
    }


    public function get_peripheral_by_player($sn)
    {
        $this->db->select('p.id,p.name,p.address,p.baudrate,p.data_bits,p.stop_bits,p.parity');
        $this->db->from("cat_peripherals p");
        $this->db->join('player_peripheral pp', 'pp.peripheral_id = p.id', 'left');
        $this->db->join('cat_player player', 'player.id=pp.player_id', 'right');
        $this->db->where('player.sn', $sn);
        $query = $this->db->get();
        if ($query->num_rows()) {
            return $query->result();
        }
        return false;
    }



    /**
     * get_players_by_peripheral
     *
     * @param object $id
     * @return
     */
    public function get_players_by_peripheral($peripheral_id)
    {
        $this->db->select('p.id,p.name');
        $this->db->from('cat_player p');
        $this->db->join('player_peripheral cp', 'cp.player_id=p.id', "LEFT");
        $this->db->where('cp.peripheral_id', $peripheral_id);

        $query = $this->db->get();

        $result = array();
        if ($query->num_rows()) {
            $result = $query->result();
        }
        return $result;
    }

    public function get_peripheral_player_ids($peripheral_id)
    {
        $this->db->select("player_id");
        $this->db->from('player_peripheral');
        $this->db->where('peripheral_id', $peripheral_id);

        $query = $this->db->get();


        if ($query->num_rows()) {
            $result = $query->result_array();
            return array_column($result, 'player_id');
        }
        return false;
    }

    public function get_peripheral_command_list($peripheral_id, $offset = 0, $limit = -1, $order_item = 'name', $order = 'asc', $filter_array = array())
    {
        $array = array();

        $this->db->select('p.*');
        $this->db->from("peripheral_commands p");
        if ($peripheral_id) {
            $this->db->where('peripheral_id', $peripheral_id);
        }


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

    public function get_command($id)
    {
        $this->db->select("*");
        $this->db->from("peripheral_commands");
        $this->db->where('id', $id);

        $query = $this->db->get();


        if ($query->num_rows()) {
            $result = $query->row();
            return $result;
        }
        return false;
    }

    public function add_command($array)
    {
        if (empty($array)) {
            return 0;
        }
        if ($this->db->insert('peripheral_commands', $array)) {
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
    public function update_command($array, $id)
    {
        if (empty($array)) {
            return 0;
        }
        $this->db->where('id', $id);
        if ($this->db->update('peripheral_commands', $array)) {
            return true;
        } else {
            return false;
        }
    }

    public function delete_command($id)
    {
        $this->db->trans_begin();

        if (is_array($id)) {
            $this->db->where_in('id', $id);
        } else {
            $this->db->where('id', $id);
        }
        $this->db->delete("peripheral_commands");

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();

            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }

    public function get_command_and_settings($id)
    {
        $this->db->select("c.name,c.command,c.peripheral_id,p.address,p.baudrate,p.data_bits,p.stop_bits,p.parity");
        $this->db->from("peripheral_commands c");
        $this->db->join('cat_peripherals p', 'p.id=c.peripheral_id', 'LEFT');
        $this->db->where('c.id', $id);

        $query = $this->db->get();


        if ($query->num_rows()) {
            $result = $query->row();
            return $result;
        }
        return false;
    }

    public function get_scheduled_commands($force = false)
    {

        $redis = new Redis();
        $redis->connect('127.0.0.1', 6379);
        //$redis->auth('REDIS_PASSWORD');

        $key = 'scheduled_commands';
        $commands = array();
        if (!$redis->get($key) || $force) {

            $this->db->select('*');
            $this->db->from("peripheral_commands");
            $this->db->where('auto_mode>', 0);

            $query = $this->db->get();
            if ($query->num_rows()) {
                $commands = $query->result();
                $query->free_result();
            }

            $redis->set($key, serialize($commands));
            //$redis->expire($key, 10);
        } else {
            $commands = unserialize($redis->get($key));
        }

        return $commands;
    }
}
