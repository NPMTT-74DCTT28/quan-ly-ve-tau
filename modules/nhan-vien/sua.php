<?php
require_once __DIR__ . '/../../bootstrap.php';

requireLogin();
requireAdmin();

$current_user_id = $_SESSION['user']['id'];
$id = $_GET['id'] ?? $current_user_id;

if (!isAdmin() && $id != $current_user_id) {
    echo "<script>
            alert('Cảnh báo: Bạn đang cố truy cập thông tin không thuộc quyền hạn!'); 
            window.location.href='" . BASE_URL . "';
          </script>";
    exit();
}

$conn = $db->getConnection();
$stmt = $conn->prepare("SELECT * FROM nhan_vien WHERE id = ?");
$stmt->execute([$id]);
$nv = $stmt->fetch();

if (!$nv) {
    die("Nhân viên không tồn tại!");
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ho_ten = $_POST['ho_ten'];
    $ngay_sinh = $_POST['ngay_sinh'];
    $gioi_tinh = $_POST['gioi_tinh'];
    $sdt = $_POST['sdt'];
    $email = $_POST['email'];
    $dia_chi = $_POST['dia_chi'];

    if (isAdmin()) {
        $vai_tro = $_POST['vai_tro'];
    } else {
        $vai_tro = $nv['vai_tro'];
    }

    try {
        $check = $conn->prepare("SELECT COUNT(*) FROM nhan_vien WHERE sdt = ? AND id != ?");
        $check->execute([$sdt, $id]);

        if ($check->fetchColumn() > 0) {
            $error = "Số điện thoại này đã được sử dụng bởi nhân viên khác!";
        } else {
            if (empty($email)) $email = null;

            $sql = "UPDATE nhan_vien SET ho_ten=?, ngay_sinh=?, gioi_tinh=?, sdt=?, email=?, dia_chi=?, vai_tro=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$ho_ten, $ngay_sinh, $gioi_tinh, $sdt, $email, $dia_chi, $vai_tro, $id]);

            if ($id == $current_user_id) {
                $_SESSION['user']['ho_ten'] = $ho_ten;
            }

            if (isAdmin()) {
                echo "<script>alert('Cập nhật thành công!'); window.location.href='index.php';</script>";
            } else {
                echo "<script>alert('Cập nhật thông tin cá nhân thành công!'); window.location.href='sua.php?id=$id';</script>";
            }
            exit();
        }
    } catch (PDOException $e) {
        $error = "Lỗi: " . $e->getMessage();
    }
}

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="container" style="padding: 20px; max-width: 800px; margin: 0 auto;">
    <h2>
        <?php echo ($id == $current_user_id) ? "Thông tin cá nhân" : "Sửa nhân viên: " . $nv['ho_ten']; ?>
    </h2>

    <?php if ($error): ?>
        <div style="color: red; background: #f8d7da; padding: 10px; margin-bottom: 15px; border-radius: 4px;">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="" style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">

        <div class="form-group" style="margin-bottom: 15px;">
            <label>Mã nhân viên</label>
            <input type="text" class="form-control" value="<?php echo $nv['ma_nhan_vien']; ?>" disabled style="width: 100%; padding: 8px; margin-top: 5px; background: #eee;">
        </div>

        <div class="form-group" style="margin-bottom: 15px;">
            <label>Họ và tên</label>
            <input type="text" name="ho_ten" class="form-control" value="<?php echo $nv['ho_ten']; ?>" required style="width: 100%; padding: 8px; margin-top: 5px;">
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
            <?php if (isAdmin()): ?>
                <select name="vai_tro" class="form-control" style="width: 100%; padding: 8px; margin-top: 5px;">
                    <option value="<?php echo ROLE_NHAN_VIEN; ?>" <?php echo $nv['vai_tro'] == ROLE_NHAN_VIEN ? 'selected' : ''; ?>><?php echo ROLE_NHAN_VIEN; ?></option>
                    <option value="<?php echo ROLE_ADMIN; ?>" <?php echo $nv['vai_tro'] == ROLE_ADMIN ? 'selected' : ''; ?>><?php echo ROLE_ADMIN; ?></option>
                </select>
            <?php else: ?>
                <input type="text" class="form-control" value="<?php echo $nv['vai_tro']; ?>" disabled style="width: 100%; padding: 8px; margin-top: 5px; background: #eee;">
            <?php endif; ?>
        </div>

        <button type="submit" style="background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;">
            Lưu thay đổi
        </button>
        <a href="<?php echo BASE_URL; ?>" style="margin-left: 10px; color: #666; text-decoration: none;">Về trang chủ</a>

    </form>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>