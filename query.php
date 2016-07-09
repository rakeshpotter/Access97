<?php
session_start();
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
        $isSelect = strpos($query, "select");
        $isInto = strpos($query, "into");
        if ($isSelect === FALSE || $isInto !== FALSE) {
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
        <table border="1" style="border-collapse: collapse;">
            <thead>
                <tr>
                    <?php
                    $row = $table[0];
                    foreach ($row as $col => $val) {
                        echo "<th>$col</th>";
                    }
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($table as $row) {
                    echo "<tr>";
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
