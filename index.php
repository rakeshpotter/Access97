<?php
define("PASSWORD", $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME']);

require_once './__sessionChecking.php';

$user = '';
$password = '';
$dbname = NULL;
if (isset($_POST['dbname'])) {
    if (!isset($_SESSION['access97'])) {
        $password = $_POST['dbname'];
        if ($password == PASSWORD) {
            $_SESSION['access97'] = PASSWORD;
            $_SESSION['username'] = $_POST['username'];
            header("location: index.php");
        } else {
            echo 'Authentication Failed.';
        }
    } else {
        $path = $_POST['dbname'];
        if (is_file($path)) {
            $dbname = $path;
            $_SESSION['dbname'] = $dbname;
        } else {
            echo "Incorrect Path : '$path'";
        }
    }
} else {
    if (isset($_SESSION['dbname']) && $_SESSION['dbname'] != '') {
        $path = $_SESSION['dbname'];
        if (is_file($path)) {
            $dbname = $path;
            $_SESSION['dbname'] = $dbname;
        }
    }
}
if (!isset($_SESSION['access97'])) {
    $autoComplete = 'autocomplete="off"';
    $usernameBox = TRUE;
} else {
    $autoComplete = '';
    $usernameBox = FALSE;
}
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
        <script src="jquery/jquery.js"></script>
        <style type="text/css">
            div.tableList{
                height: 90%; width: 20%;
                overflow-y: scroll;
                background-color: #cccccc;
                float: left;
                border: #000 solid thin;
            }
            div.tableList a{
                display: block;
                padding-left: 10px;
                text-decoration: none;
                color: #000;
            }
            div.tableList a:hover{
                background-color: #ccffff;
            }
            div.tableList a.active{
                background-color: #999999;
                color: #ffffff;
            }
            div.table{
                float: left;
                width: 79%; height: 90%;
            }
            div.table div{
                float: left;
                width: 100%; height: 10%;
            }
            div.table iframe{
                float: left;
                width: 100%; height: 90%;
                overflow: scroll;
            }
        </style>
    </head>
    <body>

        <div>
            <form method="post">
                <input <?= $autoComplete ?> type="text" name="dbname" value="<?= @$path ?>" style="width:600px;"/><input type="submit" value="Change MDB Path."/><br>
                <?php
                if ($usernameBox) {
                    echo '<input type="password" name="username" value="username"/>';
                }
                ?>
            </form>
            <a href="index.php"><b>MDB Path: </b><?= $dbname ?></a>
        </div>
        <?php
        if ($dbname != NULL) {
            $dbh = odbc_connect("Driver={Microsoft Access Driver (*.mdb)};Dbq=$dbname", $user, $password);

            $result = odbc_tables($dbh);
            ?>
            <div class="tableList">
                <?php
                $i = 0;
                while (odbc_fetch_row($result)) {
                    if (odbc_result($result, "TABLE_TYPE") == "TABLE" || 0) {
                        $i++;
                        echo "<a href='#' id='" . odbc_result($result, 'TABLE_NAME') . "' onclick='showTable(\"" . odbc_result($result, 'TABLE_NAME') . "\")'>$i) " . odbc_result($result, "TABLE_NAME") . "</a>";
//                echo "<br>" . odbc_result_all($result);
                    }
                }
                ?>
            </div>
            <div class="table">
                <iframe name="tableIframe" src=""></iframe>
                <div>
                    <form id="queryForm" method="post" action="query.php" target="tableIframe">
                        <table  style="width:100%;">
<!--                            <tr>
                                <td>
                                    <input type="text" id="text1"/>
                                    <input type="text" id="text2"/>
                                    <input type="text" id="text3"/>
                                    <input type="text" id="text4"/>
                                    <input type="text" id="text5"/>
                                </td>
                                <td>
                                </td>
                                <td>
                                </td>
                            </tr>-->
                            <tr>
                                <td style="width:90%; height: 100%;">
                                    <textarea rows="5" name="query" id="query" style="width:100%; background: #cccccc;"><?= @$query ?></textarea>
                                </td>
<!--                                <td style="width:10%;">
                                    <input type="button" value="Fill" onclick="executeFillQuery()"/>
                                </td>-->
                                <td style="width:10%;">
                                    <input type="button" value="Execute" onclick="executeQuery()"/>
                                </td>
                            </tr>
                        </table>
                    </form>
                </div>
            </div>
            <form id="form" method="post" action="table.php" target="tableIframe" style="display: none;">
                <input type="hidden" value="" id="tableName" name="tableName"/>
            </form>
            <?php
        }
        ?>
        <script type="text/javascript">
            $(document).ready(function () {
                $('#query').focusin(function () {
                    $(this).select();
                });
            });
            function showTable(t) {
                $('.active').removeClass('active');
                $('a#' + t).addClass('active');
                var q = "SELECT * FROM " + t;
//                var q = "DROP TABLE " + t;
                $('#query').val(q);
                $('#tableName').val(t);
                $('#form').submit();
            }
            function executeQuery() {
                $('#queryForm').submit();
            }
            function executeFillQuery() {
                var v1 = $('#text1').val();
                var v2 = $('#text2').val();
                var v3 = $('#text3').val();
                var v4 = $('#text4').val();
                var v5 = parseInt($('#text5').val());
                $('#text5').val(v5 + 1);
                var q = "INSERT INTO SIZERATIO (SIZECODE,SIZERUN,SIZE,RATIO,SEQUENCE)\
                        VALUES ('" + v1 + "','" + v2 + "','" + v3 + "'," + v4 + "," + v5 + ")";
                $('#query').val(q);
            }
        </script>
    </body>
</html>
