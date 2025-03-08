<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * PHP Mailer Library
 * ----------------------------------------------------------
 *
 * @author: Shivam Gautam
 * @version: 0.0.1
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer_Lib
{
    private $mailer = null;
    private $error = null;
    public function __construct()
    {
        $this->mailer = new PHPMailer(true);
        //Server settings
        $this->mailer->SMTPDebug = 0;                                       // Disable verbose debug output
        $this->mailer->isSMTP();                                            // Set mailer to use SMTP
        $this->mailer->Host       = 'smtp.hostinger.com';                     // Specify main and backup SMTP servers
        $this->mailer->SMTPAuth   = true;                                   // Enable SMTP authentication
        $this->mailer->Username   = 'testmail@solutionspool.in';               // SMTP username
        $this->mailer->Password   = '#Testmail@4578';                  // SMTP password
        // $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption, `PHPMailer::ENCRYPTION_SMTPS` also accepted
        $this->mailer->Port       = 587;                                    // TCP port to connect to
        $this->mailer->setFrom('testmail@solutionspool.in', 'IFRJ-Index');
    }

    public function send_mail($to, $subject, $message, $attachments = [])
    {
        try {
            $this->mailer->addAddress($to);
            foreach ($attachments as $attachment) {
                $this->mailer->addAttachment($attachment);
            }
            $this->mailer->isHTML(true);
            $this->mailer->Subject = $subject;
            $this->mailer->Body    = $message;
            $this->mailer->send();
            return true;
        } catch (Exception $e) {
            $this->error = $e->getMessage().':'.$this->mailer->ErrorInfo;
            return false;
        }
    }
    public function error()
    {
        return $this->error;
    }
}
