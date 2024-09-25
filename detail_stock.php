<?php
include 'inc/check_login.php';
checkLogin();
include 'header.php';
include 'db.php';
include 'inc/links.php';
include 'inc/functions.php';

$stock_in_id = $_GET['stock_in_id'] ?? null;

if (!$stock_in_id) {
    die("ไม่พบหมายเลขนำเข้าสินค้า");
}

// ดึงข้อมูลรายการสินค้าจากฐานข้อมูล
$sql_items = "SELECT products.product_name, stock_in_items.quantity, pc.cost_per_unit
              FROM stock_in_items
              JOIN products ON stock_in_items.product_id = products.product_id 
              LEFT JOIN product_costs AS pc ON stock_in_items.product_id = pc.product_id 
              AND stock_in_items.stock_in_id = pc.stock_in_id
              WHERE stock_in_items.stock_in_id = ?";
$stmt_items = $conn->prepare($sql_items);
$stmt_items->bind_param("i", $stock_in_id);
$stmt_items->execute();
$result_items = $stmt_items->get_result();
$order_items = [];
while ($row = $result_items->fetch_assoc()) {
    $order_items[] = $row;
}

$sql_stock_in = "SELECT stock_in_id ,date FROM stock_in WHERE stock_in_id = ?";
$stmt_stock_in = $conn->prepare($sql_stock_in);
$stmt_stock_in->bind_param("i", $stock_in_id);
$stmt_stock_in->execute();
$result_stock_in = $stmt_stock_in->get_result();
$stock_in = $result_stock_in->fetch_assoc();

// ปิดการเชื่อมต่อฐานข้อมูล
$conn->close();
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <title>ใบนำเข้าสินค้า</title>
</head>

<body>
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-chevron p-3 rounded-3">
                <li class="breadcrumb-item">
                    <a class="link-body-emphasis fw-semibold text-decoration-none" href="home.php">Home</a>
                </li>
                <li class="breadcrumb-item">
                    <a class="link-body-emphasis fw-semibold text-decoration-none" href="order.php">คำสั่งซื้อ</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">รายละเอียดคำสั่งซื้อ</li>
            </ol>
        </nav>
        <div class="a4-container">
            <div class="row">
                <div class="col-12 text-center mb-4">
                    <h2>ใบนำเข้าสินค้า</h2>
                </div>
                <div class="row d-flex justify-content-between align-items-start mb-4">
                    <div class="col-12 text-end" style="margin-top: -1px;">
                        <h5>เลขที่นำเข้าสินค้า: <?php echo $stock_in_id; ?></h5>
                        <p>วันที่: <?php echo formatThaiDate(strtotime($stock_in['date'])); ?></p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <h5>รายการสินค้าที่นำเข้า</h5>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ลำดับที่</th>
                                <th>สินค้า</th>
                                <th>จำนวน</th>
                                <th>ราคาต้นทุน</th> <!-- เพิ่มคอลัมน์ราคาต้นทุน -->
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($order_items)): ?>
                                <?php $index = 1; ?>
                                <?php foreach ($order_items as $item): ?>
                                    <tr>
                                        <td><?php echo $index++; ?></td>
                                        <td><?php echo htmlspecialchars($item["product_name"]); ?></td>
                                        <td><?php echo htmlspecialchars($item["quantity"]); ?></td>
                                        <td><?php echo htmlspecialchars(number_format($item["cost_per_unit"], 2)); ?></td> <!-- แสดงราคาต้นทุน -->
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center">ไม่มีรายการสินค้า</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>

</html>