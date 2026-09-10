<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * ============================================================================
 *  临时诊断控制器 —— 用完即删 (TEMPORARY DIAGNOSTIC, DELETE AFTER USE)
 * ============================================================================
 *  用途：排查发布 campaign 时的 Overbooking(OB) 报错，判断是否由最近的
 *        "medium 工作日(weekday)对齐"改动 (commit: weekday of medium while caclulating) 引起。
 *
 *  核心判据（airtight）：
 *    对每个 player / 每天 / 每个相关 campaign，比较
 *      新口径 get_campaign_media_info_by_date(日期+工作日)  vs
 *      旧口径 media_info_by_date_old(仅日期)
 *    的 media_cnt / total_time。
 *      - 若全部相等 => try_allocate_campaign 输入完全一致 => 分配必然一致 => OB 与本改动无关。
 *      - 若存在差异 => 再跑 A/B 仿真，看差异是否把结果从"通过"翻成"OB"。
 *
 *  本脚本【只读】：不调用 update_playlist / delete_planed_records / saveMany_Planed，
 *  仅复用真实只读方法(get_time_slots_byTimer / get_campaign_media_info_by_date /
 *  try_allocate_campaign)在内存里复刻发布循环。
 *
 *  运行（项目根目录，建议在装有真实库的服务器上）：
 *      php cli.php obdiag campaign 1659
 *      php cli.php obdiag campaign 1659 2026-09-10
 *
 *  PHP 8.x 下老 CI 会刷 deprecation 噪音，可过滤：
 *      php cli.php obdiag campaign 1659 2>&1 | grep -vE \
 *        "Severity|Message:|Filename:|Line Number|Backtrace|Function:|File:|Line:|PHP Error|deprecated|dynamic property|^$"
 * ============================================================================
 */
