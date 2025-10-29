<?php

use RobThree\Auth\TwoFactorAuth;
use RobThree\Auth\Providers\Qr\BaconQrCodeProvider;

class Tfa extends MY_Controller
{
    public function __construct()
    {
        parent::__construct(false);
    }

    public function vertification()
    {
        $data = $this->get_data();

        $qrCodeProvider = new BaconQrCodeProvider(format: "svg");

        $tfa = new TwoFactorAuth(
            issuer: "ICAT",
            qrcodeprovider: $qrCodeProvider,
        );
        $data['tfa'] = $tfa;

        $this->load->view('bootstrap/users/vertification_code', $data);
    }
    public function doVertification()
    {
        $code = $this->input->get('code');
        $tfa = new TwoFactorAuth();
        if ($tfa->verifyCode($this->get_2fw_secret(), $code)) {
            $this->session->set_userdata('tfa_verified', 1);
            $data['code'] = 0;
            $data['msg'] = "ahthrized success";
        } else {
            $this->session->set_userdata('tfa_verified', 0);
            $data['msg'] = "Two-factor code verification failed. Please try again.";
            $data['code'] = 1;
        }
        echo json_encode($data);
    }
}
