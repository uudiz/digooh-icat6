<?php if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
//require '../vendor/autoload.php';

class Mailer
{
    public $mail;
 
    public function __construct()
    {
        // require_once('PHPMailer/class.phpmailer.php');

        $this->CI = &get_instance();
        $this->mail = new PHPMailer(true);
        //$this->mail->isHTML(true);
        
        /*
        $this->mail->CharSet = "utf-8";                                          // 一定要設定 CharSet 才能正確處理中文
        $this->mail->SMTPDebug  = 4;                                             // enables SMTP debug information
        $this->mail->SMTPAuth   = true;                                          // enable SMTP authentication
       // $this->mail->SMTPSecure = "ssl";                                         // sets the prefix to the servier
        $this->mail->Host       = $this->CI->config->item('email.smtp_server');      // sets GMAIL as the SMTP server
        $this->mail->Port       = $this->CI->config->item('email.smtp_port');        // set the SMTP port for the GMAIL server
        $this->mail->Username   = $this->CI->config->item('email.from_mail');        // Username
        $this->mail->Password   = $this->CI->config->item('email.password');         // password
        $this->mail->AddReplyTo($this->CI->config->item('email.reply_to'), 'Notify');
        */
       
        // $this->mail->From = $this->CI->config->item('email.from_mail');
        $this->mail->SMTPDebug = 0;//SMTP::DEBUG_SERVER;                      // Enable verbose debug output
        $this->mail->isSMTP();                                            // Send using SMTP
        $this->mail->Host       = $this->CI->config->item('email.smtp_server');                    // Set the SMTP server to send through
        $this->mail->SMTPAuth   = true;                                   // Enable SMTP authentication
        $this->mail->Username   = $this->CI->config->item('email.from_mail');                     // SMTP username
        $this->mail->Password   = $this->CI->config->item('email.password');                               // SMTP password
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
        $this->mail->Port       = $this->CI->config->item('email.smtp_port');                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

        //Recipients
        $this->mail->setFrom($this->CI->config->item('email.from_mail'));
    }
 
    public function sendmail($to, $to_name, $subject, $body)
    {
        try {
            if (is_array($to)) {
                foreach ($to as $toaddr) {
                    $this->mail->addAddress($toaddr);
                }
            } else {
                $this->mail->addAddress($to);
            }
            $this->mail->isHTML(true);
            $this->mail->Subject = $subject;
            $this->mail->Body    = $body;
            return $this->mail->send();
            
            //echo "Message Sent OK</p>\n";  //测试
        } catch (phpmailerException $e) {
            echo $e->errorMessage();
            return $e->errorMessage(); //Pretty error messages from PHPMailer
        } catch (Exception $e) {
            echo $e->errorMessage();
            return $e->getMessage(); //Boring error messages from anything else!
        }
    }
}
 
/* End of file mailer.php */
