<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<link rel="stylesheet"href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>

/* =========================
   TOP NAVBAR
========================= */

.admin-navbar{
    position:fixed;
    left:324px;
    right:0;
top:0;
    height:96px;

    background:white;

    display:flex;

    justify-content:space-between;

    align-items:center;
    padding:0 40px;

    box-shadow:0 2px 15px rgba(0,0,0,0.08);

    z-index:999;
}

/* =========================
   LEFT
========================= */

.admin-navbar-left h2{

    margin-left:15px;

    color:#0f172a;

    font-size:24px;
}

.admin-navbar-left p{
     padding-bottom:25px;
    margin:5px 0 0;
    margin-left:15px;
    color:#64748b;
    font-size:14px;
}

/* =========================
   RIGHT
========================= */

.admin-navbar-right{

    display:flex;

    align-items:center;

    gap:20px;
}

.admin-admin-box{

    display:flex;

    align-items:center;

    gap:12px;

    background:#f8fafc;

    padding:10px 16px;

    border-radius:14px;
}

.admin-avatar{

    width:45px;
    height:45px;

    border-radius:50%;

    background:#2563eb;

    color:white;

    display:flex;

    align-items:center;

    justify-content:center;

    font-weight:bold;

    font-size:18px;
}

.admin-info h4{

    margin:0;

    color:#0f172a;

    font-size:15px;
}

.admin-info p{

    margin:2px 0 0;

    color:#64748b;

    font-size:13px;
}

/* =========================
   MOBILE
========================= */

@media(max-width:768px){

    .admin-navbar{

        left:0;

        padding:15px;

        height:auto;

        flex-direction:column;

        align-items:flex-start;

        gap:15px;
    }

    .admin-navbar-left h2{

        font-size:20px;
    }

    .admin-navbar-right{

        width:100%;
    }
}

</style>
<div class="admin-navbar">

    <div class="admin-navbar-left">

        <h2>Admin Control Panel</h2>

        <p> University Transportation Management System</p>

    </div>

    <div class="admin-navbar-right">

        <div class="admin-admin-box">

            <div class="admin-avatar">
                A
            </div>
            <div class="admin-info">

                <h4>
                   <?= isset($_SESSION['admin_username']) 
? $_SESSION['admin_username'] 
: 'Administrator' ?>
                </h4>

                <p>System Administrator</p>

           </div>
        </div>
    </div>
</div>