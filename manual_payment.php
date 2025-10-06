<?php
require_once "includes/headx.php";
if (!isset($_SESSION['admin_session']) || $_SESSION['user_role'] != 'employer') {
    $commons->redirectTo(SITE_PATH . 'login.php');
}
require_once "includes/classes/admin-class.php";
$admins = new Admins($dbh);

$customer_id = isset($_GET['customer']) ? $_GET['customer'] : '';
if (empty($customer_id)) {
    echo "Customer not found.";
    exit;
}

// Fetch data
$customer_info = $admins->getCustomerInfo($customer_id);
if (!$customer_info) {
    echo "Customer not found.";
    exit;
}
$packages = $admins->getPackages();
$bills = $admins->fetchAllIndividualBill($customer_id);

$unpaid_months = [];
$package_fee = 0;

$package_info = $admins->getPackageInfo($customer_info->package_id);
if ($package_info) {
    $package_fee = $package_info->fee;
}

if ($bills) {
    foreach ($bills as $bill) {
        if ($bill->status == 'Unpaid') {
            $unpaid_months[] = $bill->r_month;
        }
    }
}

// Generate a list of the next 12 months for advance payment
$future_months = [];
$current_date = new DateTime();
for ($i = 0; $i < 12; $i++) {
    $future_months[] = $current_date->format('F Y');
    $current_date->modify('+1 month');
}

// Combine unpaid and future months, ensuring no duplicates
$available_months = array_unique(array_merge($unpaid_months, $future_months));

?>
<!doctype html>
<html lang="en" class="no-js">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:300,400,700' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="component/css/bootstrap.css">
    <link rel="stylesheet" href="component/css/bootstrap-select.min.css">
    <link rel="stylesheet" href="component/css/style.css">
    <link rel="stylesheet" href="component/css/reset.css">
    <script src="component/js/modernizr.js"></script>
    <title>Manual Payment | Cornerstone</title>
</head>

<body>
    <?php include 'includes/header.php'; ?>
    <div class="container" style="margin-top: 20px;">
        <h2>Manual Payment</h2>
        <hr>

        <div class="customer-info">
            <h4>Customer Details</h4>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($customer_info->full_name); ?></p>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($customer_info->address); ?></p>
            <p><strong>Contact:</strong> <?php echo htmlspecialchars($customer_info->contact); ?></p>
            <p><strong>Account Number:</strong> <?php echo htmlspecialchars($customer_info->ip_address); ?></p>
        </div>
        <hr>

        <form action="process_manual_payment.php" method="POST" class="form-horizontal">
            <input type="hidden" name="customer_id" value="<?php echo $customer_id; ?>">
            <input type="hidden" name="employer_id" value="<?php echo $_SESSION['user_id']; ?>">

            <div class="form-group">
                <label for="package" class="col-sm-2 control-label">Package</label>
                <div class="col-sm-10">
                    <select name="package_id" id="package" class="form-control">
                        <option value="">Select Package</option>
                        <?php foreach ($packages as $pkg) : ?>
                            <option value="<?php echo $pkg->id; ?>" data-price="<?php echo $pkg->fee; ?>" <?php echo ($pkg->id == $customer_info->package_id) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($pkg->name); ?> (₱<?php echo htmlspecialchars($pkg->fee); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="amount" class="col-sm-2 control-label">Amount Paid</label>
                <div class="col-sm-10">
                    <input type="number" name="amount" id="amount" class="form-control" value="<?php echo $package_fee; ?>" step="0.01" required>
                </div>
            </div>

            <div class="form-group">
                <label for="balance" class="col-sm-2 control-label">Balance</label>
                <div class="col-sm-10">
                    <input type="number" name="balance" id="balance" class="form-control" readonly>
                </div>
            </div>

            <div class="form-group">
                <label for="months" class="col-sm-2 control-label">Months Paid</label>
                <div class="col-sm-10">
                    <select name="months[]" id="months" class="form-control selectpicker" multiple required data-live-search="true" title="Select months...">
                        <?php foreach ($available_months as $month) : ?>
                            <option value="<?php echo $month; ?>" <?php echo in_array($month, $unpaid_months) ? 'selected' : ''; ?>>
                                <?php echo $month; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" class="btn btn-primary">Submit Payment</button>
                </div>
            </div>
        </form>
    </div>

    <?php include 'includes/footer.php'; ?>
    <script src="component/js/bootstrap-select.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const packageSelect = document.getElementById('package');
            const amountInput = document.getElementById('amount');
            const balanceInput = document.getElementById('balance');
            const monthsSelect = document.getElementById('months');

            function calculateBalance() {
                const selectedOption = packageSelect.options[packageSelect.selectedIndex];
                const packagePrice = parseFloat(selectedOption.getAttribute('data-price')) || 0;
                const amountPaid = parseFloat(amountInput.value) || 0;

                let balance = 0;
                // Calculate balance only if a package is selected
                if (packagePrice > 0) {
                    balance = packagePrice - amountPaid;
                }

                balanceInput.value = balance > 0 ? balance.toFixed(2) : '0.00';
            }

            function updateAmount() {
                const selectedOption = packageSelect.options[packageSelect.selectedIndex];
                const packagePrice = parseFloat(selectedOption.getAttribute('data-price')) || 0;
                amountInput.value = packagePrice.toFixed(2);
                calculateBalance();
            }

            packageSelect.addEventListener('change', updateAmount);
            amountInput.addEventListener('input', calculateBalance);

            // Initial calculation on page load
            calculateBalance();
        });
    </script>
</body>

</html>