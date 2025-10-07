<?php
require_once 'config/dbconnection.php';
require_once 'includes/classes/admin-class.php';

try {
    $db = new Dbconnect();
    $conn = $db;
    $admins = new Admins($conn);
    $notifications = $admins->getPendingPayments();

    header('Content-Type: application/json');
    echo json_encode($notifications);
} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
}
?>