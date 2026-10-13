<?php

include "db.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

$email = trim($_POST['email']);

/*CHECK EMAIL*/

$stmt = $conn->prepare("
SELECT *
FROM students
WHERE email=?
");

$stmt->bind_param("s", $email);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {

    header("Location: /university_system/student/forgot_password.html?error=email_not_found");
    exit();
}

$user = $result->fetch_assoc();

/* GENERATE TOKEN*/

$token = bin2hex(random_bytes(32));

$expires =date("Y-m-d H:i:s", strtotime("+1 hour"));

/* SAVE TOKEN */

$update = $conn->prepare("
UPDATE students
SET reset_token=?,
reset_expires=?
WHERE university_id=?
");

$update->bind_param(
    "ssi",
    $token,
    $expires,
    $user['university_id']
);

$update->execute();

/* SEND EMAIL */

$mail = new PHPMailer(true);

try {

    $mail->isSMTP();

    $mail->Host = 'smtp.gmail.com';

    $mail->SMTPAuth = true;

    $mail->Username ='university.transport.system@gmail.com';

    $mail->Password ='etteghfqvuzgvkzr';

    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;

    $mail->Port = 465;

    $mail->setFrom(
        'university.transport.system@gmail.com',
        'University Transportation System'
    );

    $mail->addAddress($email);

    $reset_link =
    "http://localhost/university_system/php/reset_password.php?token=$token";

    $mail->isHTML(true);

    $mail->Subject ='Reset Your Password';

    $mail->Body = "

    <h2>Password Reset</h2>

    <p>Hello {$user['username']},</p>

    <p>Click below to reset your password:</p>

    <a href='$reset_link'
    style='
    background:#2563eb;
    color:white;
    padding:12px 20px;
    text-decoration:none;
    border-radius:6px;
    display:inline-block;
    '>

    Reset Password

    </a>

    <p>
    This link expires in 1 hour.
    </p>

    ";

    $mail->send();
sleep(1);
    header("Location: /university_system/student/reset_notice.php");
exit();

   
} catch (Exception $e) {

    echo "Mailer Error: {$mail->ErrorInfo}";
}
?>