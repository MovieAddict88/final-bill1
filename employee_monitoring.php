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

<style>
    .page-title {
        text-align: center;
        font-size: 2.5em;
        font-weight: bold;
        color: #333;
        margin-bottom: 30px;
        text-transform: uppercase;
        letter-spacing: 1.5px;
    }

    .monitoring-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(480px, 1fr));
        gap: 25px;
        padding: 20px;
    }

    .employer-card {
        background: linear-gradient(135deg, #FF8C00, #FF4500); /* Orange gradient */
        color: white;
        border-radius: 15px;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        display: flex;
        align-items: center;
        padding: 20px;
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .employer-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3);
    }

    .employer-avatar {
        flex-shrink: 0;
        margin-right: 20px;
    }

    .employer-avatar img {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        border: 4px solid white;
        object-fit: cover;
    }

    .employer-info {
        flex-grow: 1;
    }

    .employer-info h3 {
        margin: 0;
        font-size: 1.6em;
        font-weight: bold;
    }

    .employer-info .location {
        font-size: 1.1em;
        margin-bottom: 15px;
        opacity: 0.9;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        margin-top: 15px;
    }

    .stat-item {
        background-color: rgba(255, 255, 255, 0.15);
        padding: 10px;
        border-radius: 8px;
        text-align: center;
    }

    .stat-item .label {
        font-size: 0.9em;
        font-weight: 300;
        display: block;
    }

    .stat-item .value {
        font-size: 1.4em;
        font-weight: bold;
    }

    .stat-item.total-customers {
        grid-column: 1 / -1; /* Span full width */
        background-color: rgba(0, 0, 0, 0.2);
    }

    .stat-item.paid {
        color: #C8E6C9; /* Light Green */
    }
    .stat-item.unpaid {
        color: #FFCDD2; /* Light Red */
    }

    .no-data {
        text-align: center;
        font-size: 1.5em;
        color: #777;
        margin-top: 50px;
    }

    @media (max-width: 520px) {
        .monitoring-container {
            grid-template-columns: 1fr;
        }

        .employer-card {
            flex-direction: column;
            text-align: center;
        }

        .employer-avatar {
            margin-right: 0;
            margin-bottom: 15px;
        }
    }
</style>

<h1 class="page-title">Employee Monitoring</h1>

<div class="monitoring-container">
    <?php if (!empty($monitoring_data)): ?>
        <?php foreach ($monitoring_data as $data): ?>
            <div class="employer-card">
                <div class="employer-avatar">
                    <img src="1112.jpg" alt="Employer Avatar">
                </div>
                <div class="employer-info">
                    <h3><?php echo htmlspecialchars($data->info->full_name); ?></h3>
                    <p class="location"><?php echo htmlspecialchars($data->info->location); ?></p>

                    <div class="stats-grid">
                        <div class="stat-item total-customers">
                            <span class="label">Total Customers</span>
                            <span class="value"><?php echo $data->stats['total_customers']; ?></span>
                        </div>
                        <div class="stat-item paid">
                            <span class="label">Paid Customers</span>
                            <span class="value"><?php echo $data->stats['paid_customers']; ?></span>
                        </div>
                        <div class="stat-item unpaid">
                            <span class="label">Unpaid Customers</span>
                            <span class="value"><?php echo $data->stats['unpaid_customers']; ?></span>
                        </div>
                        <div class="stat-item paid">
                            <span class="label">Month Paid Collection</span>
                            <span class="value">₱<?php echo number_format($data->stats['monthly_paid_collection'], 2); ?></span>
                        </div>
                        <div class="stat-item unpaid">
                            <span class="label">Month Unpaid Amount</span>
                            <span class="value">₱<?php echo number_format($data->stats['monthly_unpaid_collection'], 2); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="no-data">No employee data to display.</p>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>