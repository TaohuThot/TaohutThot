<?php
include 'inc/check_login.php';
checkLogin();
include 'db.php';
include 'header.php';
include 'inc/links.php';

$alert_message = '';
$redirect_script = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $category_name = $_POST['category_name'];


    $sql = "INSERT INTO categories (category_name)
            VALUES (?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $category_name);

    if ($stmt->execute()) {
        $alert_message = "<div class='alert alert-success d-flex align-items-center' role='alert'>
                        <svg class='bi flex-shrink-0 me-2' width='24' height='24' role='img' aria-label='Success:'><use xlink:href='#check-circle-fill'/></svg>
                        <div>เพิ่มหมวดหมู่ใหม่เรียบร้อย</div>
                      </div>";
        $redirect_script = "<script>
                            setTimeout(function() {
                                window.location.href = 'category.php';
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
    <title>เพิ่มหมวดหมู่</title>
</head>

<body>
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-chevron p-3 rounded">
                <li class="breadcrumb-item">
                    <a class="link-body-emphasis fw-semibold text-decoration-none" href="home.php">Home</a>
                </li>
                <li class="breadcrumb-item">
                    <a class="link-body-emphasis fw-semibold text-decoration-none" href="category.php">หมวดหมู่</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">เพิ่มหมวดหมู่</li>
            </ol>
        </nav>
        <?php echo $alert_message; ?>
        <?php echo $redirect_script; ?>
        <h2>เพิ่มหมวดหมู่</h2>
        <form method="post" action="add_category.php">
            <div class="mb-3">
                <label for="name" class="form-label">ชื่อหมวดหมู่</label>
                <input type="text" class="form-control" id="category_name" name="category_name" placeholder="ชื่อหมวดหมู่" required>
            </div>
            <button type="submit" class="btn btn-primary custom-btn-t">ยืนยัน</button>
            <a href="product.php" class="btn btn-outline-secondary custom-btn-s">ยกเลิก</a>
        </form>
    </div>
</body>

</html>