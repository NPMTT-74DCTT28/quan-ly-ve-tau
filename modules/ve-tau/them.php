<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../includes/header.php';

requireLogin();

$conn = $db->getConnection();

$sql_khach_hang = "SELECT id, ho_ten, sdt FROM khach_hang ORDER BY ho_ten";
$stmt_khach_hang = $conn->query($sql_khach_hang);
$khach_hang_list = $stmt_khach_hang->fetchAll();

$sql_lich_trinh = "SELECT id, ma_lich_trinh, ngay_di FROM lich_trinh WHERE ngay_di > NOW() ORDER BY ngay_di";
$stmt_lich_trinh = $conn->query($sql_lich_trinh);
$lich_trinh_list = $stmt_lich_trinh->fetchAll();

$ma_ve = $id_khach_hang = $id_lich_trinh = $id_ghe = $id_nhan_vien = $gia_ve = $trang_thai = '';
$show_success = $show_error = false;
$success_message = $error_message = '';
$ghe_list = [];

if (isset($_POST['id_lich_trinh']) && !empty($_POST['id_lich_trinh'])) {
    $id_lich_trinh = $_POST['id_lich_trinh'];
    if (isset($_POST['ma_ve'])) $ma_ve = $_POST['ma_ve'];
    if (isset($_POST['gia_ve'])) $gia_ve = $_POST['gia_ve'];
    if (isset($_POST['id_khach_hang'])) $id_khach_hang = $_POST['id_khach_hang'];
    if (isset($_POST['id_nhan_vien'])) $id_nhan_vien = $_POST['id_nhan_vien'];
    if (isset($_POST['trang_thai'])) $trang_thai = $_POST['trang_thai'];
    if (isset($_POST['id_ghe'])) $id_ghe = $_POST['id_ghe'];

    $sql_ghe = "SELECT g.id, g.so_ghe, t.ma_toa 
                FROM ghe g 
                JOIN toa_tau t ON g.id_toa_tau = t.id 
                JOIN tau tau ON t.id_tau = tau.id 
                JOIN lich_trinh lt ON lt.id_tau = tau.id 
                WHERE lt.id = ? 
                AND g.id NOT IN (SELECT id_ghe FROM ve_tau WHERE id_lich_trinh = ? AND trang_thai NOT IN ('Đã hủy'))
                ORDER BY t.ma_toa, g.so_ghe";
    $stmt_ghe = $conn->prepare($sql_ghe);
    $stmt_ghe->execute([$id_lich_trinh, $id_lich_trinh]);
    $ghe_list = $stmt_ghe->fetchAll();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnAdd'])) {
    $ma_ve = $_POST['ma_ve'];
    $id_khach_hang = $_POST['id_khach_hang'];
    $id_lich_trinh = $_POST['id_lich_trinh'];
    $id_ghe = $_POST['id_ghe'] ?? '';
    $id_nhan_vien = $_POST['id_nhan_vien'];
    $gia_ve = $_POST['gia_ve'];
    $trang_thai = $_POST['trang_thai'];

    if (empty($ma_ve) || empty($id_khach_hang) || empty($id_lich_trinh) || empty($id_ghe) || empty($gia_ve) || empty($trang_thai)) {
        $error_message = "Vui lòng điền đầy đủ thông tin bắt buộc!";
        $show_error = true;
    } else {
        $sql_check = "SELECT id FROM ve_tau WHERE ma_ve = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->execute([$ma_ve]);

        if ($stmt_check->rowCount() > 0) {
            $error_message = "Mã vé đã tồn tại! Vui lòng nhập mã khác.";
            $show_error = true;
        } else {
            $sql_check_ghe = "SELECT id FROM ve_tau WHERE id_lich_trinh = ? AND id_ghe = ? AND trang_thai NOT IN ('Đã hủy')";
            $stmt_check_ghe = $conn->prepare($sql_check_ghe);
            $stmt_check_ghe->execute([$id_lich_trinh, $id_ghe]);

            if ($stmt_check_ghe->rowCount() > 0) {
                $error_message = "Ghế này vừa được đặt bởi người khác!";
                $show_error = true;
            } else {
                $sql_insert = "INSERT INTO ve_tau (ma_ve, id_khach_hang, id_lich_trinh, id_ghe, id_nhan_vien, gia_ve, trang_thai) 
                               VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt_insert = $conn->prepare($sql_insert);

                if ($stmt_insert->execute([$ma_ve, $id_khach_hang, $id_lich_trinh, $id_ghe, $id_nhan_vien, $gia_ve, $trang_thai])) {
                    $success_message = "Thêm vé tàu thành công!";
                    $show_success = true;
                    $ma_ve = $id_khach_hang = $id_lich_trinh = $id_ghe = $id_nhan_vien = $gia_ve = $trang_thai = '';
                    $ghe_list = [];
                } else {
                    $error_message = "Thêm vé tàu thất bại!";
                    $show_error = true;
                }
            }
        }
    }
}
?>

