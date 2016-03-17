<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
if (isset($_POST['tableName']) && $_POST['tableName'] != '') {
    $tableName = $_POST['tableName'];
    include_once './scripts/msaccessDatabase.class.php';
    $db = new MsaccessDatabase("D:\\xampp\\htdocs\\Reports\\ACCWIZW.MDB");
    $db->query("select * from $tableName");
    $tableRows = $db->resultset();
} else {
    exit("No Table To Show.");
}
?>
<html>
    <head>
        <style type="text/css">
            .right{
                text-align: right;
            }
            .center{
                text-align: center;
            }
            th{
                background: #666666; color: #fff;
            }
            td{
                background: #cccccc;
            }
        </style>
    </head>
    <body>
        <?php
        /* Start The Connection */
        $user = '';
        $password = '';
        $dbname = 'D:\xampp\htdocs\Reports\ACCWIZW.MDB';
        $dbh = odbc_connect("Driver={Microsoft Access Driver (*.mdb)};Dbq=$dbname", $user, $password);


        $cols = array();
        if ($result = odbc_exec($dbh, "select * from $tableName;")) {
            for ($i = 1; $i <= odbc_num_fields($result); $i++) {
                $cols[odbc_field_name($result, $i)] = array(odbc_field_type($result, $i), odbc_field_len($result, $i));
            }
        } else {
            exit("Error in SQL Query");
        }
        /* Print The Array */
        if (!empty($cols)) {
            echo "<h4>Table : $tableName (" . count($tableRows) . ")</h4>";
            echo "<table border='1'>";
            echo "<thead><tr>";
            echo "<th>Name</th>";
            foreach ($cols as $k => $c) {
                echo "<th>$k</th>";
            }
            echo "</tr><tr>";
            echo "<th>Type</th>";
            foreach ($cols as $k => $c) {
                echo "<th>$c[0]</th>";
            }
            echo "</tr><tr>";
            echo "<th>Size</th>";
            foreach ($cols as $k => $c) {
                echo "<th>$c[1]</th>";
            }
            echo "</tr></thead>";
            $i = 1;
            foreach ($tableRows as $row) {
                echo "<tr>";
                echo "<th>" . $i++ . "</th>";
                foreach ($row as $key => $val) {
                    formatCell($val, $cols[$key]);
                }
                echo "</tr>";
            }
            echo "</table>";
        }
        ?>
    </body>
</html>
<?php

function formatCell($val, $prop) {
    $r = "<td class='right'>";
    $l = "<td>";
    $c = "<td class='center'>";

    $e = "</td>";
    switch ($prop[0]) {
        case "VARCHAR":
            if ($prop[1] <= 10) {
                echo $c . $val . $e;
            } else {
                echo $l . $val . $e;
            }break;
        case "INTEGER":
            echo $r . $val . $e;
            break;
        case "SMALLINT":
            echo $r.$val.$e;break;
        case "DATETIME":
            echo $c . date('m-d-Y', strtotime($val)) . $e;
            break;
        case "CURRENCY":
            echo $r . number_format($val, 2) . $e;
            break;
        case "BIT":
            echo $c . $val . $e;
            break;
        case "COUNTER":
            echo $c . $val . $e;
            break;
        case "LONGCHAR":
            if (strlen($val) > 25) {
                echo $l . substr($val, 0, 10) . " .... " . substr($val, strlen($val) - 10) . $e;
            } else {
                echo $l . $val . $e;
            }
            break;
        default :
            echo $l . $val . $e;
    }
}
?>