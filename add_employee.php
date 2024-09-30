<?php
include 'inc/check_login.php';
checkLogin();
include 'db.php';
include 'header.php';
include 'inc/links.php';

$alert_message = '';
$redirect_script = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $address = $_POST['address'];
    $district = $_POST['district'];
    $city = $_POST['city'];
    $province = $_POST['province'];
    $phone = $_POST['phone'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // ตรวจสอบว่ารหัสผ่านและยืนยันรหัสผ่านตรงกันหรือไม่
    if ($password !== $confirm_password) {
        $alert_message = "<div class='alert alert-danger'>รหัสผ่านไม่ตรงกัน</div>";
    } else {
        // แปลงรหัสผ่านเป็น hash
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // เพิ่มข้อมูลพนักงานในตาราง employees
        $sql = "INSERT INTO employees (name, address, district, city, province, phone)
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $name, $address, $district, $city, $province, $phone);

        if ($stmt->execute()) {
            // เพิ่มข้อมูลผู้ใช้ในตาราง users
            $employee_id = $stmt->insert_id; // ดึง employee_id ล่าสุดจากการเพิ่มข้อมูลพนักงาน
            $sql_user = "INSERT INTO users (username, password, role, employee_id)
                         VALUES (?, ?, 'user', ?)";
            $stmt_user = $conn->prepare($sql_user);
            $stmt_user->bind_param("ssi", $username, $hashed_password, $employee_id);

            if ($stmt_user->execute()) {
                $alert_message = "<div class='alert alert-success d-flex align-items-center' role='alert'>
                                    <svg class='bi flex-shrink-0 me-2' width='24' height='24' role='img' aria-label='Success:'><use xlink:href='#check-circle-fill'/></svg>
                                    <div>เพิ่มพนักงานใหม่เรียบร้อย</div>
                                  </div>";
                $redirect_script = "<script>
                                        setTimeout(function() {
                                            window.location.href = 'employee.php';
                                        }, 1500);
                                    </script>";
            } else {
                $alert_message = "<div class='alert alert-danger'>เกิดข้อผิดพลาดในการเพิ่มผู้ใช้</div>";
            }
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
        $stmt_user->close();
        $conn->close();
    }
}

?>
<!DOCTYPE html>
<html>

<head>
    <title>เพิ่มพนักงาน</title>
</head>

<body>
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-chevron p-3 rounded">
                <li class="breadcrumb-item">
                    <a class="link-body-emphasis fw-semibold text-decoration-none" href="home.php">Home</a>
                </li>
                <li class="breadcrumb-item">
                    <a class="link-body-emphasis fw-semibold text-decoration-none" href="employee.php">พนักงาน</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">เพิ่มพนักงาน</li>
            </ol>
        </nav>
        <?php echo $alert_message; ?>
        <?php echo $redirect_script; ?>
        <h2>เพิ่มพนักงาน</h2>
        <form method="post" action="add_employee.php">
            <div class="mb-3">
                <label for="name" class="form-label">ชื่อพนักงาน</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="ชื่อพนักงาน"
                    autocomplete="off" required>
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">ที่อยู่</label>
                <textarea class="form-control" id="address" name="address" rows="3" placeholder="ที่อยู่"
                    required></textarea>
            </div>
            <div class="row gx-3 mb-3">
                <div class="col-md-6">
                    <label for="district" class="form-label">ตำบล</label>
                    <input type="text" class="form-control" id="district" name="district" placeholder="ตำบล"
                        autocomplete="off" required>
                </div>
                <div class="col-md-6">
                    <label for="city" class="form-label">อำเภอ</label>
                    <input type="text" class="form-control" id="amphoe" name="city" placeholder="อำเภอ"
                        autocomplete="off" required>
                </div>
            </div>
            <div class="mb-3">
                <label for="province" class="form-label">จังหวัด</label>
                <input type="text" class="form-control" id="province" name="province" placeholder="จังหวัด"
                    autocomplete="off" required>
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">โทรศัพท์</label>
                <input type="number" class="form-control" id="phone" name="phone" maxlength="10" pattern="\d{10}"
                    title="โปรดกรอกหมายเลขโทรศัพท์ที่มี 10 หลัก" placeholder="หมายเลขโทรศัพท์" autocomplete="off"
                    required>
            </div>
            <div class="mb-3">
                <label for="username" class="form-label">ชื่อผู้ใช้</label>
                <input type="text" class="form-control" id="username" name="username" placeholder="ชื่อผู้ใช้"
                    autocomplete="off" required>
            </div>
            <div class="row gx-3 mb-3">
                <div class="col-md-6">
                    <label for="password" class="form-label">รหัสผ่าน</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="รหัสผ่าน"
                        autocomplete="off" required>
                </div>
                <div class="col-md-6">
                    <label for="confirm_password" class="form-label">ยืนยันรหัสผ่าน</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password"
                        placeholder="ยืนยันรหัสผ่าน" autocomplete="off" required>
                </div>
            </div>
            <button type="submit" class="btn btn-outline-secondary custom-btn-t mb-4">ยืนยัน</button>
            <a href="employee.php" class="btn btn-outline-secondary custom-btn-s mb-4">ยกเลิก</a>
    </div>
</body>
<?php
include 'inc/script.php';
?>
</html>