<?php

session_start();
define("PASSWORD", $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME']);

if (isset($_SESSION['access97']) && $_SESSION['access97'] == PASSWORD) {
    echo "<a href='logout.php' style='float:right;'>Logout</a>";
} else {
    $_SESSION['dbname'] = NULL;
}
?>