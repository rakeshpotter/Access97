<?php
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
        $isSELECT = strpos($query, "SELECT");
        $isSelect = strpos($query, "select");
        $isINTO = strpos($query, "INTO");
        $isInto = strpos($query, "into");
        if (($isSelect === FALSE && $isSELECT === FALSE) || ($isInto !== FALSE && $isINTO !== FALSE)) {
            $response = $db->execute();
            echo "Response = " . $response;
        } else {
            $response = $db->resultset();
            printTable($response);
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