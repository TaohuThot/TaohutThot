<?php
// ตรวจสอบว่ามีการเริ่มต้นเซสชั่นแล้วหรือไม่ก่อนเรียก session_start()
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ตรวจสอบว่าฟังก์ชัน checkLogin ถูกประกาศแล้วหรือไม่ก่อนที่จะประกาศใหม่
if (!function_exists('checkLogin')) {
    function checkLogin() {
        if (!isset($_SESSION['user_id'])) {
            // ถ้าไม่ได้ล็อกอิน จะรีไดเรกไปหน้า login.php
            header("Location: index.php");
            exit();
        }
    }
}
?>
