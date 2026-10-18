<?php
include '../../php/db.php';
include '../includes/admin_session.php';

if(isset($_GET['id'])){

    $id = intval($_GET['id']);

    $stmt = $conn->prepare("

    DELETE FROM announcements
    WHERE id=?

    ");

    $stmt->bind_param("i",$id);

    $stmt->execute();
}
header("Location: view_announcements.php");
exit();
?>

