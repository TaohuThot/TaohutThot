<?php
include 'db.php';

// ตรวจสอบว่ามี category_id ส่งมาหรือไม่
if (isset($_GET['category_id'])) {
    $category_id = $_GET['category_id'];

    // ตรวจสอบว่าหมวดหมู่นี้มีสินค้าที่เกี่ยวข้องอยู่หรือไม่
    $sql = "SELECT COUNT(*) AS product_count FROM products WHERE category_id = ?";
    $stmt = $conn->prepare($sql);

    // ตรวจสอบการเตรียมคำสั่ง SQL
    if ($stmt) {
        $stmt->bind_param('i', $category_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if ($row['product_count'] > 0) {
            // หากมีสินค้าอยู่ในหมวดหมู่ ส่งข้อความเตือนกลับไป
            echo "ไม่สามารถลบหมวดหมู่เนี่องจากยังมีสินค้าอยู่";
        } else {
            // หากไม่มีสินค้าในหมวดหมู่ ลบหมวดหมู่
            $sql = "DELETE FROM categories WHERE category_id = ?";
            $stmt = $conn->prepare($sql);

            if ($stmt) {
                $stmt->bind_param('i', $category_id);
                $stmt->execute();

                if ($stmt->affected_rows > 0) {
                    echo "ลบสำเร็จ";
                } else {
                    echo "เกิดข้อผิดพลาดในการลบ";
                }
            } else {
                echo "ไม่สามารถเตรียมคำสั่ง SQL สำหรับการลบหมวดหมู่ได้";
            }
        }
    } else {
        echo "ไม่สามารถเตรียมคำสั่ง SQL สำหรับการตรวจสอบได้";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "ไม่มีข้อมูล category_id ที่ถูกส่งมา";
}
?>