<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../includes/header.php';

if (!isset($_SESSION['user'])) {
    header("Location: " . BASE_URL . "modules/auth/dang_nhap.php");
    exit();
}

$conn = $db->getConnection();

$id = $cccd = $ho_ten = $ngay_sinh = $gioi_tinh = $sdt = $dia_chi = '';
$error_message = '';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql_select = "SELECT * FROM khach_hang WHERE id = ?";
    $stmt = $conn->prepare($sql_select);
    $stmt->execute([$id]);
    $row = $stmt->fetch();

    if ($row) {
        $id = $row['id'];
        $cccd = $row['cccd'];
        $ho_ten = $row['ho_ten'];
        $ngay_sinh = !empty($row['ngay_sinh']) ? date('Y-m-d', strtotime($row['ngay_sinh'])) : '';
        $gioi_tinh = $row['gioi_tinh'];
        $sdt = $row['sdt'];
        $dia_chi = $row['dia_chi'];
    } else {
        echo "<script>alert('Không tìm thấy khách hàng!'); window.location='index.php';</script>";
        exit;
    }
} else {
    echo "<script>alert('Không có ID khách hàng!'); window.location='index.php';</script>";
    exit;
}

if (isset($_POST['btnEdit'])) {
    $id = $_POST['id'];
    $cccd = trim($_POST['cccd']);
    $ho_ten = trim($_POST['ho_ten']);
    $ngay_sinh = $_POST['ngay_sinh'] ? $_POST['ngay_sinh'] . ' 00:00:00' : null;
    $gioi_tinh = $_POST['gioi_tinh'];
    $sdt = trim($_POST['sdt']);
    $dia_chi = trim($_POST['dia_chi']);

    $sql_check_sdt = "SELECT * FROM khach_hang WHERE sdt = ? AND id != ?";
    $stmt_check_sdt = $conn->prepare($sql_check_sdt);
    $stmt_check_sdt->execute([$sdt, $id]);

    if ($stmt_check_sdt->rowCount() > 0) {
        $error_message = "Số điện thoại đã tồn tại! Vui lòng nhập số khác.";
    } else {
        if (!empty($cccd)) {
            $sql_check_cccd = "SELECT * FROM khach_hang WHERE cccd = ? AND id != ?";
            $stmt_check_cccd = $conn->prepare($sql_check_cccd);
            $stmt_check_cccd->execute([$cccd, $id]);
            if ($stmt_check_cccd->rowCount() > 0) {
                $error_message = "Số CCCD đã tồn tại! Vui lòng nhập số khác.";
            }
        }

        if (empty($error_message)) {
            $sql_update = "UPDATE khach_hang SET cccd=?, ho_ten=?, ngay_sinh=?, gioi_tinh=?, sdt=?, dia_chi=? WHERE id=?";
            $stmt_update = $conn->prepare($sql_update);
            if ($stmt_update->execute([$cccd, $ho_ten, $ngay_sinh, $gioi_tinh, $sdt, $dia_chi, $id])) {
                echo "<script>alert('Cập nhật thông tin thành công!'); window.location='index.php';</script>";
                exit;
            } else {
                $error_message = "Cập nhật thông tin thất bại!";
            }
        }
    }
}
?>

<div class="main-content">
    <h1>CẬP NHẬT THÔNG TIN KHÁCH HÀNG</h1>

    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger" style="color: #721c24; background-color: #f8d7da; border-color: #f5c6cb; padding: 15px; margin-bottom: 20px; border-radius: 4px;">
            <?= htmlspecialchars($error_message) ?>
        </div>
    <?php endif; ?>

    <form method="post" action="">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">

        <div class="row" style="display: flex; flex-wrap: wrap; margin-right: -15px; margin-left: -15px;">
            <div class="col-md-6" style="flex: 0 0 50%; max-width: 50%; padding-right: 15px; padding-left: 15px; box-sizing: border-box;">
                <div class="form-group mb-20">
                    <label style="font-weight: 600; color: #34495e; display: block; margin-bottom: 5px;">CCCD:</label>
                    <input type="text" name="cccd" value="<?php echo htmlspecialchars($cccd); ?>"
                        class="form-control" style="width: 100%; padding: 8px 12px; border: 1px solid #ced4da; border-radius: 4px;">
                </div>

                <div class="form-group mb-20">
                    <label style="font-weight: 600; color: #34495e; display: block; margin-bottom: 5px;">Họ và Tên (*):</label>
                    <input type="text" name="ho_ten" value="<?php echo htmlspecialchars($ho_ten); ?>" required
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
                    <input type="text" name="sdt" value="<?php echo htmlspecialchars($sdt); ?>" required
                        class="form-control" style="width: 100%; padding: 8px 12px; border: 1px solid #ced4da; border-radius: 4px;">
                </div>

                <div class="form-group mb-20">
                    <label style="font-weight: 600; color: #34495e; display: block; margin-bottom: 5px;">Địa chỉ (*):</label>
                    <input type="text" name="dia_chi" value="<?php echo htmlspecialchars($dia_chi); ?>" required
                        class="form-control" style="width: 100%; padding: 8px 12px; border: 1px solid #ced4da; border-radius: 4px;">
                </div>
            </div>
        </div>

        <div style="text-align: center; margin-top: 20px;">
            <button type="submit" name="btnEdit" style="background: #007bff; color: white; padding: 10px 25px; border: none; border-radius: 4px; cursor: pointer; font-weight: 600;">
                <i class="bi bi-save"></i> Cập nhật
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