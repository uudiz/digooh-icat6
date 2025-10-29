<?php
class Feedback extends MY_Model
{
    public function get_playback($postdate, $mediaid, $playerid)
    {
        $this->db->select("id,times,duration");
        $this->db->from("cat_playback_new");
        $this->db->where("post_date", $postdate);
        $this->db->where("media_id", $mediaid);
        $this->db->where("player_id", $playerid);
        $query = $this->db->get();
        if ($query->num_rows()) {
            return $query->row();
        }
        return false;
    }

    /**
     * 添加播放反馈
     *
     * @param object $array
     * @return
     */
    public function add_feedback($array)
    {
        if (empty($array)) {
            return false;
        }
        $id = false;
        $detail = array();

        $recorditem = $this->get_playback($array['post_date'], $array['media_id'], $array['player_id']);
        $duration = $array['duration'];

        if ($recorditem) {
            //Update
            $duration = $array['duration'] + $recorditem->duration;
            $data = array("times" => $array['times'] + $recorditem->times, "duration" => $array['duration'] + $recorditem->duration);
            $this->db->where("id", $recorditem->id);
            if ($this->db->update("cat_playback_new", $data)) {
                $id = $recorditem->id;
                $detail_rec = $this->get_playback_detail($id);
            }
        } else {
            //Query and insert
            $this->db->select("pm.playlist_id,m.name as media_name, cp.name as campaign_name");
            $this->db->from("cat_playlist_area_media pm");
            $this->db->join('cat_media m', 'm.id=pm.media_id', 'left');
            $this->db->join('cat_playlist cp', 'cp.id=pm.playlist_id', 'left');
            $this->db->where('pm.id', $array['media_id']);

            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                $result = $query->row_array();

                $array['media_name'] = $result['media_name'];
                $array['campaign_name'] = $result['campaign_name'];
                $array['campaign_id'] = $result['playlist_id'];

                if ($this->db->insert('cat_playback_new', $array)) {
                    $id = $this->db->insert_id();
                }
            }
        }
        if ($id) {
            if ($this->config->item('new_playback_detail')) {
                //Get planed times
                $this->db->select("*");
                $this->db->from("cat_player_campaign_planed");
                $this->db->where('medium_id', $array['media_id']);
                $this->db->where('date', $array['post_date']);
                $this->db->where('player_id', $array['player_id']);

                $query = $this->db->get();

                if ($query->num_rows() > 0) {
                    $planded = $query->row();
                    $detail['planed_times'] = $planded->planed_times;
                    $detail['fulfillment_planed'] = round($duration / $planded->planed_times, 4) * 100;
                    $detail['fulfillment_booked'] = $planded->booked_times ? round($duration / $planded->booked_times, 4) * 100 : 0;
                    $detail['playback_id'] = $id;
                    if ($id) {

                        if ($detail_rec) {
                            $this->db->update("cat_playback_detail", $detail, array("id" => $detail_rec->id));
                        } else {
                            $this->db->insert('cat_playback_detail', $detail);
                        }
                    }
                }
            }
        }
        return $id;
    }

    public function get_playback_detail($playback_id)
    {
        if ($this->config->item('new_playback_detail')) {
            $this->db->select("id");
            $this->db->from("cat_playback_detail");
            $this->db->where('playback_id', $playback_id);

            $query = $this->db->get();
            if ($query->num_rows() > 0) {
                return $query->row();
            }
        }
        return false;
    }

    public function add_feedback_batch($feedbacks)
    {
        foreach ($feedbacks as $feedback) {
            $this->add_feedback($feedback);
        }
    }

    /**
     * 获取公司下的反馈
     *
     * @param object $company_id
     * @return
     */
    public function get_playback_count($company_id)
    {
        $this->db->select("id");
        $this->db->from('cat_playback_new pb');
        $this->db->where('pb.company_id', $company_id);
        $query = $this->db->get();
        return $query->num_rows();
    }
    /**
     * 查询某个公司下指定的记录信息
     *
     * @param object $company_id
     * @param object $group_ids [optional]
     * @param object $player_id [optional]
     * @param object $start_date [optional]
     * @param object $end_date [optional]
     * @param object $offset [optional]
     * @param object $limit [optional]
     * @return
     */
    public function query($company_id, $filter_array = false, $offset = 0, $limit = -1, $order_item = 'post_date', $order = 'asc')
    {
        $runtime_start = microtime(true);

        $this->db->select("pb.id");
        $this->db->from('cat_playback_new pb');
        $this->db->join('cat_player p', 'pb.player_id = p.id', 'left');


        if ($filter_array && is_array($filter_array)) {
            foreach ($filter_array as $key => $value) {
                if (empty($value)) {
                    continue;
                }
                if ($key == 'start_date') {
                    $this->db->where('pb.post_date >= ', $value);
                } elseif ($key == 'end_date') {
                    $this->db->where('pb.post_date <= ', $value);
                } elseif ($key == 'campaign') {
                    $this->db->where('pb.campaign_name', $value);
                } elseif ($key == 'player') {
                    $this->db->where('pb.player_id', $value);
                } elseif ($key == 'media') {
                    //$this->db->group_start();
                    $this->db->where('pb.media_name', $value);
                    //$this->db->group_end();
                } elseif ($key == 'campaign_id') {
                    $this->db->where('pb.campaign_id', $value);
                }
            }
        }

        $this->db->where('pb.company_id', $company_id);

        $query  = $this->db->get();
        $total = $query->num_rows();
        //$total = $this->db->count_all_results('', false);




        $array = array();
        if ($total > 0) {
            if (!$this->config->item('with_template')) {
                if ($this->config->item('new_playback_detail')) {
                    $this->db->select("pb.*,p.name as player_name, p.sn,pd.planed_times, pd.fulfillment_planed,pd.fulfillment_booked");
                    $this->db->join('cat_playback_detail pd', 'pd.playback_id=pb.id', 'left');
                } else {
                    $this->db->select("pb.*,p.name as player_name, p.sn,cp.planed_times, FORMAT((pb.duration/cp.planed_times)*100,2) as fulfillment_planed,FORMAT((pb.duration/cp.booked_times)*100,2) as fulfillment_booked");
                    $this->db->join('cat_player_campaign_planed cp', 'cp.campaign_id=pb.campaign_id AND cp.medium_id=pb.media_id AND cp.date=pb.post_date AND cp.player_id=pb.player_id AND 
                (cp.date >="' . $filter_array['start_date'] . '" AND cp.date <="' . $filter_array['end_date'] . '")', 'left');
                }
            } else {
                $this->db->select("pb.*,p.name as player_name, p.sn");
            }
            $this->db->from('cat_playback_new pb');
            $this->db->join('cat_player p', 'pb.player_id = p.id', 'left');
            if ($filter_array && is_array($filter_array)) {
                foreach ($filter_array as $key => $value) {
                    if (empty($value)) {
                        continue;
                    }
                    if ($key == 'start_date') {
                        $this->db->where('pb.post_date >= ', $value);
                    } elseif ($key == 'end_date') {
                        $this->db->where('pb.post_date <= ', $value);
                    } elseif ($key == 'campaign') {
                        $this->db->where('pb.campaign_name', $value);
                    } elseif ($key == 'player') {
                        $this->db->where('pb.player_id', $value);
                    } elseif ($key == 'media') {
                        //$this->db->group_start();
                        $this->db->where('pb.media_name', $value);
                        //$this->db->group_end();
                    } elseif ($key == 'campaign_id') {
                        $this->db->where('pb.campaign_id', $value);
                    }
                }
            }

            $this->db->where('pb.company_id', $company_id);
            if ($order_item == 'id') {
                $this->db->order_by('pb.id', $order);
            } elseif ($order_item == "player_name") {
                $this->db->order_by('p.name', $order);
            } elseif ($order_item == "player_id") {
                $this->db->order_by('p.id', $order);
            } elseif ($order_item == "cam_name") {
                $this->db->order_by('pb.campaign_name', $order);
            } else {
                $this->db->order_by($order_item, $order);
            }
            if ($limit > 0) {
                $this->db->limit($limit, $offset);
            }

            //echo $this->db->get_sql();
            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                $array = $query->result();
                $query->free_result();
            }
        }
        return array('total' => $total, 'data' => $array);
    }

    public function query_not_played_players($company_id, $filter_array = false, $offset = 0, $limit = -1, $order_item = 'post_date', $order = 'asc')
    {
        $this->load->helper('chrome_logger');
        $runtime_start = microtime(true);
        $this->db->select("cp.id,cp.planed_times,cp.booked_times,cp.date as post_date, player.name as player_name,cam.name as campaign_name, m.name as media_name");
        $this->db->from('cat_player_campaign_planed cp');
        $this->db->join('cat_playback_new pb', 'pb.campaign_id=cp.campaign_id AND pb.media_id=cp.medium_id AND pb.player_id=cp.player_id and pb.post_date=cp.date', 'left');
        $this->db->join('cat_playlist cam', 'cam.id=cp.campaign_id', 'left');
        $this->db->join('cat_playlist_area_media pm', 'pm.id=cp.medium_id', 'left');
        $this->db->join('cat_player player', 'player.id=cp.player_id', 'left');
        $this->db->join('cat_media m', 'm.id=pm.media_id', 'left');
        $this->db->where('pb.player_id is null');

        /*
        $this->db->select("cp.id");
        $this->db->from('cat_player_campaign_planed cp');
        $this->db->join('cat_playback_new pb', 'pb.campaign_id=cp.campaign_id AND pb.media_id=cp.medium_id AND pb.player_id=cp.player_id and pb.post_date=cp.date', 'left');
        $this->db->join('cat_playlist cam', 'cam.id=cp.campaign_id', 'left');
        $this->db->join('cat_playlist_area_media pm', 'pm.id=cp.medium_id', 'left');
        $this->db->join('cat_media m', 'm.id=pm.media_id', 'left');
        $this->db->where('pb.player_id is null');
        */

        if ($filter_array && is_array($filter_array)) {
            foreach ($filter_array as $key => $value) {
                if (empty($value)) {
                    continue;
                }
                if ($key == 'start_date') {
                    $this->db->where('cp.date >= ', $value);
                } elseif ($key == 'end_date') {
                    $this->db->where('cp.date <= ', $value);
                } elseif ($key == 'campaign') {
                    $this->db->where('cam.name', $value);
                } elseif ($key == 'player') {
                    $this->db->where('cp.player_id', $value);
                } elseif ($key == 'media') {
                    $this->db->where('m.name', $value);
                } elseif ($key == 'campaign_id') {
                    $this->db->where('pb.campaign_id', $value);
                }
            }
        }

        $this->db->where('player.company_id', $company_id);
        $this->db->distinct();
        // $query = $this->db->get();
        // $total = $query->num_rows();
        $total = $this->db->count_all_results('', false);

        $array = array();
        if ($total > 0) {
            if ($order_item == 'id') {
                $this->db->order_by('pb.id', $order);
            } elseif ($order_item == "player_name") {
                $this->db->order_by('player_name', $order);
            } elseif ($order_item == "player_id") {
                $this->db->order_by('player_id', $order);
            } elseif ($order_item == "cam_name") {
                $this->db->order_by('campaign_name', $order);
            } else {
                $this->db->order_by($order_item, $order);
            }
            if ($limit > 0) {
                $this->db->limit($limit, $offset);
            }


            //echo $this->db->get_sql();
            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                $array = $query->result();
                $query->free_result();
            }
        }
        $this->db->reset_query();
        return array('total' => $total, 'data' => $array);
    }

    public function merge_playback_detail($start_date, $months)
    {
        $offset = 0;
        while (1) {
            $this->db->select("*");
            $this->db->from("cat_playback_new");
            if ($start_date) {
                $this->db->where('post_date >=', $start_date);
                if ($months) {
                    $this->db->where('post_date <=', date('Y-m-d', strtotime($start_date . ' +' . $months . ' month')));
                }
            }
            $this->db->limit(10000, $offset);

            $query = $this->db->get();
            $total = $query->num_rows();
            $offset += $total;
            if ($query->num_rows()) {
                $playbacks = $query->result();
                $query->free_result();
                foreach ($playbacks as $playback) {
                    $this->db->select("*");
                    $this->db->from("cat_player_campaign_planed");
                    $this->db->where('medium_id', $playback->media_id);
                    $this->db->where('date', $playback->post_date);
                    $this->db->where('player_id', $playback->player_id);
                    echo "cacluating..." . $playback->post_date . PHP_EOL . "<br>";
                    $query = $this->db->get();

                    if ($query->num_rows() > 0) {
                        $planded = $query->row();
                        $detail['planed_times'] = $planded->planed_times;
                        $detail['fulfillment_planed'] = round($playback->duration / $planded->planed_times, 4) * 100;
                        $detail['fulfillment_booked'] = $planded->booked_times ? round($playback->duration / $planded->booked_times, 4) * 100 : 0;
                        $detail['playback_id'] = $playback->id;
                        $this->db->insert('cat_playback_detail', $detail);
                        $query->free_result();
                    }
                }
            } else {
                break;
            }
        }
    }

    public function query_summary($company_id, $filter_array = false)
    {
        $runtime_start = microtime(true);

        $filter_campaign = false;
        $filter_player = false;
        $filter_media = false;
        if (isset($filter_array['campaign']) && !empty($filter_array['campaign'])) {
            $filter_campaign = true;
            $this->db->where('pb.campaign_name', $filter_array['campaign']);
        }
        if (isset($filter_array['player']) && !empty($filter_array['player'])) {
            $filter_player = true;
            $this->db->where('pb.player_id', $filter_array['player']);
        }
        if (isset($filter_array['campaign_id']) && !empty($filter_array['campaign_id'])) {
            $filter_campaign = true;
            $this->db->where('pb.campaign_id', $filter_array['campaign_id']);
        }

        if (isset($filter_array['media']) && !empty($filter_array['media'])) {
            $this->db->where('pb.media_name', $filter_array['media']);
            $filter_media = true;
        }
        if (isset($filter_array['post_date']) && !empty($filter_array['post_date'])) {
            $this->db->where('pb.post_date', $filter_array['post_date']);
        } else {
            if (isset($filter_array['start_date']) && !empty($filter_array['start_date'])) {
                $this->db->where('pb.post_date >=', $filter_array['start_date']);
                $this->db->where('pb.post_date <=', $filter_array['end_date']);
            }
            if (isset($filter_array['end_date']) && !empty($filter_array['end_date'])) {
                $this->db->where('pb.post_date <=', $filter_array['end_date']);
            }
        }
        $this->db->select("pb.post_date as date,count(distinct(pb.player_id)) as player_cnt,count(distinct(pb.media_id)) as media_cnt,count(distinct(pb.campaign_name)) as cam_cnt,
            sum(pb.times) as total_times,sum(pb.duration) as total_duration, sum(pd.planed_times) as total_planed, avg(pd.fulfillment_planed) as avg_ful_planed,avg(pd.fulfillment_booked) as avg_ful_booked");


        $this->db->group_by('pb.post_date');
        if ($filter_campaign) {
            $this->db->select("pb.campaign_name as name");
            $this->db->group_by('pb.campaign_name');
        }
        if ($filter_player) {
            $this->db->select('pb.player_id as name');
            $this->db->group_by('pb.player_id');
        }
        if ($filter_media) {
            $this->db->select('pb.media_name as name');
            $this->db->group_by('pb.media_name');
        }

        $this->db->from('cat_playback_new pb');
        $this->db->join('cat_playback_detail pd', 'pd.playback_id=pb.id', 'left');


        $this->db->where('pb.company_id', $company_id);
        $this->db->order_by("pb.post_date", "asc");

        $query = $this->db->get();

        $array = array();
        if ($query->num_rows() > 0) {
            if ($query->num_rows() > 0) {
                $array = $query->result();
                $query->free_result();
                return $array;
            }
        }
        return false;
    }
}