class Obdiag extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (isset($_SERVER['REMOTE_ADDR']) || php_sapi_name() !== 'cli') {
            die('Permission denied.');
        }
        $this->load->model('program');
        $this->load->model('membership');
        $this->load->library('TimeSlot');
        $this->load->helper('week');
    }

    public function campaign($id = 1659, $today = null)
    {
        $id    = (int) $id;
        $today = $today ? $today : date('Y-m-d');
        $HR    = str_repeat('=', 108);
        $hr    = str_repeat('-', 108);

        // ---- Phase 0: 配置 + campaign 概览 ----
        $cfg_week     = (bool) $this->config->item('medium_with_weekNtime');
        $cfg_partners = (bool) $this->config->item('with_partners');
        $cfg_xslot    = (bool) $this->config->item('xslot_on');
        $cfg_tags     = (bool) $this->config->item('campaign_with_tags');
        $cfg_template = (bool) $this->config->item('with_template');
        $area         = (int) $this->config->item('area_video');

        echo "\n$HR\n";
        echo "OB 诊断 | campaign id={$id} | today={$today} (" . date('D', strtotime($today)) . ")\n";
        echo "config: area_video={$area} medium_with_weekNtime=" . var_export($cfg_week, true)
            . " with_partners=" . var_export($cfg_partners, true)
            . " xslot_on=" . var_export($cfg_xslot, true)
            . " campaign_with_tags=" . var_export($cfg_tags, true)
            . " with_template=" . var_export($cfg_template, true) . "\n";
        echo "$HR\n";

        $playlist = $this->program->get_playlist($id);
        if (!$playlist) {
            echo "[!] get_playlist({$id}) 返回空：campaign 不存在或被删除。\n";
            return;
        }
        if ($cfg_template) {
            echo "[!] with_template=true：do_publish_time_slot 会直接返回成功，不会走排期/OB 逻辑。\n";
        }

        $company = $this->membership->get_company($playlist->company_id);
        printf(
            "campaign: name=%s company_id=%d priority=%d play_cnt_type=%d is_grouped=%d\n",
            $playlist->name, $playlist->company_id, $playlist->priority, $playlist->play_cnt_type, $playlist->is_grouped
        );
        printf(
            "          date=%s~%s  time_flag=%d startH=%s endH=%s  play_weight=%s play_count=%s play_total=%s play_totalperhour=%s\n",
            $playlist->start_date, $playlist->end_date, $playlist->time_flag,
            isset($playlist->start_timeH) ? $playlist->start_timeH : '-',
            isset($playlist->end_timeH) ? $playlist->end_timeH : '-',
            isset($playlist->play_weight) ? $playlist->play_weight : '-',
            isset($playlist->play_count) ? $playlist->play_count : '-',
            isset($playlist->play_total) ? $playlist->play_total : '-',
            isset($playlist->play_totalperhour) ? $playlist->play_totalperhour : '-'
        );
        printf("          media_cnt=%d total_time=%s (fill_campaign_media_info 全量)\n", $playlist->media_cnt, $playlist->total_time);

        // 发布前置判断（与 do_publish_time_slot 头部一致，均非 OB）
        if ($playlist->media_cnt == 0 && $playlist->priority != 5) {
            echo "[!] media_cnt==0 且 priority!=5 => 发布会先报 'empty media'，不是 OB。\n";
        }
        if (in_array($playlist->priority, array(3, 6, 8))) {
            echo "[!] priority={$playlist->priority} (3/6/8) => do_publish_time_slot 直接返回成功，不排期、不会 OB。\n";
        }
        if ($playlist->end_date < $today) {
            echo "[!] end_date < today => 发布报 'expired date'，不是 OB。\n";
        }

        // ---- players + 相关 campaign ----
        $players = $this->program->get_player_by_campaign($id);
        if (!$players) {
            echo "[!] 该 campaign 未指派任何 player => 发布报 'not assigned'，不是 OB。\n";
            return;
        }
        foreach ($players as $player) {
            $this->program->fill_player_details($player);
        }
        echo "指派 player 数: " . count($players) . "\n";

        // 收集"相关 campaign"：playlist + 每个 player 上共存的已发布 campaign(priority 1/2/5/7, 同 company, 排除自己)
        $involved = array();
        $involved[$playlist->id] = $playlist;
        foreach ($players as $player) {
            if (empty($player->campaigns)) {
                continue;
            }
            foreach ($player->campaigns as $c) {
                if (($c->priority == 1 || $c->priority == 2 || $c->priority == 5 || $c->priority == 7)
                    && $c->id != $playlist->id && $c->company_id == $playlist->company_id
                ) {
                    $involved[$c->id] = $c;
                }
            }
        }

        // ---- Phase 1: 工作日受限媒体扫描（决定性）----
        echo "\n$hr\n[Phase 1] 工作日受限媒体扫描 (week_flag=1 且 weekday!=127)\n$hr\n";
        $wk_media_total = 0;
        foreach ($involved as $cid => $c) {
            $wk = array();
            if (!empty($c->media)) {
                foreach ($c->media as $m) {
                    if (!empty($m['week_flag']) && isset($m['weekday']) && $m['weekday'] != 127) {
                        $wk[] = $m;
                    }
                }
            }
            $wk_media_total += count($wk);
            printf(
                "  cid=%-6d prio=%-2d grouped=%-2d media_cnt=%-3d total_time=%-8s 工作日受限媒体=%d  %s\n",
                $cid, $c->priority, $c->is_grouped, $c->media_cnt, $c->total_time, count($wk), $c->name
            );
            foreach ($wk as $m) {
                printf(
                    "        media id=%-6s play_time=%-6s weekday=%-4s 今日(%s)%s\n",
                    $m['id'], $m['play_time'], $m['weekday'], date('D', strtotime($today)),
                    isDayEnabled($m['weekday'], (int) date('w', strtotime($today))) ? '✓启用' : '✗剔除'
                );
            }
        }

        $verdict_noop = (!$cfg_week) || ($wk_media_total == 0);
        echo "\n";
        if (!$cfg_week) {
            echo ">>> 判定：medium_with_weekNtime=FALSE，工作日过滤被配置整体关闭 => 本改动是 NO-OP => OB 与本改动【无关】。\n";
        } elseif ($wk_media_total == 0) {
            echo ">>> 判定：相关 campaign 中【没有任何工作日受限媒体】=> 工作日过滤不剔除任何媒体 => 本改动是 NO-OP => OB 与本改动【无关】。\n";
        } else {
            echo ">>> 判定：存在 {$wk_media_total} 条工作日受限媒体 => 本改动【可能】影响分配，继续看 Phase 2/3 的逐日差异与 A/B 结果。\n";
        }

        // ---- Phase 2: 逐日 新旧口径 delta（决定性证据）----
        echo "\n$hr\n[Phase 2] 逐日 media_cnt/total_time 新旧口径差异 (新=日期+工作日, 旧=仅日期)\n$hr\n";
        $start_date = ($playlist->start_date < $today) ? $today : $playlist->start_date;
        $deltas     = $this->scan_deltas($playlist, $players, $start_date);
        if (empty($deltas)) {
            echo "  无任何差异：所有 player/天/campaign 的新旧口径 media_cnt 与 total_time 完全一致。\n";
            echo "  >>> 因此 try_allocate_campaign 的输入与改动前逐字节相同 => OB 结果必然相同 => OB 与本改动【无关】(数学确定)。\n";
        } else {
            echo "  发现 " . count($deltas) . " 处差异（仅列出前 40 条）：\n";
            foreach (array_slice($deltas, 0, 40) as $d) {
                printf(
                    "    %s player=%-6d cid=%-6d %-24s | 旧 cnt=%-3d t=%-8s => 新 cnt=%-3d t=%-8s\n",
                    $d['day'], $d['player_id'], $d['cid'], substr($d['name'], 0, 24),
                    $d['old_cnt'], $d['old_t'], $d['new_cnt'], $d['new_t']
                );
            }
            echo "  >>> 存在差异，需用 Phase 3 A/B 判断差异是否真的把结果翻成 OB。\n";
        }

        // ---- Phase 3: A/B 仿真（只读复刻发布循环）----
        echo "\n$hr\n[Phase 3] A/B 只读仿真发布 (定位 OB 发生点)\n$hr\n";
        $real_today = date('Y-m-d');
        if ($today != $real_today) {
            echo "  注：仿真始终用真实今天({$real_today})，因为 do_publish_time_slot 就是从今天起排期；传入的 today={$today} 仅用于 Phase 1/2 展示。\n";
        }
        $res_old = $this->simulate_publish($playlist, $players, $company, false);
        $res_new = $this->simulate_publish($playlist, $players, $company, true);

        echo "  [旧口径 仅日期]     : " . $this->fmt_result($res_old) . "\n";
        if (!empty($res_old['detail'])) {
            echo $res_old['detail'];
        }
        echo "  [新口径 日期+工作日]: " . $this->fmt_result($res_new) . "\n";
        if (!empty($res_new['detail'])) {
            echo $res_new['detail'];
        }

        echo "\n$HR\n[最终结论]\n";
        if ($verdict_noop) {
            echo "  本改动对该 campaign 是 NO-OP（配置关闭或无工作日受限媒体）。\n";
            echo "  => OB 不是最近的工作日改动引起的，属既有的真实超额预订(或日期口径, 该口径改动前已存在)。\n";
        } elseif (!$res_old['ob'] && $res_new['ob']) {
            echo "  旧口径可发布、新口径 OB => 【确认是工作日改动引起的回归】。\n";
            echo "  触发点: {$res_new['where']}\n";
        } elseif ($res_old['ob'] && $res_new['ob']) {
            echo "  新旧口径都 OB => OB 与本改动【无关】(改动前就会 OB)，是真实超额预订。\n";
            echo "  触发点: {$res_new['where']}\n";
        } elseif ($res_old['ob'] && !$res_new['ob']) {
            echo "  旧口径 OB、新口径可发布 => 本改动【修复】了一个 OB(工作日剔除媒体后占位下降)。\n";
        } else {
            echo "  新旧口径仿真都未复现 OB。\n";
            echo "  => 真实 OB 可能来自本仿真未覆盖的分支：tag 冲突(campaign.exclusive.intersection)、\n";
            echo "     extended campaigns、type2 的 play_totalperhour 写库差异，或发布后数据已变动。\n";
            echo "     请对照线上实际弹出的 OB 文案(含日期/campaign/player)再定位。\n";
        }
        echo "$HR\n\n";
    }

    /** 逐日扫描新旧口径差异，返回 delta 列表 */
    private function scan_deltas($playlist, $players, $start_date)
    {
        $deltas = array();
        $begin  = new DateTime($start_date);
        $end    = (new DateTime($playlist->end_date))->modify('+1 day');
        $range  = new DatePeriod($begin, new DateInterval('P1D'), $end);

        foreach ($players as $player) {
            $ptimer = $player->timers;
            $cams   = array();
            if (!empty($player->campaigns)) {
                foreach ($player->campaigns as $c) {
                    if (($c->priority == 1 || $c->priority == 2 || $c->priority == 5 || $c->priority == 7)
                        && $c->id != $playlist->id && $c->company_id == $playlist->company_id && $c->media_cnt
                    ) {
                        $cams[$c->id] = $c;
                    }
                }
            }
            $cams[$playlist->id] = $playlist;

            foreach ($range as $checkday) {
                $day   = $checkday->format('Y-m-d');
                $weekd = (int) $checkday->format('w');
                if ($weekd == 0) {
                    $weekd = 7;
                }
                // 跳过 timer 休息日（与发布循环一致）
                if ($ptimer && $ptimer['type'] != 0 && !empty($ptimer['offwds']) && in_array($weekd, $ptimer['offwds'])) {
                    continue;
                }
                foreach ($cams as $cid => $c) {
                    if (!($day >= $c->start_date && $day <= $c->end_date)) {
                        continue;
                    }
                    $new = $this->program->get_campaign_media_info_by_date($c, $day);
                    $old = $this->media_info_by_date_old($c, $day);
                    if ((int) $new['media_cnt'] !== (int) $old['media_cnt'] || (float) $new['total_time'] !== (float) $old['total_time']) {
                        $deltas[] = array(
                            'day' => $day, 'player_id' => $player->id, 'cid' => $cid, 'name' => $c->name,
                            'old_cnt' => $old['media_cnt'], 'old_t' => $old['total_time'],
                            'new_cnt' => $new['media_cnt'], 'new_t' => $new['total_time'],
                        );
                    }
                }
            }
        }
        return $deltas;
    }

    /** 旧口径（改动前）：仅按日期过滤，不含工作日 */
    private function media_info_by_date_old($cam, $today)
    {
        if (isset($cam->media) && $cam->media) {
            $media_cnt  = 0;
            $total_time = 0;
            $today_media = array_filter($cam->media, function ($m) use ($today) {
                return ($m['date_flag'] == 0 || ($m['date_flag'] == 1 && $today >= $m['start_date'] && $today <= $m['end_date']));
            });
            if ($today_media) {
                $media_cnt  = count($today_media);
                $total_time = array_sum(array_column($today_media, 'play_time'));
            }
            return array('media_cnt' => $media_cnt, 'total_time' => $total_time);
        }
        if ($cam->priority == 5) {
            return array('media_cnt' => 1, 'total_time' => 10);
        }
        return array('media_cnt' => 0, 'total_time' => 0);
    }

    /**
     * 只读复刻 do_publish_time_slot 的 player/day 分配循环。
     * $use_weekday=true 用新口径(日期+工作日)，false 用旧口径(仅日期)。
     * 返回首个 OB 点或成功。不写库。
     */
    private function simulate_publish($playlist, $players, $company, $use_weekday)
    {
        $pl = clone $playlist;

        $start_date = ($pl->start_date < date('Y-m-d')) ? date('Y-m-d') : $pl->start_date;
        if ($pl->end_date < date('Y-m-d')) {
            return array('ob' => true, 'reason' => 'expired date', 'where' => '-');
        }
        $begin = new DateTime($start_date);
        $end   = (new DateTime($pl->end_date))->modify('+1 day');
        $range = new DatePeriod($begin, new DateInterval('P1D'), $end);

        $cfg_partners = (bool) $this->config->item('with_partners');
        $cfg_xslot    = (bool) $this->config->item('xslot_on');

        // type2: 只读计算 play_totalperhour（不写库）
        if ($pl->priority != 3 && $pl->priority != 6 && $pl->play_cnt_type == 2) {
            $totalmin = $this->program->get_total_minutes($pl, $players);
            if ($totalmin == 0) {
                return array('ob' => true, 'reason' => 'no.intersection (totalmin=0)', 'where' => 'preamble');
            }
            $pph = ceil($pl->play_total / ($totalmin / 60));
            if ($pph == 0) {
                return array('ob' => true, 'reason' => 'total.too.small', 'where' => 'preamble');
            }
            if (($pph * 10) > 3600) {
                return array('ob' => true, 'reason' => 'tv.too.big', 'where' => 'preamble');
            }
            $pl->play_totalperhour = $pph;
        }

        $original_percentage = ($pl->play_cnt_type == 1) ? $pl->play_weight : null;

        foreach ($players as $player) {
            $quota    = 100;
            $partners = isset($player->partners) ? $player->partners : array();

            if ($cfg_partners && isset($partners[$pl->company_id])) {
                $quota = $partners[$pl->company_id]->quota;
                if ($quota == 0) {
                    continue;
                }
                if ($pl->play_cnt_type == 1 && $quota <= 100) {
                    $pl->play_weight = $original_percentage * ($quota / 100);
                }
            }

            if ($pl->is_grouped && $pl->play_cnt_type == 1 && $pl->total_time > 3600 * ($pl->play_weight / 100)) {
                return array('ob' => true, 'reason' => 'percentage.too.small', 'where' => "player{$player->id} preamble");
            }

            $ptimer = $player->timers;
            $publishedcampaigns = $player->campaigns;
            $company_id = $pl->company_id;
            if ($publishedcampaigns) {
                $publishedcampaigns = array_filter($publishedcampaigns, function ($v) use ($company_id, $pl) {
                    if ($v->priority == 1 || $v->priority == 2 || $v->priority == 5 || $v->priority == 7) {
                        if ($v->id == $pl->id) {
                            return false;
                        }
                        if ($v->company_id == $company_id && $v->media_cnt) {
                            return true;
                        }
                    }
                    return false;
                });
            }
            // 注：tag 冲突检查(campaign.exclusive.intersection)会产生"另一种"报错，非 OB，此处略过。

            foreach ($range as $checkday) {
                $today       = $checkday->format('Y-m-d');
                $todaystimer = null;
                if ($ptimer) {
                    if ($ptimer['type'] != 0) {
                        $weekd = (int) $checkday->format('w');
                        if ($weekd == 0) {
                            $weekd = 7;
                        }
                        if (!empty($ptimer['offwds']) && in_array($weekd, $ptimer['offwds'])) {
                            continue;
                        }
                        $todaystimer = $ptimer['data'][$weekd];
                    } else {
                        $todaystimer = $ptimer['data'][0];
                    }
                }

                $today_campaigns = null;
                if ($publishedcampaigns) {
                    $today_campaigns = array_filter($publishedcampaigns, function ($v) use ($today) {
                        return ($today >= $v->start_date && $today <= $v->end_date);
                    });
                }

                $time_slots = $this->program->get_time_slots_byTimer($todaystimer, $quota);
                if (!$time_slots || empty($time_slots)) {
                    return array('ob' => true, 'reason' => 'no.intersection (no time slots)', 'where' => "{$today}/player{$player->id}");
                }

                // 先分配共存 campaign
                if ($today_campaigns) {
                    foreach ($today_campaigns as $cam) {
                        $c = clone $cam;
                        if ($cfg_xslot) {
                            $c->nxslot = $company->nxslot;
                        }
                        $minfo = $use_weekday
                            ? $this->program->get_campaign_media_info_by_date($c, $today)
                            : $this->media_info_by_date_old($c, $today);
                        if ($minfo['media_cnt']) {
                            $c->media_cnt  = $minfo['media_cnt'];
                            $c->total_time = $minfo['total_time'];
                        } else {
                            continue;
                        }
                        if ($cfg_partners && isset($partners[$c->company_id])) {
                            $q = $partners[$c->company_id]->quota;
                            if ($q == 0) {
                                continue;
                            }
                            if ($c->play_cnt_type == 1 && $q <= 100) {
                                $c->play_weight = $c->play_weight * ($q / 100);
                            }
                        }
                        $ret = $this->program->try_allocate_campaign($time_slots, $c);
                        if ($ret['status'] == false) {
                            return array(
                                'ob' => true, 'reason' => 'co-campaign OB',
                                'where' => "{$today}/player{$player->id}/cid{$c->id}({$c->name})",
                                'detail' => $this->ob_detail($time_slots, $c),
                            );
                        }
                    }
                }

                // 再分配被发布的 playlist 本身
                if (in_array($pl->priority, array(1, 2, 4, 5, 7))) {
                    if ($cfg_xslot) {
                        $pl->nxslot = $company->nxslot;
                    }
                    $pminfo = $use_weekday
                        ? $this->program->get_campaign_media_info_by_date($pl, $today)
                        : $this->media_info_by_date_old($pl, $today);
                    if ($pminfo['media_cnt']) {
                        $pl->media_cnt  = $pminfo['media_cnt'];
                        $pl->total_time = $pminfo['total_time'];
                    } else {
                        continue;
                    }
                    $ret = $this->program->try_allocate_campaign($time_slots, $pl);
                    if ($ret['status'] == false) {
                        return array(
                            'ob' => true, 'reason' => 'playlist OB',
                            'where' => "{$today}/player{$player->id}/cid{$pl->id}({$pl->name})",
                            'detail' => $this->ob_detail($time_slots, $pl),
                        );
                    }
                }
                $time_slots = null;
            }
        }
        return array('ob' => false, 'reason' => 'publish OK (no OB)', 'where' => '-');
    }

    /** dump OB 发生时各 slot 的占用，以及失败 campaign 的"需要 vs 剩余" */
    private function ob_detail($time_slots, $cam)
    {
        $out = "      slot 占用快照 (total/quota/used/free + 已排入 campaign):\n";
        $cam_startH = $cam->time_flag ? 0 : $cam->start_timeH;
        $cam_stopH  = $cam->time_flag ? 24 : $cam->end_timeH;
        foreach ($time_slots as $slot) {
            if (!$cam->time_flag && !($slot->startH >= $cam_startH && $slot->startH < $cam_stopH)) {
                continue; // 只看该 campaign 时间范围内的 slot
            }
            $total = ceil($slot->total_time * ($slot->quota / 100));
            $free  = $total - $slot->used_time;
            printf(
                "        %02d:%02d-%02d:%02d total=%-5d quota=%-6s used=%-7d free=%-7d %s\n",
                $slot->startH, $slot->startM, $slot->stopH, $slot->stopM,
                $slot->total_time, $slot->quota, $slot->used_time, $free,
                $free <= 0 ? '<== 已满' : ''
            );
            if (!empty($slot->campaigns)) {
                foreach ($slot->campaigns as $sc) {
                    printf("            in-slot cid=%-6d prio=%-2d grouped=%-2d count=%-5d used=%-7d %s\n", $sc['compaign_id'], $sc['priority'], $sc['is_grouped'], $sc['count'], $sc['used'], $sc['name']);
                }
            }
        }
        // 失败 campaign 每小时"需要"多少（按 add_campagin 公式，取一个 3600 slot 估算）
        if ($cam->media_cnt > 0) {
            $avg = $cam->total_time / $cam->media_cnt;
            $out .= sprintf(
                "      失败 campaign: cid=%d play_cnt_type=%d is_grouped=%d media_cnt=%d total_time=%s avg=%.2f\n",
                $cam->id, $cam->play_cnt_type, $cam->is_grouped, $cam->media_cnt, $cam->total_time, $avg
            );
        }
        return $out;
    }

    private function fmt_result($r)
    {
        return ($r['ob'] ? '❌ OB/失败' : '✅ 通过') . " | reason: {$r['reason']} | where: {$r['where']}";
    }
}
