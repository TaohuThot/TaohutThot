<?php
include 'db.php';

// ตรวจสอบว่ามี customer_id ส่งมาหรือไม่
if (isset($_GET['customer_id'])) {
    $customer_id = intval($_GET['customer_id']);

    // ตรวจสอบว่าลูกค้ามีออเดอร์อยู่หรือไม่
    $sql = "SELECT COUNT(*) AS order_count FROM orders WHERE customer_id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param('i', $customer_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if ($row['order_count'] > 0) {
            // หากลูกค้ามีออเดอร์อยู่ ส่งข้อความเตือนกลับไป
            echo "ไม่สามารถลบลูกค้าได้เนื่องจากมีออเดอร์อยู่";
        } else {
            // หากไม่มีออเดอร์ในลูกค้า ลบลูกค้า
            $sql = "DELETE FROM customers WHERE customer_id = ?";
            $stmt = $conn->prepare($sql);

            if ($stmt) {
                $stmt->bind_param('i', $customer_id);
                $stmt->execute();

                if ($stmt->affected_rows > 0) {
                    echo "ลบสำเร็จ";
                } else {
                    echo "เกิดข้อผิดพลาดในการลบ";
                }
            } else {
                echo "ไม่สามารถเตรียมคำสั่ง SQL สำหรับการลบลูกค้าได้";
            }
        }
    } else {
        echo "ไม่สามารถเตรียมคำสั่ง SQL สำหรับการตรวจสอบได้";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "ไม่มีข้อมูล customer_id ที่ถูกส่งมา";
}
?>
