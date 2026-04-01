<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
/*
 * Created on 2011-12-11
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

use RobThree\Auth\TwoFactorAuth;
use RobThree\Auth\Providers\Qr\BaconQrCodeProvider;

class Login extends MY_Controller
{
    public function __construct()
    {
        //echo "exit __construct";exit;
        parent::__construct(false);
        $this->lang->load('login');
        $this->lang->load('common');
        //$this->load->helper('language');
        $this->load->helper('form');
    }

    /**
     * 登录默认页
     */
    public function index($data = array())
    {
        //echo "exit index";exit;
        if (!isset($data['err_msg'])) {
            $data['err_msg'] = '';
        }
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $browser_lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 4);
            if (preg_match("/de/i", $browser_lang)) {
                $lang = 'germany';
            } else {
                $lang = "english";
            }
        } else {
            $lang = "germany";
        }
        $data['lang'] = $lang;

        $this->load->view('bootstrap/users/login', $data);
    }

    public function forget_password()
    {
        $this->load->view('bootstrap/users/forget-password');
    }

    public function reset_password()
    {
        $this->load->model('membership');
        $this->load->library('smtp');
        $name = $this->input->post('name');
        $email = $this->input->post('email');
        $user = $this->membership->get_user_by_name_and_email($name, $email);
        if (!$user) {
            $data['msg'] = "We couldn't match Name $name and Email $email you entered with information in our database." . "</br>" . "Try entering your Name again.";
            $data['status'] = 0;
            echo json_encode($data);
        } else {
            $this->membership->change_passd($user->id, $name);
            $email_to = $user->email;
            $message = 'Dear ' . $name . ',<br>' . 'Your password has been reset to [' . $name . '].You can use this one to login. ' . '<br>' .
                'Thank you for your business with us.' . '<br><br><br>'
                . 'Support Center and Customer Service';

            $this->load->library('mailer');
            $status = $this->mailer->sendmail($email_to, '', 'Password Helper', $message);
            $arr = explode('@', $email_to);
            $email_addr = substr_replace($arr[0], '****', 2, -1) . '@' . $arr[1];
            $data['msg'] = "Your password has been reset and send to $user->email";
            $data['email_addr'] = $email_addr;
            $data['status'] = 1;
            echo json_encode($data);
        }
    }
    public function send_mail($email_to, $subject, $message)
    {
        if ($email_to) {
            //$message=ereg_replace("(^|(\r\n))(\.)", "\1.\3", $message);

            $from_name = $this->config->item('email.from_name');
            $from_mail = $this->config->item('email.from_mail');
            $smtpserver = $this->config->item('email.smtp_server');
            $replyto = $this->config->item('email.reply_to');
            $password   = $this->config->item('email.password');
            $serverport = 25;
            $smtp = new SMTP();
            $smtp->do_debug = 0;
            if ($smtp->Connect($smtpserver, $serverport)) {
                $smtp->Hello($from_mail);
                $uid = md5(uniqid(time()));
                if ($smtp->Authenticate($from_mail, $password)) {
                    $smtp->Mail($from_mail);
                    $smtp->Recipient($email_to);
                    //$smtp->Recipient('gongenjian123@163.com');
                    $header = "From: " . $from_name . " <" . $from_mail . ">\r\n";
                    $header .= "To: " . $email_to . "\r\n";
                    $header .= "Subject: " . $subject . "\r\n";

                    $header .= "Reply-To: " . $replyto . "\r\n";
                    $header .= "MIME-Version: 1.0\r\n";

                    $header .= "Content-type: text/html; charset=UTF-8\r\n";
                    $header .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
                    $header .= $message . "\r\n\r\n";
                    //$smtp->Data($header);
                    if ($smtp->Data($header)) {
                        return true;
                    } else {
                        return false;
                    }
                }
                $smtp->Quit();
                $smtp->Close();
            }
        }
    }

    /**
     * 执行登录校验
     */
    public function doLogin()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('username', $this->lang->line('user_name'), 'trim|required');
        if (!$this->input->post('redirect')) {
            $this->form_validation->set_rules('password', $this->lang->line('password'), 'trim|required');
        }

        if ($this->form_validation->run() == false) {
            return;
            // $this->index();
        } else {
            $this->load->model("membership");
            $result = $this->membership->validate_login();

            if ($result['code'] == 0) {

                if ($this->config->item('tfa_enabled') == 1 && $result['data']['tfa_enabled'] == 1) {

                    $qrCodeProvider = new BaconQrCodeProvider(format: "svg");
                    $tfa = new TwoFactorAuth(
                        issuer: "ICAT",
                        qrcodeprovider: $qrCodeProvider,
                    );
                    if (isset($result['data']['tfa_secret']) && $result['data']['tfa_secret']) {
                        $secret = $result['data']['tfa_secret'];
                    } else {
                        $secret = $tfa->createSecret();
                        $this->membership->update_user(array('tfa_secret' => $secret), $result['data']['uid']);
                        $result['data']['tfa_secret'] = $secret;
                    }

                    $data['tfa_enabled'] = $result['data']['tfa_enabled'];
                }

                $this->session->set_userdata($result['data']);
                session_write_close();

                $this->load->helper("url");
                $data['code'] = 0;
            } else {
                $data['code'] = 1;
                $data['msg'] = $this->lang->line('login_code_' . $result['code']);
            }
            echo json_encode($data);
        }
    }

    public function sso()
    {
        if ((int) $this->config->item('sso_enabled') !== 1) {
            show_404();
            return;
        }

        $token = $this->get_sso_token();
        if (!$token) {
            $this->respond_sso_error('missing token', 400);
            return;
        }

        $secret = (string) $this->config->item('sso_jwt_secret');
        $issuers = $this->normalize_sso_issuers($this->config->item('sso_jwt_issuers'));
        if ($secret === '' || empty($issuers)) {
            $this->respond_sso_error('sso not configured', 500);
            return;
        }

        $clockSkew = 60;
        $clockSkew = $this->config->item('sso_clock_skew') ? ((int) $this->config->item('sso_clock_skew')) : $clockSkew;
        if ($clockSkew < 0) {
            $clockSkew = 60;
        }


        $ttl = (int) $this->config->item('sso_jwt_ttl');
        if ($ttl <= 0) {
            $ttl = 120;
        }

        $this->load->library('sso_jwt');
        $verifyResult = $this->sso_jwt->verify($token, $issuers, $secret, $clockSkew, $ttl);
        if (!$verifyResult['ok']) {
            $this->respond_sso_error($verifyResult['error'], 401);
            return;
        }

        $payload = $verifyResult['payload'];
        $nonceResult = $this->consume_sso_nonce($payload['iss'], $payload['jti'], (int) $payload['exp'], $clockSkew);
        if (!$nonceResult['ok']) {
            $this->respond_sso_error($nonceResult['error'], 401);
            return;
        }

        $this->load->model("membership");
        $result = $this->membership->validate_login_by_uid((int) $payload['uid'], 'SSO Login');
        if ($result['code'] != 0) {
            $this->respond_sso_error('user validation failed code ' . $result['code'], 401);
            return;
        }

        $sessionData = $result['data'];
        $sessionData['sso_login'] = 1;

        $this->session->sess_regenerate(true);
        $this->session->set_userdata($sessionData);
        session_write_close();

        $path = $this->input->post('path', true);
        if ($path === null || $path === '') {
            $path = $this->input->get('path', true);
        }
        if (($path === null || $path === '') && isset($payload['path'])) {
            $path = $payload['path'];
        }
        redirect($path);
    }

    private function get_sso_token()
    {
        $token = $this->input->post('token', true);
        if ($token) {
            return trim((string) $token);
        }

        $token = $this->input->get('token', true);
        if ($token) {
            return trim((string) $token);
        }

        $authHeader = $this->input->get_request_header('Authorization', true);
        if ($authHeader && preg_match('/Bearer\s+(.+)/i', $authHeader, $matches)) {
            return trim($matches[1]);
        }

        return '';
    }

    private function normalize_sso_issuers($issuers)
    {
        if (is_string($issuers)) {
            $issuers = explode(',', $issuers);
        }
        if (!is_array($issuers)) {
            return array();
        }

        $normalized = array();
        foreach ($issuers as $issuer) {
            $issuer = trim((string) $issuer);
            if ($issuer !== '') {
                $normalized[] = $issuer;
            }
        }

        return array_values(array_unique($normalized));
    }

    private function consume_sso_nonce($issuer, $jti, $exp, $clockSkew = 0)
    {
        $issuer = trim((string) $issuer);
        $jti = trim((string) $jti);
        $exp = (int) $exp;
        if ($issuer === '' || $jti === '' || $exp <= 0) {
            return array('ok' => false, 'error' => 'invalid nonce claims');
        }

        if (!class_exists('Redis')) {
            return array('ok' => false, 'error' => 'redis extension missing');
        }

        try {
            $redisClass = 'Redis';
            $redis = new $redisClass();
            $host = $this->config->item('redis_server') ?: '127.0.0.1';
            $port = $this->config->item('redis_port') ?: 6379;
            $redis->connect($host, $port);

            $password = $this->config->item('redis_password');
            if ($password) {
                if (!$redis->auth($password)) {
                    return array('ok' => false, 'error' => 'redis authentication failed');
                }
            }

            $key = 'icat:sso:nonce:' . sha1($issuer . '|' . $jti);
            if (!$redis->setnx($key, '1')) {
                return array('ok' => false, 'error' => 'token replayed');
            }

            $ttl = $exp - time() + max(0, (int) $clockSkew);
            if ($ttl < 1) {
                $ttl = 1;
            }
            $redis->expire($key, $ttl);

            return array('ok' => true);
        } catch (Exception $e) {
            log_message('error', 'SSO nonce check failed: ' . $e->getMessage());
            return array('ok' => false, 'error' => 'nonce check unavailable');
        }
    }

    private function respond_sso_error($reason, $httpCode = 401)
    {
        log_message('error', 'SSO login failed [' . $reason . '] ip[' . $this->input->ip_address() . ']');

        $this->output->set_status_header((int) $httpCode);
        if ($this->input->is_ajax_request()) {
            $this->output->set_content_type('application/json');
            echo json_encode(array('code' => 1, 'msg' => 'SSO login failed'));
            return;
        }

        $this->index(array('err_msg' => 'SSO login failed. Please login with username/password.'));
    }



    public function doLogout()
    {
        $this->load->model("membership");
        if ($this->get_uid()) {
            $this->membership->user_log($this->membership->OP_TYPE_SYSTEM, 'Logout', $this->session->userdata('uid'), $this->session->userdata('cid'));
        }
        $this->session->sess_destroy();
        redirect('/login');
    }
    public function doRedirect()
    {

        $this->load->model("membership");
        $result = $this->membership->validate_login();

        if ($result['code'] == 0) {
            $this->session->set_userdata($result['data']);
        } else {
            $result['msg'] = $this->lang->line('login_code_' . $result['code']);
        }
        echo json_encode($result);
    }
}
