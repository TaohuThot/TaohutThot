<?php
include 'inc/check_login.php';
checkLogin();
include 'db.php';
include 'header.php';
include 'inc/links.php';

$alert_message = '';
$redirect_script = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customer_name = $_POST['customer_name'];
    $address = $_POST['address'];
    $district = $_POST['district'];
    $city = $_POST['city'];
    $province = $_POST['province'];
    $phonenumber = $_POST['phonenumber'];

    $sql = "INSERT INTO customers (customer_name, address, district, city, province, phonenumber)
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $customer_name, $address, $district, $city, $province, $phonenumber);

    if ($stmt->execute()) {
        $alert_message = "<div class='alert alert-success d-flex align-items-center' role='alert'>
                        <svg class='bi flex-shrink-0 me-2' width='24' height='24' role='img' aria-label='Success:'><use xlink:href='#check-circle-fill'/></svg>
                        <div>เพิ่มลูกค้าใหม่เรียบร้อย</div>
                      </div>";
        $redirect_script = "<script>
                            setTimeout(function() {
                                window.location.href = 'customer.php';
                            }, 1500);
                        </script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>เพิ่มลูกค้า</title>
</head>

<body>
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-chevron p-3 rounded">
                <li class="breadcrumb-item">
                    <a class="link-body-emphasis fw-semibold text-decoration-none" href="home.php">Home</a>
                </li>
                <li class="breadcrumb-item">
                    <a class="link-body-emphasis fw-semibold text-decoration-none" href="customer.php">ลูกค้า</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">เพิ่มลูกค้า</li>
            </ol>
        </nav>
        <?php echo $alert_message; ?>
        <?php echo $redirect_script; ?>
        <h2>เพิ่มลูกค้า</h2>
        <form method="post" action="add_customer.php">
            <div class="mb-3">
                <label for="name" class="form-label">ชื่อลูกค้า</label>
                <input type="text" class="form-control" id="customer_name" name="customer_name" placeholder="ชื่อลูกค้า"
                    autocomplete="off" required>
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">ที่อยู่</label>
                <textarea class="form-control" id="address" name="address" rows="3" placeholder="ที่อยู่"
                    autocomplete="off" required></textarea>
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
                <input type="number" class="form-control" id="phonenumber" name="phonenumber" maxlength="10"
                    pattern="\d{10}" title="โปรดกรอกหมายเลขโทรศัพท์ที่มี 10 หลัก" placeholder="หมายเลขโทรศัพท์"
                    required>
            </div>
            <button type="submit" class="btn btn-outline-secondary custom-btn-t">ยืนยัน</button>
            <a href="customer.php" class="btn btn-outline-secondary custom-btn-s">ยกเลิก</a>
        </form>
    </div>
</body>
<?php
include 'inc/script.php';
?>

</html>