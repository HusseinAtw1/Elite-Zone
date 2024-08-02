<?php 
include 'adminheader.php';
include '../classes/dashboard.php';

// Default time unit
$timeUnit = isset($_POST['timeUnit']) ? $_POST['timeUnit'] : 'day';
$dashboard = new Dashboard();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-moment@latest"></script>

    <style>
        .chart-container {
            height: 400px;
        }
        .abc {
            height: 400px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .dashboard-boxes {
            justify-content: center;
            display: flex;
            gap: 20px;
            margin: 1rem 0 1rem 0;
        }

        .box {
            text-align: center;
            background: #f9f9f9;
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 5px;
            width: 200px;
        }

        .box h3 {
            margin: 0;
            font-size: 16px;
            color: #333;
        }

        .box p {
            margin: 5px 0;
        }

        .increase {
            color: green;
            font-weight: bold;
        }

        .form-container {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

    <div class="container my-4">
        <form method="post" action="" class="row g-3 align-items-center">
            <div class="col-auto">
                <label for="timeUnit" class="form-label">Select Time Unit:</label>
            </div>
            <div class="col-auto">
                <select name="timeUnit" id="timeUnit" class="form-select" onchange="this.form.submit()">
                    <option value="day" <?php echo $timeUnit === 'day' ? 'selected' : ''; ?>>Day</option>
                    <option value="week" <?php echo $timeUnit === 'week' ? 'selected' : ''; ?>>Week</option>
                    <option value="month" <?php echo $timeUnit === 'month' ? 'selected' : ''; ?>>Month</option>
                    <option value="year" <?php echo $timeUnit === 'year' ? 'selected' : ''; ?>>Year</option>
                </select>
            </div>
        </form>
    </div>

    <!-- Dashboard Boxes -->
    <?php echo $dashboard->generateDashboardBoxes(); ?>

    <!-- Charts -->
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-6 mb-4">
                <div class="chart-container">
                    <?php echo $dashboard->generateUserGrowthGraph($timeUnit); ?>
                </div>
            </div>
            <div class="col-12 col-lg-6 mb-4">
                <div class="chart-container">
                    <?php echo $dashboard->generateOrderGrowthGraph($timeUnit); ?>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-12 col-lg-6 mb-4">
                <div class="chart-container">
                    <?php echo $dashboard->generateRevenueChart($timeUnit); ?>
                </div>
            </div>
            
            <div class="col-12 col-lg-6 mb-4">
                <div class="chart-container">
                    <?php echo $dashboard->generateTotalSalesChart($timeUnit); ?>
                </div>
            </div>
        </div>
    </div>

    <div class="abc">
        <?php echo $dashboard->generateBrandSalesChart(); ?>
    </div>
</body>
</html>
