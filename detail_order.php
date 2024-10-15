<?php
include 'inc/check_login.php';
checkLogin();
include 'header.php';
include 'db.php';
include 'inc/links.php';
include 'inc/functions.php';

$order_id = $_GET['order_id'] ?? null;

if (!$order_id) {
    die("ไม่พบหมายเลขคำสั่งซื้อ");
}

// ดึงข้อมูลบริษัทจากฐานข้อมูล
$company_id = 1; // เปลี่ยนตามค่า company_id ที่ถูกต้อง
$sql_company = "SELECT company_name, company_address, company_phone, company_email FROM companies WHERE company_id = ?";
$stmt_company = $conn->prepare($sql_company);
$stmt_company->bind_param("i", $company_id);
$stmt_company->execute();
$result_company = $stmt_company->get_result();
$company = $result_company->fetch_assoc();

// ดึงข้อมูลลูกค้าจากฐานข้อมูล
$sql_customer = "SELECT customer_name, address, district, city, province, phonenumber FROM customers WHERE customer_id = (SELECT customer_id FROM orders WHERE order_id = ?)";
$stmt_customer = $conn->prepare($sql_customer);
$stmt_customer->bind_param("i", $order_id);
$stmt_customer->execute();
$result_customer = $stmt_customer->get_result();
$customer = $result_customer->fetch_assoc();

// ดึงข้อมูลรายการสินค้าจากฐานข้อมูล
$sql_items = "SELECT products.product_name, order_items.quantity, order_items.total_price, order_items.is_cancelled 
              FROM order_items 
              JOIN products ON order_items.product_id = products.product_id 
              WHERE order_id = ?";
$stmt_items = $conn->prepare($sql_items);
$stmt_items->bind_param("i", $order_id);
$stmt_items->execute();
$result_items = $stmt_items->get_result();
$order_items = [];
while ($row = $result_items->fetch_assoc()) {
    $order_items[] = $row;
}

// ดึงข้อมูลคำสั่งซื้อ
$sql_order = "SELECT order_date, discount, tax_rate FROM orders WHERE order_id = ?";
$stmt_order = $conn->prepare($sql_order);
$stmt_order->bind_param("i", $order_id);
$stmt_order->execute();
$result_order = $stmt_order->get_result();
$order = $result_order->fetch_assoc();

// ตรวจสอบว่าคำสั่งซื้อนั้นมีการยกเลิกหรือไม่
$is_order_cancelled = false;
foreach ($order_items as $item) {
    if ($item["is_cancelled"]) {
        $is_order_cancelled = true;
        break;
    }
}

