<?php
include 'inc/check_login.php';
checkLogin();
include 'header.php';
include 'db.php';
include 'inc/links.php';

$search = isset($_GET['search']) ? $_GET['search'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'product_name'; // Default sorting column
$order = isset($_GET['order']) ? $_GET['order'] : 'ASC'; // Default sorting order
$records_per_page = 8; // จำนวนข้อมูลที่จะแสดงต่อหน้า
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
        <div class="d-flex justify-content-between align-items-center mb-2 ">
            <h2>สินค้า</h2>
            <div class="me-4"></div>
            <a href="product.php" class="btn btn-outline-secondary custom-btn me-2">
                <i class="bi-table "></i>
            </a>
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
        <div class="row">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='col-md-3 mb-3'>
                    <a href='detail_product.php?product_id=" . htmlspecialchars($row['product_id']) . "' class='text-decoration-none text-dark'>
                        <div class='card' style='width: 19.5rem;'>
                            <img class='card-img-top' src='uploads/" . htmlspecialchars($row['image']) . "' alt='Product Image' style='width: 307px; height: 200px; object-fit: cover;'>
                            <div class='card-body'>
                                <h5 class='card-title'>" . htmlspecialchars($row['product_name']) . "</h5>
                                <h6 class='card-text mb-1'>ราคา: &nbsp; &nbsp; &nbsp; &nbsp; ฿" . number_format(htmlspecialchars($row['price']), 2) . "</h6>
                                <h6 class='card-text'>คงเหลือ: &nbsp; &nbsp;" . htmlspecialchars($row['quantity']) . "</h6>
                            </div>
                        </div>
                    </a>
                </div>";
                }
            } else {
                echo "<div class='col-12'><p>ไม่มีข้อมูลสินค้า</p></div>";
            }

            $stmt->close();
            $conn->close();
            ?>
        </div>

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
</body>

</html>