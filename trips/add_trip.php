<?php

include '../../php/db.php';
include '../includes/admin_session.php';
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

/*  GET BUS LINES*/

$lines = $conn->query("

SELECT *
FROM bus_line
ORDER BY line_name

");

/* =========================
   HANDLE FORM
========================= */

$success = "";
$error = "";


if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $bus_line_id = intval($_POST['bus_line_id']);

    $departure_time = $_POST['departure_time'];

    $arrival_time = $_POST['arrival_time'];

    $days = trim($_POST['days']);

    $capacity = intval($_POST['capacity']);

    /* VALIDATION */

    if(
        empty($departure_time)
        ||
        empty($arrival_time)
        ||
        empty($days)
        ||
        $capacity <= 0
    ){

        $error = "Please fill all fields correctly.";
    }

    else{

        /* =========================
           DAILY → INSERT 3 ROWS
        ========================= */

        if($days == "Daily"){

            $trip_days = ["MW", "TTH", "Daily"];
        }

        /* =========================
           NORMAL
        ========================= */

        else{

            $trip_days = [$days];
        }

        /* =========================
           INSERT
        ========================= */

        $stmt = $conn->prepare("

        INSERT INTO bus_trips(

            arrival_time,
            departure_time,
            days,
            capacity,
            bus_line_id

        )

        VALUES(?,?,?,?,?)

        ");

        foreach($trip_days as $day_type){

            $stmt->bind_param(

                "sssii",

                $arrival_time,
                $departure_time,
                $day_type,
                $capacity,
                $bus_line_id
            );

            $stmt->execute();
        }

        $_SESSION['trip_success'] ="Trip added successfully.";

header("Location: view_trips.php");

exit();
    }
}?>


<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Add Trip</title>

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
   CARD
========================= */

.form-card{

    max-width:800px;

    background:white;

    padding:40px;

    border-radius:20px;

    box-shadow:0 5px 20px rgba(0,0,0,0.08);
}

/* =========================
   TITLE
========================= */

.form-card h1{

    margin-top:0;

    margin-bottom:30px;

    color:#0f172a;
}

/* =========================
   FORM
========================= */

.form-grid{

    display:grid;

    grid-template-columns:1fr 1fr;

    gap:20px;
}

.input-group{

    display:flex;

    flex-direction:column;
}

.input-group label{

    margin-bottom:8px;

    font-weight:bold;

    color:#334155;
}

.input-group input,
.input-group select{

    padding:14px;

    border:1px solid #cbd5e1;

    border-radius:12px;

    font-size:15px;
}

/* FULL WIDTH */

.full-width{

    grid-column:1 / -1;
}

/* BUTTON */

.submit-btn{

    background:#2563eb;

    color:white;

    border:none;

    padding:15px;

    border-radius:12px;

    font-size:16px;

    font-weight:bold;

    cursor:pointer;

    transition:0.3s;
}

.submit-btn:hover{

    background:#1d4ed8;
}
.back-btn{

    display:inline-block;

    margin-bottom:20px;

    text-decoration:none;

    color:#2563eb;

    font-weight:bold;
}


/* ALERTS */

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


/* MOBILE */

@media(max-width:768px){

    .main-content{

        margin-left:0;

        padding:120px 20px 20px;
    }

    .form-grid{

        grid-template-columns:1fr;
    }

    .form-card{

        padding:25px;
    }
}

</style>

</head>

<body>

<?php include '../includes/admin_sidebar.php'; ?>
<?php include '../includes/admin_navbar.php'; ?>

<div class="main-content">
<a href="/university_system/admin/trips/view_trips.php"class="back-btn"> ← Back to Trips</a>
    <div class="form-card">

        <h1>
            🚌 Add New Trip
        </h1>

        <?php if($error): ?>

            <div class="error-box">

                <?= $error ?>

            </div>

        <?php endif; ?>

        <form method="POST" id="tripForm">

            <div class="form-grid">

                <!-- BUS LINE -->

                <div class="input-group">

                    <label>  Bus Line</label>

                    <select name="bus_line_id" required>

                        <option value=""> Select Line</option>

                        <?php while($line = $lines->fetch_assoc()): ?>

                            <option
                            value="<?= $line['id'] ?>">

                                <?= htmlspecialchars($line['line_name']) ?>

                            </option>

                        <?php endwhile; ?>

                    </select>

                </div>

                <!-- CAPACITY -->

                <div class="input-group">

                    <label>
                        Capacity
                    </label>

                    <input
                    type="number"

                    name="capacity"

                    min="1"

                    required>

                </div>

                <!-- DEPARTURE -->

                <div class="input-group">

                    <label>
                        Departure Time
                    </label>

                    <input
                    type="time"

                    name="departure_time"

                    required>

                </div>

                <!-- ARRIVAL -->

                <div class="input-group">

                    <label>
                        Arrival Time
                    </label>

                    <input
                    type="time"

                    name="arrival_time"

                    required>

                </div>

                <!-- DAYS -->

                <div class="input-group full-width">

                   <label>Operating Days</label>

<select name="days" required>

    <option value="">Select Days</option>

    <option value="Daily">Daily</option>

    <option value="MW">Monday & Wednesday (MW)</option>

    <option value="TTH">Tuesday & Thursday (TTH)</option>

</select>

                </div>

                <!-- BUTTON -->

                <div class="full-width">

                    <button
                    type="submit"

                    class="submit-btn">

                        Add Trip

                    </button>

                </div>

            </div>

        </form>

    </div>

</div>
<script>

if(window.history.replaceState){

    window.history.replaceState(
        null,
        null,
        window.location.pathname
    );
}
window.addEventListener("pageshow", function(){

    document.getElementById("tripForm").reset();

});

</script>
</body>
</html>