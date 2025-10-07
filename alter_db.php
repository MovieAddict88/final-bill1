<?php
require_once 'config/dbconnection.php';

try {
    $dbh = new Dbconnect();
    $query = "ALTER TABLE payments ADD paymaya_name VARCHAR(255) DEFAULT NULL, ADD paymaya_number VARCHAR(255) DEFAULT NULL";
    $dbh->exec($query);
    echo "Table 'payments' altered successfully.";
} catch (PDOException $e) {
    echo "Error altering table: " . $e->getMessage();
}
?>