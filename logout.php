<?php
session_start();
session_unset(); // ล้างข้อมูล session
session_destroy(); // ทำลาย session
header("Location: index.php"); // กลับไปที่หน้า login
exit();
