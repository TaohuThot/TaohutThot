<?php
include 'inc/check_login.php';
checkLogin();
include 'db.php';
include 'header.php';
include 'inc/links.php';

$alert_message = "";
$redirect_script = "";
$employee_id = isset($_GET['employee_id']) ? intval($_GET['employee_id']) : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_username = isset($_POST['new_username']) ? $_POST['new_username'] : '';
    $new_password = isset($_POST['new_password']) ? $_POST['new_password'] : '';
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

    // อัปเดตเฉพาะ username ถ้ามีการกรอกข้อมูล
    if (!empty($new_username)) {
        $sql_user = "UPDATE users SET username = ? WHERE employee_id = ?";
        $stmt_user = $conn->prepare($sql_user);
        $stmt_user->bind_param("si", $new_username, $employee_id);
        if ($stmt_user->execute()) {
            $alert_message = "<div class='alert alert-success' id='alertMessage'>
                            <svg class='bi flex-shrink-0 me-2' width='24' height='24' role='img' aria-label='Success:'><use xlink:href='#check-circle-fill'/></svg>
                            ชื่อผู้ใช้งานถูกอัปเดตแล้ว</div>";
            
            $redirect_script = "<script>
                                        setTimeout(function() {
                                            window.location.href = 'employee.php';
                                        }, 1500);
                                    </script>";
        } else {
            $alert_message = "<div class='alert alert-danger' id='alertMessage'>เกิดข้อผิดพลาดในการอัปเดตชื่อผู้ใช้งาน</div>";
            $redirect_script = "<script>
                                        setTimeout(function() {
                                            window.location.href = 'edit_employee_password.php';
                                        }, 1500);
                                    </script>";
        }
    }

    // ตรวจสอบว่ารหัสผ่านและยืนยันรหัสผ่านถูกกรอก
    if (!empty($new_password) && !empty($confirm_password)) {
        // ตรวจสอบว่ารหัสผ่านตรงกัน
        if ($new_password === $confirm_password) {
            // ทำการ hash รหัสผ่านใหม่
            $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

            // อัปเดตรหัสผ่านในฐานข้อมูล
            $sql = "UPDATE users SET password = ? WHERE employee_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $hashed_password, $employee_id);

            if ($stmt->execute()) {
                $alert_message = "<div class='alert alert-success' id='alertMessage'>
                                    <svg class='bi flex-shrink-0 me-2' width='24' height='24' role='img' aria-label='Success:'><use xlink:href='#check-circle-fill'/></svg>
                                    รหัสผ่านถูกเปลี่ยนเรียบร้อยแล้ว</div>";
                $redirect_script = "<script>
                                        setTimeout(function() {
                                            window.location.href = 'employee.php';
                                        }, 1500);
                                    </script>";
            } else {
                $alert_message = "<div class='alert alert-danger'>เกิดข้อผิดพลาดในการอัปเดตรหัสผ่าน</div>";
                $redirect_script = "<script>
                setTimeout(function() {
                    window.location.href = 'edit_employee_password.php';
                }, 1500);
            </script>";
            }
        } else {
            $alert_message = "<div class='alert alert-danger'>รหัสผ่านไม่ตรงกัน</div>";
            $redirect_script = "<script>
                                        setTimeout(function() {
                                            window.location.href = 'edit_employee_password.php';
                                        }, 1500);
                                    </script>";
        }
    }
}

// ดึงชื่อพนักงานเพื่อนำมาแสดง
$sql = "SELECT name FROM employees WHERE employee_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $employee_id);
$stmt->execute();
$result = $stmt->get_result();
$employee = $result->fetch_assoc();

$conn->close();
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <title>เปลี่ยนรหัสผ่านพนักงาน</title>
</head>

<body>
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-chevron p-3 rounded -3">
                <li class="breadcrumb-item">
                    <a class="link-body-emphasis fw-semibold text-decoration-none" href="home.php">Home</a>
                </li>
                <li class="breadcrumb-item">
                    <a class="link-body-emphasis fw-semibold text-decoration-none" href="employee.php">พนักงาน</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">เปลี่ยนรหัสผ่าน</li>
            </ol>
        </nav>
        <?php echo $alert_message; ?>
        <?php echo $redirect_script; ?>
        <div class="d-flex justify-content-between align-items-center">
            <h2>เปลี่ยนชื่อผู้ใช้ / รหัสผ่านพนักงาน</h2>
        </div>
        <form method="post" action="edit_employee_password.php?employee_id=<?php echo $employee_id; ?>">
            <div class="mb-3">
                <label for="new_username">ชื่อผู้ใช้</label>
                <input type="text" name="new_username" class="form-control" placeholder="ชื่อผู้ใช้">
            </div>
            <div class="row gx-3 mb-3">
                <div class="col-md-6">
                    <label for="new_password">รหัสผ่าน</label>
                    <input type="password" name="new_password" class="form-control" placeholder="รหัสผ่าน">
                </div>
                <div class="col-md-6">
                    <label for="confirm_password">ยืนยันรหัสผ่าน</label>
                    <input type="password" name="confirm_password" class="form-control"
                        placeholder="ยืนยันรหัสผ่าน">
                </div>
            </div>
            <button type="submit" class="btn btn-primary custom-btn-t">บันทึก</button>
            <a href="employee.php" class="btn btn-outline-secondary custom-btn-s">ยกเลิก</a>
        </form>
    </div>
</body>

</html>