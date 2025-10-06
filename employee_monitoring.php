<?php
include 'includes/header.php';
require_once "includes/classes/admin-class.php";

// Ensure only admins can access this page
if ($_SESSION['user_role'] !== 'admin') {
    // Redirect non-admin users to the dashboard
    echo '<script>window.location.href = "index.php";</script>';
    exit;
}

$admins = new Admins($dbh);
$monitoring_data = $admins->getEmployerMonitoringData();
?>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

<style>
    body {
        font-family: 'Poppins', sans-serif;
    }

    .page-title {
        text-align: center;
        font-size: 2.2em;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 20px;
        padding-top: 0;
        padding-bottom: 0;
    }

    .monitoring-container {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        padding: 0 20px 20px;
        justify-content: center;
    }

    .employer-card {
        background-color: #ffffff;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        display: flex;
        flex-direction: column;
        width: 350px;
        padding: 25px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .employer-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
    }

    .employer-header {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
    }

    .employer-avatar img {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        object-fit: cover;
        margin-right: 15px;
    }

    .employer-details {
        flex-grow: 1;
    }

    .employer-details h3 {
        margin: 0;
        font-size: 1.4em;
        font-weight: 600;
        color: #34495e;
    }

    .employer-details .location {
        font-size: 0.95em;
        color: #7f8c8d;
        margin: 0;
    }

    .stats-container {
        width: 100%;
    }

    .stat-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid #ecf0f1;
    }

    .stat-item:last-child {
        border-bottom: none;
    }

    .stat-label {
        font-size: 0.9em;
        color: #555;
    }

    .stat-value {
        font-size: 1.1em;
        font-weight: 600;
    }

    .total-customers .stat-value { color: #3498db; } /* Blue */
    .paid-customers .stat-value { color: #2ecc71; } /* Green */
    .unpaid-customers .stat-value { color: #e74c3c; } /* Red */
    .monthly-paid .stat-value { color: #27ae60; } /* Darker Green */
    .monthly-unpaid .stat-value { color: #c0392b; } /* Darker Red */

    /* Colorful indicators */
    .stat-item {
        border-left: 5px solid transparent;
        padding-left: 15px;
    }

    .total-customers { border-left-color: #3498db; }
    .paid-customers { border-left-color: #2ecc71; }
    .unpaid-customers { border-left-color: #e74c3c; }
    .monthly-paid { border-left-color: #27ae60; }
    .monthly-unpaid { border-left-color: #c0392b; }


    .no-data {
        text-align: center;
        font-size: 1.2em;
        color: #777;
        margin-top: 50px;
        width: 100%;
    }

    @media (max-width: 768px) {
        .monitoring-container {
            padding: 10px;
        }
        .employer-card {
            width: 100%;
        }
    }
</style>

<h1 class="page-title">Employee Monitoring</h1>

<div class="monitoring-container">
    <?php if (!empty($monitoring_data)): ?>
        <?php foreach ($monitoring_data as $data): ?>
            <div class="employer-card">
                <div class="employer-header">
                    <div class="employer-avatar">
                        <img src="1112.jpg" alt="Employer Avatar">
                    </div>
                    <div class="employer-details">
                        <h3><?php echo htmlspecialchars($data->info->full_name); ?></h3>
                        <p class="location"><?php echo htmlspecialchars($data->info->location); ?></p>
                    </div>
                </div>

                <div class="stats-container">
                    <div class="stat-item total-customers">
                        <span class="stat-label">Total Customers</span>
                        <span class="stat-value"><?php echo $data->stats['total_customers']; ?></span>
                    </div>
                    <div class="stat-item paid-customers">
                        <span class="stat-label">Paid Customers</span>
                        <span class="stat-value"><?php echo $data->stats['paid_customers']; ?></span>
                    </div>
                    <div class="stat-item unpaid-customers">
                        <span class="stat-label">Unpaid Customers</span>
                        <span class="stat-value"><?php echo $data->stats['unpaid_customers']; ?></span>
                    </div>
                    <div class="stat-item monthly-paid">
                        <span class="stat-label">Month Paid Collection</span>
                        <span class="stat-value">₱<?php echo number_format($data->stats['monthly_paid_collection'], 2); ?></span>
                    </div>
                    <div class="stat-item monthly-unpaid">
                        <span class="stat-label">Month Unpaid Amount</span>
                        <span class="stat-value">₱<?php echo number_format($data->stats['monthly_unpaid_collection'], 2); ?></span>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="no-data">No employee data to display.</p>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>