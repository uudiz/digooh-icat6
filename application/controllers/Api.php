<?php
class Api extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
    }
    function getIdAreas()
    {
        $playlist_id = $this->input->get('playlist_id');
        if ($playlist_id) {
            $this->load->model('program');
            $id_zones = $this->program->get_playlist_id_areas($playlist_id);


            header('Access-Control-Allow-Origin: *');

            if ($id_zones) {
                echo json_encode($id_zones);
                return;
            }
        }
        set_status_header(404, 'playlist id is not valid');
    }
    function getSerialList()
    {
        $player_sn = $this->input->get('sn');
        if ($player_sn) {
            header('Access-Control-Allow-Origin: *');
            $this->load->model('peripheral');
            $ret = $this->peripheral->get_peripheral_by_player($player_sn);

            /*
            $ret1 = array_map(function ($item) {
                return array(
                    'id' => $item->id,
                    'name' => $item->name,
                );
            }, $ret);
            */

            if ($ret) {
                echo json_encode($ret);
                return;
            }
        }
        set_status_header(404, 'no serial device');
    }
    function products()
    {
        $this->load->model('product');
        $store_id = $this->input->get('store_id');
        $name = $this->input->get('q');

        $filter_array = array('store_id' => $store_id);
        if ($name) {
            $filter_array['name'] = $name;
        }

        $ret = $this->product->get_all(0, -1, 'name', 'asc', $filter_array);
        header('Access-Control-Allow-Origin: *');
        echo json_encode($ret);
    }
    function getProductPrice()
    {
        $product_id = $this->input->get('product_id');
        $this->load->model('product');
        $ret = $this->product->get_price($product_id);
        header('Access-Control-Allow-Origin: *');
        echo json_encode($ret);
    }
}
