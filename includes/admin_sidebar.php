<link rel="stylesheet"href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>

/* =========================
   SIDEBAR
========================= */

.admin-sidebar{

    position:fixed;
    color:white;
    width:320px;
    height:100vh;
    background:#0f172a;
    overflow-y:auto;
    overflow-x:hidden;
    padding-bottom:80px;
    scrollbar-width:thin;

    z-index:1000;
}

/* LOGO */

.admin-logo{

    font-size:24px;

    font-weight:bold;

    margin-bottom:40px;

    line-height:1.5;

    color:#fbbf24;
}

/* MENU */

.admin-menu{

    display:flex;

    flex-direction:column;

    gap:12px;
}

.admin-menu a{

    color:white;

    text-decoration:none;

    padding:15px 18px;

    border-radius:14px;

    transition:0.3s;

    font-size:15px;

    display:flex;

    align-items:center;

    gap:12px;
}

.admin-menu a:hover{

    background:#1e293b;

    transform:translateX(5px);
}

.admin-menu a.active{

    background:#2563eb;
}

/* TOP USER BOX */

.admin-user{
	margin:0 15px;
    background:#1e293b;
    width:75%;
    padding:10px;
    border-radius:16px;
    margin-bottom:30px;
}

.admin-user h3{
    font-size:18px;
    margin-bottom:6px;
}

.admin-user p{

    color:#cbd5e1;
    font-size:13px;
}

/* MOBILE */

.mobile-topbar{

    display:none;
}

@media(max-width:900px){

    .admin-sidebar{

        width:100%;

        min-height:auto;

        position:relative;
    }

    .admin-logo{

        margin-bottom:20px;
    }

    .admin-menu{

        gap:10px;
    }
}


</style>

<div class="admin-sidebar">

<div class="admin-logo">

<br>🛡️ Admin Control Panel
</div>

<div class="admin-user">
<h3><?= $admin_name ?></h3>

<p>System Administrator</p>
</div>

<div class="admin-menu">

<a href="/university_system/admin/admin_dashboard.php" class="active">
<i class="fa-solid fa-chart-line"></i>Dashboard</a>

<a href="/university_system/admin/drivers/view_drivers.php">
<i class="fa fa-id-card"></i>  Driver Management</a>

<a href="/university_system/admin/trips/view_trips.php">
<i class="fa-solid fa-bus"></i>Trip Management</a>

<a href="/university_system/admin/lines/view_lines.php">
<i class="fa-solid fa-route"></i>Bus Lines</a>

<a href="/university_system/admin/students/view_students.php">
<i class="fa-solid fa-user-graduate"></i>Students</a>

<a href="/university_system/admin/subscriptions/bus_subscriptions.php">
<i class="fa-solid fa-clipboard-list"></i>Bus Subscriptions</a>

<a href="/university_system/admin/subscriptions/parking_subscriptions.php">
<i class="fa-solid fa-car"></i>Parking Subscriptions</a>

<a href="/university_system/admin/parking/parking_management.php">
<i class="fa-solid fa-square-parking"></i>Parking</a>

<a href="/university_system/admin/reports/reports_dashboard.php">
<i class="fa-solid fa-chart-pie"></i>Reports</a>

<a href="/university_system/admin/announcements/view_announcements.php">
<i class="fa-solid fa-bell"></i>Announcements</a>

<a href="/university_system/php/logout.php">
<i class="fa-solid fa-right-from-bracket"></i>Logout</a>
</div>
</div>