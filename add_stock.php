<?php
include 'inc/check_login.php';
checkLogin();
include 'header.php';
include 'db.php';
include 'inc/links.php';

$alert_message = '';
$redirect_script = '';

$sql = "SELECT * FROM products";
$result = $conn->query($sql);

// สร้างรายการสินค้าในรูปแบบ array
$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_ids = $_POST['product_id'];
    $quantities = $_POST['quantity'];
    $costs = $_POST['cost']; // เพิ่มการเก็บข้อมูลต้นทุนสินค้า

    $conn->begin_transaction();

    try {
        $sql_stock_in = "INSERT INTO stock_in (date) VALUES (NOW())";
        $conn->query($sql_stock_in);
        $stock_in_id = $conn->insert_id;

        for ($i = 0; $i < count($product_ids); $i++) {
            $product_id = $product_ids[$i];
            $quantity = intval($quantities[$i]);
            $cost_per_unit = floatval($costs[$i]); // เก็บต้นทุนสินค้า

            // บันทึกข้อมูลใน stock_in_items
            $sql = "INSERT INTO stock_in_items (stock_in_id, product_id, quantity) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iii", $stock_in_id, $product_id, $quantity);
            $stmt->execute();

            // บันทึกข้อมูลต้นทุนสินค้าใน product_costs
            $sql_cost = "INSERT INTO product_costs (product_id, stock_in_id, cost_per_unit, quantity) VALUES (?, ?, ?, ?)";
            $stmt_cost = $conn->prepare($sql_cost);
            $stmt_cost->bind_param("iidi", $product_id, $stock_in_id, $cost_per_unit, $quantity);
            $stmt_cost->execute();

            // อัปเดตจำนวนสินค้าคงเหลือ
            $update_sql = "UPDATE products SET quantity = quantity + ? WHERE product_id = ?";
            $stmt_update = $conn->prepare($update_sql);
            $stmt_update->bind_param("ii", $quantity, $product_id);
            $stmt_update->execute();

            $stmt->close();
            $stmt_cost->close();
            $stmt_update->close();
        }

        $conn->commit();

        // แจ้งเตือนเมื่อสำเร็จ
        $alert_message = "<div class='alert alert-success d-flex align-items-center' role='alert'>
                                <svg class='bi flex-shrink-0 me-2' width='24' height='24' role='img' aria-label='Success:'><use xlink:href='#check-circle-fill'/></svg>
                                <div>นำเข้าสินค้าเรียบร้อย</div>
                        </div>";
        $redirect_script = "<script>
                                setTimeout(function() {
                                    window.location.href = 'product.php';
                                }, 1500);
                            </script>";

    } catch (Exception $e) {
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="th">

<head>
    <title>นำเข้าสินค้า</title>
    <script>
        $(document).ready(function () {
            let products = <?= json_encode($products) ?>;

            function addRow() {
                let newRow = `<tr>
                    <td></td>
                    <td>
                        <select class="form-control product-select" name="product_id[]" required>
                            <?php foreach ($products as $product): ?>
                                                        <option value="<?= $product['product_id'] ?>" data-price="<?= $product['price'] ?>"><?= $product['product_name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td><input type="number" name="quantity[]" class="form-control quantity" placeholder="กรอกจำนวน" autocomplete="off" required></td>
                    <td><input type="number" name="cost[]" class="form-control cost" placeholder="กรอกราคาต้นทุน" autocomplete="off" required></td>
                    <td><button type="button" class="btn btn-danger removeRow">ลบ</button></td>
                </tr>`;
                $('#productTable tbody').append(newRow);
                updateRowNumbers();
            }

            function updateRowNumbers() {
                $('#productTable tbody tr').each(function (index) {
                    $(this).find('td:first').text(index + 1);
                });
            }

            $('#addRow').click(function () {
                addRow();
            });

            $('#productTable').on('click', '.removeRow', function () {
                $(this).closest('tr').remove();
                updateRowNumbers();
            });

            updateRowNumbers();
        });
    </script>
</head>

<body>
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-chevron p-3 rounded -3">
                <li class="breadcrumb-item">
                    <a class="link-body-emphasis fw-semibold text-decoration-none" href="home.php">Home</a>
                </li>
                <li class="breadcrumb-item">
                    <a class="link-body-emphasis fw-semibold text-decoration-none" href="product.php">สินค้า</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">นำเข้าสินค้า</li>
            </ol>
        </nav>
        <?php echo $alert_message; ?>
        <?php echo $redirect_script; ?>
        <div class="header text-center mb-4">
            <h2>นำเข้าสินค้า</h2>
        </div>

        <form method="post">
            <div class="text-end mb-3">
                <button type="button" id="addRow" class="btn btn-primary custom-btn">+
                    เพิ่มแถวรายการ</button>
            </div>

            <table class="table" id="productTable">
                <thead>
                    <tr>
                        <th>ลำดับที่</th>
                        <th>สินค้า</th>
                        <th>จำนวน</th>
                        <th>ราคาต้นทุน</th>
                        <th>ลบ</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>
                            <select class="form-control product-select" name="product_id[]" required>
                                <?php foreach ($products as $product): ?>
                                    <option value="<?= $product['product_id'] ?>"><?= $product['product_name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td><input type="number" name="quantity[]" class="form-control quantity" placeholder="กรอกจำนวน"
                                autocomplete="off" required></td>
                        <td><input type="number" name="cost[]" class="form-control cost" placeholder="กรอกราคาต้นทุน"
                                autocomplete="off" required></td>
                        <td><button type="button" class="btn btn-danger removeRow">ลบ</button></td>
                    </tr>
                </tbody>
            </table>

            <div>
                <button type="submit" class="btn btn-primary custom-btn-t mb-5">ยืนยัน</button>
            </div>
        </form>
    </div>
</body>

</html>