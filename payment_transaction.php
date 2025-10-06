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
        body { font-family: Arial, sans-serif; font-size: 14px; }
        .header { text-align: center; }
        .header h2 { margin: 0; }
        .details { margin: 20px 0; }
        table { border-collapse: collapse; width: 100%; }
        table, th, td { border: 1px solid black; }
        th, td { padding: 8px; text-align: center; }
        .amount-due { font-weight: bold; margin-top: 20px; }
        .footer { margin-top: 40px; font-size: 12px; }
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
	<title>Invoice | Netway</title>
</head>
<body>
<div class="container">
        <?php
            $info = $admins->getCustomerInfo($id); 
            if (isset($info) && sizeof($info) > 0) {
            $package_id = $info->package_id;
            $packageInfo = $admins->getPackageInfo($package_id);
        ?>
    <div class="row">
       <div class="brand"><img src="component/img/cs.png" alt=""></div>
       <div class="header">
    <h2>CORNERSTONE INNOVATE TECH SOL</h2>
    <p>#11 Caco Apartment Mambog IV, Bacoor Cavite<br>
       Brix Bryan S. Villas-Prog.<br>
       NON-VAT Reg. TIN: 434-028-840-000</p>
</div>
        <h2><strong>STATEMENT OF ACCOUNT</h2><div
        </div>
        <div class="pull-right">Date: <?=date("j F Y")?></div><br></div>
        <p><strong>Subject:</strong> PAYMENT TRANSACTION</p>
        <div class="em"><b>Name   : </b> <em><?=$info->full_name?></em></div>
        <div class="em"><b>Address:</b> <em><?=$info->address ?></em></div>
        <div class="em"><b>Contact :</b> <em><?=$info->contact ?></em> </div>
        <div class="em"><b>Account Number:</b> <em><?=$info->ip_address?></em></div>
        <?php } ?>
        <div class="row">
        <table class="table table-striped table-bordered">
            <thead class="thead-inverse">
                <tr>
                    <th>Plan </th>
                    <th>25Mbps</th>
                    <th>Amount</th>
                    <th>Balance</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $bills = $admins->fetchPaymentSlip($id);
                    $amount = $_POST['amount'];
                    if (isset($bills) && sizeof($bills) > 0){
                        
                ?>
                <tr>
                     <td><?=date("F ", strtotime("-6 month"))?></td>  <!-- last month -->
                 <td><?=date("F ", strtotime("-5 month"))?></td>  <!-- last month -->
                       <td><?=$bills->bill_amount?></td>
                        <td>₱ 0.00</td>
                    </tr>
                     <td><?=date("F ", strtotime("-5 month"))?></td>  <!-- last month -->
                 <td><?=date("F ", strtotime("-4 month"))?></td>  <!-- last month -->
                       <td><?=$bills->bill_amount?></td>
                          <td>₱ 0.00</td>
                    <tr>
                     <td><?=date("F ", strtotime("-4 month"))?></td>  <!-- last month -->
                 <td><?=date("F ", strtotime("-3 month"))?></td>  <!-- last month -->
                 
                       <td><?=$bills->bill_amount?></td>
                          <td>₱ 0.00</td>
                    <tr>
                     <td><?=date("F ", strtotime("-3 month"))?></td>  <!-- last month -->
                 <td><?=date("F ", strtotime("-2 month"))?></td>  <!-- last month -->
                       <td><?=$bills->bill_amount?></td>
                          <td>₱ 0.00</td>
                    </tr>
                     <td><?=date("F ", strtotime("-2 month"))?></td>  <!-- last month -->
                     <td><?=date("F ", strtotime("-1 month"))?></td>  <!-- last month -->
                       <td><?=$bills->bill_amount?></td>
                          <td>₱ 0.00</td>
                    </tr>
                    <td><?=date("F ", strtotime("-1 month"))?></td>  <!-- last month -->
                 <td><?=date("F ")?></td>                         <!-- present month -->
                       <<td>₱<?=number_format($balance, 2)?></td>
                   </tr>
            </tbody>
           <tfoot>
                 
                    <tr>
                        <th>In Words</td>
                        <th colspan="3"><?=getToText($bills->bill_amount)?> Pesos.</th>
                    </tr>
                </tfoot>
            <?php 
                } ?>
        </table>
    <p><strong>Contact us</strong><br>
    FB Page | Customer Service: 0951-6651142 | Billing Department: 0985-3429675</p>
    <p><strong>CORNERSTONE INNOVATE TECH SOL</strong></p>
  </div>
   <div class="printbutton hide-on-small-only pull-left"><a href="#" onClick="javascript:window.print()">Print</a></div>
    <div class="footer">
</div>
</body>
<?php include 'includes/footer.php'; ?>
<script src="component/js/bootstrap-select.min.js"></script>
<script>
    $('#months').on('changed.bs.select', function (e) {
        console.log(this.value);
      });
</script>