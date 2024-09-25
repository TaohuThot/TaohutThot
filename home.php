<?php
// เรียกใช้งาน check_login.php ซึ่งมีฟังก์ชัน checkLogin()
include 'inc/check_login.php';

// เรียกใช้งานฟังก์ชัน checkLogin เพื่อป้องกันการเข้าถึงโดยไม่ได้ล็อกอิน
checkLogin();
if (!isset($_SESSION['role'])) {
    // ถ้าไม่มีการล็อกอินหรือไม่มี role ใน session, ส่งผู้ใช้กลับไปที่หน้า login
    header("Location: index.php");
    exit();
}

include 'db.php'; // เรียกใช้ไฟล์เชื่อมต่อฐานข้อมูล
include 'header.php';
include 'inc/links.php';
include 'inc/functions.php';

// ดึงข้อมูลจำนวนคำสั่งซื้อ
$sql_order_count = "SELECT COUNT(*) AS order_count FROM orders";
$result_order_count = $conn->query($sql_order_count);
$order_count = $result_order_count->fetch_assoc()['order_count'];

// ดึงข้อมูลจำนวนสินค้า
$sql_product_count = "SELECT COUNT(*) AS product_count FROM products";
$result_product_count = $conn->query($sql_product_count);
$product_count = $result_product_count->fetch_assoc()['product_count'];

// ดึงข้อมูลจำนวนลูกค้า
$sql_customer_count = "SELECT COUNT(*) AS customer_count FROM customers";
$result_customer_count = $conn->query($sql_customer_count);
$customer_count = $result_customer_count->fetch_assoc()['customer_count'];

// ดึงข้อมูลจำนวนพนักงาน
$sql_employee_count = "SELECT COUNT(*) AS employee_count FROM employees";
$result_employee_count = $conn->query($sql_employee_count);
$employee_count = $result_employee_count->fetch_assoc()['employee_count'];

$employee_id = $_SESSION['employee_id']; // สมมติว่าคุณเก็บ employee_id ใน session
$sql_employee_name = "SELECT name FROM employees WHERE employee_id = '$employee_id'";
$result_employee_name = $conn->query($sql_employee_name);
if ($result_employee_name && $result_employee_name->num_rows > 0) {
    $employee_name = $result_employee_name->fetch_assoc()['name'];
} else {
    $employee_name = "พนักงานไม่ทราบชื่อ";
}

// ดึงข้อมูลสินค้าขายดี 10 อันดับ
$sql_best_selling_products = "
    SELECT products.product_name, SUM(order_items.quantity) AS total_quantity
    FROM order_items
    JOIN products ON order_items.product_id = products.product_id
    GROUP BY products.product_name
    ORDER BY total_quantity DESC
    LIMIT 10";
$result_best_selling_products = $conn->query($sql_best_selling_products);

// เก็บข้อมูลในอาร์เรย์เพื่อใช้หลายครั้ง
$best_selling_data = [];
$best_selling_products = [];
$best_selling_quantities = [];

if ($result_best_selling_products->num_rows > 0) {
    while ($row = $result_best_selling_products->fetch_assoc()) {
        $best_selling_data[] = $row; // เก็บข้อมูลในอาร์เรย์
        $best_selling_products[] = $row['product_name'];
        $best_selling_quantities[] = $row['total_quantity'];
    }
}
// ดึงข้อมูลสินค้าที่ใกล้หมด
$threshold = 30; // กำหนดเกณฑ์ปริมาณต่ำสุดที่ถือว่าใกล้หมด
$sql_low_stock_products = "
    SELECT product_name, quantity
    FROM products
    WHERE quantity <= $threshold
    ORDER BY quantity ASC
    LIMIT 10";
$result_low_stock_products = $conn->query($sql_low_stock_products);

// ดึงข้อมูลยอดขายรายวันสำหรับสัปดาห์ปัจจุบัน
$start_date = date('Y-m-d', strtotime('last sunday')); // วันแรกของสัปดาห์
$dates = [];
$sales_data = array_fill(0, 7, 0); // เริ่มต้นด้วยค่า 0 สำหรับ 7 วัน

// เก็บวันที่ในรูปแบบเดิมสำหรับการจับคู่กับฐานข้อมูล
for ($i = 0; $i < 7; $i++) {
    $dates[] = date('Y-m-d', strtotime($start_date . " +$i day"));
}

