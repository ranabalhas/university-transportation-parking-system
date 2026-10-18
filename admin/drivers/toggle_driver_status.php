<?php

include '../../php/db.php';
include '../includes/admin_session.php';

/* GET DRIVER*/

if(!isset($_GET['id'])){

    die("Driver ID missing.");
}

$id = intval($_GET['id']);

$stmt = $conn->prepare("

SELECT status
FROM driver
WHERE id=?

");

$stmt->bind_param("i", $id);
$stmt->execute();

$driver = $stmt->get_result()->fetch_assoc();

if(!$driver){

    die("Driver not found.");
}

/*  TOGGLE STATUS */

$new_status =
$driver['status'] == 'active'
? 'inactive'
: 'active';

$update = $conn->prepare("

UPDATE driver
SET status=?
WHERE id=?

");

$update->bind_param(
    "si",
    $new_status,
    $id
);

$update->execute();


header("Location: view_drivers.php");

exit();
?>