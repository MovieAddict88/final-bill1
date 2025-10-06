<?php
session_start();
require_once 'includes/customer_header.php';
require_once 'includes/classes/admin-class.php';

$admins = new Admins($dbh);

if (isset($_POST['login_code'])) {
    $login_code = $_POST['login_code'];
    $customer = $admins->fetchCustomerByLoginCode($login_code);
    if ($customer) {
        $_SESSION['customer_id'] = $customer->id;
    } else {
        header('Location: customer_login.php?error=invalid_code');
        exit();
    }
}

if (!isset($_SESSION['customer_id'])) {
    header('Location: customer_login.php');
    exit();
}

$customer_id = $_SESSION['customer_id'];
$customer = $admins->getCustomerInfo($customer_id);
$package = $admins->getPackageInfo($customer->package_id);
$payments = $admins->fetchindIvidualBill($customer_id);

?>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card mt-5">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3>Welcome, <?php echo $customer->full_name; ?></h3>
                    <a href="customer_logout.php" class="btn btn-danger">Logout</a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h4>Customer Details</h4>
                            <p><strong>Name:</strong> <?php echo $customer->full_name; ?></p>
                            <p><strong>Email:</strong> <?php echo $customer->email; ?></p>
                            <p><strong>Contact:</strong> <?php echo $customer->contact; ?></p>
                            <p><strong>Address:</strong> <?php echo $customer->address; ?></p>
                        </div>
                        <div class="col-md-6">
                            <h4>Package Information</h4>
                            <p><strong>Package:</strong> <?php echo $package->name; ?></p>
                            <p><strong>Fee:</strong> <?php echo $package->fee; ?></p>
                        </div>
                    </div>
                    <hr>
                    <h4>Payment History</h4>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Month</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($payments): ?>
                                <?php foreach ($payments as $payment): ?>
                                    <tr>
                                        <td><?php echo $payment->r_month; ?></td>
                                        <td><?php echo $payment->amount; ?></td>
                                        <td><?php echo $payment->paid ? 'Paid' : 'Unpaid'; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center">No payment history found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once 'includes/customer_footer.php';
?>