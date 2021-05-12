<?php

class Database {
    private static $conn = null;
    private $db_host = 'localhost';
    private $db_name = '';
    private $db_user = '';
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

    
    public function prepare($query) {
        try {
            return self::$conn->prepare($query);
        } catch (PDOException $e) {
            throw new Exception("ERROR: Could not prepare the query: {$e->getMessage()}");
        }
    }
}


