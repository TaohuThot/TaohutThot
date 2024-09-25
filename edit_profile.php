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
    $company_name = isset($_POST['company_name']) ? $_POST['company_name'] : '';
    $company_address = isset($_POST['company_address']) ? $_POST['company_address'] : '';
    $company_phone = isset($_POST['company_phone']) ? $_POST['company_phone'] : '';
    $company_email = isset($_POST['company_email']) ? $_POST['company_email'] : '';
    $delete_image = isset($_POST['delete_image']) ? $_POST['delete_image'] : 0;

    // อัปเดตข้อมูลบริษัท
    $sql = "UPDATE companies 
            SET company_name = ?, company_address = ?, company_phone = ?, company_email = ? 
            WHERE company_id = 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $company_name, $company_address, $company_phone, $company_email);

    if ($stmt->execute()) {
        $alert_message = "<div class='alert alert-success'>
                            <svg class='bi flex-shrink-0 me-2' width='24' height='24' role='img' aria-label='Success:'><use xlink:href='#check-circle-fill'/></svg>
                            ข้อมูลบริษัทถูกอัปเดตแล้ว</div>";
    } else {
        $alert_message = "<div class='alert alert-danger'>เกิดข้อผิดพลาดในการอัปเดตข้อมูลบริษัท</div>";
    }

    // ลบรูปภาพโปรไฟล์ถ้าถูกตั้งค่าให้ลบ
    if ($delete_image == 1) {
        $sql = "SELECT profile_image FROM companies WHERE company_id = 1";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();

        if ($row && !empty($row['profile_image'])) {
            $image_path = 'uploads/' . $row['profile_image'];
            if (file_exists($image_path)) {
                unlink($image_path);
            }
            $sql = "UPDATE companies SET profile_image = NULL WHERE company_id = 1";
            $conn->query($sql);
        }
    }

    // อัปโหลดและบันทึกรูปภาพใหม่
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['profile_image']['tmp_name'];
        $file_name = basename($_FILES['profile_image']['name']);
        $file_path = 'uploads/' . $file_name;

        // สร้างโฟลเดอร์ 'uploads' ถ้ายังไม่มี
        if (!is_dir('uploads')) {
            mkdir('uploads', 0755, true);
        }

        // ย้ายไฟล์ไปยังโฟลเดอร์ 'uploads'
        if (move_uploaded_file($file_tmp, $file_path)) {
            $sql = "UPDATE companies SET profile_image = ? WHERE company_id = 1";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $file_name);
            $stmt->execute();
            $stmt->close();
        } else {
            echo 'เกิดข้อผิดพลาดในการอัปโหลดไฟล์';
        }
    }

    $redirect_script = "<script>
                            setTimeout(function() {
                                window.location.href = 'edit_profile.php';
                            }, 1500);
                        </script>";
}

$sql = "SELECT company_name, company_address, company_phone, company_email, profile_image FROM companies WHERE company_id = 1 LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $company = $result->fetch_assoc();
} else {
    echo "ไม่พบข้อมูลบริษัท";
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <title>แก้ไขโปรไฟล์</title>
</head>

<body>
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-chevron p-3 rounded -3">
                <li class="breadcrumb-item">
                    <a class="link-body-emphasis fw-semibold text-decoration-none" href="home.php">Home</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">แก้ไขโปรไฟล์</li>
            </ol>
        </nav>
        <?php echo $alert_message; ?>
        <?php echo $redirect_script; ?>
        <div class="d-flex justify-content-between align-items-center">
            <h2>แก้ไขโปรไฟล์</h2>
        </div>
        <form method="post" action="edit_profile.php" enctype="multipart/form-data">
            <div class="row">
                <div class="col-xl-4">
                    <div class="mb-4 mt-2 text-center mb-xl-0">
                        <div class="card-header">
                            <h5>รูปโปรไฟล์</h5>
                        </div>
                        <div class="card-body text-center">
                            <?php if ($company['profile_image']): ?>
                                <img class="img-account-profile rounded-circle mb-4"
                                    src="uploads/<?php echo htmlspecialchars($company['profile_image']); ?>"
                                    alt="Profile Image">
                                <br>
                            <?php endif; ?>
                            <input class="form-control" id="inputProfileImage" type="file" name="profile_image">
                            <button type="button" class="btn btn-danger btn-sm mt-2"
                                onclick="document.getElementById('delete_image').value='1'; this.disabled=true; this.innerText='ลบรูปภาพ';">ลบรูปภาพ</button>
                        </div>
                    </div>
                </div>
                <div class="col-xl-8">
                    <div class="card-header mt-2">
                        <h3>รายละเอียด</h3>
                    </div>
                    <div class="card-body">
                        <input type="hidden" id="delete_image" name="delete_image" value="0">
                        <div class="mb-3">
                            <label class="small mb-1" for="inputCompanyName">ชื่อบริษัท</label>
                            <input class="form-control" id="inputCompanyName" type="text" name="company_name"
                                placeholder="Enter your company name"
                                value="<?php echo htmlspecialchars($company['company_name']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="inputCompanyAddress" class="form-label">ที่อยู่บริษัท</label>
                            <textarea class="form-control" id="inputCompanyAddress" name="company_address" rows="3"
                                required><?php echo htmlspecialchars($company['company_address']); ?></textarea>
                        </div>
                        <div class="row gx-3 mb-3">
                            <div class="col-md-6">
                                <label class="small mb-1" for="inputCompanyEmail">อีเมล์</label>
                                <input class="form-control" id="inputCompanyEmail" type="email" name="company_email"
                                    placeholder="Enter your email address"
                                    value="<?php echo htmlspecialchars($company['company_email']); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="small mb-1" for="inputCompanyPhone">โทรศัพท์</label>
                                <input class="form-control" id="inputCompanyPhone" type="tel" name="company_phone"
                                    placeholder="Enter your phone number"
                                    value="<?php echo htmlspecialchars($company['company_phone']); ?>">
                            </div>
                        </div>
                        <button class="btn btn-outline-secondary custom-btn-t" type="submit">บันทึก</button>
                        <a href="Home.php" class="btn btn-outline-secondary custom-btn-s">ยกเลิก</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</body>

</html>
