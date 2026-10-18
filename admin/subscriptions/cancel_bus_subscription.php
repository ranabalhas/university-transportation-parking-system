<?php
include '../../php/db.php';
include '../includes/admin_session.php';

if(!isset($_GET['id'])){

    header("Location: bus_subscriptions.php");
    exit();
}

$id = intval($_GET['id']);

$stmt = $conn->prepare("
DELETE FROM bus_subscriptions
WHERE id=?");

$stmt->bind_param("i", $id);

$stmt->execute();
header("Location: bus_subscriptions.php");
exit();
?>