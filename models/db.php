<?php


/**
 * Creates and returns a database connection.
 * Returns false on error.
 *
 * @return PDO
 */
function db_connect() {
    $db_host = 'localhost';
    $db_name = '';
    $db_user = '';
    $db_pwd = '';

    try {
        $conn = new PDO('mysql:host='.$db_host.";dbname=".$db_name, $db_user, $db_pwd);
    } catch (PDOException $e) {
        return false;
    } finally {
        return $conn;
    }
}