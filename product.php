<?php
include 'inc/check_login.php';
checkLogin();
include 'header.php';
include 'db.php';
include 'inc/links.php';

$search = isset($_GET['search']) ? $_GET['search'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'product_name'; // Default sorting column
$order = isset($_GET['order']) ? $_GET['order'] : 'ASC'; // Default sorting order
$records_per_page = 5; // จำนวนข้อมูลที่จะแสดงต่อหน้า
$current_page = isset($_GET['page']) ? intval($_GET['page']) : 1; // หน้าปัจจุบัน
$offset = ($current_page - 1) * $records_per_page; // คำนวณตำแหน่งเริ่มต้นของข้อมูลในหน้านั้น

$sql = "SELECT p.product_id, p.product_name, c.category_name, p.price, p.quantity, p.image,
        AVG(pc.cost_per_unit) AS avg_cost
        FROM products p
        JOIN categories c ON p.category_id = c.category_id
        LEFT JOIN product_costs pc ON p.product_id = pc.product_id";

if ($search) {
    $sql .= " WHERE p.product_name LIKE ?";
}

$sql .= " GROUP BY p.product_id ORDER BY $sort $order LIMIT ? OFFSET ?";

$stmt = $conn->prepare($sql);

if ($search) {
    $search_param = "%$search%";
    $stmt->bind_param("sii", $search_param, $records_per_page, $offset);
} else {
    $stmt->bind_param("ii", $records_per_page, $offset);
}

$stmt->execute();
$result = $stmt->get_result();

$count_sql = "SELECT COUNT(*) as total_records FROM products";
if ($search) {
    $count_sql .= " WHERE product_name LIKE ?";
    $count_stmt = $conn->prepare($count_sql);
    $count_stmt->bind_param("s", $search_param);
    $count_stmt->execute();
    $count_result = $count_stmt->get_result();
} else {
    $count_result = $conn->query($count_sql);
}

$total_records = $count_result->fetch_assoc()['total_records'];
$total_pages = ceil($total_records / $records_per_page);

?>

<!DOCTYPE html>
<html lang="th">

<head>
    <title>สินค้า</title>
    <style>
        .sortable {
            cursor: pointer;
        }

        .sortable:after {
            content: "▲";
            font-size: 0.8em;
            margin-left: 0px;
        }

        .sortable.desc:after {
            content: "▼";
        }
    </style>
</head>

<body>
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-chevron p-3 rounded -3">
                <li class="breadcrumb-item">
                    <a class="link-body-emphasis fw-semibold text-decoration-none" href="home.php">Home</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">สินค้า</li>
            </ol>
        </nav>
        <div class="d-flex justify-content-between align-items-center">
            <h2>สินค้า</h2>
            <div class="me-4"></div>
            <a href="product_card.php" class="btn btn-outline-secondary custom-btn me-2">
                <i class="bi bi-image" style="color: #A6896F;"></i>
            </a>
            <div class="checkbox">
                <input type="checkbox" id="cbx" style="display:none">
                <label for="cbx" class="toggle"><span></span></label>
            </div>
            <div class="d-flex flex-grow-1 justify-content-end align-items-center gap-2">
                <div>
                    <?php if ($_SESSION['role'] === 'admin'): ?>
                        <a href="add_product.php" class="btn btn-primary custom-btn">+ เพิ่มสินค้า</a>
                        <a href="add_stock.php" class="btn btn-primary custom-btn">+ นำเข้าสินค้า</a>
                    <?php endif; ?>
                </div>
                <form class="d-flex m-0" action="" method="get">
                    <input class="form-control me-2" type="search" name="search" placeholder="ค้นหาสินค้า"
                        aria-label="Search" value="<?php echo htmlspecialchars($search); ?>">
                    <button class="btn btn-outline-secondary custom-btn-t" type="submit">ค้นหา</button>
                </form>
            </div>
        </div>
        <div class="table-responsive ">
            <table class="table table-striped shadow border-0 text-truncate">
                <thead>
                    <tr>
                        <th>สินค้า</th>
                        <th>
                            <a href="?search=<?php echo urlencode($search); ?>&sort=category_name&order=<?php echo ($sort === 'category_name' && $order === 'ASC') ? 'DESC' : 'ASC'; ?>"
                                class="sortable <?php echo ($sort === 'category_name' ? ($order === 'ASC' ? '' : 'desc') : ''); ?>">
                                หมวดหมู่
                            </a>
                        </th>
                        <th>
                            <a href="?search=<?php echo urlencode($search); ?>&sort=price&order=<?php echo ($sort === 'price' && $order === 'ASC') ? 'DESC' : 'ASC'; ?>"
                                class="sortable <?php echo ($sort === 'price' ? ($order === 'ASC' ? '' : 'desc') : ''); ?>">
                                ราคาขาย
                            </a>
                        </th>
                        <th>ต้นทุนเฉลี่ย</th>
                        <th>
                            <a href="?search=<?php echo urlencode($search); ?>&sort=quantity&order=<?php echo ($sort === 'quantity' && $order === 'ASC') ? 'DESC' : 'ASC'; ?>"
                                class="sortable <?php echo ($sort === 'quantity' ? ($order === 'ASC' ? '' : 'desc') : ''); ?>">
                                คงเหลือ
                            </a>
                        </th>
                        <th>รายละเอียด</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>
                                        <div class='product-info'>
                                            <img src='uploads/" . htmlspecialchars($row['image']) . "' alt='Product Image' class='img-thumbnail'>
                                            <p>" . htmlspecialchars($row['product_name']) . "</p>
                                        </div>
                                    </td>";
                            echo "<td>" . htmlspecialchars($row['category_name']) . "</td>";
                            echo "<td>฿ " . number_format(htmlspecialchars($row['price']), 2) . "</td>";
                            echo "<td>฿ " . number_format($row['avg_cost'], 2) . "</td>";
                            echo "<td>" . htmlspecialchars($row['quantity']) . "</td>";
                            echo "<td>
                                        <a href='detail_product.php?product_id=" . $row['product_id'] . "' class='custom-link'>
                                        <i class='bi bi-eye'></i> ดูรายละเอียด
                                        </a>";
                            if ($_SESSION['role'] === 'admin') {
                                echo " &nbsp;
                                        <a href='edit_product.php?product_id=" . $row['product_id'] . "' class='custom-link'>
                                        <i class='bi bi-pencil'></i> แก้ไข
                                        </a> &nbsp;
                                        <a href='#' class='custom-link' onclick='confirmDelete(" . $row['product_id'] . ")'>
                                        <i class='bi bi-trash'></i> ลบ                                            
                                        </a>";
                            }
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6'>ไม่มีข้อมูลสินค้า</td></tr>";
                    }

                    $stmt->close();
                    $conn->close();
                    ?>
                </tbody>
            </table>
            <nav aria-label="Page navigation example">
                <ul class="pagination justify-content-end">
                    <div class="mt-2 me-3">
                        <?php
                        $start_number = ($current_page - 1) * $records_per_page + 1;
                        $end_number = min($start_number + $records_per_page - 1, $total_records);
                        echo "จำนวนสินค้า $start_number - $end_number จากทั้งหมด $total_records รายการ";
                        ?>
                    </div>
                    <?php if ($current_page > 1): ?>
                        <li class="page-item">
                            <a class="page-link"
                                href="?page=<?php echo $current_page - 1; ?>&search=<?php echo urlencode($search); ?>&sort=<?php echo $sort; ?>&order=<?php echo $order; ?>">ก่อนหน้า</a>
                        </li>
                    <?php else: ?>
                        <li class="page-item disabled">
                            <a class="page-link">ก่อนหน้า</a>
                        </li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?php echo ($i == $current_page) ? 'active' : ''; ?>">
                            <a class="page-link"
                                href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&sort=<?php echo $sort; ?>&order=<?php echo $order; ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($current_page < $total_pages): ?>
                        <li class="page-item">
                            <a class="page-link"
                                href="?page=<?php echo $current_page + 1; ?>&search=<?php echo urlencode($search); ?>&sort=<?php echo $sort; ?>&order=<?php echo $order; ?>">ถัดไป</a>
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
    function confirmDelete(product_id) {
        Swal.fire({
            title: 'ยืนยันการลบสินค้า',
            text: 'คุณแน่ใจว่าต้องการลบสินค้านี้?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'ยืนยัน',
            cancelButtonText: 'ยกเลิก',
            confirmButtonColor: '#C7A98B',
            cancelButtonColor: '#cccccc'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('delete_product.php?product_id=' + product_id)
                    .then(response => response.text())
                    .then(data => {
                        if (data.includes("ลบสินค้าเรียบร้อยแล้ว")) {
                            Swal.fire({
                                icon: 'success',
                                title: 'ลบสินค้าเรียบร้อยแล้ว',
                                confirmButtonColor: '#C7A98B'
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'ไม่สามารถลบสินค้าได้',
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