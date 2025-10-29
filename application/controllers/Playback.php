<?php

use League\Csv\Writer;
use avadim\FastExcelWriter\Excel;

class Playback extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->lang->load('my_date');
        $this->lang->load('playback');
    }

    public function index()
    {
        //$this->addJs("/assets/js/playback.js", false);
        $data = $this->get_data();


        if ($this->get_auth() == 1 && $this->config->item("new_campaign_user")) {
            $data['body_file'] = 'bootstrap/playbacks/index';
        } else if ($this->get_auth() <= 2) {
            $data['body_file'] = 'bootstrap/401';
        } else {
            $data['body_file'] = 'bootstrap/playbacks/index';
        }


        $this->load->view('bootstrap/layout/basiclayout', $data);
    }

    public function getTableData()
    {

        $data = $this->get_data();
        $offset = $this->input->post('offset');
        $limit = $this->input->post('limit');
        $order_item = $this->input->post('sort');
        $order = $this->input->post('order');
        $start_date = $this->input->post('start_date');
        $end_date  = $this->input->post('end_date');

        $campaign = $this->input->post('campaign');
        $player = $this->input->post('player');
        $media = $this->input->post('media');


        $filter_array = array('start_date' => $start_date, 'end_date' => $end_date);
        if ($campaign) {
            $filter_array['campaign'] = $campaign;
        }
        if ($media) {
            $filter_array['media'] = $media;
        }
        if ($player) {
            $filter_array['player'] = $player;
        }

        $this->load->model('feedback');
        $rest = $this->feedback->query($this->get_cid(), $filter_array, $offset, $limit, $order_item, $order);
        $data['total'] = $rest['total'];
        $data['rows']  = $rest['data'];

        echo json_encode($data);
    }
    public function getNoPlaybackTableData()
    {

        $data = $this->get_data();
        $offset = $this->input->post('offset');
        $limit = $this->input->post('limit');
        $order_item = $this->input->post('sort');
        $order = $this->input->post('order');
        $start_date = $this->input->post('start_date');
        $end_date  = $this->input->post('end_date');

        $campaign = $this->input->post('campaign');
        $player = $this->input->post('player');
        $media = $this->input->post('media');


        $filter_array = array('start_date' => $start_date, 'end_date' => $end_date);
        if ($campaign) {
            $filter_array['campaign'] = $campaign;
        }
        if ($media) {
            $filter_array['media'] = $media;
        }
        if ($player) {
            $filter_array['player'] = $player;
        }

        $this->load->model('feedback');
        $rest = $this->feedback->query_not_played_players($this->get_cid(), $filter_array, $offset, $limit, $order_item, $order);
        $data['total'] = $rest['total'];
        $data['rows']  = $rest['data'];

        echo json_encode($data);
    }



    /**
     * 查询记录信息，默认第一页数据
     *
     * @param object $curpage [optional]
     * @return
     */
    public function query($curpage = 1, $order_item = 'post_date', $order = 'asc')
    {
        //$player_id = $this->input->get('player_id');

        $player_id = 0;
        $this->load->model('feedback');
        $this->load->model('device');

        $start_date = $this->input->get('start_date');
        $end_date  = $this->input->get('end_date');

        $campaign = $this->input->get('campaign');
        $player = $this->input->get('player');
        $media = $this->input->get('media');


        $filter_array = array('start_date' => $start_date, 'end_date' => $end_date);
        if ($campaign) {
            $filter_array['campaign'] = $campaign;
        }
        if ($media) {
            $filter_array['media'] = $media;
        }
        if ($player) {
            $filter_array['player'] = $player;
        }

        $limit = $this->config->item('page_default_size');
        $offset = ($curpage - 1) * $limit;

        $result = $this->feedback->query($this->get_cid(), $filter_array, $offset, $limit, $order_item, $order);

        $data = $this->get_data();

        $data['total'] = $result['total'];
        $data['data'] = $result['data'];
        $data['offset'] = $offset;
        $data['limit'] = $limit;
        $data['curpage'] = $curpage;
        $data['order_item'] = $order_item;
        $data['order'] = $order;
        $data['start_date'] = $start_date;
        $data['end_date'] = $end_date;

        $this->load->view('playback/table_list', $data);
    }


    public function excel()
    {
        set_time_limit(0);
        $timer = microtime(true);
        $this->load->model('feedback');
        $this->load->helper('serial');
        $start_date = $this->input->get('start_date');
        $end_date  = $this->input->get('end_date');

        $campaign = $this->input->get('campaign');
        $player = $this->input->get('player');
        $media = $this->input->get('media');

        $auth = $this->get_auth();
        $pid = $this->get_parent_company_id();
        $show_fulfilments = false;
        if ($auth == 5 && !$pid) {
            $show_fulfilments = true;
        }

        $filter_array = array('start_date' => $start_date, 'end_date' => $end_date);
        if ($campaign) {
            $filter_array['campaign'] = $campaign;
        }
        if ($media) {
            $filter_array['media'] = $media;
        }
        if ($player) {
            $filter_array['player'] = $player;
        }


        $filter_array = array('start_date' => $start_date, 'end_date' => $end_date);
        if ($campaign) {
            $filter_array['campaign'] = $campaign;
        }
        if ($media) {
            $filter_array['media'] = $media;
        }
        if ($player) {
            $filter_array['player'] = $player;
        }

        $cid = $this->get_parent_company_id() ?: $this->get_cid();
        $result = $this->feedback->query($cid, $filter_array)['data'];

        /*
        $excel = Excel::create(['Sheet1']);
        $sheet = $excel->getSheet();

        if (!$this->config->item('with_template')) {
            if ($show_fulfilments) {
                $head = array(
                    $this->lang->line('date.time'), $this->lang->line('player'), $this->lang->line('campaign'), $this->lang->line('media'), $this->lang->line('planed_times'),
                    $this->lang->line('times'), $this->lang->line('duration'), $this->lang->line('fulfillment_planed'), $this->lang->line('fulfillment_booked')
                );
            } else {
                $head = array(
                    $this->lang->line('date.time'), $this->lang->line('player'), $this->lang->line('campaign'), $this->lang->line('media'), $this->lang->line('planed_times'),
                    $this->lang->line('times'), $this->lang->line('duration')
                );
            }
        } else {
            $head = array(
                $this->lang->line('date.time'), $this->lang->line('player'), $this->lang->line('campaign'), $this->lang->line('media'),
                $this->lang->line('times'), $this->lang->line('duration'),
            );
        }
        $sheet->writeHeader($head);
        foreach ($result as $row) {
            if (!$this->config->item('with_template')) {
                if ($show_fulfilments) {
                    $record = array(
                        $row->post_date, $row->player_name, $row->campaign_name, $row->media_name, isset($row->planed_times) ? $row->planed_times : "",
                        isset($row->times) ? $row->times : "", isset($row->duration) ? $row->duration : "", isset($row->fulfillment_planed) ? $row->fulfillment_planed : "", isset($row->fulfillment_booked) ? $row->fulfillment_booked : ""
                    );
                } else {
                    $record = array(
                        $row->post_date, $row->player_name, $row->campaign_name, $row->media_name, isset($row->planed_times) ? $row->planed_times : "",
                        isset($row->times) ? $row->times : "", isset($row->duration) ? $row->duration : ""
                    );
                }
            } else {
                $record = array(
                    $row->post_date, $row->player_name, $row->campaign_name, $row->media_name, $row->times, $row->duration
                );
            }
            $sheet->writeRow($record);
        }

        $filename = "Report(" . $start_date . "_" . substr($end_date, 5) . ").xlsx";
        $excel->save('/tmp/' . $filename);

        $this->load->helper('download');
        force_download('/tmp/' . $filename, NULL);
        */


        $csv = Writer::createFromFileObject(new SplTempFileObject());

        $csv->setOutputBOM(Writer::BOM_UTF8);
        if (!$this->config->item('with_template')) {
            if ($show_fulfilments) {
                $head = array(
                    $this->lang->line('date.time'), $this->lang->line('player'), $this->lang->line('campaign'), $this->lang->line('media'), $this->lang->line('planed_times'),
                    $this->lang->line('times'), $this->lang->line('duration'), $this->lang->line('fulfillment_planed'), $this->lang->line('fulfillment_booked')
                );
            } else {
                $head = array(
                    $this->lang->line('date.time'), $this->lang->line('player'), $this->lang->line('campaign'), $this->lang->line('media'), $this->lang->line('planed_times'),
                    $this->lang->line('times'), $this->lang->line('duration')
                );
            }
        } else {
            $head = array(
                $this->lang->line('date.time'), $this->lang->line('player'), $this->lang->line('campaign'), $this->lang->line('media'),
                $this->lang->line('times'), $this->lang->line('duration'),
            );
        }
        $csv->insertOne($head);

        foreach ($result as $row) {
            if (!$this->config->item('with_template')) {
                if ($show_fulfilments) {
                    $record = array(
                        $row->post_date, $row->player_name, $row->campaign_name, $row->media_name, isset($row->planed_times) ? $row->planed_times : "",
                        isset($row->times) ? $row->times : "", isset($row->duration) ? $row->duration : "", isset($row->fulfillment_planed) ? $row->fulfillment_planed : "", isset($row->fulfillment_booked) ? $row->fulfillment_booked : ""
                    );
                } else {
                    $record = array(
                        $row->post_date, $row->player_name, $row->campaign_name, $row->media_name, isset($row->planed_times) ? $row->planed_times : "",
                        isset($row->times) ? $row->times : "", isset($row->duration) ? $row->duration : ""
                    );
                }
            } else {
                $record = array(
                    $row->post_date, $row->player_name, $row->campaign_name, $row->media_name, $row->times, $row->duration
                );
            }
            $csv->insertOne($record);
        }

        $filename = "Report(" . $start_date . '_' . substr($end_date, 5) . ").csv";


        if ($this->config->item('with_template')) {
            $csv->output($filename);
            return;
        }
        $result = $this->feedback->query_not_played_players($cid, $filter_array);
        if ($result['total'] > 0) {
            $players = $result['data'];
            foreach ($players as $row) {
                $record = array(
                    $row->post_date, $row->player_name, $row->campaign_name, $row->media_name, isset($row->planed_times) ? $row->planed_times : "",
                    isset($row->times) ? $row->times : "", isset($row->duration) ? $row->duration : "", isset($row->fulfillment_planed) ? $row->fulfillment_planed : "", isset($row->fulfillment_booked) ? $row->fulfillment_booked : ""
                );
                $csv->insertOne($record);
            }
        }
        $csv->output($filename);

        /*
        header('Content-Type: application/vnd.ms-excel');
        $headstr = sprintf("Content-Disposition: attachment;filename=Report(%s_%s).csv", $start_date, substr($end_date, 5));
        header($headstr);
        header('Cache-Control: max-age=0');

        $fp = fopen('php://output', 'a');

        //BOM for UTF-8
        fwrite($fp, "\xEF\xBB\xBF");


        if (!$this->config->item('with_template')) {
            $head = array(
                $this->lang->line('date.time'), $this->lang->line('player'), $this->lang->line('campaign'), $this->lang->line('media'), $this->lang->line('planed_times'),
                $this->lang->line('times'), $this->lang->line('duration'), $this->lang->line('fulfillment_planed'), $this->lang->line('fulfillment_booked')
            );
        } else {
            $head = array(
                $this->lang->line('date.time'), $this->lang->line('player'), $this->lang->line('campaign'), $this->lang->line('media'),
                $this->lang->line('times'), $this->lang->line('duration'),
            );
        }


        // 将数据通过fputcsv写到文件句柄
        fputcsv($fp, $head);

        // 计数器
        $cnt = 0;
        $limit = 1024;


        $i = 2;


        foreach ($result as $row) {
            $cnt++;

            //每隔$limit行，刷新一下输出buffer，不要太大，也不要太小 ,大数据量时处理
            if ($limit == $cnt) { //刷新一下输出buffer，防止由于数据过多造成问题
                ob_flush();
                flush();  //刷新buffer
                $cnt = 0;
            }

            $rows[$i] = $row->post_date;
            if (!$this->config->item('with_template')) {
                $rows[$i + 1] = $row->player_name;
                $rows[$i + 2] = $row->campaign_name;

                $rows[$i + 3] = $row->media_name;
                $rows[$i + 4] = isset($row->planed_times) ? $row->planed_times : "";
                $rows[$i + 5] = isset($row->times) ? $row->times : "";
                $rows[$i + 6] = isset($row->duration) ? $row->duration : "";

                $rows[$i + 7] = isset($row->fulfillment_planed) ? $row->fulfillment_planed : "";
                $rows[$i + 8] = isset($row->fulfillment_booked) ? $row->fulfillment_booked : "";
            } else {
                $rows[$i + 1] = $row->player_name;
                $rows[$i + 2] = $row->campaign_name;

                $rows[$i + 3] = $row->media_name;
                $rows[$i + 4] = $row->times;
                $rows[$i + 5] = $row->duration;
            }
            fputcsv($fp, $rows);
        }

      
        if ($this->config->item('with_template')) {
            return;
        }
        $result = $this->feedback->query_not_played_players($cid, $filter_array);

        $cnt = 0;
        if ($result['total'] > 0) {
            $players = $result['data'];
            foreach ($players as $row) {
                $cnt++;

                if ($limit == $cnt) {
                    ob_flush();
                    flush();  //刷新buffer
                    $cnt = 0;
                }

                if (!$this->config->item('with_template')) {
                    $rows[$i] = $row->post_date;
                    $rows[$i + 1] = $row->player_name;
                    $rows[$i + 2] = $row->campaign_name;

                    $rows[$i + 3] = $row->media_name;
                    $rows[$i + 4] = isset($row->planed_times) ? $row->planed_times : "";
                    $rows[$i + 5] = isset($row->times) ? $row->times : "";
                    $rows[$i + 6] = isset($row->duration) ? $row->duration : "";

                    $rows[$i + 7] = isset($row->fulfillment_planed) ? $row->fulfillment_planed : "";
                    $rows[$i + 8] = isset($row->fulfillment_booked) ? $row->fulfillment_booked : "";
                } else {
                    $rows[$i] = $row->post_date;
                    $rows[$i + 1] = $row->player_name;
                    $rows[$i + 2] = $row->campaign_name;

                    $rows[$i + 3] = $row->media_name;
                    $rows[$i + 4] = $row->times;
                    $rows[$i + 5] = $row->duration;
                }
                fputcsv($fp, $rows);
            }
        }
         */
    }

    public function get_select_data()
    {
        $page = $this->input->post("page");
        $type = $this->input->post("type");
        $querystr = $this->input->post("q");

        $limit = 20;
        $offset = ($page) * $limit;

        $filter_array = array();
        $filter_array['name'] = $querystr;
        $cid = $this->get_cid();


        if ($type == 'media') {
            $this->load->model('material');

            $items =  $this->material->get_media_list($cid, $offset, $limit, 'name', 'asc', $filter_array);
        } elseif ($type == "campaign") {
            $this->load->model('program');
            $filter_array['withExpired'] = 1;
            $items = $this->program->get_playlist_list($cid, $filter_array, false, $offset, $limit);
        } elseif ($type == "player") {
            $this->load->model('device');
            $pid = $this->get_parent_company_id();

            $cris = $this->get_criteria($cid, $pid);
            $data['criteria'] = $cris['criteria'];
            if (isset($cris['filter_array'])) {
                $filter_array = array_merge($filter_array, $cris['filter_array']);
            }
            $items = $this->device->get_player_list($cid, $filter_array, false, $offset, $limit);
        }

        $result = array();
        foreach ($items['data'] as $item) {
            $result[] = array(
                'id' => $item->id,
                'text' => $item->name
            );
        }

        $data['items'] = $result;
        $data['page'] = $page;
        $data['total_count'] = $items['total'];

        echo json_encode($data);
    }
    public function summary()
    {
        set_time_limit(0);
        $timer = microtime(true);
        $this->load->model('feedback');
        $this->load->helper('serial');
        $start_date = $this->input->get('start_date');
        $end_date  = $this->input->get('end_date');

        $campaign = $this->input->get('campaign');
        $player = $this->input->get('player');
        $media = $this->input->get('media');

        $auth = $this->get_auth();
        $pid = $this->get_parent_company_id();
        $show_fulfilments = false;
        if ($auth == 5 && !$pid) {
            $show_fulfilments = true;
        }

        $player_name = null;
        $filter_array = array('start_date' => $start_date, 'end_date' => $end_date);
        if ($campaign) {
            $filter_array['campaign'] = $campaign;
        }
        if ($media) {
            $filter_array['media'] = $media;
        }
        if ($player) {
            $filter_array['player'] = $player;
            $this->load->model('device');
            $player_data = $this->device->get_player($player);
            if ($player_data) {
                $player_name = $player_data->name;
            }
        }


        $cid = $this->get_parent_company_id() ?: $this->get_cid();
        $result = $this->feedback->query_summary($cid, $filter_array);


        $excel = Excel::create();
        $sheet = $excel->getSheet();

        $head = array(
            $this->lang->line('labels'), $this->lang->line('player_number'), $this->lang->line('campaign_number'), $this->lang->line('media_number'), $this->lang->line('total_planed'),
            $this->lang->line('total_count'), $this->lang->line('total_duration'), $this->lang->line('avg_ful_planed'), $this->lang->line('avg_ful_booked')
        );

        $sheet
            ->setColWidths([40, 20, 20, 20, 20, 20, 20]);

        $sheet->writeHeader($head);


        $sum_record = array('label' =>  $this->lang->line('total'), 'total_player' => 0, 'total_cam' => 0, 'total_media' => 0, 'total_planed' => 0, 'total_times' => 0, 'total_duration' => 0);
        $total_ful_booked = 0;
        $total_ful_planed = 0;
        $ful_booked_cnt = 0;
        $ful_planed_cnt = 0;
        foreach ($result as $row) {
            $sum_record['total_player'] += $row->player_cnt;
            $sum_record['total_cam'] += $row->cam_cnt;
            $sum_record['total_media'] += $row->media_cnt;
            $sum_record['total_planed'] += $row->total_planed;
            $sum_record['total_times'] += $row->total_times;
            $sum_record['total_duration'] += $row->total_duration;
            if ($row->avg_ful_booked) {
                $total_ful_booked += $row->avg_ful_booked;
                $ful_booked_cnt++;
            }
            if ($row->avg_ful_planed) {
                $total_ful_planed += $row->avg_ful_planed;
                $ful_planed_cnt++;
            }
            $label = $row->date;
            if ($player_name) {
                $label .= " - " . $player_name;
            }
            if ($campaign) {
                $label .= " - " . $campaign;
            }
            if ($media) {
                $label .= " - " . $media;
            }

            $record = array(
                $label, $row->player_cnt, $row->cam_cnt, $row->media_cnt, $row->total_planed, $row->total_times, $row->total_duration, $row->avg_ful_planed, $row->avg_ful_booked
            );
            $sheet->writeRow($record);
        }
        $sum_record['avg_ful_booked'] = $ful_booked_cnt > 0 ? round($total_ful_booked / $ful_booked_cnt, 2) : null;
        $sum_record['avg_ful_planed'] = $ful_planed_cnt > 0 ? round($total_ful_planed / $ful_planed_cnt, 2) : null;

        $sheet->writeRow($sum_record);


        $filename = "Report(" . $start_date . "_" . substr($end_date, 5) . ").xlsx";

        $excel->save('/tmp/' . $filename);

        $this->load->helper('download');
        force_download('/tmp/' . $filename, NULL);
        unlink('/tmp/' . $filename);
    }
}
