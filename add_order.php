<?php
include 'inc/check_login.php';
checkLogin();
ob_start();
include 'header.php';
include 'db.php';
include 'inc/links.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // รับค่าจากฟอร์ม
    $customer_id = $_POST['customer_id'];
    $company_id = $_POST['company_id'];
    $discount = isset($_POST['discount']) ? floatval($_POST['discount']) : 0; // ตรวจสอบและกำหนดส่วนลด
    $tax_rate = 7; // อัตราภาษี 7%
    $total_before_discount = 0;
    $total_tax = 0;
    $grand_total = 0;

    $product_ids = $_POST['product_id'];
    $quantities = $_POST['quantity'];
    $prices = $_POST['price'];

    // ตรวจสอบสต็อกสินค้า
    $stock_check_passed = true;
    $insufficient_stock_products = [];

    for ($i = 0; $i < count($product_ids); $i++) {
        $product_id = $product_ids[$i];
        $quantity = intval($quantities[$i]);
        $price = floatval($prices[$i]);
        $total = $quantity * $price;

        // คำนวณยอดรวมก่อนหักส่วนลด
        $total_before_discount += $total;

        // ตรวจสอบสต็อกก่อนการหักจำนวน
        $sql_check_stock = "SELECT quantity FROM products WHERE product_id = ?";
        $stmt_check_stock = $conn->prepare($sql_check_stock);
        $stmt_check_stock->bind_param("i", $product_id);
        $stmt_check_stock->execute();
        $stmt_check_stock->bind_result($current_stock);
        $stmt_check_stock->fetch();
        $stmt_check_stock->close();

        if ($quantity > $current_stock) {
            $stock_check_passed = false;
            $insufficient_stock_products[] = $product_id;
        }
    }

    if ($stock_check_passed) {
        // บันทึกข้อมูลลงในตาราง orders โดยไม่ระบุ order_date
        $sql_order = "INSERT INTO orders (customer_id, company_id, discount, tax_rate, total_price) VALUES (?, ?, ?, ?, ?)";
        $stmt_order = $conn->prepare($sql_order);
        $stmt_order->bind_param("iiddd", $customer_id, $company_id, $discount, $tax_rate, $grand_total);
        $stmt_order->execute();
        $order_id = $stmt_order->insert_id; // รับค่า order_id ที่เพิ่งบันทึก

        // บันทึกรายการสินค้าใน order_items
        for ($i = 0; $i < count($product_ids); $i++) {
            $product_id = $product_ids[$i];
            $quantity = intval($quantities[$i]);
            $price = floatval($prices[$i]);
            $total = $quantity * $price;

            // บันทึกรายการสินค้าใน order_items
            $sql_item = "INSERT INTO order_items (order_id, product_id, quantity, total_price) VALUES (?, ?, ?, ?)";
            $stmt_item = $conn->prepare($sql_item);
            $stmt_item->bind_param("iiid", $order_id, $product_id, $quantity, $total);
            $stmt_item->execute();

            // อัปเดตจำนวนสินค้าที่เหลือในตาราง products
            $sql_update_stock = "UPDATE products SET quantity = quantity - ? WHERE product_id = ?";
            $stmt_update_stock = $conn->prepare($sql_update_stock);
            $stmt_update_stock->bind_param("ii", $quantity, $product_id);
            $stmt_update_stock->execute();

            // ปิดการเชื่อมต่อหลังจากรันคำสั่ง SQL เสร็จ
            $stmt_item->close();
            $stmt_update_stock->close();
        }

        // คำนวณส่วนลดและภาษี
        $total_discount_amount = ($total_before_discount * $discount) / 100;
        $total_after_discount = $total_before_discount - $total_discount_amount;
        $total_tax = ($total_after_discount * $tax_rate) / 100;
        $grand_total = $total_after_discount + $total_tax;

        // อัปเดตยอดรวมทั้งหมดในตาราง orders
        $sql_update_order = "UPDATE orders SET total_price = ? WHERE order_id = ?";
        $stmt_update_order = $conn->prepare($sql_update_order);
        $stmt_update_order->bind_param("di", $grand_total, $order_id);
        $stmt_update_order->execute();

        // หลังจากบันทึกข้อมูลเสร็จ เปลี่ยนหน้าไปยังหน้ารายละเอียดคำสั่งซื้อ
        header("Location: detail_order.php?order_id=" . $order_id);
        ob_end_flush();
        exit();
    } else {
        // แสดงข้อความแจ้งเตือนหากสินค้าคงเหลือไม่เพียงพอ
        $product_ids_str = implode(', ', $insufficient_stock_products);
        echo "<script>alert('ไม่สามารถสร้างคำสั่งซื้อได้เนื่องจากสินค้าคงเหลือไม่เพียงพอในสต็อกสำหรับสินค้า ID: $product_ids_str');</script>";
    }
}

