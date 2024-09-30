<?php
include 'inc/check_login.php';
checkLogin();
include 'db.php';
include 'header.php';
include 'inc/links.php';
include 'inc/functions.php';

if (isset($_GET['product_id'])) {
    $product_id = intval($_GET['product_id']);

    // Fetch categories for the dropdown
    $sql_categories = "SELECT * FROM categories";
    $result_categories = $conn->query($sql_categories);

    // Get product details for the specific product ID
    $sql_product = "SELECT * FROM products WHERE product_id = $product_id";
    $result_product = $conn->query(query: $sql_product);

    if ($result_product->num_rows > 0) {
        $product = $result_product->fetch_assoc();
    } else {
        echo "Product not found.";
        exit;
    }

    // Fetch stock-in history for the product
    $sql_stock_in = "SELECT si.stock_in_id, si.product_id, s.date AS stock_in_date, si.quantity, pc.cost_per_unit 
                 FROM stock_in_items si
                 JOIN stock_in s ON si.stock_in_id = s.stock_in_id
                 LEFT JOIN product_costs pc ON si.stock_in_id = pc.stock_in_id
                 WHERE si.product_id = ?
                 GROUP BY si.stock_in_id, pc.cost_per_unit";
    $stmt_stock_in = $conn->prepare($sql_stock_in);
    $stmt_stock_in->bind_param("i", $product_id);
    $stmt_stock_in->execute();
    $result_stock_in = $stmt_stock_in->get_result();
} else {
    echo "No product ID provided.";
    exit;
}