<div class="main-content">
    <h1>THÊM VÉ TÀU MỚI</h1>

    <?php if ($show_success): ?>
        <div class="alert alert-success" style="color: #155724; background-color: #d4edda; border-color: #c3e6cb; padding: 15px; margin-bottom: 20px; border-radius: 4px;">
            <?= htmlspecialchars($success_message) ?>
        </div>
    <?php endif; ?>

    <?php if ($show_error): ?>
        <div class="alert alert-danger" style="color: #721c24; background-color: #f8d7da; border-color: #f5c6cb; padding: 15px; margin-bottom: 20px; border-radius: 4px;">
            <?= htmlspecialchars($error_message) ?>
        </div>
    <?php endif; ?>

    <form method="post" action="">
        <div class="row" style="display: flex; flex-wrap: wrap; margin-right: -15px; margin-left: -15px;">

            <div class="col-md-6" style="flex: 0 0 50%; max-width: 50%; padding-right: 15px; padding-left: 15px; box-sizing: border-box;">
                <div class="form-group mb-20">
                    <label style="font-weight: 600; color: #34495e; display: block; margin-bottom: 5px;">Mã vé (*):</label>
                    <input type="text" class="form-control" name="ma_ve" value="<?php echo htmlspecialchars($ma_ve); ?>" required placeholder="VD: VE2024..."
                        style="width: 100%; padding: 8px 12px; border: 1px solid #ced4da; border-radius: 4px;">
                </div>

                <div class="form-group mb-20">
                    <label style="font-weight: 600; color: #34495e; display: block; margin-bottom: 5px;">Khách hàng (*):</label>
                    <select class="form-control" name="id_khach_hang" required style="width: 100%; padding: 8px 12px; border: 1px solid #ced4da; border-radius: 4px;">
                        <option value="">-- Chọn khách hàng --</option>
                        <?php foreach ($khach_hang_list as $kh): ?>
                            <option value="<?php echo $kh['id']; ?>" <?php echo $id_khach_hang == $kh['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($kh['ho_ten'] . ' - ' . $kh['sdt']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group mb-20">
                    <label style="font-weight: 600; color: #34495e; display: block; margin-bottom: 5px;">Nhân viên bán vé (*):</label>
                    <select class="form-control" name="id_nhan_vien" required style="width: 100%; padding: 8px 12px; border: 1px solid #ced4da; border-radius: 4px;" disabled>
                        <option value="<?php echo $id_nhan_vien = $_SESSION['user']['id']; ?>">
                            <?php echo htmlspecialchars($_SESSION['user']['ho_ten']); ?>
                        </option>
                    </select>
                </div>
            </div>

            <div class="col-md-6" style="flex: 0 0 50%; max-width: 50%; padding-right: 15px; padding-left: 15px; box-sizing: border-box;">
                <div class="form-group mb-20">
                    <label style="font-weight: 600; color: #34495e; display: block; margin-bottom: 5px;">Giá vé (VNĐ) (*):</label>
                    <input type="number" class="form-control" name="gia_ve" id="gia_ve" disabled
                        value="<?php echo htmlspecialchars($gia_ve); ?>" required min="0" placeholder="VD: 500000"
                        style="width: 100%; padding: 8px 12px; border: 1px solid #ced4da; border-radius: 4px;">
                </div>

                <div class="form-group mb-20">
                    <label style="font-weight: 600; color: #34495e; display: block; margin-bottom: 5px;">Trạng thái (*):</label>
                    <select class="form-control" name="trang_thai" required style="width: 100%; padding: 8px 12px; border: 1px solid #ced4da; border-radius: 4px;">
                        <option value="">-- Chọn trạng thái --</option>
                        <option value="Chờ xác nhận" <?php echo $trang_thai == 'Chờ xác nhận' ? 'selected' : ''; ?>>Chờ xác nhận</option>
                        <option value="Đã xác nhận" <?php echo $trang_thai == 'Đã xác nhận' ? 'selected' : ''; ?>>Đã xác nhận</option>
                        <option value="Hoàn thành" <?php echo $trang_thai == 'Hoàn thành' ? 'selected' : ''; ?>>Hoàn thành</option>
                        <option value="Đã hủy" <?php echo $trang_thai == 'Đã hủy' ? 'selected' : ''; ?>>Đã hủy</option>
                    </select>
                </div>

                <div class="form-group mb-20">
                    <label style="font-weight: 600; color: #34495e; display: block; margin-bottom: 5px;">Lịch trình (*):</label>
                    <select class="form-control" name="id_lich_trinh" id="id_lich_trinh" required onchange="this.form.submit()" style="width: 100%; padding: 8px 12px; border: 1px solid #ced4da; border-radius: 4px;">
                        <option value="">-- Chọn lịch trình để hiện ghế trống --</option>
                        <?php foreach ($lich_trinh_list as $lt): ?>
                            <option value="<?php echo $lt['id']; ?>" <?php echo $id_lich_trinh == $lt['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($lt['ma_lich_trinh'] . ' - ' . date('d/m/Y H:i', strtotime($lt['ngay_di']))); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="col-12" style="width: 100%; padding: 0 15px; margin-top: 15px;">
                <?php if (!empty($ghe_list)): ?>
                    <label style="font-weight: 600; color: #34495e; display: block; margin-bottom: 10px;">Chọn ghế trống:</label>
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
                            background-color: #28a745 !important;
                            color: white;
                            border-color: #28a745;
                        }

                        .ghe-item:has(input:checked) span {
                            color: #eee;
                        }

                        .ghe-item:hover {
                            background-color: #f0f0f0;
                        }
                    </style>

                <?php elseif (!empty($id_lich_trinh)): ?>
                    <div class="alert alert-warning" style="background: #fff3cd; color: #856404; padding: 10px; border-radius: 4px;">
                        Không còn ghế trống cho lịch trình này.
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div style="text-align: center; margin-top: 30px;">
            <button type="submit" name="btnAdd" style="background: #28a745; color: white; padding: 10px 25px; border: none; border-radius: 4px; cursor: pointer; font-weight: 600;">
                <i class="bi bi-plus-circle"></i> Thêm vé
            </button>
            <a href="index.php" style="margin-left: 15px; color: #333; text-decoration: none; padding: 10px 20px; background: #e2e6ea; border-radius: 4px; display: inline-block;">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
        </div>
    </form>
</div>

<script>
document.querySelectorAll('.ghe-item').forEach(item => {
    item.addEventListener('click', function() {
        const idGhe = this.querySelector('input[name="id_ghe"]').value;
        const idLichTrinh = document.getElementById('id_lich_trinh').value;

        if (idGhe && idLichTrinh) {
            fetch(`tinhtientudong.php?id_lich_trinh=${idLichTrinh}&id_ghe=${idGhe}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        
                        document.querySelector('input[name="gia_ve"]').value = data.price;
                    }
                })
                .catch(error => console.error('Lỗi:', error));
        }
    });
});
</script>

<?php
require_once '../../includes/footer.php';
?>