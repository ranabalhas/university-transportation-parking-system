<?php

$verified = isset($_GET['verified']);

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<title>Email Verification</title>

<link rel="stylesheet"href="/university_system/css/style.css">

<?php if ($verified): ?>

<meta http-equiv="refresh"content="3;url=/university_system/student/student_portal.php">

<?php endif; ?>

</head>

<body class="auth-body">

<div class="auth-container">

<div class="auth-box">

<?php if (!$verified): ?>

<h2>📧 Verify Your Email</h2>

<p style="margin-top:20px; line-height:1.8;">

A verification email has been sent successfully.<br><br>

Please check your:<br><br>

✅ Inbox<br><br>

Click the verification link to activate your account.

</p>

<br>

<a href="student_auth.php"class="auth-btn"style="text-decoration:none; display:inline-block;">

Back To Login</a>

<?php else: ?>

<h2>✅ Account Verified</h2>

<p style="margin-top:20px; line-height:1.8;">

Your account has been activated successfully.<br><br>

Redirecting to Student Portal...</p>

<?php endif; ?>
</div>
</div>
</body>
</html>