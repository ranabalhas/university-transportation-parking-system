<?php

include '../../php/db.php';
include '../includes/admin_session.php';

/* =========================
   HANDLE FORM
========================= */

$success = "";
$error = "";
if(isset($_GET['success'])){

    $success = "Bus line added successfully.";
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $line_name = trim($_POST['line_name']);

    /* VALIDATION */

    if(empty($line_name)){

        $error = "Please enter line name.";
    }

    else{

        /* CHECK DUPLICATE */

        $check = $conn->prepare("

        SELECT id
        FROM bus_line
        WHERE line_name=?

        ");

        $check->bind_param("s", $line_name);

        $check->execute();

        $result = $check->get_result();

        if($result->num_rows > 0){

            $error = "Bus line already exists.";
        }

        else{

            /* INSERT */

            $stmt = $conn->prepare("

            INSERT INTO bus_line(line_name)

            VALUES(?)

            ");

            $stmt->bind_param("s", $line_name);

            if($stmt->execute()){

    $_SESSION['line_success'] ="Bus line added successfully.";

      header("Location: view_lines.php");
      exit();
}

            else{

                $error = "Something went wrong.";
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Add Bus Line</title>

<style>

/* =========================
   PAGE
========================= */

body{

    margin:0;
    font-family:Arial;
    background:#f4f7fb;
}

/* =========================
   MAIN
========================= */

.main{

    margin-left:351px;
    padding:110px 30px 30px;
}

/* =========================
   CARD
========================= */

.form-card{

    max-width:650px;

    background:white;

    padding:35px;

    border-radius:20px;

    box-shadow:0 5px 20px rgba(0,0,0,0.08);
}

.form-card h1{

    margin-top:0;
    color:#0b2d5c;
}

/* =========================
   FORM
========================= */

.form-group{

    margin-bottom:20px;
}

.form-group label{

    display:block;

    margin-bottom:8px;

    font-weight:bold;

    color:#334155;
}

.form-group input{

    width:100%;

    padding:14px;

    border:1px solid #dbeafe;

    border-radius:10px;

    font-size:15px;

    box-sizing:border-box;
}

/* =========================
   BUTTON
========================= */

.submit-btn{

    width:100%;

    padding:14px;

    border:none;

    background:#2563eb;

    color:white;

    font-size:16px;

    font-weight:bold;

    border-radius:12px;

    cursor:pointer;
}
.back-btn{

    display:inline-block;

    margin-bottom:20px;

    text-decoration:none;

    color:#2563eb;

    font-weight:bold;
}


/* =========================
   ALERTS
========================= */

.success{

    background:#dcfce7;
    color:#166534;

    padding:14px;

    border-radius:10px;

    margin-bottom:20px;
}

.error{

    background:#fee2e2;
    color:#991b1b;

    padding:14px;

    border-radius:10px;

    margin-bottom:20px;
}

/* =========================
   MOBILE
========================= */

@media(max-width:768px){

    .main{

        margin-left:0;
        padding:120px 15px 20px;
    }

    .form-card{

        padding:20px;
    }
}

</style>

</head>

<body>

<?php include '../includes/admin_sidebar.php'; ?>

<?php include '../includes/admin_navbar.php'; ?>

<div class="main">

<a href="/university_system/admin/lines/view_lines.php"class="back-btn"> ← Back to Lines</a>
    <div class="form-card">

        <h1>
            Add Bus Line
        </h1>

        <?php if($success): ?>

            <div class="success">

                <?= $success ?>

            </div>

        <?php endif; ?>

        <?php if($error): ?>

            <div class="error">

                <?= $error ?>

            </div>

        <?php endif; ?>

        <form method="POST" id="lineForm">

            <div class="form-group">

                <label>
                    Bus Line Name
                </label>

                <input type="text"

                name="line_name"

                placeholder="Example: Beirut Line"

                required>

            </div>

            <button class="submit-btn">

                Add Bus Line

            </button>

        </form>

    </div>

</div>
<script>

if(window.history.replaceState){

    const url =
    new URL(window.location);

    url.searchParams.delete("success");

    window.history.replaceState(
        {},
        "",
        url
    );
}

window.addEventListener("pageshow", function(){

    document.getElementById("lineForm").reset();

});
</script>
</body>
</html>