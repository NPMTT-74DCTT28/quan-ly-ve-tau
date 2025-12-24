<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../includes/header.php';

if (!isset($_SESSION['user'])) {
    header("Location: " . BASE_URL . "modules/auth/dang_nhap.php");
    exit();
}

$conn = $db->getConnection();

/* =======================
   LẤY DỮ LIỆU SELECT
======================= */
$tau_list = $conn->query(
    "SELECT id, ma_tau, ten_tau FROM tau ORDER BY ma_tau"
)->fetchAll(PDO::FETCH_ASSOC);

$tuyen_duong_list = $conn->query(
    "SELECT id, ma_tuyen, ten_tuyen FROM tuyen_duong ORDER BY ma_tuyen"
)->fetchAll(PDO::FETCH_ASSOC);

/* =======================
   KHỞI TẠO BIẾN
======================= */
$ma_lich_trinh = $id_tau = $id_tuyen_duong = '';
$ngay_di = $ngay_den = $trang_thai = '';
$error_message = $success_message = '';

/* =======================
   XỬ LÝ FORM
======================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['btnAdd'])) {
        $ma_lich_trinh   = trim($_POST['ma_lich_trinh']);
        $id_tau          = $_POST['id_tau'];
        $id_tuyen_duong  = $_POST['id_tuyen_duong'];
        $ngay_di         = $_POST['ngay_di'];
        $ngay_den        = $_POST['ngay_den'];
        $trang_thai      = $_POST['trang_thai'];

        /* 1. Kiểm tra rỗng */
        if (empty($ma_lich_trinh) || empty($id_tau) || empty($id_tuyen_duong) || empty($ngay_di) || empty($ngay_den) || empty($trang_thai)) {
            $error_message = "Vui lòng nhập đầy đủ thông tin!";
        }
        /* 2. Kiểm tra ngày */ elseif (strtotime($ngay_den) < strtotime($ngay_di)) {
            $error_message = "Ngày đến không được trước ngày đi!";
        }
        /* 3. Kiểm tra trùng mã */ else {
            $stmt = $conn->prepare("SELECT 1 FROM lich_trinh WHERE ma_lich_trinh = ?");
            $stmt->execute([$ma_lich_trinh]);

            if ($stmt->fetch()) {
                $error_message = "Mã lịch trình đã tồn tại! Vui lòng nhập mã khác.";
            } else {
                /* 4. Insert */
                $stmt = $conn->prepare(
                    "INSERT INTO lich_trinh (ma_lich_trinh, id_tau, id_tuyen_duong, ngay_di, ngay_den, trang_thai)
                    VALUES (?, ?, ?, ?, ?, ?)"
                );

                if ($stmt->execute([$ma_lich_trinh, $id_tau, $id_tuyen_duong, $ngay_di, $ngay_den, $trang_thai])) {
                    $success_message = "Thêm lịch trình thành công!";
                    $ma_lich_trinh = $id_tau = $id_tuyen_duong = '';
                    $ngay_di = $ngay_den = $trang_thai = '';
                } else {
                    $error_message = "Có lỗi xảy ra, vui lòng thử lại!";
                }
            }
        }
    }
}
?>

<div class="main-content">
    <h1>THÊM LỊCH TRÌNH MỚI</h1>

    <?php if ($error_message): ?>
        <div class="alert alert-danger" style="color: #721c24; background-color: #f8d7da; border-color: #f5c6cb; padding: 15px; margin-bottom: 20px; border-radius: 4px;">
            <?= htmlspecialchars($error_message) ?>
        </div>
    <?php endif; ?>

    <?php if ($success_message): ?>
        <div class="alert alert-success" style="color: #155724; background-color: #d4edda; border-color: #c3e6cb; padding: 15px; margin-bottom: 20px; border-radius: 4px;">
            <?= htmlspecialchars($success_message) ?>
        </div>
    <?php endif; ?>

    <form method="post">
        <div class="row" style="display: flex; flex-wrap: wrap; margin-right: -15px; margin-left: -15px;">

            <div class="col-md-6" style="flex: 0 0 50%; max-width: 50%; padding-right: 15px; padding-left: 15px; box-sizing: border-box;">
                <div class="form-group mb-20">
                    <label style="font-weight: 600; color: #34495e; display: block; margin-bottom: 5px;">Mã lịch trình (*):</label>
                    <input type="text" name="ma_lich_trinh" value="<?= htmlspecialchars($ma_lich_trinh) ?>" placeholder="VD: LT001"
                        class="form-control" style="width: 100%; padding: 8px 12px; border: 1px solid #ced4da; border-radius: 4px;">
                </div>

                <div class="form-group mb-20">
                    <label style="font-weight: 600; color: #34495e; display: block; margin-bottom: 5px;">Tàu (*):</label>
                    <select name="id_tau" class="form-control" style="width: 100%; padding: 8px 12px; border: 1px solid #ced4da; border-radius: 4px;">
                        <option value="">-- Chọn tàu --</option>
                        <?php foreach ($tau_list as $t): ?>
                            <option value="<?= $t['id'] ?>" <?= $id_tau == $t['id'] ? 'selected' : '' ?>>
                                <?= $t['ma_tau'] ?> - <?= $t['ten_tau'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group mb-20">
                    <label style="font-weight: 600; color: #34495e; display: block; margin-bottom: 5px;">Tuyến đường (*):</label>
                    <select name="id_tuyen_duong" class="form-control" style="width: 100%; padding: 8px 12px; border: 1px solid #ced4da; border-radius: 4px;">
                        <option value="">-- Chọn tuyến --</option>
                        <?php foreach ($tuyen_duong_list as $td): ?>
                            <option value="<?= $td['id'] ?>" <?= $id_tuyen_duong == $td['id'] ? 'selected' : '' ?>>
                                <?= $td['ma_tuyen'] ?> - <?= $td['ten_tuyen'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="col-md-6" style="flex: 0 0 50%; max-width: 50%; padding-right: 15px; padding-left: 15px; box-sizing: border-box;">
                <div class="form-group mb-20">
                    <label style="font-weight: 600; color: #34495e; display: block; margin-bottom: 5px;">Trạng thái (*):</label>
                    <select name="trang_thai" class="form-control" style="width: 100%; padding: 8px 12px; border: 1px solid #ced4da; border-radius: 4px;">
                        <option value="">-- Chọn --</option>
                        <option <?= $trang_thai == 'Chưa chạy' ? 'selected' : '' ?>>Chưa chạy</option>
                        <option <?= $trang_thai == 'Đang chạy' ? 'selected' : '' ?>>Đang chạy</option>
                        <option <?= $trang_thai == 'Đã hoàn thành' ? 'selected' : '' ?>>Đã hoàn thành</option>
                        <option <?= $trang_thai == 'Hủy' ? 'selected' : '' ?>>Hủy</option>
                    </select>
                </div>

                <div class="form-group mb-20">
                    <label style="font-weight: 600; color: #34495e; display: block; margin-bottom: 5px;">Ngày đi (*):</label>
                    <input type="datetime-local" name="ngay_di" value="<?= htmlspecialchars($ngay_di) ?>"
                        class="form-control" style="width: 100%; padding: 8px 12px; border: 1px solid #ced4da; border-radius: 4px;">
                </div>

                <div class="form-group mb-20">
                    <label style="font-weight: 600; color: #34495e; display: block; margin-bottom: 5px;">Ngày đến (*):</label>
                    <input type="datetime-local" name="ngay_den" value="<?= htmlspecialchars($ngay_den) ?>"
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
require_once __DIR__ . '/../../includes/footer.php';
?>