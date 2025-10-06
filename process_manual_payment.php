<?php
session_start();
require_once 'config/dbconnection.php';
require_once 'includes/classes/admin-class.php';

$admins = new Admins($dbh);

if (!isset($_SESSION['admin_session'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_id = $_POST['customer_id'];
    $bill_ids = explode(',', $_POST['bill_ids']);
    $months = $_POST['months'];
    $reference_number = $_POST['reference_number'];
    $total = $_POST['total'];

    // Prevent duplicate entry
    foreach ($bill_ids as $bill_id) {
        if ($admins->checkManualPaymentExists($bill_id)) {
            echo "<script>
                alert('A manual payment for this bill already exists.');
                window.close();
            </script>";
            exit();
        }
    }

    if ($admins->processManualPayment($customer_id, $bill_ids, $months, $reference_number, $total)) {
        echo "<script>
            alert('Manual payment submitted successfully. Waiting for approval.');
            window.opener.location.reload();
            window.close();
        </script>";
    } else {
        echo "<script>
            alert('Failed to submit manual payment. Please try again.');
            window.close();
        </script>";
    }
} else {
    header('Location: bills.php');
    exit();
}
?>