$alert_message = '';
$redirect_script = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['update_product'])) {
        $product_name = $_POST['product_name'];
        $description = $_POST['description'];
        $price = $_POST['price'];
        $category_id = $_POST['category_id'];
        $quantity = $_POST['quantity'];
        $delete_image = isset($_POST['delete_image']) ? $_POST['delete_image'] : 0;

        // Update the product in the database
        $sql = "UPDATE products 
                SET product_name = ?, description = ?, price = ?, category_id = ?, quantity = ? 
                WHERE product_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdiid", $product_name, $description, $price, $category_id, $quantity, $product_id);

        if ($stmt->execute()) {
            // Handle image upload and delete if needed
            if ($delete_image == 1 && !empty($product['image'])) {
                $image_path = 'uploads/' . $product['image'];
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
                $sql = "UPDATE products SET image = NULL WHERE product_id = ?";
                $stmt_image = $conn->prepare($sql);
                $stmt_image->bind_param("i", $product_id);
                $stmt_image->execute();
                $stmt_image->close();
            }

            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $file_tmp = $_FILES['image']['tmp_name'];
                $file_name = $_FILES['image']['name'];
                $file_path = 'uploads/' . basename($file_name);
                if (!is_dir('uploads')) {
                    mkdir('uploads', 0755, true);
                }

                if (move_uploaded_file($file_tmp, $file_path)) {
                    $sql = "UPDATE products SET image = ? WHERE product_id = ?";
                    $stmt_image = $conn->prepare($sql);
                    $stmt_image->bind_param("si", $file_name, $product_id);
                    $stmt_image->execute();
                    $stmt_image->close();
                } else {
                    echo 'เกิดข้อผิดพลาดในการอัปโหลดไฟล์';
                }
            }

            // Update stock-in entries
            if (isset($_POST['stock_in_id'])) {
                foreach ($_POST['stock_in_id'] as $key => $stock_in_id) {
                    // รับค่าต้นทุนเฉลี่ยต่อหน่วยจากฟอร์ม
                    $cost_per_unit = $_POST['cost_per_unit'][$key];

                    // ตรวจสอบว่าข้อมูล cost_per_unit ไม่เป็นค่าว่างเปล่า
                    if (!empty($cost_per_unit)) {
                        // อัปเดตราคาต้นทุนต่อหน่วยใน product_costs
                        $sql_update_cost = "UPDATE product_costs SET cost_per_unit = ? WHERE stock_in_id = ?";
                        $stmt_update_cost = $conn->prepare($sql_update_cost);
                        $stmt_update_cost->bind_param("di", $cost_per_unit, $stock_in_id);
                        if (!$stmt_update_cost->execute()) {
                            echo "Error updating cost_per_unit: " . $stmt_update_cost->error;
                        }
                        $stmt_update_cost->close();
                    }
                }
            }

            $alert_message = "<div class='alert alert-success d-flex align-items-center' role='alert'>
                                <svg class='bi flex-shrink-0 me-2' width='24' height='24' role='img' aria-label='Success:'><use xlink:href='#check-circle-fill'/></svg>
                                <div>แก้ไขสินค้าเสร็จเรียบร้อย</div>
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
    }
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <title>แก้ไขสินค้า</title>
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
                <li class="breadcrumb-item active" aria-current="page">แก้ไขสินค้า</li>
            </ol>
        </nav>
        <?php echo $alert_message; ?>
        <?php echo $redirect_script; ?>
        <h2>แก้ไขสินค้า</h2>
        <form method="post" action="" enctype="multipart/form-data">
            <input type="hidden" name="update_product" value="1">
            <div class="row md-2">
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
                            <?php endif; ?>
                            <input class="form-control" id="inputProductImage" type="file" name="image">
                            <button type="button" class="btn btn-danger btn-sm mt-2"
                                onclick="document.getElementById('delete_image').value='1'; this.disabled=true; this.innerText='ลบรูปภาพ';">ลบรูปภาพ</button>
                        </div>
                    </div>
                </div>
                <div class="col-xl-8">
                    <div class="card-header mt-2">
                        <h3>รายละเอียดสินค้า</h3>
                    </div>
                    <div class="card-body">
                        <input type="hidden" id="delete_image" name="delete_image" value="0">
                        <div class="row gx-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label" for="product_name">ชื่อสินค้า</label>
                                <input class="form-control" id="product_name" type="text" name="product_name"
                                    value="<?php echo htmlspecialchars($product['product_name']); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="category_id">หมวดหมู่</label><br>
                                <select class="form-control" id="category_id" name="category_id" required>
                                    <?php while ($row = $result_categories->fetch_assoc()): ?>
                                        <option value="<?php echo $row['category_id']; ?>" <?php if ($row['category_id'] == $product['category_id'])
                                               echo 'selected'; ?>>
                                            <?php echo $row['category_name']; ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">คำอธิบาย</label>
                            <textarea class="form-control" id="description" name="description" rows="3"
                                required><?php echo htmlspecialchars($product['description']); ?></textarea>
                        </div>
                        <div class="row gx-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label" for="price">ราคา</label>
                                <input class="form-control" id="price" type="number" name="price" autocomplete="off"
                                    value="<?php echo htmlspecialchars(number_format($product['price'], 2, '.', '')); ?>"
                                    required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="stockQuantity">จำนวนสินค้า</label>
                                <input class="form-control" id="quantity" type="number" name="quantity"
                                    value="<?php echo isset($product['quantity']) ? htmlspecialchars($product['quantity']) : 0; ?>"
                                    required>
                            </div>
                        </div>
                        <button type="button" onclick="checkChanges()"
                            class="btn btn-outline-secondary custom-btn-t">ยืนยัน</button>
                        <a href="product.php" class="btn btn-outline-secondary custom-btn-s">ยกเลิก</a>
                    </div>
                </div>
            </div>
            <div class="mt-5">
                <h3>ประวัติการนำเข้าสินค้า</h3>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>วันที่นำเข้า</th>
                            <th>ราคาต้นทุนต่อหน่วย</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($stock_in = $result_stock_in->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <?php
                                    if (isset($stock_in['stock_in_date'])) {
                                        $formatted_date = formatThaiDate(strtotime($stock_in['stock_in_date']));
                                        echo $formatted_date;
                                    } else {
                                        echo 'N/A';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <input type="hidden" name="stock_in_id[]"
                                        value="<?php echo $stock_in['stock_in_id']; ?>">
                                    <input type="text" name="cost_per_unit[]"
                                        value="<?php echo htmlspecialchars($stock_in['cost_per_unit']); ?>" required>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </form>
    </div>

    <script src="path/to/bootstrap.bundle.js"></script>
    <script>
        // เก็บค่าดั้งเดิมของสินค้าคงเหลือ
        var originalStockQuantity = document.getElementById('quantity').value;

        function checkChanges() {
            // ค่าปัจจุบันของสินค้าคงเหลือ
            var currentStockQuantity = document.getElementById('quantity').value;

            // ตรวจสอบว่ามีการเปลี่ยนแปลงในสินค้าคงเหลือหรือไม่
            if (originalStockQuantity != currentStockQuantity) {
                // เรียก modal ยืนยันถ้ามีการเปลี่ยนแปลง
                confirmSave();
            } else {
                // ส่งฟอร์มถ้ามีการเปลี่ยนแปลงในข้อมูลอื่น ๆ
                document.querySelector('form').submit();
            }
        }

        function checkChanges() {
            var currentStockQuantity = document.getElementById('quantity').value;
            if (originalStockQuantity != currentStockQuantity) {
                confirmSave();
            } else {
                document.querySelector('form').submit();
            }
        }

        function confirmSave() {
            Swal.fire({
                title: 'คำเตือน!!',
                text: 'ยืนยันที่จะแก้ไขจำนวนสินค้า (จะไม่บันทึกในรายงานสินค้านำเข้า)',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'ยืนยัน',
                cancelButtonText: 'ยกเลิก',
                confirmButtonColor: '#C7A98B',
                cancelButtonColor: '#cccccc',
            }).then((result) => {
                if (result.isConfirmed) {
                    // ส่งฟอร์มถ้าผู้ใช้ยืนยัน
                    document.querySelector('form').submit();
                }
            });
        }

    </script>
</body>

</html>