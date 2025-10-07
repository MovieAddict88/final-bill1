<?php
include 'includes/header.php';
require_once "includes/classes/admin-class.php";

$admins = new Admins($dbh);

// Check user role from session
$user_role = $_SESSION['user_role'] ?? 'admin';

if ($user_role == 'employer') {
    // Employer Dashboard
    $employer_id = $_SESSION['user_id'];
    $customers = $admins->fetchCustomersByEmployer($employer_id);
    $products = $admins->fetchProductsByEmployer($employer_id);
?>
<h3>Employer Dashboard</h3>
<style>
    .table-custom thead {
        background-color: #008080;
        color: white;
    }
    .table-custom .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
    }

    /* Styles for Mini Doughnut Charts */
    #mini-chart-container {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-around;
        gap: 20px;
        padding: 20px 10px;
        font-family: 'Poppins', sans-serif;
    }
    .mini-chart-wrapper {
        position: relative;
        text-align: center;
        width: 100%;
        padding: 15px;
        border-radius: 12px;
        background: #f9f9f9;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .mini-chart-wrapper:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    }
    .mini-chart-wrapper canvas {
        max-width: 150px;
        max-height: 150px;
        margin: 0 auto;
    }
    .chart-title {
        margin-top: 15px;
        font-size: 1rem;
        font-weight: 500;
        color: #555;
    }
</style>
<div class="row">
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">Your Assigned Customers</div>
            <div class="panel-body">
                <table class="table table-striped table-custom">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Address</th>
                            <th>Contact</th>
                            <th>Login Code</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($customers): ?>
                            <?php foreach ($customers as $customer): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($customer->full_name); ?></td>
                                    <td><?php echo htmlspecialchars($customer->address); ?></td>
                                    <td><?php echo htmlspecialchars($customer->contact); ?></td>
                                    <td><?php echo htmlspecialchars($customer->login_code); ?></td>
                                    <td><?php echo htmlspecialchars($customer->status); ?></td>
                                    <td>
                                        <a href="pay.php?customer=<?php echo $customer->id; ?>&action=bill" class="btn btn-primary btn-sm">Invoice</a>
                                        <a href="pay.php?customer=<?php echo $customer->id; ?>" class="btn btn-info btn-sm">Bill</a>
                                        <a href="manual_payment.php?customer=<?php echo $customer->id; ?>" class="btn btn-success btn-sm">Pay</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6">No customers found for this location.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">Products Availed by Your Customers</div>
            <div class="panel-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Package Name</th>
                            <th>Fee</th>
                            <th>Number of Customers</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($products): ?>
                            <?php foreach ($products as $product): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($product->name); ?></td>
                                    <td><?php echo htmlspecialchars($product->fee); ?></td>
                                    <td><?php echo htmlspecialchars($product->customer_count); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3">No products found for this location.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                Overview - Subscribers Count
            </div>
            <div class="panel-body">
                <div class="row" id="mini-chart-container">
                    <!-- Mini charts will be injected here by Chart.js -->
                </div>
            </div>
        </div>
    </div>
</div>
<?php
} else {
    // Admin Dashboard
?>
<style>
    .chart-legend {
        width: 100%;
    }
    
    .legend-items-container {
        display: grid;
        grid-template-columns: 1fr;
        gap: 8px;
        width: 100%;
    }
    
    .legend-item {
        display: flex;
        align-items: center;
        padding: 6px 0;
    }
    
    .legend-color {
        width: 20px;
        height: 10px;
        margin-right: 8px;
        border-radius: 3px;
        flex-shrink: 0;
    }
    
    .legend-text {
        flex: 1;
        min-width: 0;
        word-wrap: break-word;
    }

    /* === Tablet / Small Laptop (768px - 1024px) === */
    @media (min-width: 768px) {
        .legend-items-container {
            grid-template-columns: repeat(2, 1fr);
            max-height: 400px;
            overflow-y: auto;
        }
    }

    /* === Standard Laptop / Desktop (1024px and above) === */
    @media (min-width: 1024px) {
        .legend-items-container {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    /* === Large Desktop / Smart TV (1400px and above) === */
    @media (min-width: 1400px) {
        .legend-items-container {
            grid-template-columns: repeat(4, 1fr);
        }
        .chart-legend {
            font-size: 1.1em;
        }
    }
</style>

<div class="col-md-12">
    <div class="panel panel-default">
        <div class="panel-heading">
            Customer Connection Locations
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-8">
                    <canvas id="locationChart"></canvas>
                </div>
                <div class="col-md-4">
                    <div id="location-legend" class="chart-legend"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-md-6">
    <div class="panel panel-default">
        <div class="panel-heading">
        Balance &amp; Cash Collection Chart:
        </div>
        <div class="panel-body">
            <canvas id="myChart">
					
				</canvas>
        </div>
    </div>
</div>
<div class="col-md-6">
    <div class="panel panel-default">
        <div class="panel-heading">
        Monthly Bill Collection : 2016
        </div>
        <div class="panel-body">
            <canvas id="chart2">
					
				</canvas>
        </div>
    </div>
</div>
<?php
} // End of role check
include 'includes/footer.php';
?>
<script type="text/javascript">
    $(document).ready(function() {
        <?php if ($user_role == 'employer'): ?>
        // Plugin to draw text in the middle of a doughnut chart
        Chart.pluginService.register({
            beforeDraw: function(chart) {
                if (chart.config.options.elements.center) {
                    var ctx = chart.chart.ctx;
                    var centerConfig = chart.config.options.elements.center;
                    var fontStyle = centerConfig.fontStyle || 'Arial';
                    var txt = centerConfig.text;
                    var color = centerConfig.color || '#333';
                    var maxFontSize = centerConfig.maxFontSize || 75;
                    var sidePadding = centerConfig.sidePadding || 20;
                    var sidePaddingCalculated = (sidePadding / 100) * (chart.innerRadius * 2)
                    
                    ctx.font = "30px " + fontStyle;
                    var stringWidth = ctx.measureText(txt).width;
                    var elementWidth = (chart.innerRadius * 2) - sidePaddingCalculated;
                    var widthRatio = elementWidth / stringWidth;
                    var newFontSize = Math.floor(30 * widthRatio);
                    var elementHeight = (chart.innerRadius * 2);
                    var fontSizeToUse = Math.min(newFontSize, elementHeight, maxFontSize);

                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    var centerX = ((chart.chartArea.left + chart.chartArea.right) / 2);
                    var centerY = ((chart.chartArea.top + chart.chartArea.bottom) / 2);
                    ctx.font = fontSizeToUse + "px " + fontStyle;
                    ctx.fillStyle = color;
                    
                    // Draw text in center
                    ctx.fillText(txt, centerX, centerY - (fontSizeToUse / 4));

                    // Draw subtext
                    if(centerConfig.subText){
                        ctx.font = (fontSizeToUse / 2) + "px " + fontStyle;
                        ctx.fillText(centerConfig.subText, centerX, centerY + (fontSizeToUse / 2));
                    }
                }
            }
        });

		// New logic for mini doughnut charts
        $.ajax({
            url: "customer_status_chart_data.php",
            method: "GET",
            dataType: 'JSON',
            success: function(data) {
                const statusOrder = ['Paid', 'Unpaid', 'Prospects', 'Reject'];
                const statusColors = {
                    'Paid': { background: 'rgba(40, 167, 69, 0.1)', color: '#28a745' },
                    'Unpaid': { background: 'rgba(220, 53, 69, 0.1)', color: '#dc3545' },
                    'Prospects': { background: 'rgba(0, 123, 255, 0.1)', color: '#007bff' },
                    'Reject': { background: 'rgba(108, 117, 125, 0.1)', color: '#6c757d' }
                };

                let totalCustomers = 0;
                const statusCounts = {};
                statusOrder.forEach(status => statusCounts[status] = 0);

                data.forEach(item => {
                    if (statusCounts.hasOwnProperty(item.status)) {
                        const count = parseInt(item.count, 10);
                        statusCounts[item.status] = count;
                        totalCustomers += count;
                    }
                });

				// Handle 'Prospects' from DB if it exists, map to 'Prospect'
                const dbProspects = data.find(item => item.status === 'Prospects');
                if (dbProspects) {
                    statusCounts['Prospects'] = parseInt(dbProspects.count, 10);
                }


                const container = $('#mini-chart-container');
                container.empty();

                statusOrder.forEach(status => {
                    const count = statusCounts[status];
                    const percentage = totalCustomers > 0 ? ((count / totalCustomers) * 100).toFixed(1) : 0;
                    const colors = statusColors[status];

                    const chartId = `mini-chart-${status.toLowerCase()}`;
                    const chartHtml = `
                        <div class="col-md-3 col-sm-6 text-center">
                            <div class="mini-chart-wrapper">
                                <canvas id="${chartId}"></canvas>
                                <div class="chart-title">${status}</div>
                            </div>
                        </div>
                    `;
                    container.append(chartHtml);

                    const ctx = document.getElementById(chartId).getContext('2d');
                    new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            datasets: [{
                                data: [count, totalCustomers - count],
                                backgroundColor: [colors.color, colors.background],
                                borderWidth: 0
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutoutPercentage: 75,
                            legend: { display: false },
                            tooltips: { enabled: false },
                            animation: {
                                animateScale: true,
                                animateRotate: true
                            },
                            elements: {
                                center: {
                                    text: `${percentage}%`,
                                    subText: `${count} ${status}`,
                                    color: colors.color,
                                    fontStyle: 'Poppins',
                                    sidePadding: 20
                                }
                            }
                        }
                    });
                });
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error("AJAX Error:", textStatus, errorThrown);
                $('#mini-chart-container').html('<p class="text-center text-danger">Could not load chart data.</p>');
            }
        });
        <?php else: ?>
        var ctx = document.getElementById('myChart').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Sat', 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri'],
                datasets: [{
                    label: 'Cash Collection',
                    data: [12, 19, 3, 17, 6, 3, 20],
                    backgroundColor: "rgba(153,255,51,0.6)"
                }, {
                    label: 'Balance',
                    data: [2, 29, 5, 5, 2, 3, 10],
                    backgroundColor: "rgba(245,0,0,0.6)"
                }]
            }
        });
        var ctx = document.getElementById('chart2').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Monthly Bill Collection',
                    data: [50000, 60000, 30000, 45000, 48000, 38000, 80000, 50000, 0],
                    backgroundColor: "rgba(0,255,51,0.6)"
                }]
            }
        });

        $.ajax({
            url: "location_chart_data.php",
            method: "GET",
            dataType: 'JSON',
            success: function(data) {
                var locations = [];
                var counts = [];
                var backgroundColors = [];

                for (var i in data) {
                    locations.push(data[i].conn_location);
                    counts.push(data[i].count);
                    // Auto-generate random colors
                    var r = Math.floor(Math.random() * 255);
                    var g = Math.floor(Math.random() * 255);
                    var b = Math.floor(Math.random() * 255);
                    backgroundColors.push('rgba(' + r + ',' + g + ',' + b + ', 0.6)');
                }

                var chartdata = {
                    labels: locations,
                    datasets: [{
                        label: 'Customer Count',
                        backgroundColor: backgroundColors,
                        data: counts
                    }]
                };

                var ctx = $('#locationChart');

                var pieChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: chartdata,
                    options: {
                        responsive: true,
                        legend: {
                           display: false
                        },
                        title: {
                            display: false,
                            text: 'Customer Connection Locations'
                        },
                        animation: {
                            animateScale: true,
                            animateRotate: true
                        }
                    }
                });

                // FIXED: Generate custom legend with responsive classes
                var legendContainer = $('#location-legend');
                var legendHtml = '<div class="legend-items-container">'; // Remove inline column-count
                pieChart.data.labels.forEach(function(label, index) {
                    var color = pieChart.data.datasets[0].backgroundColor[index];
                    var value = pieChart.data.datasets[0].data[index];
                    legendHtml += '<div class="legend-item" style="display: flex; align-items: center; margin-bottom: 8px; padding: 4px 0;">' +
                                 '<span class="legend-color" style="background-color:' + color + '; width: 20px; height: 10px; display: inline-block; margin-right: 8px; border-radius: 3px; flex-shrink: 0;"></span>' +
                                 '<span class="legend-text" style="flex: 1; min-width: 0;">' + label + ': ' + value + '</span>' +
                                 '</div>';
                });
                legendHtml += '</div>';
                legendContainer.html(legendHtml);
            },
            error: function(data) {
                console.log(data);
            }
        });

        $.ajax({
            url: "chart.php",
            method: "GET",
            dataType: 'JSON',
            success: function(data) {
                console.log(data);
                var raw = [];
                var qty = [];

                for (var i in data) {
                    raw.push(data[i].name);
                    qty.push(data[i].quantity);
                }
                console.log(raw);
                console.log(qty);
                var chartdata = {
                    labels: raw,
                    datasets: [{
                        label: 'Product Stock',
                        backgroundColor: 'rgba(0,200,225,.5)',
                        borderColor: 'rgba(200, 200, 200, 0.75)',
                        hoverBackgroundColor: 'rgba(200, 200, 200, 1)',
                        hoverBorderColor: 'rgba(200, 200, 200, 1)',
                        data: qty
                    }]
                };

                var ctx = $('#myChart2');

                var barGraph = new Chart(ctx, {
                    type: 'bar',
                    data: chartdata
                });
            },
            error: function(data) {
                console.log(data);
            }
        });
        <?php endif; ?>
    });
</script>