<?php

class TimeSlot
{
    public $startH;
    public $startM;
    public $stopH;
    public $stopM;
    public $total_time;
    public $used_time;
    public $campaigns = null;
    public $xslot = 0;
    public $fulled = false;
    public $quota = 100;
    public $promatic_booking_time = 0;

    public function __construct($para = array())
    {
        if ($para) {
            $this->total_time = $para['total_time'];
            $this->startH = $para['startH'];
            $this->startM = $para['startM'];
            $this->stopH = $para['stopH'];
            $this->stopM = $para['stopM'];
            if (isset($para['used_time'])) {
                $this->used_time = $para['used_time'];
            } else {
                $this->used_time = 0;
            }
        }
        $this->campaigns = array();
    }

    public function init($para)
    {
        if ($para) {
            $this->total_time = $para['total_time'];
            $this->startH = $para['startH'];
            $this->startM = $para['startM'];
            $this->stopH = $para['stopH'];
            $this->stopM = $para['stopM'];
            if (isset($para['used_time'])) {
                $this->used_time = $para['used_time'];
            } else {
                $this->used_time = 0;
            }
        }
        $this->campaigns = array();
    }

    public function add_campagin_array($campaign_ary)
    {
        $this->campaigns[] = $campaign_ary;
    }


    public function add_campagin($campaign, $play_num = 1, $checkob = 1)
    {
        $cam_used = 0;
        if ($play_num <= 0) {
            return false;
        }

        //total time or grouped campaign
        if ($campaign->play_cnt_type == 2 || $campaign->is_grouped) {
            $cam_used = $campaign->total_time * $play_num;
        } else {
            $cam_used = $play_num * ($campaign->total_time / $campaign->media_cnt);
        }

        if ($checkob) {
            $total = ceil($this->total_time * ($this->quota / 100));

            if ($cam_used > ($total - $this->used_time) || !$campaign) {
                return false;
            }
        }
        $exsit = false;
        $this->used_time = $this->used_time + $cam_used;

        if (!empty($this->campaigns)) {
            foreach ($this->campaigns as &$cam) {
                if ($cam['compaign_id'] == $campaign->id) {
                    $exsit = true;
                    $cam['count'] = $cam['count'] + $play_num;
                    $cam['used'] = $cam['used'] + $cam_used;
                    break;
                }
            }
        }

        if (!$exsit) {
            $cam_ary = array(
                "compaign_id" => $campaign->id,
                "name" => $campaign->name,
                "count" => $play_num,
                'priority' => $campaign->priority,
                'company_id' => $campaign->company_id,
                'publish_time' => $campaign->update_time,
                'playcnt_type' => $campaign->play_cnt_type,
                'play_weight' => $campaign->play_weight,
                'used' => $cam_used,
                'is_grouped' => $campaign->is_grouped
            );
            if ($campaign->play_cnt_type == 9) {
                $cam_ary['xslot'] = $campaign->nxslot;
            }
            if (isset($campaign->tag_options) && $campaign->tags) {
                $cam_ary['tags'] = is_array($campaign->tags) ? $campaign->tags : explode(',', $campaign->tags);
            }
            if (isset($campaign->extended_campaigns_id)) {
                $cam_ary['extended_campaigns_id'] = $campaign->extended_campaigns_id;
                $cam_ary['has_replace_main'] = isset($campaign->has_replace_main) ? $campaign->has_replace_main : 0;
            }
            $this->campaigns[] = $cam_ary;
        }


        if ($campaign->priority == 7) {
            $this->promatic_booking_time += $cam_used;
        }
        return $cam_used;
        //return true;
    }
    public function get_startTime()
    {
        return sprintf("%02d:%02d", $this->startH, $this->startM);
    }

    public function get_stopTime()
    {
        return sprintf("%02d:%02d", $this->stopH, $this->stopM);
    }

    public function get_company_used($company_id)
    {
        $company_used = 0;
        if ($this->campaigns) {
            foreach ($this->campaigns as $cam) {
                if ($cam['company_id'] == $company_id) {
                    $company_used += $cam['used'];
                }
            }
        }
        return $company_used;
    }

    public function fill_with_campaigns($fill_campaigns, $partner = false)
    {
        if ($partner != false) {
            $partner_used = $this->get_company_used($partner->partner_id);
            $partner_avi_time = $this->total_time * ($partner->quota / 100) - $partner_used;
        } else {
            $partner_avi_time = $this->total_time - $this->used_time;
        }


        $camcnt = count($fill_campaigns);

        $fillin_used = 0;
        $exit_flag = false;


        $slot_start = $this->startH;
        $slot_campaigns  =  array_filter($fill_campaigns, function ($cam) use ($slot_start) {
            if ($cam->time_flag || ($cam->time_flag == 0 && $this->startH >= $cam->start_timeH && $this->startH < $cam->end_timeH)) {
                return true;
            }
            return false;
        });

        if (empty($slot_campaigns)) {
            return;
        }

        while (!$exit_flag) {
            $failed_cnt = 0;

            foreach ($slot_campaigns as $fillin) {
                $avi_time = $partner_avi_time - $fillin_used;
                if ($fillin->total_time > $avi_time) {
                    if ($fillin->priority == 6) {
                        $failed_cnt++;
                        if ($failed_cnt >= $camcnt) {
                            $exit_flag = true;
                            break;
                        }
                        continue;
                    } else {
                        $count = floor($avi_time / ($fillin->total_time / $fillin->media_cnt));
                        $this->add_campagin($fillin, $count);

                        $exit_flag = true;
                        break;
                    }
                } else {
                    $fillin_used += $fillin->total_time;
                    $this->add_campagin($fillin, $fillin->priority == 6 ? 1 : $fillin->media_cnt);
                }
            }
        }
    }
}
