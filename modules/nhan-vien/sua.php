<?php
require_once __DIR__ . '/../../bootstrap.php';

if (!isset($_SESSION['user'])) {
    header("Location: " . BASE_URL . "modules/auth/dang_nhap.php");
    exit();
}

$id = $_GET['id'] ?? 0;
$conn = $db->getConnection();
$error = '';

$stmt = $conn->prepare("SELECT * FROM nhan_vien WHERE id = ?");
$stmt->execute([$id]);
$nv = $stmt->fetch();

if (!$nv) {
    die("Nhân viên không tồn tại!");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ho_ten = $_POST['ho_ten'];
    $mat_khau = $_POST['mat_khau'];
    $ngay_sinh = $_POST['ngay_sinh'];
    $gioi_tinh = $_POST['gioi_tinh'];
    $sdt = $_POST['sdt'];
    $email = $_POST['email'];
    $dia_chi = $_POST['dia_chi'];
    $vai_tro = $_POST['vai_tro'];

    try {
        $check = $conn->prepare("SELECT COUNT(*) FROM nhan_vien WHERE sdt = ? AND id != ?");
        $check->execute([$sdt, $id]);

        if ($check->fetchColumn() > 0) {
            $error = "Số điện thoại này đã được sử dụng bởi nhân viên khác!";
        } else {
            if (empty($email)) {
                $email = null;
            }

            $sql = "UPDATE nhan_vien SET ho_ten=?, ngay_sinh=?, gioi_tinh=?, sdt=?, email=?, dia_chi=?, vai_tro=?";
            $params = [$ho_ten, $ngay_sinh, $gioi_tinh, $sdt, $email, $dia_chi, $vai_tro];

            if (!empty($mat_khau)) {
                $sql .= ", mat_khau=?";
                $params[] = password_hash($mat_khau, PASSWORD_DEFAULT);
            }

            $sql .= " WHERE id=?";
            $params[] = $id;

            $stmt = $conn->prepare($sql);
            $stmt->execute($params);

            echo "<script>alert('Cập nhật thành công!'); window.location.href='index.php';</script>";
            exit();
        }
    } catch (PDOException $e) {
        $error = "Lỗi: " . $e->getMessage();
    }
}

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="container" style="padding: 20px; max-width: 800px; margin: 0 auto;">
    <h2>Sửa thông tin: <?php echo $nv['ho_ten']; ?></h2>

    <?php if ($error): ?>
        <div style="color: red; background: #f8d7da; padding: 10px; margin-bottom: 15px; border-radius: 4px;">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="" style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">

        <div class="form-group" style="margin-bottom: 15px;">
            <label>Mã nhân viên (Không thể sửa)</label>
            <input type="text" class="form-control" value="<?php echo $nv['ma_nhan_vien']; ?>" disabled style="width: 100%; padding: 8px; margin-top: 5px; background: #eee;">
        </div>

        <div class="form-group" style="margin-bottom: 15px;">
            <label>Họ và tên</label>
            <input type="text" name="ho_ten" class="form-control" value="<?php echo $nv['ho_ten']; ?>" required style="width: 100%; padding: 8px; margin-top: 5px;">
        </div>

        <div class="form-group" style="margin-bottom: 15px;">
            <label>Mật khẩu mới (Để trống nếu không muốn đổi)</label>
            <input type="password" name="mat_khau" class="form-control" placeholder="Nhập mật khẩu mới..." style="width: 100%; padding: 8px; margin-top: 5px;">
        </div>

        <div style="display: flex; gap: 20px;">
            <div class="form-group" style="flex: 1; margin-bottom: 15px;">
                <label>Ngày sinh</label>
                <input type="date" name="ngay_sinh" class="form-control" value="<?php echo $nv['ngay_sinh']; ?>" style="width: 100%; padding: 8px; margin-top: 5px;">
            </div>
            <div class="form-group" style="flex: 1; margin-bottom: 15px;">
                <label>Giới tính</label>
                <select name="gioi_tinh" class="form-control" style="width: 100%; padding: 8px; margin-top: 5px;">
                    <option value="Nam" <?php echo $nv['gioi_tinh'] == 'Nam' ? 'selected' : ''; ?>>Nam</option>
                    <option value="Nữ" <?php echo $nv['gioi_tinh'] == 'Nữ' ? 'selected' : ''; ?>>Nữ</option>
                    <option value="Khác" <?php echo $nv['gioi_tinh'] == 'Khác' ? 'selected' : ''; ?>>Khác</option>
                </select>
            </div>
        </div>

        <div style="display: flex; gap: 20px;">
            <div class="form-group" style="flex: 1; margin-bottom: 15px;">
                <label>Số điện thoại</label>
                <input type="text" name="sdt" class="form-control" value="<?php echo $nv['sdt']; ?>" required style="width: 100%; padding: 8px; margin-top: 5px;">
            </div>
            <div class="form-group" style="flex: 1; margin-bottom: 15px;">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="<?php echo $nv['email']; ?>" style="width: 100%; padding: 8px; margin-top: 5px;">
            </div>
        </div>

        <div class="form-group" style="margin-bottom: 15px;">
            <label>Địa chỉ</label>
            <input type="text" name="dia_chi" class="form-control" value="<?php echo $nv['dia_chi']; ?>" style="width: 100%; padding: 8px; margin-top: 5px;">
        </div>

        <div class="form-group" style="margin-bottom: 20px;">
            <label>Vai trò</label>
            <select name="vai_tro" class="form-control" style="width: 100%; padding: 8px; margin-top: 5px;">

                <option value="<?php echo ROLE_NHAN_VIEN; ?>"
                    <?php echo $nv['vai_tro'] == ROLE_NHAN_VIEN ? 'selected' : ''; ?>>
                    <?php echo ROLE_NHAN_VIEN; ?>
                </option>

                <option value="<?php echo ROLE_ADMIN; ?>"
                    <?php echo $nv['vai_tro'] == ROLE_ADMIN ? 'selected' : ''; ?>>
                    <?php echo ROLE_ADMIN; ?>
                </option>
            </select>
        </div>

        <button type="submit" style="background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;">Cập nhật</button>
        <a href="index.php" style="margin-left: 10px; color: #666; text-decoration: none;">Hủy bỏ</a>
    </form>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>