<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

$mail = new PHPMailer(true);
$mail->CharSet = 'UTF-8';

try {

    /* SMTP */

    $mail->isSMTP();

    $mail->Host = 'smtp.gmail.com';

    $mail->SMTPAuth = true;

    $mail->Username = 'university.transport.system@gmail.com';

    $mail->Password = '';

    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;

    $mail->Port = 465;

    

    /* EMAIL */

    $mail->setFrom(
        'university.transport.system@gmail.com',
        'University Transportation System'
    );

    $mail->addAddress($email);

    $verify_link =
    "http://localhost/university_system/php/verify_email.php?token=$token";

    $mail->isHTML(true);

    $mail->Subject ='University Transportation System - Email Verification';

    $mail->Body = "

<div style='
font-family:Arial;
padding:20px;
line-height:1.8;
'>

<h2 style='color:#2563eb;'>
University Transportation System
</h2>

<p>Hello <b>$username</b>,</p>

<p>
Thank you for creating your account.
</p>

<p>
Please verify your email address by clicking the button below:
</p>

<br>

<a href='$verify_link'
style='
background:#2563eb;
color:white;
padding:14px 24px;
text-decoration:none;
border-radius:8px;
font-weight:bold;
display:inline-block;
'>

Verify Account

</a>

<br><br>

<p style='color:gray;font-size:13px;'>
If you did not create this account, please ignore this email.
</p>

</div>

";
$mail->AltBody ="Verify your account using this link: $verify_link";

    $mail->send();

    header("Location: /university_system/student/verify_notice.php");

    exit();

} catch (Exception $e) {

    echo "Mailer Error: {$mail->ErrorInfo}";
}
?>
