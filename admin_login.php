<?php
session_start();
include '../php/db.php';
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");


$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {

        $error = "Please fill all fields.";

    } else {

        $stmt = $conn->prepare("
        SELECT *
        FROM admins
        WHERE username=?
        ");

        $stmt->bind_param("s", $username);

        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {

            $admin = $result->fetch_assoc();

            /* TEMP SIMPLE PASSWORD CHECK */
            if (password_verify($password, $admin['password'])) {

                $_SESSION['admin_id'] = $admin['admin_id'];

                $_SESSION['admin_name'] = $admin['full_name'];
                $_SESSION['admin_username'] = $admin['username'];
                header("Location: admin_dashboard.php");
                exit();

            } else {

                $error = "Incorrect password.";
            }

        } else {

            $error = "Admin account not found.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"content="width=device-width, initial-scale=1.0">

<title>Admin Login</title>

<link rel="stylesheet"href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>

/* =========================
   BODY
========================= */

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Arial,sans-serif;
}

body{

    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;

    background:  linear-gradient(  135deg,  #0f172a, #1e293b  );
}

/* =========================
   LOGIN BOX
========================= */

.login-container{

    width:100%;
    max-width:420px;
    padding:20px;
}

.login-box{

    background:white;
    border-radius:22px;
    padding:40px 30px;
    box-shadow:
    0 10px 30px rgba(0,0,0,0.25);
}

/* =========================
   HEADER
========================= */

.login-header{

    text-align:center;
    margin-bottom:30px;
}

.login-header .icon{

    width:80px;
    height:80px;
    margin:auto;
    margin-bottom:20px;
    border-radius:20px;
    background:#2563eb;
    display:flex;
    justify-content:center;
    align-items:center;
    color:white;
    font-size:34px;
}

.login-header h1{

    font-size:30px;
    color:#0f172a;
    margin-bottom:10px;
}

.login-header p{

    color:#64748b;
    font-size:15px;
}

/* =========================
   INPUTS
========================= */

.input-group{

    margin-bottom:20px;
}

.input-group label{
    display:block;
    margin-bottom:8px;
    color:#334155;
    font-weight:bold;
    font-size:14px;
}

.input-box{

    position:relative;
}

.input-box i{

    position:absolute;
    left:14px;
    top:50%;
    transform:translateY(-50%);
    color:#64748b;
}

.input-box input{

    width:100%;
    padding:14px 14px 14px 45px;
    border:1px solid #cbd5e1;
    border-radius:12px;
    font-size:15px;
    transition:0.3s;
}

.input-box input:focus{

    outline:none;
    border-color:#2563eb;
    box-shadow:
    0 0 0 3px rgba(37,99,235,0.15);
}

/* =========================
   BUTTON
========================= */

.login-btn{

    width:100%;
    border:none;
    padding:14px;
    border-radius:12px;
    background:#2563eb;
    color:white;
    font-size:16px;
    font-weight:bold;
    cursor:pointer;
    transition:0.3s;
}

.login-btn:hover{

    background:#1d4ed8;
}

/* =========================
   ERROR
========================= */

.error-box{

    background:#fee2e2;
    color:#b91c1c;
    padding:12px;
    border-radius:10px;
    margin-bottom:20px;
    font-size:14px;
}

/* =========================
   RESPONSIVE
========================= */

@media(max-width:500px){

    .login-box{

        padding:30px 22px;
    }

    .login-header h1{

        font-size:24px;
    }
}

</style>
</head>
<body>

<div class="login-container">
<div class="login-box">
<div class="login-header">
<div class="icon"><i class="fa-solid fa-user-shield"></i>
</div>

<h1>Admin Portal</h1>

<p>University Transportation Management System</p>

</div>

<?php if(!empty($error)): ?>

<div class="error-box">
<?= $error ?>

</div>
<?php endif; ?>

<form method="POST">

<div class="input-group">

<label>Admin Username</label>

<div class="input-box">

<i class="fa-solid fa-user"></i>

<input type="text"name="username"placeholder="Enter username"required>
</div>
</div>

<div class="input-group">

<label>Password</label>

<div class="input-box">

<i class="fa-solid fa-lock"></i>

<input type="password"name="password"placeholder="Enter password"required>
</div>

</div>

<button type="submit"class="login-btn">Login to Dashboard</button>

</form>
</div>

</div>
<script>

window.addEventListener("pageshow", function(event){

    if(event.persisted){

        window.location.reload();
    }

});

</script>
</body>
</html>