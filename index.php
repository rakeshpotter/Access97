<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
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
        <?php
        $user = '';
        $password = '';
        $dbname = 'D:\xampp\htdocs\Reports\ACCWIZW.MDB';
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
        <script type="text/javascript">
            function showTable(t){
                document.getElementById('tableName').value=t;
                document.getElementById('form').submit();
            }
        </script>
    </body>
</html>
