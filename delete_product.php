<?php
include 'db.php';

if (isset($_GET['product_id'])) {
    $product_id = intval($_GET['product_id']);

    // ตรวจสอบว่ามีออเดอร์ที่เกี่ยวข้องกับสินค้านี้หรือไม่
    $order_check_sql = "SELECT COUNT(*) AS order_count FROM order_items WHERE product_id = ?";
    $stmt = $conn->prepare($order_check_sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row['order_count'] > 0) {
        // หากมีออเดอร์ที่เกี่ยวข้อง
        echo "ไม่สามารถลบสินค้าได้ เนื่องจากมีออเดอร์ที่เกี่ยวข้องอยู่";
    } else {
        // ถ้าไม่มีออเดอร์ที่เกี่ยวข้อง ลบสินค้า
        $sql = "DELETE FROM products WHERE product_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $product_id);

        if ($stmt->execute()) {
            echo "ลบสินค้าเรียบร้อยแล้ว";
        } else {
            // หากมีข้อผิดพลาดในการลบ
            echo "ไม่สามารถลบสินค้าได้ เนื่องจากเกิดข้อผิดพลาด: " . $stmt->error;
        }
    }

    $stmt->close();
} else {
    echo "ไม่มี product_id ที่ระบุ";
}

$conn->close();
?>


