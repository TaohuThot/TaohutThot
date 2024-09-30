<?php
include 'inc/check_login.php';
checkLogin();
include 'db.php';
include 'header.php';
include 'inc/links.php';

$alert_message = '';
$redirect_script = '';

if (isset($_GET['category_id'])) {
    $category_id = $_GET['category_id'];
    
    // ดึงข้อมูลหมวดหมู่ที่ต้องการแก้ไข
    $sql = "SELECT * FROM categories WHERE category_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $category_name = $row['category_name'];
    } else {
        echo "<div class='alert alert-danger'>ไม่พบหมวดหมู่ที่ต้องการแก้ไข</div>";
        exit();
    }
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $category_name = $_POST['category_name'];

    // อัปเดตข้อมูลหมวดหมู่
    $sql = "UPDATE categories SET category_name = ? WHERE category_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $category_name, $category_id);

    if ($stmt->execute()) {
        $alert_message = "<div class='alert alert-success d-flex align-items-center' role='alert'>
                            <svg class='bi flex-shrink-0 me-2' width='24' height='24' role='img' aria-label='Success:'><use xlink:href='#check-circle-fill'/></svg>
                            <div>แก้ไขหมวดหมู่เรียบร้อย</div>
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
    <title>แก้ไขหมวดหมู่</title>
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
                <li class="breadcrumb-item active" aria-current="page">แก้ไขหมวดหมู่</li>
            </ol>
        </nav>
        <?php echo $alert_message; ?>
        <?php echo $redirect_script; ?>
        <h2>แก้ไขหมวดหมู่</h2>
        <form method="post" action="edit_category.php?category_id=<?php echo $category_id; ?>">
            <div class="mb-3">
                <label for="name" class="form-label">ชื่อหมวดหมู่</label>
                <input type="text" class="form-control" id="category_name" name="category_name" placeholder="ชื่อหมวดหมู่" autocomplete="off" value="<?php echo htmlspecialchars($category_name); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary custom-btn-t">ยืนยัน</button>
            <a href="category.php" class="btn btn-outline-secondary custom-btn-s">ยกเลิก</a>
        </form>
    </div>
</body>

</html>
