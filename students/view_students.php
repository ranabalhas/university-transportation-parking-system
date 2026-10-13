<?php

include '../../php/db.php';
include '../includes/admin_session.php';

/*SEARCH*/

$search = "";

if(isset($_GET['search'])){

    $search = trim($_GET['search']);
}

/* GET STUDENTS */

if($search != ""){

    $stmt = $conn->prepare("

    SELECT *
    FROM students

    WHERE
    university_id LIKE ?
    OR username LIKE ?
    OR email LIKE ?

    ORDER BY university_id DESC

    ");

    $searchTerm = "%$search%";

    $stmt->bind_param(

        "sss",
        $searchTerm,
        $searchTerm,
        $searchTerm
    );

    $stmt->execute();

    $students = $stmt->get_result();
}

else{

    $students = $conn->query("

    SELECT *
    FROM students
    ORDER BY university_id DESC

    ");
}

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<title>View Students</title>

<link rel="stylesheet" href="../../css/style.css">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>

/* =========================
   PAGE
========================= */

.admin-page{
    margin-left:351px;
    padding:110px 70px 70px;
	
}

/* =========================
   HEADER
========================= */

.page-header{

    display:flex;
    justify-content:space-between;
    align-items:center;
 
    margin-bottom:100px;
}

.page-title{

    font-size:32px;
    color:#0b2d5c;
    font-weight:bold;
}

/* =========================
   SEARCH
========================= */

.search-box{

    display:flex;
    gap:10px;
}

.search-box input{

    padding:12px 15px;
    width:260px;

    border-radius:10px;
    border:1px solid #ccc;

    outline:none;
}

.search-box button{

    background:#2563eb;
    color:white;

    border:none;

    padding:12px 18px;

    border-radius:10px;

    cursor:pointer;
}

/* =========================
   TABLE
========================= */

.students-table{

    width:100%;

    border-collapse:collapse;

    background:white;

    border-radius:18px;

    overflow:hidden;

    box-shadow:0 5px 18px rgba(0,0,0,0.08);
}

.students-table th{

    background:#0b2d5c;

    color:white;

    padding:16px;

    text-align:left;
}

.students-table td{

    padding:15px;

    border-bottom:1px solid #eee;
}

.students-table tr:hover{

    background:#f8fafc;
}

/* =========================
   BADGES
========================= */

.badge{

    padding:6px 12px;

    border-radius:20px;

    font-size:13px;
    font-weight:bold;
}

.verified{

    background:#dcfce7;
    color:#166534;
}

.not-verified{

    background:#fee2e2;
    color:#991b1b;
}

/* =========================
   BUTTONS
========================= */

.action-btn{

    padding:8px 14px;

    border-radius:8px;

    text-decoration:none;

    color:white;

    font-size:14px;

    margin-right:5px;
}

.view-btn{

    background:#2563eb;
}

.delete-btn{

    background:#dc2626;
}

/* =========================
   MOBILE
========================= */

@media(max-width:900px){

    .admin-page{

        margin-left:0;
        padding:15px;
    }

    .page-header{

        flex-direction:column;
        align-items:flex-start;
        gap:15px;
    }

    .search-box{

        width:100%;
    }

    .search-box input{

        width:100%;
    }

    .students-table{

        display:block;
        overflow-x:auto;
    }
}

</style>

</head>

<body>

<?php include '../includes/admin_sidebar.php'; ?>

<?php include '../includes/admin_navbar.php'; ?>

<div class="admin-page">

    <!-- HEADER -->

    <div class="page-header">

        <div class="page-title">
            <i class="fa-solid fa-user-graduate"></i> Student Management </div>

        <!-- SEARCH -->

        <form class="search-box" method="GET">

            <input type="text" name="search"placeholder="Search by ID, username or email"value="<?= htmlspecialchars($search) ?>">

            <button type="submit">

                <i class="fa fa-search"></i>

            </button>

        </form>

    </div>

    <!-- TABLE -->

    <table class="students-table">

        <tr>

            <th>University ID</th>

            <th>Username</th>

            <th>Email</th>

            <th>Phone</th>

            <th>Status</th>

            <th>Actions</th>

        </tr>

        <?php while($student = $students->fetch_assoc()): ?>

        <tr>

            <td>
                <?= $student['university_id'] ?>
            </td>

            <td>
                <?= htmlspecialchars($student['username']) ?>
            </td>

            <td>
                <?= htmlspecialchars($student['email']) ?>
            </td>

            <td>
                <?= htmlspecialchars($student['phone']) ?>
            </td>

            <td>

                <?php if($student['is_verified'] == 1): ?>

                    <span class="badge verified">Verified  </span>

                <?php else: ?>

                    <span class="badge not-verified">Not Verified  </span>

                <?php endif; ?>

            </td>
            <td>
                <a
                href="student_details.php?id=<?= $student['university_id'] ?>"
                class="action-btn view-btn">View </a>
            </td>
        </tr>

        <?php endwhile; ?>

    </table>

</div>

</body>
</html>