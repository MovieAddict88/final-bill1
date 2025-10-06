<?php
require_once "includes/headx.php";
require_once "includes/classes/admin-class.php";
$admins = new Admins($dbh);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_id = $_POST['customer_id'] ?? null;
    $employer_id = $_POST['employer_id'] ?? null;
    $package_id = $_POST['package_id'] ?? null;
    $amount = $_POST['amount'] ?? 0;
    $months = $_POST['months'] ?? [];

    if ($customer_id && $employer_id && $package_id && !empty($months)) {
        $package_info = $admins->getPackageInfo($package_id);
        $package_fee = $package_info ? $package_info->fee : 0;

        if ($admins->addManualPayment($customer_id, $employer_id, $package_id, $amount, $months, $package_fee)) {
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