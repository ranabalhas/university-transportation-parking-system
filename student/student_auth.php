<?php
session_start();
$old = $_SESSION['old'] ?? [];
unset($_SESSION['old']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Access</title>
    <link rel="stylesheet" href="/university_system/css/style.css">
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body class="auth-body">

<div class="auth-container">

    <!-- LOGIN BOX -->
    <div class="auth-box" id="loginBox">
        <h2>Login</h2>

        <form action="/university_system/php/login.php" method="POST" autocomplete="off" onsubmit="showLoading(document.getElementById('loginBtn'))">
            <input type="text" name="username" placeholder="Username" required>

            <div class="input-group">
                <input type="password" id="loginPassword" name="password" placeholder="Enter your password" required>

                <span class="toggle-password" onclick="togglePassword('loginPassword', this)">
                    <i class="fa fa-eye"></i>
                </span>
            </div>
			<p><a href="forgot_password.html"style="color:#2563eb; font-size:14px;">Forgot Password?</a></p>


            <p class="switch-text">
                No account? <a href="#" onclick="showRegister(); return false;">Create one!</a>
            </p>

            <button type="submit" class="auth-btn" id="loginBtn">Login</button>
        </form>
    </div>

    <!-- REGISTER BOX -->
    <div class="auth-box hidden" id="registerBox">
        <h2>Create Account</h2>

        <form action="/university_system/php/register.php" method="POST" autocomplete="off" onsubmit="return submitRegisterForm()">

            <input type="text"name="university_id"placeholder="University ID"value="<?= htmlspecialchars($old['university_id'] ?? '') ?>"required>
            <input type="email"name="email"placeholder="University Email"value="<?= htmlspecialchars($old['email'] ?? '') ?>"required>
			<input type="text"name="phone"placeholder="Phone Number"value="<?= htmlspecialchars($old['phone'] ?? '') ?>"required>
            <input type="text"name="username"placeholder="Username"value="<?= htmlspecialchars($old['username'] ?? '') ?>"required>

            <div class="input-group">
                <input type="password" id="registerPassword" name="password" placeholder="Enter password" required>
                <span class="toggle-password" onclick="togglePassword('registerPassword', this)">
                    <i class="fa fa-eye"></i>
                </span>
            </div>
			<p style="font-size:13px;color:#6B7280;margin-bottom:15px;">Password must contain:uppercase letter, lowercase letter,number, and at least 8 characters.</p>

            <div class="input-group">
                <input type="password" id="confirmPassword" name="confirm_password" placeholder="Confirm password" required>

                <span class="toggle-password" onclick="togglePassword('confirmPassword', this)">
                    <i class="fa fa-eye"></i>
                </span>
            </div>

            <p class="switch-text">
                Already have an account? <a href="#" onclick="showLogin(); return false;">Login</a>
            </p>

            <button type="submit" class="auth-btn" id="registerBtn">Create Account</button>
        </form>
    </div>

</div>

<div id="toast" class="toast"></div>

<!-- LOADING SPINNER -->
<div id="loadingSpinner" class="loading-overlay hidden">

    <div class="spinner-box">

        <div class="spinner"></div>

        <p>Please wait...</p>

    </div>

</div>

<script>

window.onpageshow = function (event) {
    if (event.persisted) {
        window.location.reload();
    }
};

// SHOW / HIDE FORMS

function showRegister() {
    document.getElementById("loginBox").classList.add("hidden");
    document.getElementById("registerBox").classList.remove("hidden");
}

function showLogin() {
    document.getElementById("registerBox").classList.add("hidden");
    document.getElementById("loginBox").classList.remove("hidden");
}



// SHOW ERRORS ONLY

window.onload = function () {

    const params = new URLSearchParams(window.location.search);

    const error = params.get("error");

    const success = params.get("success");

    /* =========================
       SHOW REGISTER BOX IF ERROR
    ========================= */

    const registerErrors = [
        "duplicate",
        "password_mismatch",
        "id_exists",
        "username_exists",
        "email_exists",
        "invalid_id",
        "empty",
        "weak_password",
        "password_rules",
        "phone_exists",
        "student_not_found"
    ];

    if (registerErrors.includes(error)) {

        showRegister();
    }

    /* LOGIN ERRORS*/

    if (error === "nouser")
        showToast("No user found");

    if (error === "invalid")
        showToast("Wrong password");

    if (error === "verify_email")
        showToast("Please verify your email first");

    /* =========================
       REGISTER ERRORS
    ========================= */

    if (error === "duplicate")
        showToast("User already exists");

    if (error === "password_mismatch")
        showToast("Passwords do not match");

    if (error === "id_exists")
        showToast("University ID already exists");

    if (error === "username_exists")
        showToast("Username already exists");

    if (error === "email_exists")
        showToast("Email already exists");

    if (error === "invalid_id")
        showToast("University ID must be numeric");

    if (error === "empty")
        showToast("Please fill all fields");

    if (error === "weak_password")
        showToast("Password must be at least 8 characters");

    if (error === "password_rules")
        showToast("Password must contain uppercase, lowercase, and number");

    if (error === "phone_exists")
        showToast("Phone number already exists");

    if (error === "student_not_found")
        showToast("University ID or email not found");

    /* =========================
       SUCCESS MESSAGES
    ========================= */

    if (success === "verify_email")
        showToast("Verification email sent successfully", "success");

    if (success === "password_reset")
        showToast("Password reset successfully", "success");

    /* =========================
       CLEAN URL
    ========================= */

    if (window.history.replaceState) {

        window.history.replaceState(
            null,
            null,
            window.location.pathname
        );
    }
};
// ============================
// TOAST
// ============================
function showToast(message, type = "error") {

    let toast = document.getElementById("toast");

    toast.innerText = message;

    toast.className = "toast show";

    if (type === "success") {

        toast.style.background = "#22c55e";

    } else {

        toast.style.background = "#ef4444";
    }

    setTimeout(() => {

        toast.className = "toast";

    }, 3000);
}

function togglePassword(inputId, element) {

    const input = document.getElementById(inputId);

    const icon = element.querySelector("i");

    if (input.type === "password") {

        input.type = "text";

        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");

    } else {

        input.type = "password";

        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
    }
}
function showLoading(button = null) {

    document.getElementById("loadingSpinner").classList.remove("hidden");

    if (button) {

        button.disabled = true;

        button.innerText = "Please wait...";
    }
}




</script>

</body>
</html>