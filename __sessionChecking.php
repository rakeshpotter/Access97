<?php

session_start();

define("USERNAME", "1718d57986110b6af2dd96b59d3db416");

$superUser = '';
if (isset($_SESSION['access97'])) {
    if (USERNAME == md5($_SESSION['username'])) {
        $superUser = " Super User";
    }
    if ($_SERVER['SCRIPT_NAME'] == "/Access97/index.php") {
        echo "<a href='logout.php' style='float:right;'>Logout$superUser</a>";
    }
} else {
    $_SESSION['dbname'] = NULL;
}
?>