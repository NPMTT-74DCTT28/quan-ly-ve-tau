<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../includes/header.php';

requireLogin();

$conn = $db->getConnection();

$error_message = '';

// Dropdowns
$sql_khach_hang = "SELECT id, ho_ten, sdt FROM khach_hang ORDER BY ho_ten";
$khach_hang_list = $conn->query($sql_khach_hang)->fetchAll();

$sql_lich_trinh = "SELECT id, ma_lich_trinh, ngay_di FROM lich_trinh ORDER BY ngay_di";
$lich_trinh_list = $conn->query($sql_lich_trinh)->fetchAll();

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM ve_tau WHERE id = ?");
    $stmt->execute([$id]);
    $ve_tau = $stmt->fetch();

    if (!$ve_tau) {
        echo "<script>alert('Không tìm thấy vé!'); window.location='index.php';</script>";
        exit();
    }

    $ma_ve = $ve_tau['ma_ve'];
    $id_khach_hang = $ve_tau['id_khach_hang'];
    $id_lich_trinh = $ve_tau['id_lich_trinh'];
    $id_ghe = $ve_tau['id_ghe'];
    $gia_ve = $ve_tau['gia_ve'];
    $trang_thai = $ve_tau['trang_thai'];

    // Logic lấy ghế: Ghế trống + Ghế của chính vé này
    $sql_ghe = "SELECT g.id, g.so_ghe, t.ma_toa 
                FROM ghe g 
                JOIN toa_tau t ON g.id_toa_tau = t.id 
                JOIN tau tau ON t.id_tau = tau.id 
                JOIN lich_trinh lt ON lt.id_tau = tau.id 
                WHERE lt.id = ? 
                AND (g.id = ? OR g.id NOT IN (SELECT id_ghe FROM ve_tau WHERE id_lich_trinh = ? AND id != ? AND trang_thai NOT IN ('Đã hủy')))
                ORDER BY t.ma_toa, g.so_ghe";
    $stmt_ghe = $conn->prepare($sql_ghe);
    $stmt_ghe->execute([$id_lich_trinh, $id_ghe, $id_lich_trinh, $id]);
    $ghe_list = $stmt_ghe->fetchAll();
} else {
    echo "<script>alert('Không có ID!'); window.location='index.php';</script>";
    exit();
}

if (isset($_POST['btnEdit'])) {
    $id = $_POST['id'];
    $ma_ve = $_POST['ma_ve'];
    $id_khach_hang = $_POST['id_khach_hang'];
    $id_lich_trinh = $_POST['id_lich_trinh'];
    $id_ghe = $_POST['id_ghe'];
    $gia_ve = $_POST['gia_ve'];
    $trang_thai = $_POST['trang_thai'];

    $sql_check = "SELECT id FROM ve_tau WHERE ma_ve = ? AND id != ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->execute([$ma_ve, $id]);

    if ($stmt_check->rowCount() > 0) {
        $error_message = "Mã vé đã tồn tại!";
    } else {
        $sql_check_ghe = "SELECT id FROM ve_tau WHERE id_lich_trinh = ? AND id_ghe = ? AND id != ? AND trang_thai NOT IN ('Đã hủy')";
        $stmt_check_ghe = $conn->prepare($sql_check_ghe);
        $stmt_check_ghe->execute([$id_lich_trinh, $id_ghe, $id]);

        if ($stmt_check_ghe->rowCount() > 0) {
            $error_message = "Ghế này đã được đặt!";
        } else {
            $sql_update = "UPDATE ve_tau SET ma_ve=?, id_khach_hang=?, id_lich_trinh=?, id_ghe=?, gia_ve=?, trang_thai=? WHERE id=?";
            $stmt_update = $conn->prepare($sql_update);

            if ($stmt_update->execute([$ma_ve, $id_khach_hang, $id_lich_trinh, $id_ghe, $gia_ve, $trang_thai, $id])) {
                echo "<script>alert('Cập nhật thành công!'); window.location='index.php';</script>";
                exit();
            } else {
                $error_message = "Cập nhật thất bại!";
            }
        }
    }
}
?>

