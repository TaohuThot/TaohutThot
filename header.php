<?php
include 'inc/check_login.php';
checkLogin();
// ตรวจสอบว่า session ถูกเริ่มต้นแล้วหรือไม่
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // เริ่มต้น session ถ้ายังไม่มีการเริ่มต้น
}

include 'db.php';

// Query to get the profile image of the user/admin
$sql = "SELECT profile_image FROM companies WHERE company_id = 1"; // แทนด้วยเงื่อนไขเพื่อดึงผู้ใช้/admin ที่ถูกต้อง
$result = $conn->query($sql);
$profile_image = 'default.png'; // รูปโปรไฟล์เริ่มต้น

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    if (!empty($row['profile_image'])) {
        $profile_image = $row['profile_image'];
    }
}
?>
<div class="header-container">
    <div class="container">
        <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
            <a href="home.php"
                class="d-flex align-items-center my-2 my-lg-0 me-lg-auto <?php echo basename($_SERVER['PHP_SELF']) == 'home.php' ? 'text-secondary active' : 'text-black'; ?> text-decoration-none">
                <h3>Mk electronics</h3>
            </a>
            <ul class="nav col-12 col-lg-auto my-2 justify-content-center my-md-0">
                <li>
                    <a href="product.php"
                        class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'product.php' ? 'text-secondary active' : 'text-black'; ?>">
                        <img src="image/shopping-basket.png" class="bi d-block mx-auto mb-1 icon" width="20"
                            height="20">
                        สินค้า
                    </a>
                </li>
                <li>
                    <a href="category.php"
                        class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'category.php' ? 'text-secondary active' : 'text-black'; ?>">
                        <img src="image/apps.png" class="bi d-block mx-auto mb-1 icon" width="20"
                            height="20">
                        หมวดหมู่
                    </a>
                </li>
                <li>
                    <a href="customer.php"
                        class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'customer.php' ? 'text-secondary active' : 'text-black'; ?>">
                        <img src="image/user (1).png" class="bi d-block mx-auto mb-1 icon" width="20" height="20">
                        ลูกค้า
                    </a>
                </li>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <li>
                        <a href="employee.php"
                            class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'employee.php' ? 'text-secondary active' : 'text-black'; ?>">
                            <img src="image/users.png" class="bi d-block mx-auto mb-1 icon" width="20" height="20">
                            พนักงาน
                        </a>
                    </li>
                <?php endif; ?>
                <li>
                    <a href="order.php"
                        class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'order.php' ? 'text-secondary active' : 'text-black'; ?>">
                        <img src="image/shopping-cart.png" class="bi d-block mx-auto mb-1 icon" width="20" height="20">
                        คำสั่งซื้อ
                    </a>
                </li>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <li class="nav-item dropdown">
                        <a href="#"
                            class="nav-link dropdown-toggle <?php echo basename($_SERVER['PHP_SELF']) == 'report.php' ? 'text-secondary active' : 'text-black'; ?>"
                            id="dropdownReport" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="image/document.png" class="bi d-block mx-auto mb-1 icon" width="20" height="20">
                            รายงาน
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="dropdownReport">
                            <li><a class="dropdown-item" href="report_stock_in.php">รายงานสินค้านำเข้า</a></li>
                            <li><a class="dropdown-item" href="report_stock_out.php">รายงานสินค้าออก</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a href="#"
                            class="nav-link dropdown-toggle <?php echo basename($_SERVER['PHP_SELF']) == 'admin.php' ? 'text-secondary active' : 'text-black'; ?>"
                            id="dropdownProfile" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="uploads/<?php echo htmlspecialchars($profile_image); ?>"
                                class="bi d-block mx-auto mb-1 icon rounded-circle" width="20" height="20"
                                alt="Profile Image">
                            <?php echo ($_SESSION['role'] === 'admin') ? 'Admin' : 'User'; ?>
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="dropdownProfile">
                            <li><a class="dropdown-item" href="edit_profile.php">แก้ไขโปรไฟล์</a></li>
                            <?php if ($_SESSION['role'] === 'admin'): ?>
                                <li><a class="dropdown-item" href="edit_password.php">เปลี่ยนรหัสผ่าน</a></li>
                            <?php endif; ?>
                            <li><a class="dropdown-item" href="logout.php">ออกจากระบบ</a></li>
                        </ul>
                    </li>
                <?php endif; ?>
                <?php if ($_SESSION['role'] === 'user'): ?>
                    <li>
                        <a href="logout.php"
                            class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'logout.php' ? 'text-secondary active' : 'text-black'; ?>">
                            <img src="image/sign-out-alt.png" class="bi d-block mx-auto mb-1 icon" width="20" height="20">
                            ออกจากระบบ
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>