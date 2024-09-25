<?php
include 'inc/check_login.php';
checkLogin();
include 'db.php';
include 'header.php';
include 'inc/links.php';

// ตรวจสอบว่ามีการส่งค่า customer_id มาหรือไม่
if (isset($_GET['customer_id'])) {
    $customer_id = $_GET['customer_id'];

    // ดึงข้อมูลลูกค้าตาม customer_id
    $sql = "SELECT * FROM customers WHERE customer_id = $customer_id";
    $result = $conn->query($sql);

    // ตรวจสอบว่าพบข้อมูลลูกค้าหรือไม่
    if ($result->num_rows > 0) {
        $customer = $result->fetch_assoc();
    } else {
        echo "ไม่พบข้อมูลลูกค้า";
        exit;
    }
} else {
    echo "ไม่มีข้อมูลลูกค้า";
    exit;
}

$alert_message = '';
$redirect_script = '';

// อัพเดทข้อมูลลูกค้าเมื่อฟอร์มถูกส่ง
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customer_name = $_POST['customer_name'];
    $address = $_POST['address'];
    $district = $_POST['district'];
    $city = $_POST['city'];
    $province = $_POST['province'];
    $phonenumber = $_POST['phonenumber'];

    // อัพเดทข้อมูลลูกค้าในฐานข้อมูล
    $sql = "UPDATE customers SET customer_name='$customer_name', address='$address', district='$district', city='$city', province='$province', phonenumber='$phonenumber' WHERE customer_id=$customer_id";
    $stmt = $conn->prepare($sql);
    // $stmt->bind_param("ssdii", $customer_name, $address, $district, $city, $province, $phonenumber, $customer_id);


    if ($conn->query($sql) === TRUE) {
        $alert_message = "<div class='alert alert-success d-flex align-items-center' role='alert'>
                                <svg class='bi flex-shrink-0 me-2' width='24' height='24' role='img' aria-label='Success:'><use xlink:href='#check-circle-fill'/></svg>
                                <div>แก้ไขข้อมูลลูกค้าเสร็จเรียบร้อย</div>
                        </div>";
        $redirect_script = "<script>
                                setTimeout(function() {
                                    window.location.href = 'customer.php';
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
    <title>แก้ไขข้อมูลลูกค้า</title>
</head>

<body>
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-chevron p-3 rounded -3">
                <li class="breadcrumb-item">
                    <a class="link-body-emphasis fw-semibold text-decoration-none" href="home.php">Home</a>
                </li>
                <li class="breadcrumb-item">
                    <a class="link-body-emphasis fw-semibold text-decoration-none" href="customer.php">ลูกค้า</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">แก้ไขข้อมูลลูกค้า</li>
            </ol>
        </nav>
        <?php echo $alert_message; ?>
        <?php echo $redirect_script; ?>
        <h2>แก้ไขข้อมูลลูกค้า</h2>
        <form method="post" action="edit_customer.php?customer_id=<?php echo $customer_id; ?>">
            <div class="mb-3">
                <label for="customer_name" class="form-label">ชื่อลูกค้า</label>
                <input type="text" class="form-control" id="customer_name" name="customer_name"
                    value="<?php echo $customer['customer_name']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">ที่อยู่</label>
                <input type="text" class="form-control" id="address" name="address"
                    value="<?php echo $customer['address']; ?>" required>
            </div>
            <div class="row gx-3 mb-3">
                <div class="col-md-6">
                    <label for="district" class="form-label">ตำบล</label>
                    <input type="text" class="form-control" id="district" name="district"
                        value="<?php echo $customer['district']; ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="city" class="form-label">อำเภอ</label>
                    <input type="text" class="form-control" id="city" name="city"
                        value="<?php echo $customer['city']; ?>" required>
                </div>
            </div>
            <div class="mb-3">
                <label for="province" class="form-label">จังหวัด</label>
                <input type="text" class="form-control" id="province" name="province"
                    value="<?php echo $customer['province']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="phonenumber" class="form-label">โทรศัพท์</label>
                <input type="text" class="form-control" id="phonenumber" name="phonenumber" maxlength="10"
                    pattern="\d{10}" title="โปรดกรอกหมายเลขโทรศัพท์ที่มี 10 หลัก"
                    value="<?php echo $customer['phonenumber']; ?>" required>
            </div>
            <button type="submit" class="btn btn-primary custom-btn-t">บันทึก</button>
            <a href="customer.php" class="btn btn-outline-secondary custom-btn-s">ยกเลิก</a>
        </form>
    </div>
</body>

</html>