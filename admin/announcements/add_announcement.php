<?php
include '../../php/db.php';
include '../includes/admin_session.php';

/* HANDLE FORM*/

$success = "";
$error = "";

if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $title = trim($_POST['title']);

    $message = trim($_POST['message']);

    $audience = $_POST['audience'];
	$priority = $_POST['priority'];

    if(
        empty($title)
        ||
        empty($message)
    ){

        $error = "Please fill all fields.";
    }

    else{

        $stmt = $conn->prepare("

        INSERT INTO announcements(

            title,
            message,
            audience,
			priority )
        VALUES(?,?,?,?)  ");

        $stmt->bind_param(

            "ssss",

            $title,
            $message,
            $audience,
			$priority
        );

       if($stmt->execute()){

    $_SESSION['announcement_success'] =
    "Announcement published successfully.";

    header("Location: view_announcements.php");

    exit();
}
        else{
            $error = "Something went wrong."; }   }}

/* GET ANNOUNCEMENTS*/

$announcements = $conn->query("

SELECT *
FROM announcements

ORDER BY created_at DESC

");

?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">

<title>Announcements</title>

<meta name="viewport"content="width=device-width, initial-scale=1.0">

<link rel="stylesheet"href="../../css/style.css">

<style>

.page-content{

    margin-left:351px;
	padding:110px 70px 70px;
}

.page-title{

    font-size:34px;
    font-weight:bold;

    color:#0b2d5c;

    margin-bottom:30px;
}

/* =========================
   FORM BOX
========================= */

.form-box{

    background:white;

    padding:30px;

    border-radius:20px;

    box-shadow:0 5px 20px rgba(0,0,0,0.08);

    margin-bottom:35px;
}

.form-box h2{

    margin-bottom:25px;

    color:#0b2d5c;
}

.form-group{

    margin-bottom:20px;
}

.form-group label{

    display:block;

    margin-bottom:8px;

    font-weight:bold;

    color:#334155;
}

.form-group input,
.form-group textarea,
.form-group select{

    width:100%;

    padding:14px;

    border:1px solid #d1d5db;

    border-radius:12px;

    font-size:15px;
}

.form-group textarea{

    min-height:140px;

    resize:vertical;
}

/* BUTTON */

.publish-btn{

    background:#2563eb;

    color:white;

    border:none;

    padding:14px 22px;

    border-radius:12px;

    cursor:pointer;

    font-weight:bold;

    transition:0.3s;
}

.publish-btn:hover{

    background:#1d4ed8;
}
.back-btn{

    display:inline-block;

    margin-bottom:20px;

    text-decoration:none;

    color:#2563eb;

    font-weight:bold;
}

/* MESSAGES */

.success-box{

    background:#dcfce7;

    color:#166534;

    padding:15px;

    border-radius:12px;

    margin-bottom:20px;
}

.error-box{

    background:#fee2e2;

    color:#991b1b;

    padding:15px;

    border-radius:12px;

    margin-bottom:20px;
}

/* =========================
   ANNOUNCEMENT CARDS
========================= */

.announcement-card{

    background:white;

    border-radius:18px;

    padding:25px;

    margin-bottom:20px;

    box-shadow:0 5px 20px rgba(0,0,0,0.08);

    border-left:6px solid #2563eb;
}

.announcement-title{

    font-size:22px;

    font-weight:bold;

    color:#0b2d5c;

    margin-bottom:10px;
}

.announcement-message{

    line-height:1.8;

    color:#334155;

    margin-bottom:15px;
}

.announcement-meta{

    display:flex;

    justify-content:space-between;

    flex-wrap:wrap;

    gap:10px;

    font-size:14px;

    color:#64748b;
}

.audience-badge{

    background:#dbeafe;

    color:#1d4ed8;

    padding:6px 12px;

    border-radius:20px;

    font-weight:bold;
}

.priority-select {
    width: 100%;
    padding: 14px;
    border-radius: 12px;
    border: 2px solid #e2e8f0;
    background: white;
    font-size: 15px;
    cursor: pointer;
    transition: 0.3s;
}

.priority-select:focus {
    border-color: #2563eb;
    outline: none;
    box-shadow: 0 0 0 3px rgba(37,99,235,0.2);
}
/* RESPONSIVE */

@media(max-width:900px){

.page-content{

    margin-left:0;
    padding:20px;
}

}

</style>

</head>

<body>

<?php include '../includes/admin_sidebar.php'; ?>

<?php include '../includes/admin_navbar.php'; ?>

<div class="page-content">
<a href="/university_system/admin/announcements/view_announcements.php"class="back-btn"> ← Back to Announcement</a>
<div class="page-title">📢 Announcements & Notifications</div>
<div style="margin-bottom:20px;">

<a href="view_announcements.php"style="display:inline-block;padding:12px 18px;background:#2563eb;color:white;text-decoration:none;border-radius:10px;font-weight:bold;">

📢 View All Announcements

</a>

</div>
<!-- =========================
     FORM
========================= -->

<div class="form-box">

<h2>Publish New Announcement</h2>


<?php if($success != ""): ?>

<div class="success-box">

<?= $success ?>

</div>

<?php endif; ?>

<?php if($error != ""): ?>

<div class="error-box">

<?= $error ?>

</div>

<?php endif; ?>

<form method="POST" id="announcementForm">

<div class="form-group">

<label>Announcement Title</label>

<input type="text"name="title"required>

</div>

<div class="form-group">

<label>Audience</label>

<select name="audience">

<option value="all">All Users</option>

<option value="students">Students Only</option>

<option value="drivers">Drivers Only</option>

</select>

</div>
<div class="form-group">
<label>Priority Level</label>

<select name="priority" class="priority-select" required>

    <option value="normal">🟢 Normal</option>
    <option value="important">🟡 Important</option>
    <option value="urgent">🔴 Urgent</option>

</select>
</div>
<div class="form-group">

<label>Message</label>
<textarea name="message"required></textarea>
</div>
<button type="submit"class="publish-btn">📢 Publish Announcement</button>
</form>
</div>
</div>
<script>
window.addEventListener("pageshow", function(){

    document.getElementById("announcementForm").reset();

});
</script>
</body>
</html>