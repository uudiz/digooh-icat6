<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * ============================================================================
 *  临时诊断控制器  ——  用完即删 (TEMPORARY DIAGNOSTIC, DELETE AFTER USE)
 * ============================================================================
 *  用途：在真实数据上 dump 某 player 今天每个 time slot 的排期，定位"整点只填半小时"。
 *        覆盖维度：partner/quota 前提、账面 used_time、真实生成秒数、
 *                  campaign 三口径(all / video+status0 / video+status0+当日有效)、
 *                  媒体 week/time/date 限制、fill-in 候选 campaign。
 *
 *  运行（项目根目录，建议在装有真实库的服务器上）：
 *      php cli.php diag timeslots 1556
 *      php cli.php diag timeslots 1556 2026-09-09
 *
 *  PHP 8.x 下老 CI 会刷 deprecation 噪音，可过滤：
 *      php cli.php diag timeslots 1556 2>&1 | grep -vE \
 *        "Severity|Message:|Filename:|Line Number|Backtrace|Function:|File:|Line:|PHP Error|deprecated|dynamic property|^$"
 *
 *  刻意继承 CI_Controller（而非 MY_Controller）以绕过登录跳转（与 Cron 同套路）。
 * ============================================================================
 */
class Diag extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (isset($_SERVER['REMOTE_ADDR']) || php_sapi_name() !== 'cli') {
            die('Permission denied.');
        }
        $this->load->model('device');
        $this->load->model('program');
    }

    public function timeslots($player_id = 1556, $today = null)
    {
        $player_id = (int) $player_id;
        $today     = $today ? $today : date('Y-m-d');
        $area      = (int) $this->config->item('area_video');

        $player = $this->device->get_player($player_id, true); // => fill_player_details
        if (!$player) {
            echo "player {$player_id} not found\n";
            return;
        }

        $pname = isset($player->name) ? $player->name : '';
        echo "\n" . str_repeat('=', 104) . "\n";
        echo "player {$player_id} ({$pname}) | today={$today} (" . date('D', strtotime($today)) . ")\n";
        echo "company_id={$player->company_id}  timer_config_id={$player->timer_config_id}  nxslot=" . (isset($player->nxslot) ? $player->nxslot : '-') . "\n";
        echo str_repeat('=', 104) . "\n";

        // ---- partner / quota + try_fill_slots 前提检查 ----
        $has_partners = !empty($player->partners);
        $has_parent   = $has_partners && isset($player->partners[$player->company_id]);
        if ($has_partners) {
            foreach ($player->partners as $cid => $p) {
                $tag = ($cid == $player->company_id) ? '  <== PARENT(本公司)' : '';
                echo "  partner: company_id={$cid}  partner_id={$p->partner_id}  quota={$p->quota}  shareblock=" . (isset($p->shareblock) ? $p->shareblock : '?') . $tag . "\n";
            }
        } else {
            echo "  partners: 空\n";
        }
        echo "  [前提] try_fill_slots 需要 partners 非空: " . ($has_partners ? 'YES' : 'NO  <== fill-in 根本不会执行!') . "\n";
        echo "  [前提] 母公司 fill-in 需要 partners[本公司] 存在: " . ($has_parent ? 'YES' : 'NO  <== 母公司 fill-in 不会执行!') . "\n";

        // ---- fill-in 候选（不论是否被排进 slot）----
        echo "\n" . str_repeat('-', 104) . "\n";
        echo "分配给该 player 的 fill-in 候选 campaign (priority 3/6):\n";
        $this->db->select('pl.id, pl.name, pl.priority, pl.company_id, pl.play_cnt_type, pl.is_grouped, pl.start_date, pl.end_date, pl.published');
        $this->db->from('campaign_player cpl');
        $this->db->join('cat_playlist pl', 'pl.id = cpl.campaign_id');
        $this->db->where('cpl.player_id', $player_id);
        $this->db->where_in('pl.priority', array(3, 6));
        $this->db->where('pl.published', 1);
        $this->db->where('pl.deleted_at IS NULL', null, false);
        $fillins = $this->db->get()->result();
        if (!$fillins) {
            echo "  (无 priority 3/6 campaign)  <== 没有 fill-in 可用!\n";
        } else {
            foreach ($fillins as $f) {
                $inrange = ($today >= $f->start_date && $today <= $f->end_date);
                $comp_ok = ($f->company_id == $player->company_id);
                printf(
                    "  cid=%-6d prio=%d grouped=%d company=%-4d(%s)  %s~%s [%s]  %s\n",
                    $f->id, $f->priority, $f->is_grouped, $f->company_id,
                    $comp_ok ? '=母公司' : '≠母公司',
                    $f->start_date, $f->end_date, $inrange ? 'in' : 'OUT-of-range',
                    $f->name
                );
                if (!$inrange) {
                    echo "        ^ 不在有效期，fillin_campaigns 过滤会剔除它\n";
                }
                if (!$comp_ok && !$has_parent) {
                    echo "        ^ company 非母公司，且需对应 partner.shareblock=1 才会被 partner 分支填充\n";
                }
            }
        }

        // ---- 排期 ----
        $slots = $this->program->do_get_today_timeslots($player, $today);
        if (!$slots) {
            echo "\n[!] do_get_today_timeslots 返回空（timer 休息日 / 无 campaign / 全被过滤）\n";
            return;
        }

        $line = str_repeat('-', 104);
        $grand_total = 0;
        $grand_real  = 0;
        $slot_cams   = array();

        foreach ($slots as $slot) {
            $acct_used = $slot->used_time;            // 必须在 get_sorted_timeslot_medias 之前读
            $acct_free = $slot->total_time - $acct_used;

            printf(
                "\n%s\nSLOT %02d:%02d-%02d:%02d   total_time=%d   used_time(账面)=%d   free=%d   quota=%d\n%s\n",
                $line, $slot->startH, $slot->startM, $slot->stopH, $slot->stopM,
                $slot->total_time, $acct_used, $acct_free, $slot->quota, $line
            );

            echo "  [账面] slot 内 campaign:\n";
            if (empty($slot->campaigns)) {
                echo "    (none)\n";
            } else {
                foreach ($slot->campaigns as $c) {
                    printf(
                        "    cid=%-6d prio=%-2d grouped=%-2d count=%-5d used=%-7d %s\n",
                        $c['compaign_id'], $c['priority'], $c['is_grouped'], $c['count'], $c['used'], $c['name']
                    );
                    $slot_cams[$c['compaign_id']] = $c;
                }
            }

            $medias = $this->program->get_sorted_timeslot_medias($slot, $today);
            $real_seconds = 0;
            foreach ($medias as $m) {
                $real_seconds += isset($m->play_time) ? (int) $m->play_time : 0;
            }
            $ratio = $slot->total_time > 0 ? ($real_seconds / $slot->total_time * 100) : 0;
            printf(
                "  [生成] 真实媒体条数=%d   真实秒数=%d (%.1f 分钟)   填充率=%.1f%%   %s\n",
                count($medias), $real_seconds, $real_seconds / 60, $ratio,
                $ratio < 90 ? '  <== 未填满!' : ''
            );

            $grand_total += $slot->total_time;
            $grand_real  += $real_seconds;
        }

        // ---- 三口径对比 + 媒体限制 ----
        echo "\n" . str_repeat('=', 104) . "\n";
        echo "campaign 媒体口径对比 (针对 slot 内出现的 campaign):\n";
        echo "  all=旧填充口径(不分area/status/date) | video=area{$area}+status0 | video_indate=再叠加当日有效\n";
        echo str_repeat('=', 104) . "\n";
        foreach ($slot_cams as $cid => $c) {
            list($all, $vid, $vidd) = $this->media_measure3($cid, $today, $area);
            printf(
                "  cid=%-6d prio=%-2d grouped=%-2d | all:cnt=%-3d t=%-7s | video:cnt=%-3d t=%-7s | video_indate:cnt=%-3d t=%-7s | %s%s%s\n",
                $cid, $c['priority'], $c['is_grouped'],
                $all->cnt, $all->t, $vid->cnt, $vid->t, $vidd->cnt, $vidd->t,
                $c['name'],
                ((int) $all->cnt !== (int) $vid->cnt) ? '  [area/status膨胀]' : '',
                ((int) $vid->cnt !== (int) $vidd->cnt) ? '  [有过期媒体->日期gap]' : ''
            );

            // 媒体 week/time/date 限制（生成阶段会据此过滤）
            $rows = $this->media_restrictions($cid, $area);
            foreach ($rows as $r) {
                $flags = array();
                if ($r->week_flag && $r->weekday != 127) {
                    $flags[] = "week:weekday={$r->weekday}";
                }
                if ($r->time_flag) {
                    $flags[] = "time:{$r->start_time}-{$r->end_time}";
                }
                if ($r->date_flag) {
                    $flags[] = "date:{$r->start_date}~{$r->end_date}";
                }
                printf(
                    "        media id=%-6d %-28s play_time=%-6s %s\n",
                    $r->id, substr($r->name, 0, 28), $r->play_time,
                    $flags ? ('限制[' . implode(' ', $flags) . ']') : '无限制'
                );
            }
        }

        $grand_ratio = $grand_total > 0 ? ($grand_real / $grand_total * 100) : 0;
        printf(
            "\n==== 汇总: %d 个 slot, 账面总时长=%d 秒, 真实填充=%d 秒 (%.1f 分钟), 整体填充率=%.1f%% ====\n\n",
            count($slots), $grand_total, $grand_real, $grand_real / 60, $grand_ratio
        );
    }

    /** 三口径：all / video+status0 / video+status0+当日有效 */
    private function media_measure3($cam_id, $today, $area)
    {
        $this->db->select('COUNT(*) AS cnt, COALESCE(SUM(m.play_time),0) AS t');
        $this->db->from('cat_media m');
        $this->db->join('cat_playlist_area_media pm', 'pm.media_id = m.id');
        $this->db->where('pm.playlist_id', $cam_id);
        $all = $this->db->get()->row();

        $this->db->select('COUNT(*) AS cnt, COALESCE(SUM(m.play_time),0) AS t');
        $this->db->from('cat_media m');
        $this->db->join('cat_playlist_area_media pm', 'pm.media_id = m.id');
        $this->db->where('pm.playlist_id', $cam_id);
        $this->db->where('pm.area_id', $area);
        $this->db->where('pm.status', 0);
        $vid = $this->db->get()->row();

        $this->db->select('COUNT(*) AS cnt, COALESCE(SUM(m.play_time),0) AS t');
        $this->db->from('cat_media m');
        $this->db->join('cat_playlist_area_media pm', 'pm.media_id = m.id');
        $this->db->where('pm.playlist_id', $cam_id);
        $this->db->where('pm.area_id', $area);
        $this->db->where('pm.status', 0);
        $this->db->group_start();
        $this->db->where('m.date_flag', 0);
        $this->db->or_group_start();
        $this->db->where('m.date_flag', 1);
        $this->db->where('m.start_date <=', $today);
        $this->db->where('m.end_date >=', $today);
        $this->db->group_end();
        $this->db->group_end();
        $vidd = $this->db->get()->row();

        return array($all, $vid, $vidd);
    }

    /** 视频区有效媒体的 week/time/date 限制 */
    private function media_restrictions($cam_id, $area)
    {
        $this->db->select('m.id, m.name, m.play_time, m.week_flag, m.weekday, m.time_flag, m.start_time, m.end_time, m.date_flag, m.start_date, m.end_date');
        $this->db->from('cat_playlist_area_media pm');
        $this->db->join('cat_media m', 'm.id = pm.media_id');
        $this->db->where('pm.playlist_id', $cam_id);
        $this->db->where('pm.area_id', $area);
        $this->db->where('pm.status', 0);
        $this->db->order_by('pm.position', 'asc');
        return $this->db->get()->result();
    }
}
