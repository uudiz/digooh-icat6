<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Usage extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->lang->load('my_date');
        $this->lang->load('criteria');
        $this->lang->load('player');
        $this->lang->load('media');
    }

    public function index()
    {
        $this->addJs("/assets/js/criteria.js", false);
        $data = $this->get_data();
        if ($this->get_auth() <= 2) {
            $data['body_file'] = 'bootstrap/401';
        } else {
            $data['body_file'] = 'bootstrap/usage/index';
        }
        $pid = $this->get_parent_company_id();


        $filter_array = array();
        $cid = $this->get_cid();
        $cris = $this->get_criteria($cid, $pid);
        $data['criteria'] = $cris['criteria'];
        /*
        if (isset($cris['filter_array'])) {
            $filter_array = array_merge($filter_array, $cris['filter_array']);
        }
        $players = $this->device->get_player_list($pid ? $pid : $cid, $filter_array);
        */

        $this->load->view('bootstrap/layout/basiclayout', $data);
    }

    public function getTableData()
    {


        $this->load->model('device');
        $this->load->model('program');
        $data = $this->get_data();
        $offset = $this->input->post('offset');
        $limit = $this->input->post('limit');
        $order_item = $this->input->post('sort');
        $order = $this->input->post('order');

        $start_date = date("Y-m-d");
        $end_date = null;
        if ($this->input->post('with_range')) {
            $start_date = $this->input->post('start_date') ?: date("Y-m-d", time());
            $end_date = $this->input->post('end_date') ?: null;
        }

        $filter_array = array();
        $name = $this->input->post('search');
        if ($name) {
            $filter_array['filter_type'] = 'fourfields';

            if (preg_match("/(^[0-9]{3}\-[0-9]{3}\-[0-9]{4}$)/", $name)) {
                $name = trim(str_replace('-', '', $name));
            }
            $filter_array['filter'] = $name;
        }

        $filterCri = $this->input->post('criteria');

        if ($filterCri) {
            $filter_array['criteria'] = $filterCri;
        }


        $filter_array['setupdate'] = $start_date;

        $cid = $this->get_cid();
        $pid = $this->get_parent_company_id();

        $cris = $this->get_criteria($cid, $pid);
        if (isset($cris['filter_array'])) {
            $filter_array = array_merge($filter_array, $cris['filter_array']);
        }

        $affected_players = $this->device->get_player_list($pid ? $pid : $cid, $filter_array, true, $offset, $limit, $order_item, $order);

        $players = array_filter($affected_players['data'], function ($player) {
            if ($player->setupdate == '0000-00-00') {
                return false;
            }
            return true;
        });



        $capacity = $this->program->get_players_capcity($players, $start_date, $end_date, false, false, $cid);

        $objects = [];
        foreach ($capacity as $item) {
            $object = new stdClass();
            foreach ($item as $key => $value) {
                $object->$key = $value;
            }
            $objects[] =  $object;
        }

        $data['rows']  = $objects;
        $data['total'] = $affected_players['total'];

        echo json_encode($data);
    }


    /**
     * 查询记录信息，默认第一页数据
     *
     * @param object $curpage [optional]
     * @return
     */
    public function query($curpage = 1, $order_item = 'name', $order = 'asc')
    {
        $this->load->model('program');
        $this->load->helper('serial');

        $hasdate = $this->input->get('hasdate');
        if ($hasdate) {
            $start_date = $this->input->get('start_date');
        } else {
            $start_date = false;
        }


        $limit = $this->config->item('page_default_size');

        $offset = ($curpage - 1) * $limit;

        $cid = $this->get_cid();

        $players = $this->get_filterd_players($offset, $limit, $order_item, $order);


        $data = $this->get_data();

        if ($players) {
            $data['capacity'] = $this->program->get_players_capcity($players, $start_date, false, false, false, $cid);
            $data['total'] = count($players);
            $data['data']  = $players;
        } else {
            $data['total'] = 0;
        }


        $data['offset'] = $offset;
        $data['limit'] = $limit;
        $data['curpage'] = $curpage;
        $data['order_item'] = $order_item;
        $data['order'] = $order;


        $this->load->view('usage/list', $data);
    }



    public function excel()
    {
        set_time_limit(0);
        session_write_close();
        $this->load->model('membership');
        $this->load->model('device');
        $this->load->model('program');

        $hasdate = $this->input->get('with_date');


        if ($hasdate) {
            $start_date = $this->input->get('start_date');
            $end_date  = $this->input->get('end_date');
            $startStamp = strtotime($start_date);
            $endStamp = strtotime($end_date);
        } else {
            $start_date = date("Y-m-d", time());
        }

        $filter_array = array();
        $name = $this->input->get('search');
        if ($name) {
            $filter_array['filter_type'] = 'fourfields';

            if (preg_match("/(^[0-9]{3}\-[0-9]{3}\-[0-9]{4}$)/", $name)) {
                $name = trim(str_replace('-', '', $name));
            }
            $filter_array['filter'] = $name;
        }

        $filterCri = $this->input->get('criteria');

        if ($filterCri) {
            $filter_array['criteria'] = $filterCri;
        }


        $filter_array['setupdate'] = $start_date;

        $cid = $this->get_cid();
        $pid = $this->get_parent_company_id();

        $cris = $this->get_criteria($cid, $pid);
        if (isset($cris['filter_array'])) {
            $filter_array = array_merge($filter_array, $cris['filter_array']);
        }

        $affected_players = $this->device->get_player_list($pid ? $pid : $cid, $filter_array, true, 0, -1, 'name', 'asc');
        $players = array_filter($affected_players['data'], function ($player) {
            if ($player->setupdate == '0000-00-00') {
                return false;
            }
            return true;
        });

        if (!$players) {
            return false;
        }

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $exline = 2;

        if ($hasdate) {
            $worksheet->setCellValueByColumnAndRow(1, 1, $this->lang->line('custom_sn1'));
            $worksheet->setCellValueByColumnAndRow(2, 1, $this->lang->line('slotcnt'));
            $worksheet->setCellValueByColumnAndRow(3, 1, $this->lang->line('slotnum'));


            $worksheet->getDefaultColumnDimension()->setWidth(10);

            $worksheet->getColumnDimension('A')->setWidth(30);

            $worksheet->getStyle('A')->getNumberFormat()->setFormatCode('#');



            if ($players) {
                foreach ($players as $player) {
                    if (!$player->setupdate || $player->setupdate == '0000-00-00') {
                        continue;
                    }

                    for ($i = 0; $i < 6; $i++) {
                        $worksheet->setCellValue('A' . ($exline + $i), $player->custom_sn1)
                            ->setCellValue('B' . ($exline + $i), 6)
                            ->setCellValue('C' . ($exline + $i), $i + 1);
                    }

                    $excol = 4;


                    for ($checkday = $startStamp; $checkday <= $endStamp;) {
                        $worksheet->setCellValueByColumnAndRow($excol, 1, date("d.m.Y", $checkday));


                        $timeslots = $this->program->do_get_today_timeslots($player, date("Y-m-d", $checkday), false, true, $cid);


                        $total_secs = 0;
                        $used_secs = 0;
                        $slotNum = 0;
                        if ($timeslots && is_array($timeslots)) {
                            foreach ($timeslots as $slot) {
                                $total_secs += $slot->total_time;
                                $used_secs += $slot->used_time;
                            }
                        }
                        if ($total_secs && $used_secs) {
                            $avgUsed =  3600 * $used_secs / $total_secs;
                            $slotNum =  ceil($avgUsed / 600);
                        }

                        for ($i = 0; $i < 6; $i++) {
                            $worksheet->setCellValueByColumnAndRow($excol, $exline + $i, $i < $slotNum ? 1 : 0);
                        }



                        //  $worksheet->setCellValueByColumnAndRow($excol, $exline, $slotNum);

                        $checkday = strtotime("+1 days", $checkday);
                        $excol++;
                    }

                    $exline += 6;
                }
            }
        } else {
            $capacitys = $this->program->get_players_capcity($players);

            $worksheet->setCellValueByColumnAndRow(1, 1,  $this->lang->line('custom_sn1'));
            $worksheet->setCellValueByColumnAndRow(2, 1, $this->lang->line('custom_sn2'));



            $worksheet->setCellValueByColumnAndRow(3, 1, $this->lang->line('player.name'));
            $worksheet->setCellValueByColumnAndRow(4, 1, $this->lang->line('sn'));

            $worksheet->setCellValueByColumnAndRow(5, 1, $this->lang->line('criteria'));

            $worksheet->setCellValueByColumnAndRow(6, 1, $this->lang->line('next.7.day'));
            $worksheet->setCellValueByColumnAndRow(7, 1, $this->lang->line('next.month'));
            $worksheet->setCellValueByColumnAndRow(8, 1, $this->lang->line('next.6.month'));
            $worksheet->setCellValueByColumnAndRow(9, 1, 'Least free seconds');


            $worksheet->getDefaultColumnDimension()->setWidth(15);
            $worksheet->getColumnDimension('A')->setWidth(30);
            $worksheet->getStyle('A')->getNumberFormat()->setFormatCode('#');
            $worksheet->getColumnDimension('B')->setWidth(30);
            $worksheet->getStyle('B')->getNumberFormat()->setFormatCode('#');

            $worksheet->getColumnDimension('C')->setWidth(40);
            $worksheet->getStyle('C')->getAlignment()->setWrapText(true);

            $worksheet->getColumnDimension('E')->setWidth(40);
            $worksheet->getStyle('E')->getAlignment()->setWrapText(true);

            foreach ($players as $player) {
                if (!$player->setupdate || $player->setupdate == '0000-00-00') {
                    continue;
                }
                $worksheet->setCellValue('A' . $exline, $player->custom_sn1)
                    ->setCellValue('B' . $exline, $player->custom_sn2)
                    ->setCellValue('C' . $exline, $player->name)
                    ->setCellValue('D' . $exline, $player->sn)
                    ->setCellValue('E' . $exline, $player->criteria_name);


                if ((isset($capacitys[$player->id]) && $capacitys[$player->id])) {
                    $worksheet->setCellValue('F' . $exline, $capacitys[$player->id]['day7_capcity'] ? $capacitys[$player->id]['day7_capcity'] . "%" : "0")
                        ->setCellValue('G' . $exline, $capacitys[$player->id]['nextmon_capacity'] ? $capacitys[$player->id]['nextmon_capacity'] . "%" : "0")
                        ->setCellValue('H' . $exline, $capacitys[$player->id]['next6mon_capacity'] ? $capacitys[$player->id]['next6mon_capacity'] . "%" : "0")
                        ->setCellValue('I' . $exline, $capacitys[$player->id]['least_free'] ? $capacitys[$player->id]['least_free'] : "0");
                } else {
                    $worksheet->setCellValue('F' . $exline, "0")
                        ->setCellValue('G' . $exline, "0")
                        ->setCellValue('H' . $exline, "0");
                }


                $exline++;
            }
        }
        $writer = new Xlsx($spreadsheet);
        $writer->setPreCalculateFormulas(false);

        unset($spreadsheet);

        $company = $this->membership->get_company($this->get_cid());

        $filename = "Usage($company->name)_" . date("Y-m-d") . ".xlsx";
        //  header("Content-Disposition: attachment;filename=$filename");
        header("Content-Transfer-Encoding:binary");
        header("File-Name:$filename");
        $writer->save('php://output');
    }

    public function get_filterd_players()
    {
        $this->load->model('program');
        $this->load->model('device');

        $start_date = $this->input->post('start_date');
        $end_date  = $this->input->post('end_date');


        $data = $this->get_data();
        $filter_array = array();
        $filter = $this->input->get('filter');
        if ($filter != null) {
            $filter_array['filter_type'] = 'fourfields';

            if (preg_match("/(^[0-9]{3}\-[0-9]{3}\-[0-9]{4}$)/", $filter)) {
                $filter = trim(str_replace('-', '', $filter));
            }
            $filter_array['filter'] = $filter;
        }
        $filterCri = $this->input->get('filterCri');

        if ($filterCri) {
            $filter_array['criteria'] = $filterCri;
        }

        if ($start_date) {
            $filter_array['setupdate'] = $start_date;
        } else {
            $filter_array['setupdate'] = date("Y-m-d");
        }

        $cid = $this->get_cid();
        $pid = $this->get_parent_company_id();

        $cris = $this->get_criteria($cid, $pid);
        if (isset($cris['filter_array'])) {
            $filter_array = array_merge($filter_array, $cris['filter_array']);
        }

        $players = $this->device->get_player_list($pid ? $pid : $cid, $filter_array, true)['data'];
        $players = array_filter($players, function ($player) {
            if ($player->setupdate == '0000-00-00') {
                return false;
            }
            return true;
        });



        //$players = $this->device->get_player_list($pid?$pid:$cid, $filter_array, true, $offset, $limit, $order_item, $order);
        return $players;
    }
}
