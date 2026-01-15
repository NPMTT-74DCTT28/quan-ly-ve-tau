<?php
require_once __DIR__ . '/../../bootstrap.php';

requireLogin();
requireAdmin();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ma_nv = $_POST['ma_nhan_vien'];
    $ho_ten = $_POST['ho_ten'];
    $mat_khau = $_POST['mat_khau'];
    $ngay_sinh = $_POST['ngay_sinh'];
    $gioi_tinh = $_POST['gioi_tinh'];
    $sdt = $_POST['sdt'];
    $email = $_POST['email'];
    $dia_chi = $_POST['dia_chi'];
    $vai_tro = $_POST['vai_tro'];

    if (empty($ma_nv) || empty($mat_khau) || empty($ho_ten)) {
        $error = "Vui lòng nhập đầy đủ thông tin bắt buộc.";
    } else {
        if (empty($email)) {
            $email = null;
        }
        try {
            $conn = $db->getConnection();

            $stmt = $conn->prepare("SELECT COUNT(*) FROM nhan_vien WHERE ma_nhan_vien = ? OR sdt = ?");
            $stmt->execute([$ma_nv, $sdt]);
            if ($stmt->fetchColumn() > 0) {
                $error = "Mã nhân viên hoặc Số điện thoại đã tồn tại!";
            } else {
                $hashed_password = password_hash($mat_khau, PASSWORD_DEFAULT);

                $sql = "INSERT INTO nhan_vien (ma_nhan_vien, mat_khau, ho_ten, ngay_sinh, gioi_tinh, sdt, email, dia_chi, vai_tro) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$ma_nv, $hashed_password, $ho_ten, $ngay_sinh, $gioi_tinh, $sdt, $email, $dia_chi, $vai_tro]);

                $success = "Thêm nhân viên thành công!";
            }
        } catch (PDOException $e) {
            $error = "Lỗi hệ thống: " . $e->getMessage();
        }
    }
}

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="container" style="padding: 20px; max-width: 800px; margin: 0 auto;">
    <h2>Thêm Nhân viên mới</h2>

    <?php if ($error): ?>
        <div style="color: red; background: #f8d7da; padding: 10px; margin-bottom: 15px; border-radius: 4px;">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div style="color: green; background: #99FF99; padding: 10px; margin-bottom: 15px; border-radius: 4px;">
            <?php echo $success; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="" style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
        <div class="form-group" style="margin-bottom: 15px;">
            <label>Mã nhân viên (*)</label>
            <input type="text" name="ma_nhan_vien" class="form-control" required placeholder="VD: NV001" style="width: 100%; padding: 8px; margin-top: 5px;">
        </div>

        <div class="form-group" style="margin-bottom: 15px;">
            <label>Họ và tên (*)</label>
            <input type="text" name="ho_ten" class="form-control" required placeholder="Nhập họ tên" style="width: 100%; padding: 8px; margin-top: 5px;">
        </div>

        <div class="form-group" style="margin-bottom: 15px;">
            <label>Mật khẩu (*)</label>
            <input type="password" name="mat_khau" class="form-control" required placeholder="Nhập mật khẩu" style="width: 100%; padding: 8px; margin-top: 5px;">
        </div>

        <div style="display: flex; gap: 20px;">
            <div class="form-group" style="flex: 1; margin-bottom: 15px;">
                <label>Ngày sinh</label>
                <input type="date" name="ngay_sinh" class="form-control" value="<?php echo date('Y-m-d'); ?>" style="width: 100%; padding: 8px; margin-top: 5px;">
            </div>
            <div class="form-group" style="flex: 1; margin-bottom: 15px;">
                <label>Giới tính</label>
                <select name="gioi_tinh" class="form-control" style="width: 100%; padding: 8px; margin-top: 5px;">
                    <option value="Nam">Nam</option>
                    <option value="Nữ">Nữ</option>
                    <option value="Khác">Khác</option>
                </select>
            </div>
        </div>

        <div style="display: flex; gap: 20px;">
            <div class="form-group" style="flex: 1; margin-bottom: 15px;">
                <label>Số điện thoại (*)</label>
                <input type="text" name="sdt" class="form-control" required style="width: 100%; padding: 8px; margin-top: 5px;">
            </div>
            <div class="form-group" style="flex: 1; margin-bottom: 15px;">
                <label>Email</label>
                <input type="email" name="email" class="form-control" style="width: 100%; padding: 8px; margin-top: 5px;">
            </div>
        </div>

        <div class="form-group" style="margin-bottom: 15px;">
            <label>Địa chỉ</label>
            <input type="text" name="dia_chi" class="form-control" style="width: 100%; padding: 8px; margin-top: 5px;">
        </div>

        <div class="form-group" style="margin-bottom: 20px;">
            <label>Vai trò</label>
            <select name="vai_tro" class="form-control" style="width: 100%; padding: 8px; margin-top: 5px;" required>
                <option value="<?php echo ROLE_NHAN_VIEN; ?>"><?php echo ROLE_NHAN_VIEN; ?></option>
                <option value="<?php echo ROLE_ADMIN; ?>"><?php echo ROLE_ADMIN; ?></option>
            </select>
        </div>

        <button type="submit" style="background: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;">Lưu nhân viên</button>
        <a href="index.php" style="margin-left: 10px; color: #666; text-decoration: none;">Hủy bỏ</a>
    </form>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>