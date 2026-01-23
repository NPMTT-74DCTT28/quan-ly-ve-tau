<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../includes/header.php';

requireLogin();
$conn = $db->getConnection();

$cccd = $ho_ten = $ngay_sinh = $gioi_tinh = $sdt = $dia_chi = '';
$show_success = false;
$show_error = false;
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['btnAdd'])) {
        $cccd = trim($_POST['cccd']);
        $ho_ten = trim($_POST['ho_ten']);
        $ngay_sinh = $_POST['ngay_sinh'] ? $_POST['ngay_sinh'] . ' 00:00:00' : null;
        $gioi_tinh = $_POST['gioi_tinh'];
        $sdt = trim($_POST['sdt']);
        $dia_chi = trim($_POST['dia_chi']);

        if (empty($ho_ten) || empty($gioi_tinh) || empty($sdt) || empty($dia_chi)) {
            $error_message = "Vui lòng điền đầy đủ thông tin bắt buộc!";
            $show_error = true;
        } else {
            $sql_check_sdt = "SELECT * FROM khach_hang WHERE sdt = ?";
            $stmt_check_sdt = $conn->prepare($sql_check_sdt);
            $stmt_check_sdt->execute([$sdt]);

            if ($stmt_check_sdt->rowCount() > 0) {
                $error_message = "Số điện thoại đã tồn tại! Vui lòng nhập số khác.";
                $show_error = true;
            } else {
                if (!empty($cccd)) {
                    $sql_check_cccd = "SELECT * FROM khach_hang WHERE cccd = ?";
                    $stmt_check_cccd = $conn->prepare($sql_check_cccd);
                    $stmt_check_cccd->execute([$cccd]);

                    if ($stmt_check_cccd->rowCount() > 0) {
                        $error_message = "Số CCCD đã tồn tại! Vui lòng nhập số khác.";
                        $show_error = true;
                    }
                }

                if (!$show_error) {
                    $sql_insert = "INSERT INTO khach_hang (cccd, ho_ten, ngay_sinh, gioi_tinh, sdt, dia_chi) 
                                   VALUES (?, ?, ?, ?, ?, ?)";
                    $stmt_insert = $conn->prepare($sql_insert);
                    if ($stmt_insert->execute([$cccd, $ho_ten, $ngay_sinh, $gioi_tinh, $sdt, $dia_chi])) {
                        $success_message = "Thêm thông tin khách hàng thành công!";
                        $show_success = true;
                        $cccd = $ho_ten = $ngay_sinh = $gioi_tinh = $sdt = $dia_chi = '';
                    } else {
                        $error_message = "Thêm thông tin thất bại! Vui lòng thử lại.";
                        $show_error = true;
                    }
                }
            }
        }
    }
}
?>

<div class="main-content">
    <h1>THÊM KHÁCH HÀNG MỚI</h1>

    <?php if ($show_error && !empty($error_message)): ?>
        <div class="alert alert-danger" style="color: #721c24; background-color: #f8d7da; border-color: #f5c6cb; padding: 15px; margin-bottom: 20px; border-radius: 4px;">
            <?= htmlspecialchars($error_message) ?>
        </div>
    <?php endif; ?>

    <?php if ($show_success && !empty($success_message)): ?>
        <div class="alert alert-success" style="color: #155724; background-color: #d4edda; border-color: #c3e6cb; padding: 15px; margin-bottom: 20px; border-radius: 4px;">
            <?= htmlspecialchars($success_message) ?>
        </div>
    <?php endif; ?>

    <form method="post" action="">
        <div class="row" style="display: flex; flex-wrap: wrap; margin-right: -15px; margin-left: -15px;">
            <div class="col-md-6" style="flex: 0 0 50%; max-width: 50%; padding-right: 15px; padding-left: 15px; box-sizing: border-box;">
                <div class="form-group mb-20">
                    <label style="font-weight: 600; color: #34495e; display: block; margin-bottom: 5px;">CCCD:</label>
                    <input type="text" name="cccd" value="<?php echo htmlspecialchars($cccd); ?>" placeholder="VD: 012345678901"
                        class="form-control" style="width: 100%; padding: 8px 12px; border: 1px solid #ced4da; border-radius: 4px;">
                </div>

                <div class="form-group mb-20">
                    <label style="font-weight: 600; color: #34495e; display: block; margin-bottom: 5px;">Họ và Tên (*):</label>
                    <input type="text" name="ho_ten" value="<?php echo htmlspecialchars($ho_ten); ?>" required placeholder="VD: Nguyễn Văn A"
                        class="form-control" style="width: 100%; padding: 8px 12px; border: 1px solid #ced4da; border-radius: 4px;">
                </div>

                <div class="form-group mb-20">
                    <label style="font-weight: 600; color: #34495e; display: block; margin-bottom: 5px;">Ngày sinh:</label>
                    <input type="date" name="ngay_sinh" value="<?php echo htmlspecialchars($ngay_sinh); ?>"
                        class="form-control" style="width: 100%; padding: 8px 12px; border: 1px solid #ced4da; border-radius: 4px;">
                </div>
            </div>

            <div class="col-md-6" style="flex: 0 0 50%; max-width: 50%; padding-right: 15px; padding-left: 15px; box-sizing: border-box;">
                <div class="form-group mb-20">
                    <label style="font-weight: 600; color: #34495e; display: block; margin-bottom: 5px;">Giới tính (*):</label>
                    <select name="gioi_tinh" required class="form-control" style="width: 100%; padding: 8px 12px; border: 1px solid #ced4da; border-radius: 4px;">
                        <option value="">-- Chọn giới tính --</option>
                        <option value="Nam" <?php echo $gioi_tinh == 'Nam' ? 'selected' : ''; ?>>Nam</option>
                        <option value="Nữ" <?php echo $gioi_tinh == 'Nữ' ? 'selected' : ''; ?>>Nữ</option>
                        <option value="Khác" <?php echo $gioi_tinh == 'Khác' ? 'selected' : ''; ?>>Khác</option>
                    </select>
                </div>

                <div class="form-group mb-20">
                    <label style="font-weight: 600; color: #34495e; display: block; margin-bottom: 5px;">Số điện thoại (*):</label>
                    <input type="text" name="sdt" value="<?php echo htmlspecialchars($sdt); ?>" required placeholder="VD: 0912345678"
                        class="form-control" style="width: 100%; padding: 8px 12px; border: 1px solid #ced4da; border-radius: 4px;">
                </div>

                <div class="form-group mb-20">
                    <label style="font-weight: 600; color: #34495e; display: block; margin-bottom: 5px;">Địa chỉ (*):</label>
                    <input type="text" name="dia_chi" value="<?php echo htmlspecialchars($dia_chi); ?>" required placeholder="VD: 123 Đường ABC..."
                        class="form-control" style="width: 100%; padding: 8px 12px; border: 1px solid #ced4da; border-radius: 4px;">
                </div>
            </div>
        </div>

        <div style="text-align: center; margin-top: 20px;">
            <button type="submit" name="btnAdd" style="background: #28a745; color: white; padding: 10px 25px; border: none; border-radius: 4px; cursor: pointer; font-weight: 600;">
                <i class="bi bi-plus-circle"></i> Thêm mới
            </button>
            <a href="index.php" style="margin-left: 15px; color: #333; text-decoration: none; padding: 10px 20px; background: #e2e6ea; border-radius: 4px; display: inline-block;">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
        </div>
    </form>
</div>

<?php
require_once '../../includes/footer.php';
?>