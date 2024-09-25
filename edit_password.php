<?php
include 'inc/check_login.php';
checkLogin();
ob_start();
include 'header.php';
include 'db.php';
include 'inc/links.php';

$alert_message = "";
$redirect_script = "";

// ตรวจสอบว่ามีการส่งข้อมูลผ่านฟอร์มหรือไม่
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_username = isset($_POST['new_username']) ? $_POST['new_username'] : '';
    $new_password = isset($_POST['new_password']) ? $_POST['new_password'] : '';
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

    // อัปเดตเฉพาะ username ถ้ามีการกรอกข้อมูล
    if (!empty($new_username)) {
        $sql_user = "UPDATE users SET username = ? WHERE role = 'admin'";
        $stmt_user = $conn->prepare($sql_user);
        $stmt_user->bind_param("s", $new_username);
        if ($stmt_user->execute()) {
            $alert_message = "<div class='alert alert-success'>ชื่อผู้ใช้งานถูกอัปเดตแล้ว</div>";
            $redirect_script = "<script>
                                    setTimeout(function() {
                                        window.location.href = 'edit_password.php';
                                    }, 1500);
                                </script>";
        } else {
            $alert_message = "<div class='alert alert-danger'>เกิดข้อผิดพลาดในการอัปเดตชื่อผู้ใช้งาน</div>";
        }
    }

    // ตรวจสอบว่าทั้งสองฟิลด์ถูกกรอก
    if (!empty($new_password) && !empty($confirm_password)) {
        // ตรวจสอบว่ารหัสผ่านและยืนยันรหัสผ่านตรงกัน
        if ($new_password === $confirm_password) {
            $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
            $sql_password = "UPDATE users SET password = ? WHERE role = 'admin'";
            $stmt_password = $conn->prepare($sql_password);
            $stmt_password->bind_param("s", $hashed_password);
            if ($stmt_password->execute()) {
                $alert_message = "<div class='alert alert-success'>
                                    <svg class='bi flex-shrink-0 me-2' width='24' height='24' role='img' aria-label='Success:'><use xlink:href='#check-circle-fill'/></svg>
                                    รหัสผ่านถูกอัปเดตแล้ว</div>";
                $redirect_script = "<script>
                                        setTimeout(function() {
                                            window.location.href = 'edit_password.php';
                                        }, 1500);
                                    </script>";
            } else {
                $alert_message = "<div class='alert alert-danger'>เกิดข้อผิดพลาดในการอัปเดตรหัสผ่าน</div>";
            }
        } else {
            $alert_message = "<div class='alert alert-danger'>รหัสผ่านไม่ตรงกัน</div>";
            $redirect_script = "<script>
                                        setTimeout(function() {
                                            window.location.href = 'edit_password.php';
                                        }, 1500);
                                    </script>";
        }
    } else if (!empty($new_password) || !empty($confirm_password)) {
        $alert_message = "<div class='alert alert-danger'>กรุณากรอกทั้งรหัสผ่านใหม่และยืนยันรหัสผ่าน</div>";
        $redirect_script = "<script>
                                        setTimeout(function() {
                                            window.location.href = 'edit_password.php';
                                        }, 1500);
                                    </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <title>เปลี่ยนรหัสผ่าน</title>
</head>

<body>
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-chevron p-3 rounded -3">
                <li class="breadcrumb-item"><a class="link-body-emphasis fw-semibold text-decoration-none" href="home.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">เปลี่ยนชื่อผู้ใช้ / รหัสผ่าน</li>
            </ol>
        </nav>
        <?php echo $alert_message; ?>
        <?php echo $redirect_script; ?>
        <div class="d-flex justify-content-between align-items-center">
            <h2>เปลี่ยนชื่อผู้ใช้ / รหัสผ่าน</h2>
        </div>
        <form method="post" action="edit_password.php">
            <div class="mb-3">
                <label class="small mb-1" for="new_username">ชื่อผู้ใช้</label>
                <input class="form-control" id="new_username" type="text" name="new_username" placeholder="ชื่อผู้ใช้">
            </div>
            <div class="row gx-3 mb-3">
                <div class="col-md-6">
                    <label class="small mb-1" for="new_password">รหัสผ่าน</label>
                    <input class="form-control" id="new_password" type="password" name="new_password" placeholder="รหัสผ่าน">
                </div>
                <div class="col-md-6">
                    <label class="small mb-1" for="confirm_password">ยืนยันรหัสผ่าน</label>
                    <input class="form-control" id="confirm_password" type="password" name="confirm_password" placeholder="ยืนยันรหัสผ่าน">
                </div>
            </div>
            <button class="btn btn-outline-secondary custom-btn-t" type="submit">บันทึก</button>
            <a href="home.php" class="btn btn-outline-secondary custom-btn-s">ยกเลิก</a>
        </form>
    </div>
</body>

</html>
