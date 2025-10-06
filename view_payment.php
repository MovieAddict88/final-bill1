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
                    <p><strong>Amount:</strong> <?php echo $payment->amount; ?></p>
                    <p><strong>Payment Method:</strong> <?php echo $payment->payment_method; ?></p>
                    <?php if ($payment->payment_method === 'GCash'): ?>
                        <p><strong>GCash Name:</strong> <?php echo $payment->gcash_name; ?></p>
                        <p><strong>GCash Number:</strong> <?php echo $payment->gcash_number; ?></p>
                    <?php endif; ?>
                    <p><strong>Reference Number:</strong> <?php echo $payment->reference_number; ?></p>
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