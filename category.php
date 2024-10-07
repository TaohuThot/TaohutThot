<?php
include 'inc/check_login.php';
checkLogin();
include 'db.php';
include 'header.php';
include 'inc/links.php';

$search = isset($_GET['search']) ? $_GET['search'] : ''; // รับค่า search
$records_per_page = 10; // จำนวนข้อมูลที่จะแสดงต่อหน้า
$current_page = isset($_GET['page']) ? intval($_GET['page']) : 1; // หน้าปัจจุบัน
$offset = ($current_page - 1) * $records_per_page; // คำนวณตำแหน่งเริ่มต้นของข้อมูลในหน้านั้น

// เตรียมคำสั่ง SQL สำหรับดึงข้อมูลหมวดหมู่พร้อมนับจำนวนสินค้าในแต่ละหมวดหมู่
$sql = "SELECT c.*, COUNT(p.product_id) AS product_count 
        FROM categories c 
        LEFT JOIN products p ON c.category_id = p.category_id 
        WHERE c.category_name LIKE ? 
        GROUP BY c.category_id 
        LIMIT ?, ?";

// เตรียมคำสั่งสำหรับการดึงข้อมูล
$stmt = $conn->prepare($sql);
$search_param = "%$search%";
$stmt->bind_param("sii", $search_param, $offset, $records_per_page);
$stmt->execute();
$result = $stmt->get_result();

// คำสั่ง SQL สำหรับนับจำนวนหมวดหมู่ทั้งหมด
$count_sql = "SELECT COUNT(*) as total_records FROM categories WHERE category_name LIKE ?";
$count_stmt = $conn->prepare($count_sql);
$count_stmt->bind_param("s", $search_param);
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$total_records = $count_result->fetch_assoc()['total_records'];
$total_pages = ceil($total_records / $records_per_page); // คำนวณจำนวนหน้าทั้งหมด
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <title>หมวดหมู่สินค้า</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-chevron p-3 rounded -3">
                <li class="breadcrumb-item">
                    <a class="link-body-emphasis fw-semibold text-decoration-none" href="home.php">Home</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">หมวดหมู่</li>
            </ol>
        </nav>
        <div class="d-flex justify-content-between align-items-center">
            <h2>หมวดหมู่สินค้า</h2>
            <div class="d-flex flex-grow-1 justify-content-end align-items-center gap-2">
                <div>
                    <?php if ($_SESSION['role'] === 'admin'): ?>
                        <a href="add_category.php" class="btn btn-primary custom-btn">+ เพิ่มหมวดหมู่</a>
                    <?php endif; ?>
                </div>
                <form class="d-flex m-0" action="" method="get">
                    <input class="form-control me-2" type="search" name="search" placeholder="ค้นหาหมวดหมู่"
                        aria-label="Search" value="<?php echo htmlspecialchars($search); ?>">
                    <button class="btn btn-outline-secondary custom-btn-t" type="submit">ค้นหา</button>
                </form>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th class='text-center'>รหัสหมวดหมู่</th>
                        <th>ชื่อหมวดหมู่</th>
                        <th class='text-center'>จำนวนสินค้าในหมวดหมู่</th>
                        <?php if ($_SESSION['role'] === 'admin'): ?>
                            <th class='text-center'>รายละเอียด</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td class='text-center'>" . htmlspecialchars($row['category_id']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['category_name']) . "</td>";
                            echo "<td class='text-center'>" . htmlspecialchars($row['product_count']) . "</td>";
                            if ($_SESSION['role'] === 'admin') {
                                echo "<td class='text-center'>";
                                echo "<a href='edit_category.php?category_id=" . $row['category_id'] . "' class='custom-link'>
                                    <i class='bi bi-pencil'></i> แก้ไขรายละเอียด
                                </a> &nbsp;";
                                echo "<a href='#' class='custom-link' onclick='confirmDelete(" . $row['category_id'] . ")'>
                                    <i class='bi bi-trash'></i> ลบ
                                </a>";
                                echo "</td>";
                            }
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4' class='text-center'>ไม่มีข้อมูลหมวดหมู่</td></tr>";
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
                        echo "จำนวนหมวดหมู่ $start_number - $end_number จากทั้งหมด $total_records รายการ";
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
        let categoryIdToDelete;

        function confirmDelete(categoryId) {
            categoryIdToDelete = categoryId;
            Swal.fire({
                title: 'ยืนยันการลบหมวดหมู่',
                text: 'คุณแน่ใจว่าต้องการลบหมวดหมู่นี้?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'ยืนยัน',
                cancelButtonText: 'ยกเลิก',
                confirmButtonColor: '#C7A98B',  // เปลี่ยนสีปุ่มยืนยัน
                cancelButtonColor: '#cccccc'    // เปลี่ยนสีปุ่มยกเลิก
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('delete_category.php?category_id=' + categoryIdToDelete)
                        .then(response => response.text())
                        .then(data => {
                            if (data === "ลบสำเร็จ") {
                                Swal.fire({
                                    icon: "success",
                                    title: "ลบหมวดหมู่เรียบร้อยแล้ว",
                                    showConfirmButton: false,
                                    timer: 1500
                                }).then(() => {
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: "error",
                                    title: "ไม่สามารถลบได้",
                                    text: data,
                                    showConfirmButton: true,
                                    confirmButtonText: 'ยืนยัน',
                                    confirmButtonColor: '#C7A98B',
                                });
                            }
                        });
                }
            });
        }
    </script>
</body>

</html>
