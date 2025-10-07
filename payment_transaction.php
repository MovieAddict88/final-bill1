<?php
session_start();
require_once 'config/dbconnection.php';
require_once 'includes/customer_header.php';
require_once 'includes/classes/admin-class.php';

$admins = new Admins($dbh);

if (!isset($_SESSION['customer_id'])) {
    header('Location: customer_login.php');
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: customer_dashboard.php');
    exit();
}

$payment_id = $_GET['id'];
$payment = $admins->getPaymentById($payment_id); // This function needs to be created in admin-class.php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_method = $_POST['payment_method'];
    $reference_number = $_POST['reference_number'];
    $gcash_name = isset($_POST['gcash_name']) ? $_POST['gcash_name'] : null;
    $gcash_number = isset($_POST['gcash_number']) ? $_POST['gcash_number'] : null;
    $paymaya_name = isset($_POST['paymaya_name']) ? $_POST['paymaya_name'] : null;
    $paymaya_number = isset($_POST['paymaya_number']) ? $_POST['paymaya_number'] : null;
    $screenshot = isset($_FILES['screenshot']) ? $_FILES['screenshot'] : null;

    if ($admins->processPayment($payment_id, $payment_method, $reference_number, $gcash_name, $gcash_number, $paymaya_name, $paymaya_number, $screenshot)) {
        header('Location: customer_dashboard.php?payment=success');
        exit();
    } else {
        $error_message = "Failed to process payment. Please try again.";
    }
}

?>

<style>
    #payment_method {
        padding-left: 35px;
        background-repeat: no-repeat;
        background-position: 5px center;
        background-size: 25px;
    }
</style>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card mt-5">
                <div class="card-header">
                    <h3>Process Payment</h3>
                </div>
                <div class="card-body">
                    <?php if (isset($error_message)): ?>
                        <div class="alert alert-danger"><?php echo $error_message; ?></div>
                    <?php endif; ?>
                    <h4>Payment for <?php echo $payment->r_month; ?></h4>
                    <p><strong>Amount:</strong> <?php echo $payment->amount; ?></p>
                    <form action="" method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="payment_method">Payment Method</label>
                            <select name="payment_method" id="payment_method" class="form-control" required>
                                <option value="GCash" data-icon="assets/gcash.png">GCash</option>
                                <option value="PayMaya" data-icon="assets/paymaya.png">PayMaya</option>
                            </select>
                        </div>
                        <div id="gcash_fields">
                            <div class="form-group">
                                <label for="gcash_name">GCash Name</label>
                                <input type="text" name="gcash_name" id="gcash_name" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="gcash_number">GCash Number</label>
                                <input type="text" name="gcash_number" id="gcash_number" class="form-control">
                            </div>
                        </div>
                        <div id="paymaya_fields" style="display: none;">
                            <div class="form-group">
                                <label for="paymaya_name">PayMaya Name</label>
                                <input type="text" name="paymaya_name" id="paymaya_name" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="paymaya_number">PayMaya Number</label>
                                <input type="text" name="paymaya_number" id="paymaya_number" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="reference_number">Reference Number</label>
                            <input type="text" name="reference_number" id="reference_number" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="screenshot">Transaction Screenshot</label>
                            <input type="file" name="screenshot" id="screenshot" class="form-control-file" accept="image/*">
                        </div>
                        <button type="submit" class="btn btn-primary">Submit Payment</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const paymentMethodSelect = document.getElementById('payment_method');

    const setBackgroundIcon = () => {
        const selectedOption = paymentMethodSelect.options[paymentMethodSelect.selectedIndex];
        const iconUrl = selectedOption.getAttribute('data-icon');
        if (iconUrl) {
            paymentMethodSelect.style.backgroundImage = `url('${iconUrl}')`;
        } else {
            paymentMethodSelect.style.backgroundImage = 'none';
        }
    };

    paymentMethodSelect.addEventListener('change', function () {
        var gcashFields = document.getElementById('gcash_fields');
        var paymayaFields = document.getElementById('paymaya_fields');
        if (this.value === 'GCash') {
            gcashFields.style.display = 'block';
            paymayaFields.style.display = 'none';
            document.getElementById('gcash_name').required = true;
            document.getElementById('gcash_number').required = true;
            document.getElementById('paymaya_name').required = false;
            document.getElementById('paymaya_number').required = false;
        } else if (this.value === 'PayMaya') {
            gcashFields.style.display = 'none';
            paymayaFields.style.display = 'block';
            document.getElementById('gcash_name').required = false;
            document.getElementById('gcash_number').required = false;
            document.getElementById('paymaya_name').required = true;
            document.getElementById('paymaya_number').required = true;
        }
        setBackgroundIcon();
    });

    // Trigger the change event on page load to set the initial state
    paymentMethodSelect.dispatchEvent(new Event('change'));
</script>

<?php
require_once 'includes/customer_footer.php';
?>