$company_query = "SELECT company_id, company_name, company_address, company_phone, company_email FROM companies";
$company_result = $conn->query($company_query);

// เก็บข้อมูลใน array
$companies = [];
while ($row = $company_result->fetch_assoc()) {
    $companies[] = $row;
}

// Query to fetch customers
$customer_query = "SELECT customer_id, customer_name, address, district, city, province, phonenumber FROM customers";
$customer_result = $conn->query($customer_query);
$customers = [];
while ($row = $customer_result->fetch_assoc()) {
    $customers[] = $row;
}

// Query to fetch products with stock
$product_query = "SELECT product_id, product_name, price, quantity FROM products";
$product_result = $conn->query($product_query);
$products = [];
while ($row = $product_result->fetch_assoc()) {
    $products[] = $row;
}
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <title>เพิ่มคำสั่งซื้อ</title>
    <script>
        $(document).ready(function () {
            let companies = <?= json_encode($companies) ?>;
            let customers = <?= json_encode($customers) ?>;
            let products = <?= json_encode($products) ?>;

            const TAX_RATE = 7;  // อัตราภาษี 7%

            function updateCompanyAddress() {
                let companyId = $('#company_name').val();
                let company = companies.find(c => c.company_id == companyId);
                if (company) {
                    let fullAddress = company.company_address + '  ' + company.company_phone + '  ' + company.company_email;
                    $('#company_address').val(fullAddress);
                } else {
                    $('#company_address').val('');
                }
            }

            function updateCustomerAddress() {
                let customerId = $('#customer_name').val();
                let customer = customers.find(c => c.customer_id == customerId);
                if (customer) {
                    let fullAddress = customer.address + '  ' + customer.district + '  ' + customer.city + '  ' + customer.province + '  ' + customer.phonenumber;
                    $('#customer_address').val(fullAddress);
                } else {
                    $('#customer_address').val('');
                }
            }

            function calculateTotal() {
                let subtotal = 0;

                $('#productTable tbody tr').each(function () {
                    let row = $(this);
                    let quantity = parseFloat(row.find('.quantity').val()) || 0;
                    let price = parseFloat(row.find('.price').val()) || 0;
                    let total = quantity * price;

                    row.find('.total').val(total.toFixed(2));
                    subtotal += total;
                });

                let discount = $('#toggleDiscount').is(':checked') ? (parseFloat($('#discount').val()) || 0) : 0;
                let discountAmount = (subtotal * discount) / 100;
                let totalAfterDiscount = subtotal - discountAmount;

                let tax = $('#toggleTax').is(':checked') ? (totalAfterDiscount * TAX_RATE) / 100 : 0;
                let grandTotal = totalAfterDiscount + tax;

                $('#totalTax').text(tax.toFixed(2));
                $('#grandTotal').text(grandTotal.toFixed(2));
            }

            // เพิ่มแถวใหม่พร้อมช่องแสดงสินค้าคงเหลือ
            $('#addRow').click(function () {
                let newRow = `<tr>
                                    <td class = 'text-center'></td>
                                    <td>
                                        <select class="form-control product-select" name="product_id[]" required>
                                            <?php foreach ($products as $product): ?>
                                                    <option value="<?= $product['product_id'] ?>" data-price="<?= $product['price'] ?>" data-quantity="<?= $product['quantity'] ?>">
                                                        <?= $product['product_name'] ?> (คงเหลือ: <?= $product['quantity'] ?>)
                                                    </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td><input type="number" name="quantity[]" class="form-control quantity" required></td>
                                    <td><input type="number" name="price[]" class="form-control price" readonly></td>
                                    <td><input type="number" name="total[]" class="form-control total" readonly></td>
                                    <td><button type="button" class="btn btn-danger removeRow">ลบ</button></td>
                            </tr>`;

                $('#productTable tbody').append(newRow);
                updateRowNumbers();
            });

            // ฟังก์ชันสำหรับการอัปเดตลำดับที่ในแถว
            function updateRowNumbers() {
                $('#productTable tbody tr').each(function (index) {
                    $(this).find('td:first').text(index + 1);
                });
            }

            // ฟังก์ชันสำหรับการลบแถว
            $('#productTable').on('click', '.removeRow', function () {
                $(this).closest('tr').remove();
                updateRowNumbers();
                calculateTotal();
            });

            // อัปเดตราคาตามสินค้าที่เลือกและแสดงสินค้าคงเหลือ
            function updatePriceAndTotal(row) {
                var selectedProduct = row.find('.product-select option:selected');
                var price = selectedProduct.data('price');
                var quantityAvailable = selectedProduct.data('quantity');

                row.find('.price').val(price);
                row.find('.quantity-available').text(quantityAvailable); // แสดงสินค้าคงเหลือ
                calculateTotal();
            }

            // เรียกฟังก์ชันเมื่อเปลี่ยนสินค้า
            $('#productTable').on('change', '.product-select', function () {
                var row = $(this).closest('tr');
                updatePriceAndTotal(row);
            });

            // เรียกฟังก์ชันเมื่อเปลี่ยนจำนวนสินค้า
            $('#productTable').on('input', '.quantity', function () {
                calculateTotal();
            });

            // เรียกฟังก์ชันเมื่อเปลี่ยนส่วนลด
            $('#discount').on('input', function () {
                calculateTotal();
            });

            // เรียกฟังก์ชันเมื่อเปลี่ยนสถานะการคิดภาษี
            $('#toggleTax').change(function () {
                calculateTotal();
            });

            // เรียกฟังก์ชันเมื่อเปิด/ปิดส่วนลด
            $('#toggleDiscount').change(function () {
                if ($(this).is(':checked')) {
                    $('#discount').prop('disabled', false);
                } else {
                    $('#discount').prop('disabled', true).val(0);
                }
                calculateTotal();
            });

            // Call update functions when selections change
            $('#company_name').change(function () {
                updateCompanyAddress();
            });

            $('#customer_name').change(function () {
                updateCustomerAddress();
            });

            // Initial update on load
            updateCompanyAddress();
            updateCustomerAddress();
            calculateTotal();
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
                    <a class="link-body-emphasis fw-semibold text-decoration-none" href="order.php">คำสั่งซื้อ</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">เพิ่มคำสั่งซื้อ</li>
            </ol>
        </nav>
        <div class="d-flex justify-content-between align-items-center">
        </div>
        <div class="header text-center mb-4">
            <h2>ใบส่งสินค้าชั่วคราว</h2>
        </div>
        <form method="post">
            <div class="form-group mb-3">
                <label for="company_name">ชื่อบริษัท</label>
                <select class="form-control" id="company_name" name="company_id" required>
                    <?php foreach ($companies as $company): ?>
                        <option value="<?= $company['company_id'] ?>"><?= $company['company_name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group mb-3">
                <label for="company_address">ที่อยู่บริษัท</label>
                <textarea class="form-control" id="company_address" name="company_address" readonly></textarea>
            </div>

            <div class="form-group mb-3">
                <label for="customer_name">ชื่อลูกค้า</label>
                <select class="form-control" id="customer_name" name="customer_id" required>
                    <?php foreach ($customers as $customer): ?>
                        <option value="<?= $customer['customer_id'] ?>"><?= $customer['customer_name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group mb-3">
                <label for="customer_address">ที่อยู่ลูกค้า</label>
                <textarea class="form-control" id="customer_address" name="customer_address" readonly></textarea>
            </div>

            <div class="text-end mb-3">
                <button type="button" id="addRow" class="btn btn-primary custom-btn">+
                    เพิ่มแถวรายการ</button>
            </div>
            <table class="table" id="productTable">
                <thead>
                    <tr>
                        <th class = 'text-center'>ลำดับที่</th>
                        <th>รายการ</th>
                        <th>จำนวน</th>
                        <th>ราคาต่อหน่วย</th>
                        <th>ราคารวม</th>
                        <th>ลบ</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class = 'text-center'>1</td>
                        <td>
                            <select class="form-control product-select" name="product_id[]" required>
                                <?php foreach ($products as $product): ?>
                                    <option value="<?= $product['product_id'] ?>" data-price="<?= $product['price'] ?>">
                                        <?= $product['product_name'] ?> (คงเหลือ: <?= $product['quantity'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td><input type="number" name="quantity[]" class="form-control quantity" required></td>
                        <td><input type="number" name="price[]" class="form-control price" readonly></td>
                        <td><input type="number" name="total[]" class="form-control total" readonly></td>
                        <td><button type="button" class="btn btn-danger removeRow">ลบ</button></td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="text-end">
                            <input type="checkbox" id="toggleDiscount">
                            <label for="toggleDiscount" class="me-2"> ส่วนลด:</label>
                        </td>
                        <td colspan="2">
                            <input type="number" class="form-control" id="discount" name="discount" value="0" disabled>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" class="text-end">
                            <input type="checkbox" id="toggleTax" checked>
                            <label for="toggleTax" class="me-2"> ภาษี:</label>
                        </td>
                        <td colspan="2">
                            <span id="totalTax">0.00</span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" class="text-end"><strong>ยอดรวมทั้งหมด:</strong></td>
                        <td colspan="2"><strong><span id="grandTotal">0.00</span></strong></td>
                    </tr>
                </tfoot>
            </table>

            <div>
                <button type="submit" class="btn btn-primary custom-btn-t mb-5">เพิ่มคำสั่งซื้อ</button>
            </div>
        </form>
        
    </div>
</body>

</html>