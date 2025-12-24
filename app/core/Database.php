<?php
// app/core/Database.php

class Database {
    private $host = DB_HOST;
    private $user = DB_USER;
    private $pass = DB_PASS;
    private $dbname = DB_NAME;

    private $dbh; // Database Handler
    private $error;
    private $stmt; // Statement

    public function __construct() {
        // Set DSN (Data Source Name)
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname;
        // Set options
        $options = [
            PDO::ATTR_PERSISTENT => true, // Persistent connection
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Throw exceptions on errors
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4" // For UTF-8 support (Persian characters)
        ];

        // Create a new PDO instance
        try {
            
            $this->dbh = new PDO($dsn, $this->user, $this->pass, $options);
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            // In development, you might echo the error. In production, log it.
            if (APP_ENV === 'development') {
                die("Database Connection Error: " . $this->error);
            } else {
                die("A database error occurred. Please try again later.");
            }
        }
    }

    // Prepare statement with query
    public function query($sql) {
        $this->stmt = $this->dbh->prepare($sql);
    }

    // Bind values to prepared statement
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

    // Execute the prepared statement
    public function execute() {
        return $this->stmt->execute();
    }

    // Get result set as array of objects
    public function fetchAll() {
        $this->execute();
        return $this->stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // Get single record as object
    public function fetch() {
        $this->execute();
        return $this->stmt->fetch(PDO::FETCH_OBJ);
    }

    // Get row count
    public function rowCount() {
        return $this->stmt->rowCount();
    }

    // Get last inserted ID
    public function lastInsertId() {
        return $this->dbh->lastInsertId();
    }
    

public function beginTransaction() {
    return $this->dbh->beginTransaction();
}

/**
 * تایید و ذخیره نهایی تغییرات تراکنش
 */
public function commit() {
    return $this->dbh->commit();
}

/**
 * لغو تغییرات و بازگرداندن به حالت اولیه در صورت بروز خطا
 */
public function rollBack() {
    return $this->dbh->rollBack();
}
}