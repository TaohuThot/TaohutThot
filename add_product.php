<?php
include 'inc/check_login.php';
checkLogin();
include 'db.php';
include 'header.php';
include 'inc/links.php';

// Get categories for the dropdown
$sql = "SELECT * FROM categories";
$result = $conn->query($sql);

$alert_message = '';
$redirect_script = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_name = $_POST['product_name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];

    // Handle image upload
    $image_name = NULL;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['image']['tmp_name'];
        $file_name = basename($_FILES['image']['name']);
        $file_path = 'uploads/' . $file_name;

        // Create uploads folder if not exists
        if (!is_dir('uploads')) {
            mkdir('uploads', 0755, true);
        }

        // Move the file to uploads folder
        if (move_uploaded_file($file_tmp, $file_path)) {
            $image_name = $file_name;
        } else {
            echo 'Error uploading file';
        }
    }

    // Insert product into database
    $sql = "INSERT INTO products (product_name, description, price, category_id, image) 
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdss", $product_name, $description, $price, $category_id, $image_name);

    if ($stmt->execute()) {
        $alert_message = "<div class='alert alert-success d-flex align-items-center' role='alert'>
                            <svg class='bi flex-shrink-0 me-2' width='24' height='24' role='img' aria-label='Success:'><use xlink:href='#check-circle-fill'/></svg>
                            <div>เพิ่มสินค้าใหม่เรียบร้อย</div>
                          </div>";
        $redirect_script = "<script>
                                setTimeout(function() {
                                    window.location.href = 'product.php';
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
<title>เพิ่มสินค้า</title>
</head>
<body>
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-chevron p-3 rounded">
                <li class="breadcrumb-item">
                    <a class="link-body-emphasis fw-semibold text-decoration-none" href="home.php">Home</a>
                </li>
                <li class="breadcrumb-item">
                    <a class="link-body-emphasis fw-semibold text-decoration-none" href="product.php">สินค้า</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">เพิ่มสินค้า</li>
            </ol>
        </nav>
        <?php echo $alert_message; ?>
        <?php echo $redirect_script; ?>
        <h2>เพิ่มสินค้า</h2>
        <form method="post" action="add_product.php" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="image" class="form-label">รูปภาพ</label>
                <input type="file" class="form-control" id="image" name="image" accept="image/*">
            </div>
            <div class="mb-3">
                <label for="category_id" class="form-label">หมวดหมู่</label><br>
                <select class="form-control" id="category_id" name="category_id" required>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <option value="<?php echo $row['category_id']; ?>"><?php echo $row['category_name']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="product_name" class="form-label">ชื่อสินค้า</label>
                <input type="text" class="form-control" id="product_name" name="product_name" placeholder="ชื่อสินค้า" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">คำอธิบาย</label>
                <textarea class="form-control" id="description" name="description" rows="3"  placeholder="คำอธิบาย" required></textarea>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">ราคา</label>
                <input type="number" class="form-control" id="price" name="price" placeholder="ราคา" required>
            </div>
            <button type="submit" class="btn btn-primary custom-btn-t">ยืนยัน</button>
            <a href="product.php" class="btn btn-outline-secondary custom-btn-s">ยกเลิก</a>
        </form>
    </div>
</body>
</html>
