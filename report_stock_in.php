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

// คำสั่ง SQL สำหรับดึงข้อมูล
$sql = "SELECT stock_in_items.stock_in_id, products.product_name, stock_in_items.quantity, stock_in.date, 
        COALESCE(pc.cost_per_unit, 0) AS cost_per_unit 
        FROM stock_in_items 
        JOIN products ON stock_in_items.product_id = products.product_id
        JOIN stock_in ON stock_in_items.stock_in_id = stock_in.stock_in_id
        LEFT JOIN product_costs AS pc ON stock_in_items.product_id = pc.product_id 
        AND stock_in.stock_in_id = pc.stock_in_id";  // เชื่อมโยงกับ product_costs ตาม stock_in_id

if ($search) {
    $sql .= " WHERE stock_in_items.stock_in_id LIKE ?";
}

$sql .= " ORDER BY stock_in.date DESC LIMIT $records_per_page OFFSET $offset";



$stmt = $conn->prepare($sql);

if ($search) {
    $search_param = "%$search%";
    $stmt->bind_param("s", $search_param);
}

$stmt->execute();
$result = $stmt->get_result();

// คำสั่ง SQL สำหรับนับจำนวน
$count_sql = "SELECT COUNT(*) as total_records FROM stock_in_items";
if ($search) {
    $count_sql .= " WHERE stock_in_items.stock_in_id LIKE ?";
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
                <li class="breadcrumb-item active" aria-current="page">รายงานสินค้านำเข้า</li>
            </ol>
        </nav>
        <div class="d-flex justify-content-between align-items-center">
            <h2>รายงานสินค้านำเข้า</h2>
            <div class="d-flex flex-grow-1 justify-content-end align-items-center gap-2">
                <form class="d-flex m-0" action="" method="get">
                    <input class="form-control me-2" type="search" name="search" placeholder="ค้นหารหัสนำเข้า"
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
                        <th class='text-center'>รหัสสินค้านำเข้า</th>
                        <th>ชื่อสินค้า</th>
                        <th class='text-center'>จำนวน</th>
                        <th>ต้นทุนต่อหน่วย</th>
                        <th>วันเวลา</th>
                        <th>รายละเอียด</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        $row_number = $offset + 1;
                        while ($row = $result->fetch_assoc()) {
                            $formatted_date = formatThaiDate(strtotime($row['date']));
                            $cost_per_unit = number_format($row['cost_per_unit'], 2);
                            echo "<tr>
                                        <td class='text-center'>{$row_number}</td>
                                        <td class='text-center'>{$row['stock_in_id']}</td>
                                        <td class='text-truncate' style='max-width: 150px; overflow: hidden; white-space: nowrap;' >{$row['product_name']}</td>
                                        <td class='text-center'>{$row['quantity']}</td>
                                        <td>{$cost_per_unit}</td> 
                                        <td>{$formatted_date}</td>
                                        <td>
                                            <a href='detail_stock.php?stock_in_id=" . $row['stock_in_id'] . "' class='custom-link'>
                                            <i class='bi bi-eye'></i> ดูรายละเอียด
                                            </a> 
                                    </tr>";
                            $row_number++;
                        }
                    } else {
                        echo "<tr><td colspan='7'>ไม่มีข้อมูลสินค้านำเข้า</td></tr>";
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
                        echo "จำนวนนำเข้า $start_number - $end_number จากทั้งหมด $total_records รายการ";
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