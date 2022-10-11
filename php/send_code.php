<?php
require_once 'config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;


require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);

function send_code($email, $subject, $code)
{
    global $mail;
    global $email_user;
    global $email_pass;
    try {
        //Server settings
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;           //Enable verbose debug output
        $mail->isSMTP();                                 //Send using SMTP
        $mail->Host       = 'smtp.gmail.com';            //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                        //Enable SMTP authentication
        $mail->Username   = $email_user;                   //EMAIL username
        $mail->Password   = $email_pass;                   //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;       //Enable implicit TLS encryption
        $mail->Port       = 465;              //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

        //Recipients
        $mail->setFrom($email_user, 'ChatMa8');    //Add a recipient
        $mail->addAddress($email);               //Name is optional

        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body    = 'Your Verification code is : <b>' . $code . '</b>';
        $mail->send();
    } catch (Exception) {
        echo "Message could not be sent. Mailer Error: $mail->ErrorInfo";
    }
}
