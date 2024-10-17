<?php
include 'inc/check_login.php';
checkLogin();
include 'header.php';
include 'db.php';
include 'inc/links.php';

// การตั้งค่าค่าต่างๆ สำหรับการค้นหาและการนำทางเพจ
$search = isset($_GET['search']) ? $_GET['search'] : '';
$records_per_page = 10; // จำนวนข้อมูลที่จะแสดงต่อหน้า
$current_page = isset($_GET['page']) ? intval($_GET['page']) : 1; // หน้าปัจจุบัน
$offset = ($current_page - 1) * $records_per_page; // คำนวณตำแหน่งเริ่มต้นของข้อมูลในหน้านั้น

// คำสั่ง SQL สำหรับดึงข้อมูลพนักงาน
$sql = "SELECT * FROM employees";

if ($search) {
    $sql .= " WHERE name LIKE ? OR address LIKE ?";
}

$sql .= " LIMIT ? OFFSET ?";

$stmt = $conn->prepare($sql);

if ($search) {
    $search_param = "%$search%";
    $stmt->bind_param("ssii", $search_param, $search_param, $records_per_page, $offset);
} else {
    $stmt->bind_param("ii", $records_per_page, $offset);
}

$stmt->execute();
$result = $stmt->get_result();

// คำสั่ง SQL สำหรับนับจำนวนพนักงานทั้งหมด
$count_sql = "SELECT COUNT(*) as total_records FROM employees";
if ($search) {
    $count_sql .= " WHERE name LIKE ? OR address LIKE ?";
    $count_stmt = $conn->prepare($count_sql);
    $count_stmt->bind_param("ss", $search_param, $search_param);
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
<html>

<head>
    <title>พนักงาน</title>
</head>

<body>
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-chevron p-3 rounded -3">
                <li class="breadcrumb-item">
                    <a class="link-body-emphasis fw-semibold text-decoration-none" href="home.php">Home</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">พนักงาน</li>
            </ol>
        </nav>
        <div class="d-flex justify-content-between align-items-center">
            <h2>พนักงาน</h2>
            <div class="d-flex flex-grow-1 justify-content-end align-items-center gap-2">
                <div>
                    <a href="add_employee.php" class="btn btn-primary custom-btn">+ เพิ่มพนักงาน</a>
                </div>
                <form class="d-flex m-0" action="" method="get">
                    <input class="form-control me-2" type="search" name="search" placeholder="ค้นหาพนักงาน"
                        aria-label="Search" value="<?php echo htmlspecialchars($search); ?>">
                    <button class="btn btn-outline-secondary custom-btn-t" type="submit">ค้นหา</button>
                </form>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-striped ">
                <thead>
                    <tr>
                        <th class='text-center'>ลำดับ</th>
                        <th class='text-center'>รหัสพนักงาน</th>
                        <th>ชื่อพนักงาน</th>
                        <th>ที่อยู่</th>
                        <th>ตำบล</th>
                        <th>อำเภอ</th>
                        <th>จังหวัด</th>
                        <th>โทรศัพท์</th>
                        <th class='text-center'>รายละเอียด</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        $row_number = $offset + 1;
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                        <td class = 'text-center'>{$row_number}</td>
                                        <td class = 'text-center'>{$row['employee_id']}</td>
                                        <td class='text-truncate' style='max-width: 150px; overflow: hidden; white-space: nowrap;' >{$row['name']}</td>
                                        <td class='text-truncate' style='max-width: 150px; overflow: hidden; white-space: nowrap;' >{$row['address']}</td>
                                        <td>{$row['district']}</td>
                                        <td>{$row['city']}</td>
                                        <td>{$row['province']}</td>
                                        <td>{$row['phone']}</td>
                                        <td class = 'text-center'>
                                            <a href='edit_employee.php?employee_id=" . $row['employee_id'] . "' class='custom-link'>
                                                <i class='bi bi-pencil'></i> แก้ไขรายละเอียด
                                            </a> &nbsp;
                                             <a href='edit_employee_password.php?employee_id=" . $row['employee_id'] . "' class='custom-link'>
                                                <i class='bi bi-pencil'></i> เปลียนรหัสผ่าน
                                            </a> &nbsp;
                                            <a href='#' class='custom-link' onclick='confirmDelete(" . $row['employee_id'] . ")'>
                                                <i class='bi bi-trash'></i> ลบ                                            
                                            </a>
                                        </td>
                                    </tr>";
                            $row_number++;

                        }
                    } else {
                        echo "<tr><td colspan='8'>ไม่มีข้อมูลพนักงาน</td></tr>";
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
                        echo "จำนวนพนักงาน $start_number - $end_number จากทั้งหมด $total_records คน";
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
        <script>
            let employeeIdToDelete;

            function confirmDelete(employee_id) {
                employeeIdToDelete = employee_id;
                Swal.fire({
                    title: 'ยืนยันการลบพนักงาน',
                    text: 'คุณแน่ใจว่าต้องการลบพนักงานนี้?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'ยืนยัน',
                    cancelButtonText: 'ยกเลิก',
                    confirmButtonColor: '#C7A98B',  // เปลี่ยนสีปุ่มยืนยัน
                    cancelButtonColor: '#cccccc'    // เปลี่ยนสีปุ่มยกเลิก
                }).then((result) => {
                    if (result.isConfirmed) {
                        // ใช้ fetch เพื่อส่งคำขอลบ
                        fetch('delete_employee.php?employee_id=' + employeeIdToDelete)
                            .then(response => response.text())
                            .then(data => {
                                if (data.includes("ลบพนักงานเรียบร้อยแล้ว")) {
                                    Swal.fire({
                                        icon: "success",
                                        title: "ลบพนักงานเรียบร้อยแล้ว",
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

    </div>
</body>

</html>