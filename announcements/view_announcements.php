<?php
include '../../php/db.php';
include '../includes/admin_session.php';
$success = "";

if(isset($_SESSION['announcement_success'])){

    $success =
    $_SESSION['announcement_success'];

    unset($_SESSION['announcement_success']);
}

/* GET ANNOUNCEMENTS */

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

<title>View Announcements</title>

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<link rel="stylesheet"
href="../../css/style.css">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>

/* =========================
   PAGE
========================= */

body{
    margin:0;
    font-family:Arial,sans-serif;
    background:#f1f5f9;
	
}

.admin-layout{
    display:flex;
	padding-top:0px;
	
	
}

/* =========================
   MAIN
========================= */

.admin-main{
    flex:1;
	padding:110px 70px 70px;
	margin-top:0px;
	margin-left:330px;
	
}

/* =========================
   HEADER
========================= */

.page-header{
	padding-top:50px;
    display:flex;
    justify-content:space-between;
    align-items:center;

    margin-bottom:50px;
}

.page-header h1{

    color:#0f172a;
}

/* =========================
   ADD BUTTON
========================= */

.add-btn{

    background:#2563eb;
    color:white;
margin-bottom:10px;
    padding:12px 18px;

    border-radius:10px;

    text-decoration:none;

    font-weight:bold;

    transition:0.3s;
}

.add-btn:hover{

    background:#1d4ed8;
}

/* =========================
   ANNOUNCEMENT CARD
========================= */

.announcement-card{

    background:white;

    border-radius:18px;

    padding:25px;

    margin-bottom:20px;

    box-shadow:0 4px 15px rgba(0,0,0,0.08);

    border-left:6px solid #2563eb;
}

/* PRIORITIES */

.priority-normal{

    border-left-color:#2563eb;
}

.priority-important{

    border-left-color:#f59e0b;
}

.priority-urgent{

    border-left-color:#dc2626;
}

/* =========================
   TOP
========================= */

.card-top{

    display:flex;
    justify-content:space-between;
    align-items:center;

    margin-bottom:15px;
}

.card-top h2{

    margin:0;
    color:#0f172a;
}

/* =========================
   BADGES
========================= */

.badges{

    display:flex;
    gap:10px;
    flex-wrap:wrap;
}

.badge{

    padding:6px 12px;

    border-radius:20px;

    font-size:13px;

    font-weight:bold;

    color:white;
}

.normal{
    background:#2563eb;
}

.important{
    background:#f59e0b;
}

.urgent{
    background:#dc2626;
}

.students{
    background:#16a34a;
}

.drivers{
    background:#7c3aed;
}

.all{
    background:#0f172a;
}

/* =========================
   MESSAGE
========================= */

.message{

    color:#334155;

    line-height:1.8;

    margin-bottom:20px;
}

/* =========================
   FOOTER
========================= */

.card-footer{

    display:flex;
    justify-content:space-between;
    align-items:center;

    flex-wrap:wrap;

    gap:15px;
}

.date{

    color:#64748b;
    font-size:14px;
}

/* =========================
   DELETE BUTTON
========================= */

.delete-btn{

    background:#dc2626;
    color:white;

    padding:10px 15px;

    border-radius:10px;

    text-decoration:none;

    font-weight:bold;

    transition:0.3s;
}

.delete-btn:hover{

    background:#b91c1c;
}

/* =========================
   EMPTY
========================= */

.empty-box{

    background:white;

    padding:40px;

    border-radius:18px;

    text-align:center;

    color:#64748b;
}

.success-box{

    background:#dcfce7;

    color:#166534;

    padding:15px;

    border-radius:12px;

    margin-bottom:20px;
}
/* =========================
   MOBILE
========================= */

@media(max-width:768px){

    .admin-main{

        padding:20px;
    }

    .page-header{

        flex-direction:column;
        align-items:flex-start;
        gap:15px;
    }

    .card-top{

        flex-direction:column;
        align-items:flex-start;
        gap:10px;
    }

    .card-footer{

        flex-direction:column;
        align-items:flex-start;
    }
}

</style>

</head>

<body>

<div class="admin-layout">

<?php include '../includes/admin_sidebar.php'; ?>

<div class="admin-main">

<?php include '../includes/admin_navbar.php'; ?>

<?php if(!empty($success)): ?>

<div class="success-box">

    <?= $success ?>
</div>

<?php endif; ?>
<!-- HEADER -->

<div class="page-header">
<h1>📢 University Announcements</h1>

<a href="add_announcement.php"class="add-btn">+ Add Announcement</a></div>

<?php

if($announcements->num_rows > 0){

while($row = $announcements->fetch_assoc()){

?>

<div class="announcement-card
priority-<?= strtolower($row['priority']) ?>">

<div class="card-top">

<h2>
<?= htmlspecialchars($row['title']) ?>
</h2>

<div class="badges">

<span class="badge <?= strtolower($row['priority']) ?>">
<?= $row['priority'] ?>
</span>

<span class="badge <?= strtolower($row['audience']) ?>">
<?= ucfirst($row['audience']) ?>
</span>

</div>
</div>
<div class="message">

<?= nl2br(htmlspecialchars($row['message'])) ?>

</div>
<div class="card-footer">

<div class="date">🕒 Posted:
<?= date("F d, Y h:i A",strtotime($row['created_at'])) ?>
</div>

<a href="delete_announcement.php?id=<?= $row['id'] ?>"
class="delete-btn"onclick="return confirm('Delete this announcement?')">Delete</a>
</div>
</div>

<?php
}
}else{

?>

<div class="empty-box">
<h2>No announcements found</h2>
<p>Create your first university announcement.</p>

</div>
<?php } ?>

</div>
</div>
</body>
</html>