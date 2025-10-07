<?php
// This script is for one-time use to set up data for the verification script.
// It will be deleted after use.

require_once 'config/dbconnection.php';
require_once 'includes/classes/admin-class.php';

$dbh = new Dbconnect();
$admins = new Admins($dbh);

// 1. Create a dummy customer to associate the payment with.
$customer_id = $admins->addCustomer(
    'Verification Customer',
    'verify-nid-' . time(), // Needs to be unique
    '123 Verification St',
    'Test Location',
    'verify@test.com',
    1, // package_id
    '127.0.0.1',
    'Fiber',
    '555-0123',
    'verify-login-' . time(), // Needs to be unique
    null
);

if (!$customer_id) {
    echo "Error: Could not create a customer.";
    exit;
}

// 2. Create a dummy payment record for the new customer.
$month = date('Y-m');
$amount = 1500;
$query = "INSERT INTO payments (customer_id, r_month, amount, status) VALUES (?, ?, ?, 'Unpaid')";
$stmt = $dbh->prepare($query);

if ($stmt->execute([$customer_id, $month, $amount])) {
    // 3. Return the ID of the newly created payment.
    echo $dbh->lastInsertId();
} else {
    echo "Error: Could not create a payment record.";
}

?>