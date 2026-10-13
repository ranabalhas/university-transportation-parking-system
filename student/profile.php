<?php
session_start();
include '../php/db.php';
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

/* =========================
   CHECK LOGIN
========================= */
if (!isset($_SESSION['university_id'])) {
    header("Location: student_auth.php");
    exit();
}

$university_id = $_SESSION['university_id'];

/* =========================
   GET STUDENT INFO
========================= */
$stmt = $conn->prepare("
SELECT *
FROM students
WHERE university_id=?
");

$stmt->bind_param("i", $university_id);
$stmt->execute();

$student = $stmt->get_result()->fetch_assoc();

/* =========================
   UPDATE PROFILE
========================= */
$message = "";
$password_message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_profile'])) {

    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);

    /* =========================
       CHECK DUPLICATES
    ========================= */
    $check = $conn->prepare("
    SELECT *
    FROM students
    WHERE (username=? OR email=?)
    AND university_id != ?
    ");

    $check->bind_param(
        "ssi",
        $username,
        $email,
        $university_id
    );

    $check->execute();

    $exists = $check->get_result();

    if ($exists->num_rows > 0) {

        $message = "⚠ Username or Email already exists.";

    } else {

        /* =========================
           UPDATE INFO
        ========================= */
        $update = $conn->prepare("
        UPDATE students
        SET username=?, email=?, phone=?
        WHERE university_id=?
        ");

        $update->bind_param(
            "sssi",
            $username,
            $email,
            $phone,
            $university_id
        );

        if ($update->execute()) {

            $_SESSION['username'] = $username;

            $message = "✅ Profile updated successfully.";

            // refresh student data
            $stmt->execute();
            $student = $stmt->get_result()->fetch_assoc();

        } else {

            $message = "⚠ Error updating profile.";
        }
    }
}


/* =========================
   CHANGE PASSWORD
========================= */
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {

    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    /* CHECK CURRENT PASSWORD */
    if (!password_verify(
        $current_password,
        $student['password']
    )) {

        $password_message =
        "⚠ Current password is incorrect.";

    }

    /* CHECK MATCH */
    elseif ($new_password != $confirm_password) {

        $password_message =
        "⚠ New passwords do not match.";

    }

   
   /* CHECK LENGTH */
elseif (strlen($new_password) < 8) {

    $password_message =
    "⚠ Password must be at least 8 characters.";

}

/* PASSWORD STRENGTH */
elseif (
    !preg_match('/[A-Z]/', $new_password) ||
    !preg_match('/[a-z]/', $new_password) ||
    !preg_match('/[0-9]/', $new_password)
) {

    $password_message =
    "⚠ Password must contain uppercase, lowercase, and a number.";

}

    else {

        $hashed_password =  password_hash($new_password, PASSWORD_DEFAULT);

        $updatePass = $conn->prepare("
        UPDATE students
        SET password=?
        WHERE university_id=? ");

        $updatePass->bind_param(
            "si", $hashed_password, $university_id);

        if ($updatePass->execute()) {

            $password_message = "✅ Password changed successfully.";

            // refresh data
            $stmt->execute();
            $student = $stmt->get_result()->fetch_assoc();

        } else {

            $password_message =
            "⚠ Error updating password.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">

<title>Profile Settings</title>

<link rel="stylesheet" href="../css/style.css">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>

body{
    margin:0;
    font-family:Arial;
    background:#f4f7fb;
}

/* =========================
   NAVBAR
========================= */

.portal-navbar {

    display:flex;
    justify-content:space-between;
    align-items:center;
    background:#0b2d5c;
    padding:18px 40px;
    color:white;
}

.portal-navbar a {

    color:white;
    margin-left:20px;
    font-weight:bold;
}

/* =========================
   HERO
========================= */

.hero-section {

    background:linear-gradient(135deg,#0b2d5c,#123d77);
    color:white;
    padding:60px 20px;
    text-align:center;
}

.hero-section h1 {
    margin:0;
    font-size:45px;
}

.hero-section p {

    margin-top:15px;
    font-size:20px;
}

/* =========================
   CONTAINER
========================= */

.profile-container {

    width:90%;
    max-width:700px;
    margin:auto;
    margin-top:50px;
    margin-bottom:60px;
}

/* =========================
   CARD
========================= */

.profile-card {

    background:white;
    border-radius:20px;
    padding:35px;
    box-shadow:0 5px 15px rgba(0,0,0,0.08);
}

/* =========================
   FORM
========================= */

.profile-card label {

    display:block;
    margin-top:18px;
    margin-bottom:8px;
    font-weight:bold;
    color:#0b2d5c;
}

.profile-card input {

    width:100%;
    padding:14px;
    border:1px solid #ccc;
    border-radius:10px;
    font-size:16px;
}

.profile-card input:focus {

    outline:none;
    border-color:#0b2d5c;
}

/* =========================
   BUTTON
========================= */

.save-btn {

    margin-top:30px;
    width:100%;
    padding:15px;
    background:#0b2d5c;
    color:white;
    border-radius:10px;
    font-size:18px;
    cursor:pointer;
    transition:0.3s;
}

.save-btn:hover {

    background:#123d77;
}

/* =========================
   MESSAGE
========================= */

.message {

    margin-top:20px;
    text-align:center;
    font-weight:bold;
}

.success {

    color:green;
}

.error {

    color:red;
}

/* =========================
   BACK BUTTON
========================= */

.back-btn {
    display:inline-block;
    margin-top:30px;
    color:#0b2d5c;
    font-weight:bold;
}

</style>
</head>

<body>

<?php include 'navbar.php'; ?>
<!-- HERO -->
<section class="hero-section">

<h1>
👤 Profile Settings
</h1>

<p>
Manage your account information securely.
</p>

</section>

<!-- PROFILE -->
<div class="profile-container">

<div class="profile-card">

<form method="POST" onsubmit="showLoading('Saving Changes...')">

<label>University ID</label>

<input type="text" value="<?= $student['university_id'] ?>" disabled>

<label>Username</label>

<input type="text" name="username"value="<?= $student['username'] ?>"required>

<label>Email</label>

<input type="email"name="email"value="<?= $student['email'] ?>"required>

<label>Phone Number</label>

<input type="text"name="phone"value="<?= $student['phone'] ?>"required>

<button class="save-btn" name="save_profile">💾 Save Changes</button>

</form>

<?php if ($message != ''): ?>

<div class="message
<?= str_contains($message,'✅') ? 'success' : 'error' ?>">

<?= $message ?>

</div>

<?php endif; ?>
<hr style="margin:40px 0;">

<h2 style="color:#0b2d5c;">🔒 Change Password</h2>

<form method="POST" onsubmit="showLoading('Updating Password...')">

<label>Current Password</label>

<div class="input-group">

<input type="password"id="currentPassword"name="current_password"required>

<span class="toggle-password" onclick="togglePassword('currentPassword', this)">

<i class="fa fa-eye"></i>

</span>

</div>

<label>New Password</label>

<div class="input-group">

<input type="password"id="newPassword"name="new_password"required>

<span class="toggle-password" onclick="togglePassword('newPassword', this)">

<i class="fa fa-eye"></i>

</span>

</div>

<label>Confirm New Password</label>

<div class="input-group">

<input type="password"id="confirmNewPassword"name="confirm_password"required>

<span class="toggle-password" onclick="togglePassword('confirmNewPassword', this)">

<i class="fa fa-eye"></i>

</span>

</div>

<button class="save-btn" name="change_password" >🔒 Update Password</button>

</form>

<?php if ($password_message != ''): ?>

<div class="message
<?= str_contains($password_message,'✅')
? 'success'
: 'error' ?>">

<?= $password_message ?>

</div>

<?php endif; ?>

<a href="student_portal.php" class="back-btn">⬅ Back to Portal</a>

</div>

</div>
<div id="loadingSpinner" class="loading-overlay hidden">

    <div class="spinner-box">

        <div class="spinner"></div>

        <p id="loadingText">Please wait...</p>

    </div>

</div>
<script>

function showLoading(text = "Please wait...") {

    document.getElementById("loadingSpinner").classList.remove("hidden");

    document.getElementById("loadingText").innerText = text;
}

/* =========================
   DELAY FORM SUBMIT
========================= */

document.querySelectorAll("form").forEach(form => {

    form.addEventListener("submit", function(e) {

        e.preventDefault();

        let text = "Please wait...";

        // detect which form
        if (form.innerHTML.includes("change_password")) {

            text = "Updating Password...";

        } else {

            text = "Saving Changes...";
        }

        showLoading(text);

        // 1 second delay
        setTimeout(() => {

            const hiddenInput = document.createElement("input");

             hiddenInput.type = "hidden";

if (form.innerHTML.includes("change_password")) {

    hiddenInput.name = "change_password";

} else {

    hiddenInput.name = "save_profile";
}

hiddenInput.value = "1";

form.appendChild(hiddenInput);

form.submit();

        }, 1000);

    });

});

if (window.history.replaceState) {

    window.history.replaceState(
        null,
        null,
        window.location.pathname
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