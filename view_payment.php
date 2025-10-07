<?php
session_start();
require_once 'includes/header.php';
require_once 'includes/classes/admin-class.php';

$admins = new Admins($dbh);

if (!isset($_SESSION['admin_session'])) {
    header('Location: login.php');
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: bills.php');
    exit();
}

$payment_id = $_GET['id'];
$payment = $admins->getPaymentById($payment_id);
$customer = $admins->getCustomerInfo($payment->customer_id);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['approve'])) {
        if ($admins->approvePayment($payment_id)) {
            echo "<script>window.opener.location.reload(); window.close();</script>";
            exit();
        } else {
            $error_message = "Failed to approve payment.";
        }
    } elseif (isset($_POST['reject'])) {
        if ($admins->rejectPayment($payment_id)) {
            echo "<script>window.opener.location.reload(); window.close();</script>";
            exit();
        } else {
            $error_message = "Failed to reject payment.";
        }
    }
}
?>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card mt-5">
                <div class="card-header">
                    <h3>Payment Details</h3>
                </div>
                <div class="card-body">
                    <?php if (isset($error_message)): ?>
                        <div class="alert alert-danger"><?php echo $error_message; ?></div>
                    <?php endif; ?>
                    <h4>Customer Information</h4>
                    <p><strong>Name:</strong> <?php echo $customer->full_name; ?></p>
                    <p><strong>Email:</strong> <?php echo $customer->email; ?></p>
                    <p><strong>Contact:</strong> <?php echo $customer->contact; ?></p>
                    <hr>
                    <h4>Payment Information</h4>
                    <p><strong>Month:</strong> <?php echo $payment->r_month; ?></p>
                    <?php
                        // Determine the correct amounts based on payment type
                        if ($payment->r_month === 'Initial Payment') {
                            // For initial payments, 'total_amount' comes from the package fee, and 'amount' is the paid amount.
                            $total_amount = $payment->total_amount;
                            $paid_amount = $payment->amount;
                            $balance = $total_amount - $paid_amount;
                        } else {
                            // For regular bills, 'amount' is the total, and 'balance' is the remaining amount.
                            $total_amount = $payment->amount;
                            $balance = $payment->balance;
                            $paid_amount = $total_amount - $balance;
                        }
                    ?>
                    <p><strong>Total Amount:</strong> <?php echo htmlspecialchars(number_format($total_amount, 2)); ?></p>

                    <?php // Only show the breakdown for partial payments
                    if ($paid_amount > 0 && $paid_amount < $total_amount): ?>
                        <p><strong>Paid Amount:</strong> <?php echo htmlspecialchars(number_format($paid_amount, 2)); ?></p>
                        <p><strong>Balance:</strong> <?php echo htmlspecialchars(number_format($balance, 2)); ?></p>
                    <?php endif; ?>
                    <p><strong>Payment Method:</strong> <?php echo $payment->payment_method; ?></p>
                    <?php if ($payment->payment_method === 'Manual' && !empty($payment->employer_id)):
                        $employer_name = $admins->getEmployerNameById($payment->employer_id);
                        if ($employer_name): ?>
                            <p><strong>Paid by Employer:</strong> <?php echo htmlspecialchars($employer_name); ?></p>
                        <?php endif;
                    endif; ?>
                    <?php if ($payment->payment_method === 'GCash'): ?>
                        <p><strong>GCash Name:</strong> <?php echo $payment->gcash_name; ?></p>
                        <p><strong>GCash Number:</strong> <?php echo $payment->gcash_number; ?></p>
                    <?php endif; ?>
                    <?php if ($payment->payment_method === 'PayMaya'): ?>
                        <p><strong>PayMaya Name:</strong> <?php echo $payment->paymaya_name; ?></p>
                        <p><strong>PayMaya Number:</strong> <?php echo $payment->paymaya_number; ?></p>
                    <?php endif; ?>
                    <p><strong>Reference Number:</strong> <?php echo $payment->reference_number; ?></p>
                    <?php if ($payment->screenshot && file_exists($payment->screenshot)): ?>
                        <p><strong>Screenshot:</strong></p>
                        <img src="<?php echo $payment->screenshot; ?>" alt="Transaction Screenshot" class="img-fluid">
                    <?php endif; ?>
                    <hr>
                    <form action="" method="POST">
                        <button type="submit" name="approve" class="btn btn-success">Approve</button>
                        <button type="submit" name="reject" class="btn btn-danger">Reject</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>