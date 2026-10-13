<?php
session_start();

header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

if (!isset($_SESSION['university_id'])) {
    header("Location: student_auth.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Find Bus</title>
    <link rel="stylesheet" href="/university_system/css/style.css">
</head>

<body class="portal-page">
<?php include 'navbar.php'; ?>

<div class="portal-content">

<div class="auth-box">

<h2>Select Your Village</h2>

<form action="../php/bus_recommend.php" method="POST">
<!-- scroll container -->
<div style="max-height:350px; overflow-y:auto; padding:10px; border:1px solid #ddd;">

<div class="village-list">

<label class="village-item">
    <input type="radio" name="village" value="Aitaroun">
    <span>Aitaroun</span>
</label>

<label class="village-item">
    <input type="radio" name="village" value="Ainata">
    <span>Ainata</span>
</label>

<label class="village-item">
    <input type="radio" name="village" value="Bint Jbeil">
    <span>Bint Jbeil</span>
</label>

<label class="village-item">
    <input type="radio" name="village" value="Yater">
    <span>Yater</span>
</label>

<label class="village-item">
    <input type="radio" name="village" value="Kafra">
    <span>Kafra</span>
</label>

<hr>

<label class="village-item">
    <input type="radio" name="village" value="Touline">
    <span>Touline</span>
</label>

<label class="village-item">
    <input type="radio" name="village" value="Deir Ntar">
    <span>Deir Ntar</span>
</label>

<label class="village-item">
    <input type="radio" name="village" value="Tebnine">
    <span>Tebnine</span>
</label>

<label class="village-item">
    <input type="radio" name="village" value="Srifa">
    <span>Srifa</span>
</label>

<hr>

<label class="village-item">
    <input type="radio" name="village" value="Qana">
    <span>Qana</span>
</label>

<label class="village-item">
    <input type="radio" name="village" value="Sadiqine">
    <span>Sadiqine</span>
</label>

<label class="village-item">
    <input type="radio" name="village" value="Maarakeh">
    <span>Maarakeh</span>
</label>

<hr>

<label class="village-item">
    <input type="radio" name="village" value="Chehabiyeh" required>
    <span>Chehabiyeh</span>
</label>

<label class="village-item">
    <input type="radio" name="village" value="Borj Rahhal">
    <span>Borj Rahhal</span>
</label>

<label class="village-item">
    <input type="radio" name="village" value="Abbassiyeh">
    <span>Abbassiyeh</span>
</label>

</div>
<br>

<button class="auth-btn" type="submit">Find My Bus</button>

</form>

</div>
</div>

<script>
document.querySelector("form").addEventListener("submit", function(e) {
    const selected = document.querySelector('input[name="village"]:checked');

    if (!selected) {
        e.preventDefault();
        alert("Please select a village first");
    }
});

const radios = document.querySelectorAll('input[name="village"]');
const btn = document.getElementById("submitBtn");

radios.forEach(r => {
    r.addEventListener("change", () => {
        btn.disabled = false;
    });
});


</script>

</body>
</html>