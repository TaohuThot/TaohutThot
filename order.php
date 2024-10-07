<?php
include 'inc/check_login.php';
checkLogin();
include 'header.php';
include 'db.php';
include 'inc/links.php';
include 'inc/functions.php';

$search = isset($_GET['search']) ? $_GET['search'] : '';
$records_per_page = 10;
$current_page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($current_page - 1) * $records_per_page;

$sql = "SELECT orders.order_id, customers.customer_name, 
               SUM(order_items.total_price) as total_price, 
               orders.order_date
        FROM orders
        LEFT JOIN order_items ON orders.order_id = order_items.order_id
        LEFT JOIN customers ON orders.customer_id = customers.customer_id
        WHERE order_items.is_cancelled = 0"; 

if ($search) {
    $sql .= " AND orders.order_id LIKE ?";
}

$sql .= " GROUP BY orders.order_id
          HAVING SUM(order_items.is_cancelled) = 0
          ORDER BY orders.order_date DESC
          LIMIT ? OFFSET ?";

$stmt = $conn->prepare($sql);

if ($search) {
    $search_param = "%$search%";
    $stmt->bind_param("sii", $search_param, $records_per_page, $offset);
} else {
    $stmt->bind_param("ii", $records_per_page, $offset);
}

$stmt->execute();
$result = $stmt->get_result();

$count_sql = "SELECT COUNT(DISTINCT orders.order_id) as total_records 
              FROM orders
              LEFT JOIN order_items ON orders.order_id = order_items.order_id
              WHERE order_items.is_cancelled = 0"; // กรองรายการที่ไม่ถูกยกเลิก

if ($search) {
    $count_sql .= " AND orders.order_id LIKE     ?";
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
    <title>คำสั่งซื้อ</title>
</head>

<body>
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-chevron p-3 rounded -3">
                <li class="breadcrumb-item">
                    <a class="link-body-emphasis fw-semibold text-decoration-none" href="home.php">
                        Home</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">
                    คำสั่งซื้อ
                </li>
            </ol>
        </nav>
        <div class="d-flex justify-content-between align-items-center">
            <h2>คำสั่งซื้อ</h2>
            <div class="d-flex flex-grow-1 justify-content-end align-items-center gap-2">
                <div>
                    <?php if ($_SESSION['role'] === 'admin'): ?>
                        <a href="add_order.php" class="btn btn-primary custom-btn">+ เพิ่มคำสั่งซื้อ</a>
                    <?php endif; ?>
                </div>
                <form class="d-flex m-0" action="" method="get">
                    <input class="form-control me-2" type="search" name="search" placeholder="ค้นหาคำสั่งซื้อ"
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
                        <th>ชื่อลูกค้า</th>
                        <th>ราคารวม</th>
                        <th>วันเวลา</th>
                        <th class='text-center'>รายละเอียด</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        $row_number = $offset + 1;
                        while ($row = $result->fetch_assoc()) {
                            $formatted_date = formatThaiDate(strtotime($row['order_date']));
                            echo "<tr>
                                        <td class = 'text-center'>{$row_number}</td>
                                        <td class = 'text-center'>{$row['order_id']}</td>
                                        <td>{$row['customer_name']}</td>
                                        <td>฿ " . number_format(htmlspecialchars($row['total_price']), 2) . "</td>
                                        <td>{$formatted_date}</td>
                                        <td class = 'text-center'>
                                            <a href='detail_order.php?order_id=" . $row['order_id'] . "' class='custom-link'>
                                            <i class='bi bi-eye'></i> ดูรายละเอียด
                                            </a>";
                            if ($_SESSION['role'] === 'admin') {
                                echo " &nbsp;
                                            <a href='#' class='custom-link' onclick='confirmCancel(" . $row['order_id'] . ")'>
                                            <i class='bi bi-x-circle'></i> ยกเลิกคำสั่งซื้อ
                                            </a>";
                            }
                            echo "</td></tr>";
                            $row_number++;
                        }
                    } else {
                        echo "<tr><td colspan='6'>ไม่มีข้อมูลคำสั่งซื้อ</td></tr>";
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
                        echo "จำนวนคำสั่งซื้อ $start_number - $end_number จากทั้งหมด $total_records รายการ";
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

    <script>
    function confirmCancel(order_id) {
        Swal.fire({
            title: 'ยืนยันการยกเลิกคำสั่งซื้อ',
            text: 'คุณแน่ใจว่าต้องการยกเลิกคำสั่งซื้อนี้?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'ยืนยัน',
            cancelButtonText: 'ยกเลิก',
            confirmButtonColor: '#C7A98B',
            cancelButtonColor: '#cccccc'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('delete_order.php?order_id=' + order_id)
                    .then(response => response.text())
                    .then(data => {
                        if (data.includes("ยกเลิกคำสั่งซื้อเรียบร้อยแล้ว")) {
                            Swal.fire({
                                icon: 'success',
                                title: 'ยกเลิกคำสั่งซื้อเรียบร้อยแล้ว',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'ไม่สามารถยกเลิกคำสั่งซื้อได้',
                                text: data,
                                confirmButtonColor: '#C7A98B'
                            });
                        }
                    });
            }
        });
    }
</script>


</body>

</html>