<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class MyMail
{
    public function sendmail($fromName, $email, $subject, $messageBody)
    {
        $rndno = rand(10000000, 99999999);//OTP generate
        $token = "TOKEN: " . "<h2>" . $rndno . "</h2>";
        // Load Composer's autoloader
        require APPROOT . '/vendor/autoload.php';
        $mail = new PHPMailer(true);
        //
        try {

            $mail->isSMTP();
            $mail->Host = "smtp.gmail.com";
            $mail->SMTPAuth = true;
            $mail->Username = "youremail";
            $mail->Password = "yourpassword";
            $mail->SMTPSecure = "ssl";
            $mail->Port = 465; // for tls

            //email settings
            $mail->isHTML(true);
            $mail->setFrom("youremail", $fromName);
            $mail->addAddress($email);
            // $mail->addReplyTo("youremail", "Library Offence Doc.");
            $mail->Subject = $subject;
            $mail->Body = $messageBody;
            if($mail->send())
              return true;

        } catch (\Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }

}