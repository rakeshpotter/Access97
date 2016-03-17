<?php

class MsaccessDatabase {

    private $dbname = "";
    private $connectionString;
    private $dbh;
    private $dsn;
    private $error;
    private $errorCode = 0;
    private $stmt;

    public function __construct($dbname) {
        $this->setDbname($dbname);
        // Set DSN
//       $dsn = str_replace("/", "//", 'odbc:DRIVER={Microsoft Access Driver (*.mdb)};DBQ='.$this->dbname.';');
        $dsn = str_replace("/", "//", 'odbc:DRIVER={Microsoft Access Driver (*.mdb)};DBQ=' . $dbname . ';');
        $this->setDsn($dsn);
        // Set options
        $options = array(
            PDO::ATTR_TIMEOUT => 1200,
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        );
        // Create a new PDO instanace
        try {
            $this->dbh = new PDO($dsn);
            
        }
        // Catch any errors
        catch (PDOException $e) {
            $this->error = $e->getMessage();
            $this->errorCode = $e->getCode();
        }
    }

    public function query($query) {
        $this->stmt = $this->dbh->prepare($query);
    }

    public function bind($param, $value, $type = null) {
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }
        $this->stmt->bindValue($param, $value, $type);
    }

    public function execute() {
        return $this->stmt->execute();
    }

    public function resultset() {
        $this->execute();
        return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function single() {
        $this->execute();
        return $this->stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function rowCount() {
        return $this->stmt->rowCount();
    }

    public function lastInsertId() {
        return $this->dbh->lastInsertId();
    }

    public function beginTransaction() {
        return $this->dbh->beginTransaction();
    }

    public function endTransaction() {
        return $this->dbh->commit();
    }

    public function cancelTransaction() {
        return $this->dbh->rollBack();
    }

    public function debugDumpParams() {
        return $this->stmt->debugDumpParams();
    }

    public function closeConnection() {
        return $this->dbh = null;
    }

    public function showError() {
        return $this->error;
    }

    public function showErrorCode() {
        return $this->errorCode;
    }

    public function setDbname($dbname) {
        $this->dbname = $dbname;
    }

    public function getDbname() {
        return $this->dbname;
    }

    public function setDsn($dsn) {
        $this->dsn = $dsn;
    }

    public function getDsn() {
        return $this->dsn;
    }

}

?>