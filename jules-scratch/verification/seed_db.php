<?php
// Database credentials
$dbhost     = 'localhost';
$dbuser     = 'root';
$dbpassword = 'password';
$dbname     = 'kp_db';

try {
    $dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpassword);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // --- Create Customer ---
    $login_code = 'customer_test_code';
    $customer_sql = "INSERT INTO `customers` (`id`, `full_name`, `nid`, `address`, `conn_location`, `email`, `ip_address`, `conn_type`, `package_id`, `contact`, `login_code`, `employer_id`)
                     VALUES (1, 'Test Customer', '123456789', '123 Test St', 'Test Location', 'test@example.com', '127.0.0.1', 'Fiber', 1, '555-1234', ?, NULL)
                     ON DUPLICATE KEY UPDATE full_name = VALUES(full_name), login_code = VALUES(login_code)";
    $stmt = $dbh->prepare($customer_sql);
    $stmt->execute([$login_code]);
    $customer_id = $dbh->lastInsertId();
    if ($customer_id == 0) {
        $customer_id = 1; // If customer already exists, we know the ID is 1
    }
    echo "Test customer created/updated successfully with ID: $customer_id and login_code: $login_code\n";


    // --- Create Payment (Full) ---
    $payment_full_sql = "INSERT INTO `payments` (`id`, `customer_id`, `r_month`, `amount`, `balance`, `status`)
                         VALUES (1, ?, 'September', 800, 0.00, 'Paid')
                         ON DUPLICATE KEY UPDATE amount = VALUES(amount), balance = VALUES(balance), status = VALUES(status)";
    $stmt = $dbh->prepare($payment_full_sql);
    $stmt->execute([$customer_id]);
    echo "Full payment record created/updated successfully.\n";


    // --- Create Payment (Partial) ---
    $payment_partial_sql = "INSERT INTO `payments` (`id`, `customer_id`, `r_month`, `amount`, `balance`, `status`)
                            VALUES (2, ?, 'October', 800, 300.00, 'Unpaid')
                            ON DUPLICATE KEY UPDATE amount = VALUES(amount), balance = VALUES(balance), status = VALUES(status)";
    $stmt = $dbh->prepare($payment_partial_sql);
    $stmt->execute([$customer_id]);
    echo "Partial payment record created/updated successfully.\n";


} catch (PDOException $e) {
    die("DB ERROR: ". $e->getMessage());
}
?>