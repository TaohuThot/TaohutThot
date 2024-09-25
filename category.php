<?php
include 'inc/check_login.php';
checkLogin();
include 'db.php';
include 'header.php';
include 'inc/links.php';

// Search functionality
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sql = "SELECT c.*, COUNT(p.product_id) AS product_count 
        FROM categories c 
        LEFT JOIN products p ON c.category_id = p.category_id 
        WHERE c.category_name LIKE '%" . $conn->real_escape_string($search) . "%' 
        GROUP BY c.category_id";
$result = $conn->query($sql);
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
                    $conn->close();
                    ?>
                </tbody>
            </table>
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