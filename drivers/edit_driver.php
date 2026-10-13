<?php

include '../../php/db.php';
include '../includes/admin_session.php';

/* GET DRIVER ID */

if(!isset($_GET['id'])){

    die("Driver ID missing.");
}

$id = intval($_GET['id']);

/* GET DRIVER*/

$stmt = $conn->prepare("

SELECT *
FROM driver
WHERE id=?

");

$stmt->bind_param("i", $id);
$stmt->execute();

$driver = $stmt->get_result()->fetch_assoc();

if(!$driver){

    die("Driver not found.");
}

/* =========================
   GET BUS LINES
========================= */

$lines = $conn->query("
SELECT *
FROM bus_line
ORDER BY line_name
");

/* =========================
   UPDATE DRIVER
========================= */

$success = "";
$error = "";
if(isset($_GET['success'])){

    if($_GET['success'] == 'updated'){

        $success = "Driver updated successfully.";
    }
}

if(isset($_POST['update_driver'])){

    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $bus_line_id = $_POST['bus_line_id'];

    if(empty($name) || empty($phone)){

        $error = "Please fill all fields.";

    }else{

        $checkPhone = $conn->prepare("
        SELECT id, name
        FROM driver
        WHERE phone = ?
        AND id != ?
        ");

        $checkPhone->bind_param(
            "si",
            $phone,
            $id
        );

        $checkPhone->execute();

        $phoneResult = $checkPhone->get_result();

        if($phoneResult->num_rows > 0){

            $existingDriver = $phoneResult->fetch_assoc();

            $error =
            "Phone number <b>" .
            htmlspecialchars($phone) .
            "</b> is already assigned to driver <b>" .
            htmlspecialchars($existingDriver['name']) .
            "</b>. Please choose another phone number.";

        }else{

            $update = $conn->prepare("
            UPDATE driver
            SET
            name=?,
            phone=?,
            bus_line_id=?
            WHERE id=?
            ");

            $update->bind_param(
                "ssii",
                $name,
                $phone,
                $bus_line_id,
                $id
            );

            if($update->execute()){

                header(
                "Location: edit_driver.php?id=".$id."&success=updated"
                );
                exit();

            }else{

                $error = "Update failed.";
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

<title>Edit Driver</title>

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>

body{

    margin:0;
    font-family:Arial;

    background:#f1f5f9;
}

/* =========================
   MAIN CONTENT
========================= */

.main-content{

    margin-left:351px;

    padding:110px 40px 40px;
}

/* =========================
   PAGE TITLE
========================= */

.page-title{

    margin-bottom:25px;
}

.page-title h1{

    margin:0;

    color:#0f172a;
}

/* =========================
   FORM BOX
========================= */

.form-box{

    max-width:650px;

    background:white;

    padding:35px;

    border-radius:20px;

    box-shadow:0 5px 20px rgba(0,0,0,0.08);
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

    font-weight:bold;

    color:#334155;
}

.input-group input,
.input-group select{

    width:100%;

    padding:14px;

    border:1px solid #cbd5e1;

    border-radius:10px;

    font-size:15px;

    box-sizing:border-box;
}

.input-group input:focus,
.input-group select:focus{

    outline:none;

    border-color:#2563eb;
}

/* =========================
   BUTTON
========================= */

.submit-btn{

    background:#2563eb;

    color:white;

    border:none;

    padding:14px 20px;

    border-radius:12px;

    font-size:16px;

    font-weight:bold;

    cursor:pointer;

    transition:0.3s;
}

.submit-btn:hover{

    background:#1d4ed8;
}

/* =========================
   ALERTS
========================= */

.success-box{

    background:#dcfce7;

    color:#166534;

    padding:16px;

    border-radius:12px;

    margin-bottom:20px;
}

.error-box{

    background:#fee2e2;

    color:#991b1b;

    padding:16px;

    border-radius:12px;

    margin-bottom:20px;
}

/* =========================
   BACK BUTTON
========================= */

.back-btn{

    display:inline-block;

    margin-bottom:20px;

    text-decoration:none;

    color:#2563eb;

    font-weight:bold;
}

/* =========================
   MOBILE
========================= */

@media(max-width:768px){

    .main-content{

        margin-left:0;

        padding:120px 20px 20px;
    }

    .form-box{

        padding:25px;
    }
}

</style>

</head>

<body>

<?php include '../includes/admin_sidebar.php'; ?>
<?php include '../includes/admin_navbar.php'; ?>

<div class="main-content">

    <a href="/university_system/admin/drivers/view_drivers.php"class="back-btn"> ← Back to Drivers</a>

    <div class="page-title">

        <h1>
            ✏ Edit Driver
        </h1>
    </div>

    <div class="form-box">

        <?php if($success): ?>

            <div class="success-box">

                <?= $success ?>

            </div>

        <?php endif; ?>

        <?php if($error): ?>

            <div class="error-box">

                <?= $error ?>

            </div>

        <?php endif; ?>

        <form method="POST">

            <!-- NAME -->
            <div class="input-group">

                <label>
                    Driver Name
                </label>

                <input type="text"name="name"value="<?= htmlspecialchars($driver['name']) ?>"

                required>

            </div>

            <!-- PHONE -->
            <div class="input-group">

                <label> Phone Number</label>

                <input type="text"name="phone"value="<?= htmlspecialchars($driver['phone']) ?>"

                required>

            </div>

            <!-- LINE -->
            <div class="input-group">

                <label>Bus Line </label>

                <select name="bus_line_id">

                    <?php while($line = $lines->fetch_assoc()): ?>

                        <option
                        value="<?= $line['id'] ?>"

                        <?= $driver['bus_line_id']
                        == $line['id']
                        ? 'selected'
                        : '' ?>>

                            <?= htmlspecialchars($line['line_name']) ?>

                        </option>

                    <?php endwhile; ?>

                </select>

            </div>

            <!-- BUTTON -->
            <button type="submit" name="update_driver" class="submit-btn"> Update Driver </button>

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


</script>
</body>
</html>