<div class="main-content">
    <h1>CẬP NHẬT THÔNG TIN VÉ TÀU</h1>

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
                    <label style="font-weight: 600; color: #34495e; display: block; margin-bottom: 5px;">Mã vé:</label>
                    <input type="text" class="form-control" name="ma_ve" value="<?php echo htmlspecialchars($ma_ve); ?>" required
                        style="width: 100%; padding: 8px 12px; border: 1px solid #ced4da; border-radius: 4px;">
                </div>

                <div class="form-group mb-20">
                    <label style="font-weight: 600; color: #34495e; display: block; margin-bottom: 5px;">Khách hàng:</label>
                    <select class="form-control" name="id_khach_hang" required style="width: 100%; padding: 8px 12px; border: 1px solid #ced4da; border-radius: 4px;">
                        <?php foreach ($khach_hang_list as $kh): ?>
                            <option value="<?php echo $kh['id']; ?>" <?php echo $id_khach_hang == $kh['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($kh['ho_ten'] . ' - ' . $kh['sdt']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group mb-20">
                    <label style="font-weight: 600; color: #34495e; display: block; margin-bottom: 5px;">Nhân viên bán vé:</label>
                    <select class="form-control" name="id_nhan_vien" style="width: 100%; padding: 8px 12px; border: 1px solid #ced4da; border-radius: 4px;" disabled>
                        <option value="<?php echo $id_nhan_vien = $_SESSION['user']['id']; ?>">
                            <?php echo htmlspecialchars($_SESSION['user']['ho_ten']); ?>
                        </option>
                    </select>
                </div>
            </div>

            <div class="col-md-6" style="flex: 0 0 50%; max-width: 50%; padding-right: 15px; padding-left: 15px; box-sizing: border-box;">
                <div class="form-group mb-20">
                    <label style="font-weight: 600; color: #34495e; display: block; margin-bottom: 5px;">Giá vé (VNĐ):</label>
                    <input type="number" class="form-control" name="gia_ve" value="<?php echo htmlspecialchars($gia_ve); ?>" required min="0"
                        style="width: 100%; padding: 8px 12px; border: 1px solid #ced4da; border-radius: 4px;">
                </div>

                <div class="form-group mb-20">
                    <label style="font-weight: 600; color: #34495e; display: block; margin-bottom: 5px;">Trạng thái:</label>
                    <select class="form-control" name="trang_thai" required style="width: 100%; padding: 8px 12px; border: 1px solid #ced4da; border-radius: 4px;">
                        <option value="Chờ xác nhận" <?php echo $trang_thai == 'Chờ xác nhận' ? 'selected' : ''; ?>>Chờ xác nhận</option>
                        <option value="Đã xác nhận" <?php echo $trang_thai == 'Đã xác nhận' ? 'selected' : ''; ?>>Đã xác nhận</option>
                        <option value="Hoàn thành" <?php echo $trang_thai == 'Hoàn thành' ? 'selected' : ''; ?>>Hoàn thành</option>
                        <option value="Đã hủy" <?php echo $trang_thai == 'Đã hủy' ? 'selected' : ''; ?>>Đã hủy</option>
                    </select>
                </div>

                <div class="form-group mb-20">
                    <label style="font-weight: 600; color: #34495e; display: block; margin-bottom: 5px;">Lịch trình:</label>
                    <select class="form-control" name="id_lich_trinh" disabled style="width: 100%; padding: 8px 12px; border: 1px solid #ced4da; border-radius: 4px; background: #e9ecef;">
                        <?php foreach ($lich_trinh_list as $lt): ?>
                            <option value="<?php echo $lt['id']; ?>" <?php echo $id_lich_trinh == $lt['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($lt['ma_lich_trinh'] . ' - ' . date('d/m/Y H:i', strtotime($lt['ngay_di']))); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <input type="hidden" name="id_lich_trinh" value="<?= $id_lich_trinh ?>">
                    <small style="color: #666;">(Không thể đổi lịch trình khi sửa vé)</small>
                </div>
            </div>

            <div class="col-12" style="width: 100%; padding: 0 15px; margin-top: 15px;">
                <label style="font-weight: 600; color: #34495e; display: block; margin-bottom: 10px;">Đổi ghế (chỉ hiện ghế trống và ghế hiện tại):</label>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(80px, 1fr)); gap: 10px;">
                    <?php foreach ($ghe_list as $ghe): ?>
                        <label class="ghe-item" style="border: 1px solid #ccc; padding: 10px; text-align: center; border-radius: 4px; cursor: pointer; background: #fff; position: relative;">
                            <input type="radio" name="id_ghe" value="<?php echo $ghe['id']; ?>"
                                <?php echo $id_ghe == $ghe['id'] ? 'checked' : ''; ?>
                                style="position: absolute; opacity: 0;">
                            <div class="ghe-content">
                                <strong style="display: block; font-size: 14px;"><?php echo htmlspecialchars($ghe['so_ghe']); ?></strong>
                                <span style="font-size: 11px; color: #666;"><?php echo htmlspecialchars($ghe['ma_toa']); ?></span>
                            </div>
                        </label>
                    <?php endforeach; ?>
                </div>
                <style>
                    .ghe-item:has(input:checked) {
                        background-color: #007bff !important;
                        color: white;
                        border-color: #007bff;
                    }

                    .ghe-item:has(input:checked) span {
                        color: #eee;
                    }

                    .ghe-item:hover {
                        background-color: #f0f0f0;
                    }
                </style>
            </div>
        </div>

        <div style="text-align: center; margin-top: 30px;">
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