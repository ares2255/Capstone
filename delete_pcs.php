<?php
include "config/db.php";

// TRUNCATE is better than DELETE because it resets the PC IDs back to 1
$conn->query("TRUNCATE TABLE pcs");

header("Location: settings.php?msg=reset_complete");
exit();
?>