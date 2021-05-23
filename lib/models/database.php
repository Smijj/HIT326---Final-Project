<?php

class Database {
    private static $conn = null;
    private $db_host = 'localhost';
    private $db_name = 'aat_database';
    private $db_user = 'root';
    private $db_pwd = '';

    /**
     * Creates a database connection.
     */
    public function __construct() {
        try {
            if (self::$conn == null) {
                self::$conn = new PDO('mysql:host='.$this->db_host.";dbname=".$this->db_name, $this->db_user, $this->db_pwd);
                self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$conn->exec('SET NAMES "utf8"');
            }
        } catch (PDOException $e) {
            throw new Exception("ERROR: could not connect to database: {$e->getMessage()}");
        }
    }

    
    public function prepare($sql) {
        try {
            return self::$conn->prepare($sql);
        } catch (PDOException $e) {
            throw new Exception("ERROR: Could not prepare the query: {$e->getMessage()}");
        }
    }
    
    public function query($sql) {
        try {
            return self::$conn->query($sql);
        } catch (PDOException $e) {
            // Only throws exception if "PDO::ATTR_ERRMODE" = "PDO::ERRMODE_EXCEPTION".
            throw new DBException("ERROR: Could not query database: {$e->getMessage()}");
        }
    }
}