// ดึงข้อมูลยอดขายประจำสัปดาห์
$sql_weekly_sales = "
    SELECT DATE(o.order_date) as order_date, SUM(oi.total_price) as total_sales 
    FROM orders o
    JOIN order_items oi ON o.order_id = oi.order_id
    WHERE o.order_date >= '$start_date' 
    AND o.order_date < DATE_ADD('$start_date', INTERVAL 7 DAY)
    AND oi.is_cancelled = 0  /* กรองเฉพาะออเดอร์ที่ไม่ถูกยกเลิก */
    GROUP BY DATE(o.order_date)";
$result_weekly_sales = $conn->query($sql_weekly_sales);

// อัปเดตยอดขายสำหรับแต่ละวันในสัปดาห์
if ($result_weekly_sales->num_rows > 0) {
    while ($row = $result_weekly_sales->fetch_assoc()) {
        $date_index = array_search($row['order_date'], $dates); // ค้นหาวันที่ตรงกับ $dates
        if ($date_index !== false) {
            $sales_data[$date_index] = $row['total_sales']; // อัปเดตยอดขายตามวันที่
        }
    }
}

// แปลงวันที่ใน $dates เป็นภาษาไทยสำหรับการแสดงผล
foreach ($dates as $index => $date) {
    $dates[$index] = formatThaiDate(strtotime($date), true);
}

// ดึงข้อมูลยอดขายรายเดือน
$months = [];
$monthly_sales_data = array_fill(0, 12, 0); // เริ่มต้นด้วยค่า 0 สำหรับ 12 เดือน

for ($i = 1; $i <= 12; $i++) {
    $months[] = date('Y-m', mktime(0, 0, 0, $i, 1, date('Y')));
}

// ดึงข้อมูลยอดขายรายเดือนของทั้งปี
$sql_monthly_sales = "
    SELECT DATE_FORMAT(o.order_date, '%Y-%m') as order_month, SUM(oi.total_price) as total_sales
    FROM orders o
    JOIN order_items oi ON o.order_id = oi.order_id
    WHERE o.order_date >= DATE_FORMAT(NOW(),'%Y-01-01')
    AND oi.is_cancelled = 0  /* กรองเฉพาะออเดอร์ที่ไม่ถูกยกเลิก */
    GROUP BY DATE_FORMAT(o.order_date, '%Y-%m')";
$result_monthly_sales = $conn->query($sql_monthly_sales);

if ($result_monthly_sales->num_rows > 0) {
    while ($row = $result_monthly_sales->fetch_assoc()) {
        $month_index = array_search($row['order_month'], $months);
        if ($month_index !== false) {
            $monthly_sales_data[$month_index] = $row['total_sales'];
        }
    }
}

// แปลงชื่อเดือนและปีใน $months เป็นภาษาไทยสำหรับการแสดงผล
foreach ($months as $index => $month) {
    $year = intval(date('Y', strtotime($month))) + 543; // แปลงปีเป็น พ.ศ.
    $thai_month = formatThaiMonth(intval(date('n', strtotime($month))));
    $months[$index] = "$thai_month $year"; // รวมเดือนและปีเข้าด้วยกัน
}

// ข้อมูลยอดขายรายสัปดาห์ของเดือนปัจจุบัน
$year = date('Y');
$month = date('m');

$weeks = getWeeklyRanges($year, $month);
$weekly_sales_labels = [];
$weekly_sales_data_monthly = [];

// ดึงข้อมูลยอดขายแต่ละสัปดาห์ในเดือน
foreach ($weeks as $week) {
    $sql = "
        SELECT SUM(oi.total_price) as total_sales
        FROM orders o
        JOIN order_items oi ON o.order_id = oi.order_id
        WHERE o.order_date BETWEEN '{$week['start']}' AND '{$week['end']}' 
        AND oi.is_cancelled = 0"; // กรองเฉพาะออเดอร์ที่ไม่ถูกยกเลิก

    $result = $conn->query($sql);
    $row = $result->fetch_assoc();

    $start_thai = formatThaiDate(strtotime($week['start']), true); // วันที่เริ่มต้นภาษาไทย
    $end_thai = formatThaiDate(strtotime($week['end']), true); // วันที่สิ้นสุดภาษาไทย

    $weekly_sales_labels[] = "$start_thai - $end_thai " . (date('Y', strtotime($week['start'])) + 543); // แสดงช่วงวันที่
    $weekly_sales_data_monthly[] = $row['total_sales'] ? $row['total_sales'] : 0; // ถ้าไม่มียอดขายให้ใส่เป็น 0
}

