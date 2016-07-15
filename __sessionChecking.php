<?php

session_start();
define("PASSWORD", md5('Cresc07109'));

if (isset($_SESSION['access97']) && $_SESSION['access97'] == PASSWORD) {
    echo "<a href='logout.php'>Logout</a>";
} else {
    $_SESSION['dbname'] = NULL;
}
?>