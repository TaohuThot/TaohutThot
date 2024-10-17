<?php
include 'inc/check_login.php';
checkLogin();
include 'header.php';
include 'db.php';
include 'inc/links.php';
include 'inc/functions.php';

$search = isset($_GET['search']) ? $_GET['search'] : '';
$records_per_page = 12; // จำนวนข้อมูลที่จะแสดงต่อหน้า
$current_page = isset($_GET['page']) ? intval($_GET['page']) : 1; // หน้าปัจจุบัน
$offset = ($current_page - 1) * $records_per_page; // คำนวณตำแหน่งเริ่มต้นของข้อมูลในหน้านั้น

// คำสั่ง SQL สำหรับดึงข้อมูล โดยไม่รวมรายการสินค้าในแถวเดียวกัน
$sql = "SELECT order_items.order_item_id, order_items.order_id, products.product_name, order_items.quantity, order_items.is_cancelled, order_items.cancelled_at 
        FROM order_items 
        JOIN products ON order_items.product_id = products.product_id";

if ($search) {
    $sql .= " WHERE order_items.order_id LIKE ?";
}

$sql .= " ORDER BY order_items.order_id DESC LIMIT ? OFFSET ?";

$stmt = $conn->prepare($sql);

if ($search) {
    $search_param = "%$search%";
    $stmt->bind_param("sii", $search_param, $records_per_page, $offset);
} else {
    $stmt->bind_param("ii", $records_per_page, $offset);
}

$stmt->execute();
$result = $stmt->get_result();

// คำสั่ง SQL สำหรับนับจำนวน
$count_sql = "SELECT COUNT(*) as total_records FROM order_items";
if ($search) {
    $count_sql .= " WHERE order_items.order_id LIKE ?";
    $count_stmt = $conn->prepare($count_sql);
    $count_stmt->bind_param("s", $search_param);
    $count_stmt->execute();
    $count_result = $count_stmt->get_result();
} else {
    $count_result = $conn->query($count_sql);
}

$total_records = $count_result->fetch_assoc()['total_records'];
$total_pages = ceil($total_records / $records_per_page);

$conn->close();

?>
<!DOCTYPE html>
<html lang="th">

<head>
    <title>รายงาน</title>
</head>

<body>
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-chevron p-3 rounded -3">
                <li class="breadcrumb-item">
                    <a class="link-body-emphasis fw-semibold text-decoration-none" href="home.php">Home</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">รายงานสินค้าออก</li>
            </ol>
        </nav>
        <div class="d-flex justify-content-between align-items-center">
            <h2>รายงานสินค้าออก</h2>
            <div class="d-flex flex-grow-1 justify-content-end align-items-center gap-2">
                <form class="d-flex m-0" action="" method="get">
                    <input class="form-control me-2" type="search" name="search" placeholder="ค้นหารหัสคำสั่งซื้อ"
                        aria-label="Search" value="<?php echo htmlspecialchars($search); ?>">
                    <button class="btn btn-outline-secondary custom-btn-t" type="submit">ค้นหา</button>
                </form>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th class='text-center'>ลำดับ</th>
                        <th class='text-center'>รหัสคำสั่งซื้อ</th>
                        <th>ชื่อสินค้า</th>
                        <th class='text-center'>จำนวน</th>
                        <th>สถานะ</th>
                        <th>รายละเอียด</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        $row_number = $offset + 1;
                        while ($row = $result->fetch_assoc()) {
                            $status = $row['is_cancelled']
                                ? '<span class="text-danger">ยกเลิกเมื่อ: ' . formatThaiDate(strtotime($row['cancelled_at'])) . '</span>'
                                : '<span class="text-success">ปกติ</span>';
                            echo "<tr>
                                    <td class = 'text-center'>{$row_number}</td>
                                    <td class = 'text-center'>{$row['order_id']}</td>
                                    <td class='text-truncate' style='max-width: 150px; overflow: hidden; white-space: nowrap;' >{$row['product_name']}</td>
                                    <td class = 'text-center'>{$row['quantity']}</td>
                                    <td>{$status}</td>
                                    <td>
                                        <a href='detail_order.php?order_id=" . $row['order_id'] . "' class='custom-link'>
                                            <i class='bi bi-eye'></i> ดูรายละเอียด
                                            </a>
                                    </tr>";
                            $row_number++;
                        }
                    } else {
                        echo "<tr><td colspan='6'>ไม่มีข้อมูลสินค้าออก</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
            <nav aria-label="Page navigation example">
                <ul class="pagination justify-content-end">
                    <div class="mt-2 me-3">
                        <?php
                        $start_number = ($current_page - 1) * $records_per_page + 1;
                        $end_number = min($start_number + $records_per_page - 1, $total_records);
                        echo "จำนวนสินค้าออก $start_number - $end_number จากทั้งหมด $total_records รายการ";
                        ?>
                    </div>
                    <?php if ($current_page > 1): ?>
                        <li class="page-item">
                            <a class="page-link"
                                href="?page=<?php echo $current_page - 1; ?>&search=<?php echo urlencode($search); ?>">ก่อนหน้า</a>
                        </li>
                    <?php else: ?>
                        <li class="page-item disabled">
                            <a class="page-link">ก่อนหน้า</a>
                        </li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?php echo ($i == $current_page) ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($current_page < $total_pages): ?>
                        <li class="page-item">
                            <a class="page-link"
                                href="?page=<?php echo $current_page + 1; ?>&search=<?php echo urlencode($search); ?>">ถัดไป</a>
                        </li>
                    <?php else: ?>
                        <li class="page-item disabled">
                            <a class="page-link">ถัดไป</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </div>
</body>

</html>