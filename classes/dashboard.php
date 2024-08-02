    <?php
    class Dashboard {
        public function getUserGrowthData($timeUnit = 'day') {
            $conn = connectDB();
        
            // Adjust the query based on the time unit
            switch ($timeUnit) {
                case 'week':
                    $date_format = '%Y-%u'; // ISO week number
                    break;
                case 'month':
                    $date_format = '%Y-%m'; // YYYY-MM
                    break;
                case 'year':
                    $date_format = '%Y'; // YYYY
                    break;
                default:
                    $date_format = '%Y-%m-%d'; // Day
                    break;
            }
        
            $query = "SELECT DATE_FORMAT(date, '$date_format') as date, COUNT(*) as count 
                      FROM accounts 
                      GROUP BY DATE_FORMAT(date, '$date_format') 
                      ORDER BY date ASC";
        
            $result = $conn->query($query);
            
            $data = array();
            while ($row = $result->fetch_assoc()) {
                $data[$row['date']] = $row['count'];
            }
            
            return $data;
        }
        


        public static function generateUserGrowthGraph($timeUnit = 'day') {
            $dashboard = new Dashboard();
            $data = $dashboard->getUserGrowthData($timeUnit);
        
            // Ensure the data is in the correct format
            $formattedData = [];
            foreach ($data as $date => $count) {
                $formattedData[] = [
                    'date' => date('c', strtotime($date)),
                    'count' => $count
                ];
            }
        
            // Extract labels and values for the chart
            $labels = json_encode(array_column($formattedData, 'date'));
            $values = json_encode(array_column($formattedData, 'count'));
        
            $html = <<<HTML
            <canvas id="userGrowthChart"></canvas>
            <script>
            var ctx = document.getElementById('userGrowthChart').getContext('2d');
            var chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: $labels,
                    datasets: [{
                        label: 'Number of Users',
                        data: $values,
                        borderColor: 'rgb(75, 192, 192)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderWidth: 2,
                        pointStyle: 'circle',
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            type: 'time',
                            time: {
                                unit: '$timeUnit',
                                tooltipFormat: 'dd/MM/yyyy',
                                displayFormats: {
                                    'day': 'dd/MM',
                                    'week': 'dd/MM',
                                    'month': 'MM/yyyy',
                                    'year': 'yyyy'
                                }
                            },
                            title: {
                                display: true,
                                text: 'Date'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                borderColor: 'rgba(0,0,0,0.1)'
                            },
                            ticks: {
                                color: '#333'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            labels: {
                                color: '#333'
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0,0,0,0.7)',
                            titleColor: '#fff',
                            bodyColor: '#fff'
                        }
                    }
                }
            });
            </script>
            HTML;
        
            return $html;
        }
        

        public function getOrderGrowthData($timeUnit = 'day') {
            $conn = connectDB();
        
            // Adjust the query based on the time unit
            switch ($timeUnit) {
                case 'week':
                    $date_format = '%Y-%u'; // ISO week number
                    break;
                case 'month':
                    $date_format = '%Y-%m'; // YYYY-MM
                    break;
                case 'year':
                    $date_format = '%Y'; // YYYY
                    break;
                default:
                    $date_format = '%Y-%m-%d'; // Day
                    break;
            }
        
            $query = "SELECT DATE_FORMAT(order_date, '$date_format') as date, COUNT(*) as count 
                      FROM orders 
                      GROUP BY DATE_FORMAT(order_date, '$date_format') 
                      ORDER BY date ASC";
        
            $result = $conn->query($query);
            
            $data = array();
            while ($row = $result->fetch_assoc()) {
                $data[$row['date']] = $row['count'];
            }
            
            return $data;
        }
        
        

        public function generateOrderGrowthGraph($timeUnit = 'day') {
            $data = $this->getOrderGrowthData($timeUnit);
        
            // Ensure the data is in the correct format
            $formattedData = [];
            foreach ($data as $date => $count) {
                $formattedData[] = [
                    'date' => date('c', strtotime($date)),
                    'count' => $count
                ];
            }
        
            // Extract labels and values for the chart
            $labels = json_encode(array_column($formattedData, 'date'));
            $values = json_encode(array_column($formattedData, 'count'));
        
            $html = <<<HTML
            <canvas id="orderGrowthChart"></canvas>
            <script>
            var ctx = document.getElementById('orderGrowthChart').getContext('2d');
            var chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: $labels,
                    datasets: [{
                        label: 'Number of Orders',
                        data: $values,
                        borderColor: 'rgba(54, 162, 235, 1)',
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderWidth: 2,
                        pointStyle: 'circle',
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            type: 'time',
                            time: {
                                unit: '$timeUnit',
                                tooltipFormat: 'dd/MM/yyyy',
                                displayFormats: {
                                    'day': 'dd/MM',
                                    'week': 'dd/MM',
                                    'month': 'MM/yyyy',
                                    'year': 'yyyy'
                                }
                            },
                            title: {
                                display: true,
                                text: 'Date'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                borderColor: 'rgba(0,0,0,0.1)'
                            },
                            ticks: {
                                color: '#333'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            labels: {
                                color: '#333'
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0,0,0,0.7)',
                            titleColor: '#fff',
                            bodyColor: '#fff'
                        }
                    }
                }
            });
            </script>
            HTML;
        
            return $html;
        }
        
        

        public function getBrandSalesData() {
            $conn = connectDB();
            $query = "SELECT b.Name AS BrandName, SUM(oi.quantity) AS ProductsSold
                    FROM orderinfo oi
                    JOIN products p ON oi.Product_ID = p.Product_ID
                    JOIN brands b ON p.Brand_ID = b.Brand_ID
                    JOIN orders o ON oi.order_id = o.order_id
                    WHERE o.status = 'completed'  -- Ensure only accepted orders are considered
                    GROUP BY b.Brand_ID, b.Name
                    ORDER BY ProductsSold DESC";
            
            $result = $conn->query($query);
            
            $data = array();
            while ($row = $result->fetch_assoc()) {
                $data[$row['BrandName']] = $row['ProductsSold'];
            }
            
            return $data;
        }
        
        
        
        public function generateBrandSalesChart() {
            $data = $this->getBrandSalesData();
            
            
            $totalSales = array_sum($data);
            
            
            $percentages = array();
            foreach ($data as $brand => $sales) {
                $percentages[$brand] = ($sales / $totalSales) * 100;
            }
            
            $labels = json_encode(array_keys($percentages));
            $values = json_encode(array_values($percentages));
            
            $html = <<<HTML
            <canvas id="brandSalesChart"></canvas>
            
            <script>
            var ctx = document.getElementById('brandSalesChart').getContext('2d');
            var chart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: $labels,
                    datasets: [{
                        data: $values,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.8)',
                            'rgba(54, 162, 235, 0.8)',
                            'rgba(255, 206, 86, 0.8)',
                            'rgba(75, 192, 192, 0.8)',
                            'rgba(153, 102, 255, 0.8)',
                            'rgba(255, 159, 64, 0.8)'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'Percentage of Products Sold by Brands'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    var label = context.label || '';
                                    var value = context.raw;
                                    return label + ': ' + value.toFixed(2) + '%';
                                }
                            }
                        }
                    }
                }
            });
            </script>
            HTML;
            
            return $html;
        }
        

        public function getTotalSalesData($timeUnit = 'day') {
            $conn = connectDB();
            
            // Adjust the query based on the time unit
            switch ($timeUnit) {
                case 'week':
                    $date_format = '%Y-%u'; // ISO week number
                    break;
                case 'month':
                    $date_format = '%Y-%m'; // YYYY-MM
                    break;
                case 'year':
                    $date_format = '%Y'; // YYYY
                    break;
                default:
                    $date_format = '%Y-%m-%d'; // Day
                    break;
            }
            
            $query = "SELECT DATE_FORMAT(o.order_date, '$date_format') as date, 
                             SUM(oi.price * oi.quantity) as total_sales
                      FROM orderinfo oi
                      JOIN orders o ON oi.order_id = o.order_id
                      WHERE o.status = 'completed'
                      GROUP BY date
                      ORDER BY date ASC";
            
            $result = $conn->query($query);
            
            $data = array();
            while ($row = $result->fetch_assoc()) {
                $data[$row['date']] = $row['total_sales'];
            }
            
            return $data;
        }
        
        
        public function generateTotalSalesChart($timeUnit = 'day') {
            $data = $this->getTotalSalesData($timeUnit);
            
            // Ensure the data is in the correct format
            $formattedData = [];
            foreach ($data as $date => $total_sales) {
                $formattedData[] = [
                    'date' => date('c', strtotime($date)),
                    'total_sales' => $total_sales
                ];
            }
        
            // Extract labels and values for the chart
            $labels = json_encode(array_column($formattedData, 'date'));
            $values = json_encode(array_column($formattedData, 'total_sales'));
            
            $html = <<<HTML
            <canvas id="totalSalesChart"></canvas>
            
            
            <script>
            var ctx = document.getElementById('totalSalesChart').getContext('2d');
            var chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: $labels,
                    datasets: [{
                        label: 'Total Sales (Excluding Tax and Shipping)',
                        data: $values,
                        borderColor: 'rgb(75, 192, 192)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        tension: 0.1,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            type: 'time',
                            time: {
                                unit: '$timeUnit',
                                tooltipFormat: 'dd/MM/yyyy', // Adjusted format for tooltips
                                displayFormats: {
                                    'day': 'dd/MM',
                                    'week': 'dd/MM',
                                    'month': 'MM/yyyy',
                                    'year': 'yyyy'
                                }
                            },
                            title: {
                                display: true,
                                text: 'Date'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value, index, values) {
                                    return '$' + value.toFixed(2);
                                }
                            },
                            title: {
                                display: true,
                                text: 'Sales Amount'
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Total Sales: $' + context.parsed.y.toFixed(2);
                                }
                            }
                        },
                        legend: {
                            display: true,
                            position: 'top'
                        }
                    }
                }
            });
            </script>
            HTML;
            
            return $html;
        }
        
        
        
        



        public function getRevenueData($timeUnit = 'day') {
            $conn = connectDB();
            
            // Adjust the query based on the time unit
            switch ($timeUnit) {
                case 'week':
                    $date_format = '%Y-%u'; // ISO week number
                    break;
                case 'month':
                    $date_format = '%Y-%m'; // YYYY-MM
                    break;
                case 'year':
                    $date_format = '%Y'; // YYYY
                    break;
                default:
                    $date_format = '%Y-%m-%d'; // Day
                    break;
            }
            
            $query = "SELECT DATE_FORMAT(o.order_date, '$date_format') as date, 
                             SUM((oi.price * oi.quantity) - (oi.bought_for * oi.quantity)) as revenue
                      FROM orderinfo oi
                      JOIN orders o ON oi.order_id = o.order_id
                      WHERE o.status = 'completed'
                      GROUP BY date
                      ORDER BY date ASC";
            
            $result = $conn->query($query);
            
            $data = array();
            while ($row = $result->fetch_assoc()) {
                $data[$row['date']] = $row['revenue'];
            }
            
            return $data;
        }
        

        public function generateRevenueChart($timeUnit = 'day') {
            $data = $this->getRevenueData($timeUnit);
            
            // Ensure the data is in the correct format
            $formattedData = [];
            foreach ($data as $date => $revenue) {
                $formattedData[] = [
                    'date' => date('c', strtotime($date)),
                    'revenue' => $revenue
                ];
            }
        
            // Extract labels and values for the chart
            $labels = json_encode(array_column($formattedData, 'date'));
            $values = json_encode(array_column($formattedData, 'revenue'));
            
            $html = <<<HTML
            <canvas id="revenueChart"></canvas>
            

            
            <script>
            var ctx = document.getElementById('revenueChart').getContext('2d');
            var chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: $labels,
                    datasets: [{
                        label: 'Revenue',
                        data: $values,
                        borderColor: 'rgb(255, 99, 132)',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        tension: 0.1,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            type: 'time',
                            time: {
                                unit: '$timeUnit',
                                tooltipFormat: 'dd/MM/yyyy', // Adjusted format for tooltips
                                displayFormats: {
                                    'day': 'dd/MM',
                                    'week': 'dd/MM',
                                    'month': 'MM/yyyy',
                                    'year': 'yyyy'
                                }
                            },
                            title: {
                                display: true,
                                text: 'Date'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value, index, values) {
                                    return '$' + value.toFixed(2);
                                }
                            },
                            title: {
                                display: true,
                                text: 'Revenue Amount'
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Revenue: $' + context.parsed.y.toFixed(2);
                                }
                            }
                        },
                        legend: {
                            display: true,
                            position: 'top'
                        }
                    }
                }
            });
            </script>
            HTML;
            
            return $html;
        }
        


        private function calculatePercentageIncrease($oldValue, $newValue) {
            if ($oldValue == 0) {
                return $newValue > 0 ? 100 : 0; 
            }
            return (($newValue - $oldValue) / $oldValue) * 100;
        }
        
    
        private function getPreviousAndCurrentData($data) {
            $dates = array_keys($data);
            $currentDate = end($dates);
            $previousDate = prev($dates); 
            
            return array(
                'current' => isset($data[$currentDate]) ? $data[$currentDate] : 0,
                'previous' => isset($data[$previousDate]) ? $data[$previousDate] : 0
            );
        }
    
        private function getMetricData($metricName, $timeUnit = 'day') {
            $growthMethods = array('User', 'Order');
            if (in_array($metricName, $growthMethods)) {
                $methodName = "get{$metricName}GrowthData";
            } else {
                $methodName = "get{$metricName}Data";
            }
            
            if (method_exists($this, $methodName)) {
                $data = $this->$methodName($timeUnit);
                return $this->getPreviousAndCurrentData($data);
            }
            
            return null;
        }
        
    
        public function generateDashboardBoxes($timeUnit = 'day') {
            $metrics = array(
                'User' => 'Users',
                'Order' => 'Orders',
                'TotalSales' => 'Total Sales',
                'Revenue' => 'Total Revenue'
            );
        
            $html = '<div class="dashboard-boxes">';
            foreach ($metrics as $method => $label) {
                $data = $this->getMetricData($method, $timeUnit);
                if ($data) {
                    $increase = $this->calculatePercentageIncrease($data['previous'], $data['current']);
                    $value = ($method === 'TotalSales' || $method === 'Revenue') ? '$' . number_format($data['current'], 2) : number_format($data['current']);
                    
                    $trend = $increase >= 0 ? 'increase' : 'decrease';
                    $color = $increase >= 0 ? 'green' : 'red';
                    
                    $html .= '<div class="box">';
                    $html .= '<h3>' . $label . '</h3>';
                    $html .= '<p>' . $value . '</p>';
                    $html .= '<p class="' . $trend . '" style="color:' . $color . ';">' . ucfirst($trend) . ': ' . number_format($increase, 2) . '%</p>';
                    $html .= '</div>';
                }
            }
            $html .= '</div>';
        
            return $html;
        }
        
        
    }
    ?>