// ปิดการเชื่อมต่อฐานข้อมูล
$conn->close();
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <title>ใบส่งสินค้าชั่วคราว</title>
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
        <div class=" no-print text-end">
            <button onclick="window.print()" class="btn btn-primary custom-btn-t me-1">พิมพ์</button>
            <button id="save-pdf" class="btn btn-primary custom-btn">บันทึกเป็น PDF</button>
        </div>
        <div class="a4-container">
            <div class="row mb-2">
                <div class="col-12 text-center">
                    <h2>ใบเสร็จรับเงิน</h2>
                </div>
            </div>
            <div class="row mb-1">
                <div class="col-12 border border-dark rounded-4 p-3">
                    <div class="row">
                        <div class="col-8">
                            <h5>ข้อมูลบริษัท</h5>
                            ชื่อบริษัท:
                            <?php echo isset($company['company_name']) ? $company['company_name'] : 'ไม่พบข้อมูล'; ?><br>
                            ที่อยู่:
                            <?php echo isset($company['company_address']) ? $company['company_address'] : 'ไม่พบข้อมูล'; ?><br>
                            เบอร์โทร:
                            <?php echo isset($company['company_phone']) ? $company['company_phone'] : 'ไม่พบข้อมูล'; ?><br>
                            เลขประจำตัวผู้เสียภาษี: 1234567891234
                        </div>
                        <div class="col-4 text-end">
                            <h5>เลขที่ใบสั่งซื้อ: <?php echo $order_id; ?></h5>
                            <p>วันที่: <?php echo formatThaiDate(strtotime($order['order_date'])); ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <?php if ($is_order_cancelled): ?>
                <div class="cancel-stamp">
                    <h2>ยกเลิกแล้ว</h2>
                </div>
            <?php endif; ?>
            <div class="row mb-4">
                <div class="col-12 border border-dark rounded-4 p-3">
                    <h5>ข้อมูลลูกค้า</h5>
                    ชื่อ:
                    <?php echo isset($customer['customer_name']) ? htmlspecialchars($customer['customer_name']) : 'ไม่พบข้อมูล'; ?><br>

                    ที่อยู่:
                    <?php
                    $address = isset($customer['address']) ? htmlspecialchars($customer['address']) : 'ไม่พบข้อมูล';
                    $district = isset($customer['district']) ? htmlspecialchars($customer['district']) : 'ไม่พบข้อมูล';
                    $city = isset($customer['city']) ? htmlspecialchars($customer['city']) : 'ไม่พบข้อมูล';
                    $province = isset($customer['province']) ? htmlspecialchars($customer['province']) : 'ไม่พบข้อมูล';
                    echo $address . ' ' . $district . ' ' . $city . ' ' . $province;
                    ?><br>

                    เบอร์โทร:
                    <?php echo isset($customer['phonenumber']) ? htmlspecialchars($customer['phonenumber']) : 'ไม่พบข้อมูล'; ?>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <h5>รายการสินค้าที่สั่ง</h5>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th class='text-center'>ลำดับที่</th>
                                <th>สินค้า</th>
                                <th class='text-center'>จำนวน</th>
                                <th class='text-center'>ราคาต่อหน่วย (บาท)</th>
                                <th class='text-center'>รวม (บาท)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($order_items)): ?>
                                <?php $index = 1; ?>
                                <?php foreach ($order_items as $item): ?>
                                    <tr>
                                        <td class='text-center'><?php echo $index++; ?></td>
                                        <td><?php echo htmlspecialchars($item["product_name"]); ?></td>
                                        <td class='text-center'><?php echo number_format($item["quantity"]); ?></td>
                                        <td class='text-end'>
                                            <?php echo number_format($item["total_price"] / $item["quantity"], 2); ?>
                                        </td>
                                        <td class='text-end'><?php echo number_format($item["total_price"], 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center">ไม่มีรายการสินค้า</td>
                                </tr>
                            <?php endif; ?>

                            <?php
                            $subtotal = !empty($order_items) ? array_sum(array_column($order_items, 'total_price')) : 0;
                            $discount = isset($order['discount']) ? $order['discount'] : 0;
                            $tax_rate = isset($order['tax_rate']) ? $order['tax_rate'] : 7;
                            $discount_amount = ($subtotal * $discount) / 100;
                            $total_after_discount = $subtotal - $discount_amount;
                            $tax_amount = ($total_after_discount * $tax_rate) / 100;
                            $grand_total = $total_after_discount + $tax_amount;
                            ?>
                            <tr>
                                <td colspan="4" class="text-end">ส่วนลด :</td>
                                <td class='text-end'><?php echo number_format($discount_amount, 2); ?></td>
                            </tr>
                            <tr>
                                <td colspan="4" class="text-end">ภาษี (<?php echo $tax_rate; ?>%) :</td>
                                <td class='text-end'><?php echo number_format($tax_amount, 2); ?></td>
                            </tr>
                            <tr>
                                <td colspan="4" class="text-end "><strong>ยอดรวมสุทธิ : </strong></td>
                                <td class='text-end'><strong><?php echo number_format($grand_total, 2); ?></strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.getElementById('save-pdf').addEventListener('click', function () {
            var element = document.querySelector('.a4-container');
            html2pdf(element, {
                margin: 0,
                filename: 'order.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2, logging: true, dpi: 192, letterRendering: true },
                jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
            });
        });
    </script>
</body>

</html>