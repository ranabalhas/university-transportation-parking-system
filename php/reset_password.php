<?php
include "db.php";

$token = $_GET['token'] ?? '';

if (empty($token)) {

    die("Invalid token");
}

/*CHECK TOKEN */

$stmt = $conn->prepare("
SELECT *
FROM students
WHERE reset_token=?
AND reset_expires > NOW()
");

$stmt->bind_param("s", $token);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {

    header("Location: /university_system/student/forgot_password.html?error=expired_token");
    exit();
}

$user = $result->fetch_assoc();


if (isset($_GET['error'])) {

    if ($_GET['error'] == "nomatch") {
        $error = "Passwords do not match";
    }

    elseif ($_GET['error'] == "weak") {
        $error = "Password must be at least 8 characters";
    }

    elseif ($_GET['error'] == "rules") {
        $error =
        "Password must contain uppercase, lowercase, and a number";
    }
}
/*UPDATE PASSWORD*/

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    $error = "";
	$success = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if ($password != $confirm) {

        header("Location: reset_password.php?token=$token&error=nomatch");
exit();

    } elseif (strlen($password) < 8) {

        header("Location: reset_password.php?token=$token&error=weak");
exit();

    }
       elseif (
    !preg_match('/[A-Z]/', $password) ||
    !preg_match('/[a-z]/', $password) ||
    !preg_match('/[0-9]/', $password)
) {

    header("Location: reset_password.php?token=$token&error=rules");
exit();
}	else {

        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $update = $conn->prepare("
        UPDATE students
        SET password=?,
        reset_token=NULL,
        reset_expires=NULL
        WHERE university_id=?
        ");

        $update->bind_param(
            "si",
            $hashed,
            $user['university_id']
        );

        $update->execute();
sleep(1);
        header("Location: /university_system/student/student_auth.php?success=password_reset");
        exit();
    }
}
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<title>Reset Password</title>

<link rel="stylesheet"
href="/university_system/css/style.css">

</head>

<body class="auth-body">

<div class="auth-container">

<div class="auth-box">

<h2>Create New Password</h2>

<?php if (!empty($error)): ?>

<p style="color:red;margin-bottom:15px;font-weight:bold;">
<?= $error ?>
</p>

<?php endif; ?>

<form method="POST" onsubmit="showLoading(document.getElementById('updatePassBtn'),'Updating...')">

<div class="input-group">

<input type="password"id="resetPassword"name="password"placeholder="New Password"required>

<span class="toggle-password"onclick="togglePassword('resetPassword', this)">

<i class="fa fa-eye"></i>

</span>

</div>

<div class="input-group">

<input type="password"id="resetConfirmPassword"name="confirm_password"placeholder="Confirm Password"required>

<span class="toggle-password"onclick="togglePassword('resetConfirmPassword', this)">

<i class="fa fa-eye"></i>

</span>

</div>

<button type="submit" class="auth-btn" id="updatePassBtn">Update Password</button>

</form>

</div>
</div>

<div id="loadingSpinner" class="loading-overlay hidden">

    <div class="spinner-box">
        <div class="spinner"></div>
        <p>Please wait...</p>

    </div>
</div>

<script>

function showLoading(button = null, text = "Please wait...") {

    document.getElementById("loadingSpinner").classList.remove("hidden");

    if (button) {

        button.disabled = true;

        button.innerText = text;
    }
}

if (window.history.replaceState) {

    window.history.replaceState(
        null,
        null,
        window.location.pathname + "?token=<?= $token ?>"
    );
}

function togglePassword(inputId, element) {

    const input = document.getElementById(inputId);

    const icon = element.querySelector("i");

    if (input.type === "password") {

        input.type = "text";

        icon.classList.replace("fa-eye", "fa-eye-slash");

    } else {

        input.type = "password";

        icon.classList.replace("fa-eye-slash", "fa-eye");
    }
}
</script>
</body>
</html>