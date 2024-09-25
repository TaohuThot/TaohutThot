<?php
include 'db.php';

if (isset($_GET['employee_id'])) {
    $employee_id = intval($_GET['employee_id']);

    // ลบพนักงาน
    $sql = "DELETE FROM employees WHERE employee_id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("i", $employee_id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo "ลบพนักงานเรียบร้อยแล้ว"; // ส่งกลับข้อความนี้
        } else {
            echo "เกิดข้อผิดพลาดในการลบ หรือไม่พบพนักงาน";
        }
    } else {
        echo "ไม่สามารถเตรียมคำสั่ง SQL สำหรับการลบพนักงานได้";
    }

    $stmt->close();
} else {
    echo "ไม่มี employee_id ที่ถูกส่งมา";
}

$conn->close();
?>

