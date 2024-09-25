<?php
include 'inc/check_login.php';
checkLogin();
include 'db.php';
include 'header.php';
include 'inc/links.php';
include 'inc/functions.php';

// ตรวจสอบว่า product_id ถูกส่งมาหรือไม่
if (isset($_GET['product_id'])) {
    $product_id = intval($_GET['product_id']); // เปลี่ยนเป็น integer เพื่อความปลอดภัย

    // Get product details for the specific product ID
    $sql_product = "SELECT * FROM products WHERE product_id = $product_id";
    $result_product = $conn->query($sql_product);

    if ($result_product->num_rows > 0) {
        $product = $result_product->fetch_assoc();

        // แสดงข้อมูลหมวดหมู่
        $category_id = $product['category_id'];
        $sql_category_name = "SELECT category_name FROM categories WHERE category_id = $category_id";
        $result_category_name = $conn->query($sql_category_name);

        if ($result_category_name->num_rows > 0) {
            $category = $result_category_name->fetch_assoc();
            $category_name = $category['category_name'];
        } else {
            $category_name = "Unknown";
        }
    } else {
        echo "Product not found.";
        $conn->close();
        exit;
    }
} else {
    echo "No product ID provided.";
    $conn->close();
    exit;
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <title>รายละเอียดสินค้า</title>
</head>

<body>
    <div class="container mb-5">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-chevron p-3 rounded">
                <li class="breadcrumb-item">
                    <a class="link-body-emphasis fw-semibold text-decoration-none" href="home.php">Home</a>
                </li>
                <li class="breadcrumb-item">
                    <a class="link-body-emphasis fw-semibold text-decoration-none" href="product.php">สินค้า</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">รายละเอียดสินค้า</li>
            </ol>
        </nav>
        <h2>รายละเอียดสินค้า</h2>
        <div class="row">
            <div class="col-xl-4">
                <div class="mb-4 mt-2 text-center mb-xl-0">
                    <div class="card-header">
                        <h5>รูปสินค้า</h5>
                    </div>
                    <div class="card-body text-center">
                        <?php if ($product['image']): ?>
                            <img class="img-account-profile rounded mb-4"
                                src="uploads/<?php echo htmlspecialchars($product['image']); ?>" alt="Product Image">
                            <br>
                        <?php else: ?>
                            <p>ไม่มีรูปภาพ</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-xl-8">
                <div class="card-header mt-2">
                    <h3>รายละเอียดสินค้า</h3>
                </div>
                <div class="card-body">
                    <div class="row gx-3 mb-3">
                        <div class="col-md-6">
                            <label for="product_id" class="form-label">รหัสสินค้า</label>
                            <div class="form-control">
                                <p><?php echo htmlspecialchars($product['product_id']); ?></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="category_id" class="form-label">หมวดหมู่</label>
                            <div class="form-control">
                                <p><?php echo htmlspecialchars($category_name); ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="product_name" class="form-label">ชื่อสินค้า</label>
                        <div class="form-control">
                            <p><?php echo htmlspecialchars($product['product_name']); ?></p>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">คำอธิบาย</label>
                        <div class="form-control">
                            <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                        </div>
                    </div>
                    <div class="row gx-3 mb-3">
                        <div class="col-md-6">
                            <label for="quantity" class="form-label">คงเหลือ</label>
                            <div class="form-control">
                                <p><?php echo htmlspecialchars($product['quantity']); ?></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="price" class="form-label">ราคา</label>
                            <div class="form-control">
                                <p><?php echo number_format(htmlspecialchars($product['price']), 2); ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="created_at" class="form-label">วันเวลาที่สร้างสินค้า</label>
                        <div class="form-control">
                            <p><?php echo formatThaiDate(strtotime($product['created_at'])); ?></p>
                        </div>
                    </div>
                    <a href="product.php" class="btn btn-outline-secondary custom-btn-s">กลับ</a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>