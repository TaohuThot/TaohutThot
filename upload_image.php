<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $product_id = $_POST['product_id'];
        $file_tmp = $_FILES['image']['tmp_name'];
        $file_name = $_FILES['image']['name'];
        $file_path = 'uploads/' . basename($file_name);

        // สร้างโฟลเดอร์ 'uploads' ถ้ายังไม่มี
        if (!is_dir('uploads')) {
            mkdir('uploads', 0755, true);
        }

        // ย้ายไฟล์ไปยังโฟลเดอร์ 'uploads'
        if (move_uploaded_file($file_tmp, $file_path)) {
            // อัปเดตชื่อไฟล์ในฐานข้อมูล
            $sql = "UPDATE products SET image = ? WHERE product_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $file_name, $product_id);
            if ($stmt->execute()) {
                header("Location: edit_product.php?product_id=$product_id"); // กลับไปที่หน้าการแก้ไขสินค้าหลังจากอัปโหลดสำเร็จ
                exit;
            } else {
                echo 'เกิดข้อผิดพลาดในการอัปเดตข้อมูลในฐานข้อมูล';
            }
            $stmt->close();
        } else {
            echo 'เกิดข้อผิดพลาดในการอัปโหลดไฟล์';
        }
    } else {
        echo 'ข้อผิดพลาดในการอัปโหลดไฟล์: ' . $_FILES['image']['error'];
    }
}

$conn->close();
?>
