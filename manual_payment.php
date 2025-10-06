<?php
	require_once "includes/headx.php";
	if (!isset($_SESSION['admin_session']) )
	{
		$commons->redirectTo(SITE_PATH.'login.php');
	}
	require_once "includes/classes/admin-class.php";
    $admins	= new Admins($dbh);
    $id = isset($_GET[ 'customer' ])?$_GET[ 'customer' ]:'';
?>
    <style>
    body {
      font-family: Arial, sans-serif;
      margin: 40px;
      color: #000;
    }
    .header {
      display: flex;
      align-items: center;
      border-bottom: 2px solid #ccc;
      padding-bottom: 10px;
      margin-bottom: 20px;
    }
    .logo {
      width: 80px;
      margin-right: 20px;
    }
    .company-details {
      font-size: 14px;
    }
    h2 {
      text-align: center;
      text-decoration: underline;
      margin: 20px 0;
    }
    .info, .account {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 20px;
    }
    .info td, .account td {
      padding: 6px 10px;
    }
    .account {
      border: 1px solid #000;
    }
    .account td {
      border: 1px solid #000;
      text-align: left;
    }
    .amount-due {
      text-align: right;
      font-size: 18px;
      font-weight: bold;
      margin-top: 15px;
    }
    .footer {
      margin-top: 40px;
      font-size: 13px;
    }
    .highlight {
      background: #f8f8a6;
      font-weight: bold;
    }
    @media print {
      .no-print {
        display: none;
      }
    }
  </style>
<!doctype html>
<html lang="en" class="no-js">
<head>
	<meta charset=" utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href='https://fonts.googleapis.com/css?family=Open+Sans:300,400,700' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="component/css/bootstrap.css"> <!-- CSS bootstrap -->
	<link rel="stylesheet" href="component/css/bootstrap-select.min.css"> <!-- CSS bootstrap -->
	<link rel="stylesheet" href="component/css/style.css"> <!-- Resource style -->
    <link rel="stylesheet" href="component/css/reset.css"> <!-- Resource style -->
	<link rel="stylesheet" href="component/css/invoice.css"> <!-- CSS bootstrap -->
	<script src="component/js/modernizr.js"></script> <!-- Modernizr -->
	<title>Manual Payment | Cornerstone</title>
</head>
<body>
<div class="container">
        <?php
            $info = $admins->getCustomerInfo($id);
            if (isset($info) && is_object($info)) {
            $package_id = $info->package_id;
            $packageInfo = $admins->getPackageInfo($package_id);
        ?>
    <div class="row">
        <div class="brand"><img src="component/img/cs.png" alt=""></div>
        <h2>MANUAL PAYMENT</h2>
        </div>
        <div class="pull-right">Date: <?=date("j F Y")?></div><br>
        <div class="em"><b>Name   : </b> <em><?=$info->full_name?></em></div>
        <div class="em"><b>Address:</b> <em><?=$info->address ?></em></div>
        <div class="em"><b>Contact :</b> <em><?=$info->contact ?></em> </div>
        <div class="em"><b>Account Number:</b> <em><?=$info->ip_address?></em></div>
        <?php } ?>
    <div class="row">
        <table class="table table-striped table-bordered">
            <thead class="thead-inverse">
                <tr>
                    <th>Billing Month</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
            <?php
                $total = 0;
                $bill_ids = [];
                $monthArray = [];
                $unpaid_bills_exist = false;
                $bills = $admins->fetchAllIndividualBill($id);
                if (isset($bills) && sizeof($bills) > 0){
                    foreach ($bills as $bill){
                        if ($bill->status == 'Unpaid') {
                            $total += $bill->amount;
                            $monthArray[] = $bill->r_month;
                            $bill_ids[] = $bill->id;
                            $unpaid_bills_exist = true;
                        ?>
                    <tr>
                       <td><?=$bill->r_month?></td>
                       <td>₱<?=number_format($bill->amount, 2)?></td>
                    </tr>
                <?php   }
                    }
                } else { ?>
                    <tr>
                        <td colspan="2" class="text-center">No unpaid bills found.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <?php if ($unpaid_bills_exist): ?>
    <div class="row no-print">
     <form class="form-inline" action="process_manual_payment.php" method="POST">
            <input type="hidden" name="customer_id" value="<?=(isset($info->id) ? $info->id : '')?>">
            <input type="hidden" name="bill_ids" value="<?=implode($bill_ids,',')?>">
            <div class="form-group">
            <label for="months"></label>
            <select class="selectpicker" name="months[]" id="months" multiple required title="Select months">
                  <?php
                       if (!empty($monthArray)) {
                          foreach ($monthArray as $month) {
                            echo '<option value="'.$month.'" selected>'.$month.'</option>';
                          }
                       }
                    ?>
            </select>
            </div>
            <div class="form-group">
                <label for="reference_number">Reference Number</label>
                <input type="text" class="form-control" name="reference_number" id="reference_number" placeholder="Reference Number" required>
            </div>
            <div class="form-group">
            <label class="sr-only" for="total">Payment</label>
            <input type="number" class="form-control" name="total" id="total" placeholder="total" required="" value="<?=$total?>">
            </div>
            <button type="submit" class="btn btn-primary">Submit Payment</button>
        </form>
    </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
<script src="component/js/bootstrap-select.min.js"></script>
</body>
</html>