<?php
session_start();

header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
include '../php/db.php';

/* CHECK LOGIN */

if (!isset($_SESSION['driver_id'])) {

    header("Location: driver_login.html");
    exit();
}

/*BLOCK ACCESS AFTER PASSWORD CHANGE*/

$stmt = $conn->prepare("
SELECT first_login
FROM driver
WHERE id=?
");

$stmt->bind_param("i", $_SESSION['driver_id']);

$stmt->execute();

$result = $stmt->get_result()->fetch_assoc();

/*
If password already changed,prevent returning to this page*/

if ($result['first_login'] == 0) {

    header("Location: driver_login.html");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Change Password</title>

    <link rel="stylesheet" href="../css/style.css">

    <link rel="stylesheet"href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body class="auth-body">

<div class="auth-container">

<div class="auth-box">

<h2>Change Your Password</h2>

<?php if (isset($_GET['error'])): ?>

<p style="color:red;font-weight:bold;margin-bottom:15px;text-align:center;">

<?php

if ($_GET['error'] == 'mismatch') {

    echo "Passwords do not match";
}

elseif ($_GET['error'] == 'weak') {

    echo "Password must be at least 8 characters";
}

elseif ($_GET['error'] == 'rules') {

    echo "Password must contain uppercase, lowercase, and number";
}

?>

</p>

<?php endif; ?>

<form action="../php/update_driver_password.php"method="POST"id="passwordForm">

<!-- NEW PASSWORD -->

<div class="input-group">

<input type="password"id="newPassword"name="new_password"placeholder="New Password"required>

<span class="toggle-password"onclick="togglePassword('newPassword', this)">

<i class="fa fa-eye"></i>

</span>

</div>

<!-- CONFIRM PASSWORD -->

<div class="input-group">

<input type="password"id="confirmPassword"name="confirm_password"placeholder="Confirm Password"required>

<span class="toggle-password"onclick="togglePassword('confirmPassword', this)">

<i class="fa fa-eye"></i>

</span>

</div>

<button class="auth-btn">Update Password</button>

</form>

</div>
</div>

<!-- LOADING SPINNER -->

<div id="loadingSpinner"class="loading-overlay hidden">

    <div class="spinner-box">

        <div class="spinner"></div>

        <p>Please wait...</p>

    </div>

</div>

<script>

/*SHOW SPINNER */

function showLoading(button = null, text = "Please wait...") {

    document.getElementById("loadingSpinner").classList.remove("hidden");

    if (button) {

        button.disabled = true;

        button.innerText = text;
    }
}

/* TOGGLE PASSWORD */

function togglePassword(inputId, element) {

    const input = document.getElementById(inputId);

    const icon = element.querySelector("i");

    if (input.type === "password") {

        input.type = "text";

        icon.classList.replace(
            "fa-eye",
            "fa-eye-slash"
        );

    } else {

        input.type = "password";

        icon.classList.replace(
            "fa-eye-slash",
            "fa-eye"
        );
    }
}

/* DELAY SUBMIT 1 SECOND */

document.getElementById("passwordForm").addEventListener("submit", function(e) {

    e.preventDefault();

    const button =this.querySelector("button");

    showLoading(button, "Updating...");

    setTimeout(() => {

        this.submit();

    }, 1000);

});

/* PREVENT BACK CACHE */

window.addEventListener("pageshow", function (event) {

    if (
        event.persisted ||performance.getEntriesByType("navigation")[0].type === "back_forward"
    ) {

        window.location.reload();
    }
});

/*CLEAN URL */

if (window.history.replaceState) {

    window.history.replaceState(
        null,
        null,
        window.location.pathname
    );
}

</script>

</body>
</html>