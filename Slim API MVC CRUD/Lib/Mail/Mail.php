<?php


namespace Lib\Mail;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mail
{
    /**
     * @var Mail
     */
    private static $_i = null;
    private $mail;

    private function __construct()
    {
        $this->mail = new PHPMailer(true);
      //  $this->mail->SMTPDebug = 2;                                       // Enable verbose debug output
        $this->mail->isSMTP();                                            // Set mailer to use SMTP
        $this->mail->SMTPAuth   = false;
        $this->mail->SMTPSecure = false;
        $this->mail->SMTPAutoTLS = false;
        $this->mail->Host = '127.0.0.1';
        $this->mail->Port       = 25;                                    // TCP port to connect to
        $this->mail->setFrom('no-reply@rust.bike', 'Rust.Bike Admin');
    }

    /**
     * @return Mail
     */
    public static function I() {
        if (static::$_i == null) {
            static::$_i = new Mail();
        }
        return static::$_i;

    }

    /**
     * @param string $addr
     * @return $this
     */
    public function setTo(string $addr) {
        $this->mail->addAddress($addr);
        return $this;
    }

    /**
     * @param Message $msg
     * @throws Exception
     */
    public function send(Message $msg) {
        $this->mail->isHTML(true);                                  // Set email format to HTML
        $this->mail->Subject = $msg->subject;
        $this->mail->Body    = $msg->body;
        $this->mail->AltBody = $msg->altBody;

        $this->mail->send();
    }
}