$conn->close(); // ปิดการเชื่อมต่อฐานข้อมูล
?>
<!DOCTYPE html>
<html>

<head>
    <title>หน้าแรก</title>
</head>

<body>
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-chevron p-3 rounded -3">
                <li class="breadcrumb-item active" aria-current="page">
                    Home
                </li>
            </ol>
        </nav>
        <div class="d-flex justify-content-between align-items-center">
            <h2>Dashboard</h2>
        </div>
        <?php if ($_SESSION['role'] === 'user'): ?>
            <div class="text-center mt-4 mb-4">
                <h2>สวัสดีคุณ <?php echo $employee_name; ?></h2>
            </div>
        <?php endif; ?>
        <div class="row mb-3 mt-4">
            <div class="col-xl-3 col-sm-6 col-12">
                <a href="order.php" class="card text-decoration-none text-dark">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <span class="h6 font-semibold text-muted text-sm d-block mb-2">คำสั่งซื้อ</span>
                                <span class="h3 font-bold mb-0"><?php echo $order_count; ?> รายการ</span>
                            </div>
                            <div class="col-auto">
                                <div>
                                    <img src="image/shopping-cart.png" class="bi d-block mx-auto mb-1 icon" width="20"
                                        height="20">
                                </div>
                            </div>
                        </div>
                        <div class="mt-2 mb-0 text-sm">
                            <span class="text-nowrap text-xs text-muted">รายการ</span>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-xl-3 col-sm-6 col-12">
                <a href="product.php" class="card text-decoration-none text-dark">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <span class="h6 font-semibold text-muted text-sm d-block mb-2">สินค้า</span>
                                <span class="h3 font-bold mb-0"><?php echo $product_count; ?> รายการ</span>
                            </div>
                            <div class="col-auto">
                                <div>
                                    <img src="image/shopping-basket.png" class="bi d-block mx-auto mb-1 icon" width="20"
                                        height="20">
                                </div>
                            </div>
                        </div>
                        <div class="mt-2 mb-0 text-sm">
                            <span class="text-nowrap text-xs text-muted">รายการ</span>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-xl-3 col-sm-6 col-12">
                <a href="customer.php" class="card text-decoration-none text-dark">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <span class="h6 font-semibold text-muted text-sm d-block mb-2">ลูกค้า</span>
                                <span class="h3 font-bold mb-0"><?php echo $customer_count; ?> คน</span>
                            </div>
                            <div class="col-auto">
                                <div>
                                    <img src="image/user (1).png" class="bi d-block mx-auto mb-1 icon" width="20"
                                        height="20">
                                </div>
                            </div>
                        </div>
                        <div class="mt-2 mb-0 text-sm">
                            <span class="text-nowrap text-xs text-muted">คน</span>
                        </div>
                    </div>
                </a>
            </div>
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <div class="col-xl-3 col-sm-6 col-12">
                    <a href="employee.php" class="card text-decoration-none text-dark">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <span class="h6 font-semibold text-muted text-sm d-block mb-2">พนักงาน</span>
                                    <span class="h3 font-bold mb-0"><?php echo $employee_count; ?> คน</span>
                                </div>
                                <div class="col-auto">
                                    <div>
                                        <img src="image/user (1).png" class="bi d-block mx-auto mb-1 icon" width="20"
                                            height="20">
                                    </div>
                                </div>
                            </div>
                            <div class="mt-2 mb-0 text-sm">
                                <span class="text-nowrap text-xs text-muted">คน</span>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="row gx-3 mb-3 mt-4">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body container text-center">
                            <div class="row justify-content-end">
                                <div class="col-7">
                                    <h3 class="text-center">สินค้าขายดี</h3>
                                </div>
                                <div class="col-3">
                                    <button id="showListGroup" class="btn btn-outline-secondary custom-btn me-2">
                                        <i class="bi-list"></i>
                                    </button>

                                    <button id="showChart" class="btn btn-outline-secondary custom-btn">
                                        <i class="bi-pie-chart"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- รายการสินค้าขายดี -->
                            <div id="bestSellingList">
                                <?php if (count($best_selling_data) > 0): ?>
                                    <ul class="list-group list-group-flush">
                                        <?php foreach ($best_selling_data as $product): ?>
                                            <li class="list-group-item d-flex justify-content-between">
                                                <span><?php echo $product['product_name']; ?></span>
                                                <span><?php echo $product['total_quantity']; ?> ชิ้น</span>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <p>ไม่มีข้อมูลสินค้าขายดี</p>
                                <?php endif; ?>
                            </div>

                            <!-- กราฟวงกลมแสดงสินค้าขายดี -->
                            <div id="bestSellingChart" class="mt-4" style="display: none;">
                                <canvas id="myPieChart" width="400" height="400"></canvas>
                            </div>

                            <script>
                                // ข้อมูลสำหรับกราฟ
                                var productNames = <?php echo json_encode($best_selling_products); ?>;
                                var productQuantities = <?php echo json_encode($best_selling_quantities); ?>;

                                // คำนวณยอดรวมของสินค้าทั้งหมด
                                var totalQuantity = productQuantities.reduce(function (acc, quantity) {
                                    return acc + parseInt(quantity, 10);  // ตรวจสอบให้แน่ใจว่าเป็นจำนวนเต็ม
                                }, 0);

                                var ctx = document.getElementById('myPieChart').getContext('2d');
                                var myPieChart = new Chart(ctx, {
                                    type: 'pie',
                                    data: {
                                        labels: productNames, // ชื่อสินค้าทั้งหมด
                                        datasets: [{
                                            data: productQuantities, // จำนวนสินค้าที่ขายได้
                                            backgroundColor: [
                                                'rgba(255, 99, 132, 0.2)',
                                                'rgba(54, 162, 235, 0.2)',
                                                'rgba(255, 206, 86, 0.2)',
                                                'rgba(75, 192, 192, 0.2)',
                                                'rgba(153, 102, 255, 0.2)',
                                                'rgba(255, 159, 64, 0.2)',
                                                'rgba(199, 199, 199, 0.2)',
                                                'rgba(83, 102, 255, 0.2)',
                                                'rgba(124, 255, 64, 0.2)',
                                                'rgba(204, 102, 255, 0.2)'
                                            ],
                                            borderColor: [
                                                'rgba(255, 99, 132, 1)',
                                                'rgba(54, 162, 235, 1)',
                                                'rgba(255, 206, 86, 1)',
                                                'rgba(75, 192, 192, 1)',
                                                'rgba(153, 102, 255, 1)',
                                                'rgba(255, 159, 64, 1)',
                                                'rgba(199, 199, 199, 1)',
                                                'rgba(83, 102, 255, 1)',
                                                'rgba(124, 255, 64, 1)',
                                                'rgba(204, 102, 255, 1)'
                                            ],
                                            borderWidth: 1
                                        }]
                                    },
                                    options: {
                                        responsive: true,
                                        plugins: {
                                            legend: {
                                                position: 'top',
                                            },
                                            datalabels: {
                                                formatter: function (value, context) {
                                                    let percentage = totalQuantity ? (value / totalQuantity * 100).toFixed(2) : 0;
                                                    return percentage + '%'; // แสดงเปอร์เซ็นต์ในกราฟ
                                                },
                                                color: '#000', // สีของข้อความ
                                                font: {
                                                    weight: 'bold',
                                                    size: 14
                                                }
                                            }
                                        }
                                    },
                                    plugins: [ChartDataLabels] // เปิดใช้ ChartDataLabels
                                });

                                // ฟังก์ชันสลับแสดงรายการ
                                document.getElementById('showListGroup').addEventListener('click', function () {
                                    document.getElementById('bestSellingList').style.display = 'block';
                                    document.getElementById('bestSellingChart').style.display = 'none';
                                });

                                // ฟังก์ชันสลับแสดงกราฟ
                                document.getElementById('showChart').addEventListener('click', function () {
                                    document.getElementById('bestSellingList').style.display = 'none';
                                    document.getElementById('bestSellingChart').style.display = 'block';
                                });

                                // แสดงรายการเป็นค่าเริ่มต้น
                                document.getElementById('bestSellingList').style.display = 'block';
                            </script>

                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <a href="add_stock.php" class="card text-decoration-none text-dark">
                        <div class="card-body">
                            <h3 class="text-center">สินค้าใกล้หมด (คงเหลือ)</h3>
                            <?php if ($result_low_stock_products->num_rows > 0): ?>
                                <ul class="list-group list-group-flush">
                                    <?php while ($product = $result_low_stock_products->fetch_assoc()): ?>
                                        <li class="list-group-item d-flex justify-content-between">
                                            <span><?php echo $product['product_name']; ?></span>
                                            <span><?php echo $product['quantity']; ?> ชิ้น</span>
                                        </li>
                                    <?php endwhile; ?>
                                </ul>
                            <?php else: ?>
                                <p>ไม่มีสินค้าใกล้หมด</p>
                            <?php endif; ?>
                        </div>
                    </a>
                </div>
            </div>
            <div class="row mb-3 mt-4">
                <div class="col-lg-12 mb-5">
                    <div class="card">
                        <div class="card-body container text-center">
                            <div class="row justify-content-end mb-2">
                                <div class="col-8">
                                    <h3 id="chartTitle">ยอดขายประจำสัปดาห์</h3>
                                </div>
                                <div class="col-2">
                                    <button id="showWeeklyChart" type="button"
                                        class="btn btn-outline-secondary custom-btn me-1">สัปดาห์</button>
                                    <button id="showWeeklyMonthlyChart" type="button"
                                        class="btn btn-outline-secondary custom-btn me-1">เดือน</button>
                                    <button id="showMonthlyChart" type="button"
                                        class="btn btn-outline-secondary custom-btn">ปี</button>
                                </div>
                            </div>
                            <canvas id="salesChart" width="700" height="400"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                var ctx = document.getElementById('salesChart').getContext('2d');
                var weeklySalesLabels = <?php echo json_encode($dates); ?>; // ป้ายกำกับรายวัน
                var weeklySalesData = <?php echo json_encode($sales_data); ?>; // ยอดขายรายวัน

                var weeklySalesPerMonthLabels = <?php echo json_encode($weekly_sales_labels); ?>; // ป้ายกำกับรายสัปดาห์ในเดือน
                var weeklySalesPerMonthData = <?php echo json_encode($weekly_sales_data_monthly); ?>; // ยอดขายรายสัปดาห์

                var monthlySalesLabels = <?php echo json_encode($months); ?>; // ป้ายกำกับรายเดือน
                var monthlySalesData = <?php echo json_encode($monthly_sales_data); ?>; // ยอดขายรายเดือน

                var salesChart = new Chart(ctx, {
                    type: 'bar', // ประเภทของกราฟเป็น bar
                    data: {
                        labels: weeklySalesLabels, // ป้ายกำกับ (วันที่)
                        datasets: [{
                            label: 'ยอดขายแต่ละวันในสัปดาห์ (บาท)', // ชื่อของชุดข้อมูล
                            data: weeklySalesData, // ข้อมูลยอดขายรายวัน
                            backgroundColor: 'rgba(75, 192, 192, 0.2)', // สีพื้นหลังของแท่งกราฟ
                            borderColor: 'rgba(75, 192, 192, 1)', // สีเส้นขอบของแท่งกราฟ
                            borderWidth: 1 // ความหนาของเส้นขอบ
                        }]
                    },
                    options: {
                        scales: {
                            x: {
                                beginAtZero: true,
                                type: 'category',
                            },
                            y: {
                                beginAtZero: true // เริ่มต้นที่ 0
                            }
                        }
                    }
                });

                // ฟังก์ชันสำหรับอัปเดตกราฟ
                function updateChart(data, labels, label, title) {
                    salesChart.data.labels = labels;
                    salesChart.data.datasets[0].data = data;
                    salesChart.data.datasets[0].label = label;
                    salesChart.update();

                    document.getElementById('chartTitle').innerText = title;
                }

                // กดปุ่มเพื่อสลับแสดงยอดขายรายวัน, รายสัปดาห์ในเดือน, หรือรายเดือน
                document.getElementById('showWeeklyChart').addEventListener('click', function () {
                    updateChart(weeklySalesData, weeklySalesLabels, 'ยอดขายแต่ละวันในสัปดาห์ (บาท)', 'ยอดขายประจำสัปดาห์');
                });

                document.getElementById('showWeeklyMonthlyChart').addEventListener('click', function () {
                    updateChart(weeklySalesPerMonthData, weeklySalesPerMonthLabels, 'ยอดขายแต่ละสัปดาห์ในเดือนนี้ (บาท)', 'ยอดขายประจำเดือน');
                });

                document.getElementById('showMonthlyChart').addEventListener('click', function () {
                    updateChart(monthlySalesData, monthlySalesLabels, 'ยอดขายแต่ละเดือนในปีนี้ (บาท)', 'ยอดขายประจำปี');
                });
            </script>
        <?php endif; ?>
    </div>
</body>

</html>