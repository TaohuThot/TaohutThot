<?php
include 'inc/check_login.php';
checkLogin();
include 'db.php';
include 'inc/links.php';

if (isset($_GET['order_id'])) {
    $order_id = intval($_GET['order_id']);

    // Fetch product_id and quantity from order_items
    $sql_items = "SELECT product_id, quantity FROM order_items WHERE order_id = ?";
    $stmt_items = $conn->prepare($sql_items);
    $stmt_items->bind_param("i", $order_id);
    $stmt_items->execute();
    $result_items = $stmt_items->get_result();

    while ($item = $result_items->fetch_assoc()) {
        // Update product stock by adding the quantity back
        $product_id = $item['product_id'];
        $quantity = $item['quantity'];

        $update_sql = "UPDATE products SET quantity = quantity + ? WHERE product_id = ?";
        $stmt_update = $conn->prepare($update_sql);
        $stmt_update->bind_param("ii", $quantity, $product_id);
        $stmt_update->execute();
    }

    // Mark the order items as cancelled and update the cancelled_at time
    $cancel_sql = "UPDATE order_items SET is_cancelled = 1, cancelled_at = NOW() WHERE order_id = ?";
    $stmt_cancel = $conn->prepare($cancel_sql);
    $stmt_cancel->bind_param("i", $order_id);
    
    if ($stmt_cancel->execute()) {
        // ใช้ SweetAlert แทนการ alert
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'ยกเลิกคำสั่งซื้อเรียบร้อยแล้ว',
            }).then(() => {
                window.location.href = 'order.php';
            });
        </script>";
    } else {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'ไม่สามารถยกเลิกคำสั่งซื้อได้',
                text: 'เกิดข้อผิดพลาด: " . addslashes($conn->error) . "',
                confirmButtonColor: '#C7A98B'
            });
        </script>";
    }

    $stmt_items->close();
    $stmt_cancel->close();
}

$conn->close();
?>
