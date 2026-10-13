<?php
session_start();

session_unset();
session_destroy();

/* NO CACHE */
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
?>

<script>
window.location.replace("/university_system/index.html");
</script>