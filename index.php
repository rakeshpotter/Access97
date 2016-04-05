<?php
session_start();
$user = '';
$password = '';
$dbname = NULL;
if (isset($_POST['dbname'])) {
    $path = $_POST['dbname'];
    if (is_file($path)) {
        $dbname = $path;
        $_SESSION['dbname'] = $dbname;
    } else {
        echo "Incorrect Path : '$path'";
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
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
        <script src="jquery/jquery.js"></script>
        <style type="text/css">
            div.tableList{
                height: 650px; width: 300px;
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
            div.table iframe{
                float: left;
                width: 1000px; height: 650px;
                overflow: scroll;
            }
        </style>
    </head>
    <body>

        <div>
            <form method="post">
                <input type="text" name="dbname" value="<?= @$path ?>"/><input type="submit" value="Change MDB Path."/>
            </form>
            <b>MDB Path: </b><?= $dbname ?>&nbsp;&nbsp;&nbsp;
        </div>
        <?php
        if ($dbname != NULL) {
            $dbh = odbc_connect("Driver={Microsoft Access Driver (*.mdb)};Dbq=$dbname", $user, $password);

            $result = odbc_tables($dbh);
            ?>
            <div class="tableList">
                <?php
                while (odbc_fetch_row($result)) {
                    if (odbc_result($result, "TABLE_TYPE") == "TABLE" || 0) {
                        echo "<a href='#' onclick='showTable(\"" . odbc_result($result, 'TABLE_NAME') . "\")'>" . odbc_result($result, "TABLE_NAME") . "</a>";
//                echo "<br>" . odbc_result_all($result);
                    }
                }
                ?>
            </div>
            <div class="table">
                <iframe name="tableIframe" src=""></iframe>
            </div>
            <form id="form" method="post" action="table.php" target="tableIframe" style="display: none;">
                <input type="hidden" value="" id="tableName" name="tableName"/>
            </form>
            <?php
        }
        ?>
        <script type="text/javascript">

            function showTable(t) {
                document.getElementById('tableName').value = t;
                document.getElementById('form').submit();
            }
        </script>
    </body>
</html>
