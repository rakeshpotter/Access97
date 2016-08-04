<?php
@session_start();
require_once './__sessionChecking.php';
$dbname = NULL;

if (isset($_SESSION['dbname']) && $_SESSION['dbname'] != '') {
    $path = $_SESSION['dbname'];
    if (is_file($path)) {
        $dbname = $path;
        $_SESSION['dbname'] = $dbname;
    }
} else {
    echo "Session Failed. Please Select File Provide MDB Path.";
    exit();
}

if (isset($_POST['query']) && $_POST['query'] != '' && $dbname != NULL) {
//    echo $dbname;
    $query = $_POST['query'];
    include_once './scripts/msaccessDatabase.class.php';
    try {
        $db = new MsaccessDatabase($dbname);
        $db->query($query);
        $isSelect = stripos($query, "select");
        $isInto = stripos($query, "into");
        $isDrop = stripos($query, "drop");
        $isAlter = stripos($query, "alter");
        $isUpdate = stripos($query, "update");
        $isInsert = stripos($query, "insert");
        $isCreate = stripos($query, "create");
        $isDelete = stripos($query, "delete");
        
        if ((USERNAME != md5($_SESSION['username'])) &&
                ($isInto !== FALSE ||
                $isDrop !== FALSE ||
                $isAlter !== FALSE ||
                $isUpdate !== FALSE ||
                $isInsert !== FALSE ||
                $isCreate !== FALSE ||
                $isDelete !== FALSE)) {

            echo "Your Are Not Allowed To Alter OR Update The Database.";
            if (!isset($_SESSION['editAttempt'])) {
                $_SESSION['editAttempt'] = 1;
            }
            if ($_SESSION['editAttempt'] < 3) {
//                $_SESSION['editAttempt'] ++;
            } else {
                session_destroy();
            }
        } else {
            if ($isSelect === FALSE || $isInto !== FALSE) {
                $response = $db->execute();
                echo "Response = " . $response;
            } else {
                $response = $db->resultset();
                printTable($response);
            }
        }
    } catch (Exception $e) {
        echo "Exception Coutch.<br>";
        print_r($e);
    }
} else {
    exit("BAD REQUEST.");
}

function printTable($table) {
    if (is_array($table) && isset($table[0])) {
        ?>
        No. Of Records : <?= count($table) ?>
        <table border="1" style="border-color: #000;">
            <thead>
                <tr>
                    <?php
                    $row = $table[0];
                    echo "<th style='min-width:40px;'>#</th>";
                    foreach ($row as $col => $val) {
                        echo "<th>$col</th>";
                    }
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php
                $i = 1;
                foreach ($table as $row) {
                    echo "<tr>";
                    echo "<th>" . $i++ . "</th>";
                    foreach ($row as $val) {
                        echo "<td>$val</td>";
                    }
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
        <?php
    } else {
        echo "No Record Found.";
    }
}
?>
<style>
    th{
        background-color: #666666;
        color: #ffffff;
    }
    td{
        background: #cccccc;
    }
</style>