<?php
require_once "includes/headx.php";
require_once "includes/classes/admin-class.php";
$admins = new Admins($dbh);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_id = $_POST['customer_id'] ?? null;
    $employer_id = $_POST['employer_id'] ?? null;
    $package_id = $_POST['package_id'] ?? null;
    $amount = $_POST['amount'] ?? 0;
    $balance = $_POST['balance'] ?? 0;
    $months = $_POST['months'] ?? [];

    if ($customer_id && $employer_id && $package_id && !empty($months)) {
        if ($admins->addManualPayment($customer_id, $employer_id, $package_id, $amount, $months)) {
            $_SESSION['success_message'] = "Manual payment submitted successfully and is pending approval.";
        } else {
            $_SESSION['error_message'] = "Failed to submit manual payment.";
        }
    } else {
        $_SESSION['error_message'] = "Invalid data submitted.";
    }

    $commons->redirectTo(SITE_PATH . 'index.php');
} else {
    $commons->redirectTo(SITE_PATH . 'index.php');
}
?>