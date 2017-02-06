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
        $isSelect = stripos($query, "select");
        $isInto = stripos($query, "into");
        $isDrop = stripos($query, "drop");
        $isAlter = stripos($query, "alter");
        $isUpdate = stripos($query, "update");
        $isInsert = stripos($query, "insert");
        $isCreate = stripos($query, "create");
        $isDelete = stripos($query, "delete");
        $isQuery = stripos($query, "@:");

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
            if ($isQuery !== FALSE) {
                $query = getQuery($query);
            }
            $db->query($query);
            if ($isQuery === FALSE && ($isDelete !== FALSE || $isSelect === FALSE || $isInto !== FALSE)) {
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

function getQuery($query) {
    $arr = explode(":", $query);
    $query = '';
    if (isset($arr[1])) {
        if ($arr[1] == 'newOrderMail') {
            $query = "SELECT oheader.ORDNUM, oheader.ORDDATE, oheader.SOURCE, order_mail.OM_MAIL_SENT, order_mail.OM_MAIL_TIME, Slsman_EXTEND.SX_SL_EMAIL, oheader.CUSTPO, ICUST.IC_NAME, Slsman_EXTEND.SX_SEND_MAIL, Slsman_EXTEND.SX_ORD_MAIL
FROM (((oheader LEFT JOIN order_mail ON oheader.ordnum = order_mail.om_ordnum) LEFT JOIN Slsman ON oheader.SLSCODE1 = Slsman.SL_CODE) LEFT JOIN Slsman_EXTEND ON Slsman.SL_CODE = Slsman_EXTEND.SX_SL_CODE) LEFT JOIN ICUST ON oheader.CUSTCODE = ICUST.IC_CODE
WHERE ((oheader.FILLED)='O') AND IsNull([order_mail].[OM_MAIL_SENT]);";
        } else if ($arr[1] == 'invoiceMail') {
            $query = "SELECT DISTINCT DEPART.D_NAME, ICUST.IC_NAME, IHEADER.CUSTPO, IHEADER.ORDNUM, ITRANS.ALLOCNUM, IHEADER.INVNUM, IHEADER.SHIPVIA, TERMS.TRMCOD, IHEADER.SOURCE, IHEADER_EXTEND.IE_TRACKNUM, IHEADER.FILLED, IHEADER.DOCTYPE, INVOICE_MAIL.IM_MAIL_SENT, ICUST_CONTACTS.CC_EMAIL, ICUST_CONTACTS.CC_SEND_MAIL, ICUST_CONTACTS.CC_INV_MAIL, Slsman_EXTEND.SX_SL_EMAIL, Slsman_EXTEND.SX_SEND_MAIL, Slsman_EXTEND.SX_INV_MAIL
FROM (((((((IHEADER INNER JOIN ICUST ON IHEADER.CUSTCODE = ICUST.IC_CODE) INNER JOIN DEPART ON IHEADER.DIVISION = DEPART.D_ID) INNER JOIN ITRANS ON IHEADER.INVNUM = ITRANS.INVNUM) INNER JOIN TERMS ON IHEADER.TRMCODE = TERMS.TRMCODE) LEFT JOIN INVOICE_MAIL ON IHEADER.INVNUM = INVOICE_MAIL.IM_INVNUM) LEFT JOIN ICUST_CONTACTS ON ICUST.IC_CODE = ICUST_CONTACTS.CC_CUST_ID) LEFT JOIN Slsman_EXTEND ON IHEADER.SLSCODE1 = Slsman_EXTEND.SX_SL_CODE) LEFT JOIN IHEADER_EXTEND ON IHEADER.INVNUM = IHEADER_EXTEND.IE_INVNUM
WHERE (((IHEADER.FILLED)<>'V') AND ((IHEADER.DOCTYPE)<>'C') AND ((INVOICE_MAIL.IM_MAIL_SENT) Is Null));";
        } else {
            echo "Wrong Parameters.";
        }
        if (isset($arr[2]) && $arr[2] == 1) {
            global $printQuery;
            $printQuery = TRUE;
        }
    }
    return $query;
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
<?php
if (isset($printQuery) && $printQuery) {
    ?>
    <script src="jquery/jquery.js" type="text/javascript"></script>
    <script>
        var q = <?= json_encode($query) ?>;
        $(document).ready(function () {
            window.parent.$("#query").val(q);
        });
    </script>
    <?php
}
?>