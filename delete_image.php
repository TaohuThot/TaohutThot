<?php
include 'inc/check_login.php';
checkLogin();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['product_id'])) {
        $product_id = intval($_GET['product_id']);
        
        // ดึงชื่อไฟล์ภาพจากฐานข้อมูล
        $sql = "SELECT image FROM products WHERE product_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        if ($row) {
            $file_name = $row['image'];
            $file_path = 'uploads/' . $file_name;
            
            // ลบไฟล์จากโฟลเดอร์
            if (file_exists($file_path)) {
                unlink($file_path);
            }
            
            // ลบชื่อไฟล์จากฐานข้อมูล
            $sql = "UPDATE products SET image = NULL WHERE product_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $product_id);
            if ($stmt->execute()) {
                header("Location: edit_product.php?product_id=$product_id"); // กลับไปที่หน้าการแก้ไขสินค้าหลังจากลบรูปเสร็จ
                exit;
            } else {
                echo 'เกิดข้อผิดพลาดในการลบข้อมูลในฐานข้อมูล';
            }
        }
        $stmt->close();
    }
}

$conn->close();
?>
