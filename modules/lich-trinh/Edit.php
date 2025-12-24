<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../includes/header.php';

if (!isset($_SESSION['user'])) {
    header("Location: " . BASE_URL . "modules/auth/dang_nhap.php");
    exit();
}

// requireAdmin();

$conn = $db->getConnection();

/* =========================
   LẤY DANH SÁCH
========================= */
$sql_tau = "SELECT id, ma_tau, ten_tau FROM tau ORDER BY ma_tau";
$tau_list = $conn->query($sql_tau)->fetchAll(PDO::FETCH_ASSOC);

$sql_tuyen = "SELECT id, ma_tuyen, ten_tuyen FROM tuyen_duong ORDER BY ma_tuyen";
$tuyen_list = $conn->query($sql_tuyen)->fetchAll(PDO::FETCH_ASSOC);

/* =========================
   KHAI BÁO BIẾN
========================= */
$id = $ma_lich_trinh = $id_tau = $id_tuyen_duong = $ngay_di = $ngay_den = $trang_thai = '';
$error_message = '';

/* =========================
   LẤY DỮ LIỆU THEO ID
========================= */
if (!isset($_GET['id'])) {
    echo "<script>alert('Không có ID lịch trình!'); window.location='index.php';</script>";
    exit;
}

$id = $_GET['id'];
$sql_select = "SELECT * FROM lich_trinh WHERE id = ?";
$stmt = $conn->prepare($sql_select);
$stmt->execute([$id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    echo "<script>alert('Không tìm thấy lịch trình!'); window.location='index.php';</script>";
    exit;
}

$ma_lich_trinh = $row['ma_lich_trinh'];
$id_tau = $row['id_tau'];
$id_tuyen_duong = $row['id_tuyen_duong'];
$ngay_di = date('Y-m-d\TH:i', strtotime($row['ngay_di']));
$ngay_den = date('Y-m-d\TH:i', strtotime($row['ngay_den']));
$trang_thai = $row['trang_thai'];

/* =========================
   XỬ LÝ CẬP NHẬT
========================= */
if (isset($_POST['btnEdit'])) {
    $id = $_POST['id'];
    $ma_lich_trinh = trim($_POST['ma_lich_trinh']);
    $id_tau = $_POST['id_tau'];
    $id_tuyen_duong = $_POST['id_tuyen_duong'];
    $ngay_di = $_POST['ngay_di'];
    $ngay_den = $_POST['ngay_den'];
    $trang_thai = $_POST['trang_thai'];

    if (strtotime($ngay_den) < strtotime($ngay_di)) {
        $error_message = "Ngày đến không được trước ngày đi!";
    } else {
        $sql_check = "SELECT COUNT(*) FROM lich_trinh WHERE ma_lich_trinh = ? AND id <> ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->execute([$ma_lich_trinh, $id]);

        if ($stmt_check->fetchColumn() > 0) {
            $error_message = "Mã lịch trình đã tồn tại, vui lòng nhập mã khác!";
        } else {
            $sql_update = "UPDATE lich_trinh SET ma_lich_trinh = ?, id_tau = ?, id_tuyen_duong = ?, ngay_di = ?, ngay_den = ?, trang_thai = ? WHERE id = ?";
            $stmt_update = $conn->prepare($sql_update);

            if ($stmt_update->execute([$ma_lich_trinh, $id_tau, $id_tuyen_duong, $ngay_di, $ngay_den, $trang_thai, $id])) {
                echo "<script>alert('Cập nhật thành công!'); window.location='index.php';</script>";
                exit;
            } else {
                $error_message = "Cập nhật thất bại!";
            }
        }
    }
}
?>

<div class="main-content">
    <h1>CẬP NHẬT LỊCH TRÌNH</h1>

    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger" style="color: #721c24; background-color: #f8d7da; border-color: #f5c6cb; padding: 15px; margin-bottom: 20px; border-radius: 4px;">
            <?= htmlspecialchars($error_message) ?>
        </div>
    <?php endif; ?>

    <form method="post">

        <div class="row" style="display: flex; flex-wrap: wrap; margin-right: -15px; margin-left: -15px;">

            <div class="col-md-6" style="flex: 0 0 50%; max-width: 50%; padding-right: 15px; padding-left: 15px; box-sizing: border-box;">
                <div class="form-group mb-20">
                    <label style="font-weight: 600; color: #34495e; display: block; margin-bottom: 5px;">Mã lịch trình:</label>
                    <input type="text" class="form-control" name="ma_lich_trinh" value="<?= htmlspecialchars($ma_lich_trinh) ?>" required
                        style="width: 100%; padding: 8px 12px; border: 1px solid #ced4da; border-radius: 4px;">
                </div>

                <div class="form-group mb-20">
                    <label style="font-weight: 600; color: #34495e; display: block; margin-bottom: 5px;">Tàu:</label>
                    <select class="form-control" name="id_tau" required style="width: 100%; padding: 8px 12px; border: 1px solid #ced4da; border-radius: 4px;">
                        <option value="">-- Chọn tàu --</option>
                        <?php foreach ($tau_list as $tau): ?>
                            <option value="<?= $tau['id'] ?>" <?= $id_tau == $tau['id'] ? 'selected' : '' ?>>
                                <?= $tau['ma_tau'] . ' - ' . $tau['ten_tau'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group mb-20">
                    <label style="font-weight: 600; color: #34495e; display: block; margin-bottom: 5px;">Tuyến đường:</label>
                    <select class="form-control" name="id_tuyen_duong" required style="width: 100%; padding: 8px 12px; border: 1px solid #ced4da; border-radius: 4px;">
                        <option value="">-- Chọn tuyến --</option>
                        <?php foreach ($tuyen_list as $tuyen): ?>
                            <option value="<?= $tuyen['id'] ?>" <?= $id_tuyen_duong == $tuyen['id'] ? 'selected' : '' ?>>
                                <?= $tuyen['ma_tuyen'] . ' - ' . $tuyen['ten_tuyen'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="col-md-6" style="flex: 0 0 50%; max-width: 50%; padding-right: 15px; padding-left: 15px; box-sizing: border-box;">
                <div class="form-group mb-20">
                    <label style="font-weight: 600; color: #34495e; display: block; margin-bottom: 5px;">Trạng thái:</label>
                    <select class="form-control" name="trang_thai" style="width: 100%; padding: 8px 12px; border: 1px solid #ced4da; border-radius: 4px;">
                        <?php foreach (['Chưa chạy', 'Đang chạy', 'Đã hoàn thành', 'Hủy'] as $tt): ?>
                            <option value="<?= $tt ?>" <?= $trang_thai == $tt ? 'selected' : '' ?>><?= $tt ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group mb-20">
                    <label style="font-weight: 600; color: #34495e; display: block; margin-bottom: 5px;">Ngày đi:</label>
                    <input type="datetime-local" class="form-control" name="ngay_di" value="<?= $ngay_di ?>" required
                        style="width: 100%; padding: 8px 12px; border: 1px solid #ced4da; border-radius: 4px;">
                </div>

                <div class="form-group mb-20">
                    <label style="font-weight: 600; color: #34495e; display: block; margin-bottom: 5px;">Ngày đến:</label>
                    <input type="datetime-local" class="form-control" name="ngay_den" value="<?= $ngay_den ?>" required
                        style="width: 100%; padding: 8px 12px; border: 1px solid #ced4da; border-radius: 4px;">
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
require_once __DIR__ . '/../../includes/footer.php';
?>