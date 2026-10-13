<?php

include '../../php/db.php';
include '../includes/admin_session.php';

/*  CHECK ID */

if(!isset($_GET['id'])){

    header("Location: parking_subscriptions.php");
    exit();
}

$id = intval($_GET['id']);

/* =========================
   DELETE
========================= */

$stmt = $conn->prepare("

DELETE FROM parking_subscriptions
WHERE id=?

");

$stmt->bind_param("i", $id);

$stmt->execute();

/* =========================
   REDIRECT
========================= */

header("Location: parking_subscriptions.php");

exit();

?>