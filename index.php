<?php
session_start();
include 'db.php';
include 'inc/links.php';

$alertMessage = '';
$alertType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // ดึงข้อมูลผู้ใช้พร้อมกับ employee_id จากฐานข้อมูล
    $stmt = $conn->prepare("SELECT id, username, password, role, employee_id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // ตรวจสอบรหัสผ่าน
        if (password_verify($password, $user['password'])) {
            // กำหนด session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['employee_id'] = $user['employee_id'];  // เก็บ employee_id ใน session

            // ส่งผู้ใช้ไปที่หน้า home.php
            header("Location: home.php");
            exit();
        } else {
            $alertMessage = 'รหัสผ่านไม่ถูกต้อง';
            $alertType = 'danger';
        }
    } else {
        $alertMessage = 'ไม่พบผู้ใช้';
        $alertType = 'danger';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Login</title>
    <script>
        function hideAlert() {
            const alertElement = document.getElementById('alert-message');
            if (alertElement) {
                setTimeout(() => {
                    alertElement.style.display = 'none';
                }, 1500); 
            }
        }
    </script>
</head>

<body class="d-flex align-items-center py-4 bg-body-tertiary" onload="hideAlert()">

    <div class="form-signin w-100 m-auto text-center">
        <form method="post" action="index.php">
            <!-- <img class="mb-3 me-4" src="image/cat-ชานมไข่มุก.gif" alt="Cat Drinking Milk Tea GIF" width="200"
                height="150"> -->

            <h1 class="h3 mb-3 text-center">Login</h1>
            <div class="form-floating">
                <input type="text" name="username" class="form-control" id="floatingInput" placeholder="ชื่อผู้ใช้">
                <label for="floatingInput">ชื่อผู้ใช้</label>
            </div>
            <div class="form-floating mt-2">
                <input type="password" name="password" class="form-control" id="floatingPassword"
                    placeholder="รหัสผ่าน">
                <label for="floatingPassword">รหัสผ่าน</label>
            </div>
            <?php if ($alertMessage): ?>
            <div id="alert-message" class="alert alert-<?= $alertType; ?>"><?= $alertMessage; ?></div>
            <?php endif; ?>
            <div>
                <button class="btn btn-primary custom-btn-t w-100 py-2 mt-3" type="submit">ลงชื่อเข้าใช้</button>
            </div>
        </form>
    </div>

</body>

</html>
