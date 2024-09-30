<?php
include 'inc/check_login.php';
checkLogin();
include 'db.php';
include 'header.php';
include 'inc/links.php';

// ตรวจสอบว่ามีการส่งค่า customer_id มาหรือไม่
if (isset($_GET['employee_id'])) {
    $employee_id = $_GET['employee_id'];

    // ดึงข้อมูลลูกค้าตาม customer_id
    $sql = "SELECT * FROM employees WHERE employee_id = $employee_id";
    $result = $conn->query($sql);

    // ตรวจสอบว่าพบข้อมูลลูกค้าหรือไม่
    if ($result->num_rows > 0) {
        $employees = $result->fetch_assoc();
    } else {
        echo "ไม่พบข้อมูลพนักงาน";
        exit;
    }
} else {
    echo "ไม่มีข้อมูลพนักงาน";
    exit;
}

$alert_message = '';
$redirect_script = '';

// อัพเดทข้อมูลลูกค้าเมื่อฟอร์มถูกส่ง
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $address = $_POST['address'];
    $district = $_POST['district'];
    $city = $_POST['city'];
    $province = $_POST['province'];
    $phone = $_POST['phone'];

    // อัพเดทข้อมูลลูกค้าในฐานข้อมูล
    $sql = "UPDATE employees SET name='$name', address='$address', district='$district', city='$city', province='$province', phone='$phone' WHERE employee_id=$employee_id";
    $stmt = $conn->prepare($sql);

    if ($conn->query($sql) === TRUE) {
        $alert_message = "<div class='alert alert-success d-flex align-items-center' role='alert'>
                                <svg class='bi flex-shrink-0 me-2' width='24' height='24' role='img' aria-label='Success:'><use xlink:href='#check-circle-fill'/></svg>
                                <div>แก้ไขข้อมูลพนักงานเสร็จเรียบร้อย</div>
                        </div>";
        $redirect_script = "<script>
                                setTimeout(function() {
                                    window.location.href = 'employee.php';
                                }, 1500);
                            </script>";
    } else {
        echo "Error: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <title>แก้ไขข้อมูลพนักงาน</title>
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
                <li class="breadcrumb-item active" aria-current="page">แก้ไขข้อมูลพนักงาน</li>
            </ol>
        </nav>
        <?php echo $alert_message; ?>
        <?php echo $redirect_script; ?>
        <h2>แก้ไขข้อมูลพนักงาน</h2>
        <form method="post" action="edit_employee.php?employee_id=<?php echo $employee_id; ?>">
            <div class="mb-3">
                <label for="name" class="form-label">ชื่อพนักงาน</label>
                <input type="text" class="form-control" id="name" name="name" autocomplete="off"
                    value="<?php echo $employees['name']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">ที่อยู่</label>
                <input type="text" class="form-control" id="address" name="address" autocomplete="off"
                    value="<?php echo $employees['address']; ?>" required>
            </div>
            <div class="row gx-3 mb-3">
                <div class="col-md-6">
                    <label for="district" class="form-label">ตำบล</label>
                    <input type="text" class="form-control" id="district" name="district" autocomplete="off"
                        value="<?php echo $employees['district']; ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="city" class="form-label">อำเภอ</label>
                    <input type="text" class="form-control" id="amphoe" name="city" autocomplete="off"
                        value="<?php echo $employees['city']; ?>" required>
                </div>
            </div>
            <div class="mb-3">
                <label for="province" class="form-label">จังหวัด</label>
                <input type="text" class="form-control" id="province" name="province" autocomplete="off"
                    value="<?php echo $employees['province']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">โทรศัพท์</label>
                <input type="number" class="form-control" id="phone" name="phone" maxlength="10" pattern="\d{10}"
                    title="โปรดกรอกหมายเลขโทรศัพท์ที่มี 10 หลัก" value="<?php echo $employees['phone']; ?>"
                    autocomplete="off" required>
            </div>
            <button type="submit" class="btn btn-primary custom-btn-t">บันทึก</button>
            <a href="employee.php" class="btn btn-outline-secondary custom-btn-s">ยกเลิก</a>
        </form>
    </div>
</body>
<?php
include 'inc/script.php';
?>

</html>