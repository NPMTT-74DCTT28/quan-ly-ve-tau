<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__. '/../../includes/header.php';

$conn = $db->getConnection();

/* =========================
   LẤY DANH SÁCH TÀU
========================= */
$sql_tau = "SELECT id, ma_tau, ten_tau FROM tau ORDER BY ma_tau";
$tau_list = $conn->query($sql_tau)->fetchAll(PDO::FETCH_ASSOC);

/* =========================
   LẤY DANH SÁCH TUYẾN ĐƯỜNG
========================= */
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

    // 1️⃣ Kiểm tra ngày
    if (strtotime($ngay_den) < strtotime($ngay_di)) {
        $error_message = "Ngày đến không được trước ngày đi!";
    } else {

        // 2️⃣ Kiểm tra trùng mã lịch trình
        $sql_check = "SELECT COUNT(*) FROM lich_trinh 
                      WHERE ma_lich_trinh = ? AND id <> ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->execute([$ma_lich_trinh, $id]);

        if ($stmt_check->fetchColumn() > 0) {
            $error_message = "Mã lịch trình đã tồn tại, vui lòng nhập mã khác!";
        } else {

            // 3️⃣ Update
            $sql_update = "UPDATE lich_trinh SET 
                           ma_lich_trinh = ?, 
                           id_tau = ?, 
                           id_tuyen_duong = ?, 
                           ngay_di = ?, 
                           ngay_den = ?, 
                           trang_thai = ?
                           WHERE id = ?";
            $stmt_update = $conn->prepare($sql_update);

            if ($stmt_update->execute([
                $ma_lich_trinh,
                $id_tau,
                $id_tuyen_duong,
                $ngay_di,
                $ngay_den,
                $trang_thai,
                $id
            ])) {
                echo "<script>alert('Cập nhật thành công!'); window.location='index.php';</script>";
                exit;
            } else {
                $error_message = "Cập nhật thất bại!";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Cập nhật lịch trình</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    .custom-card {
        background-color: #81aad3ff; /* màu bạn yêu cầu */
        border-radius: 15px;
        box-shadow: 0 6px 18px rgba(0,0,0,0.15);
    }

    .form-label,
    h3 {
        color: #1f3a5a;
        font-weight: 600;
    }
</style>

    <style>
</style>

</head>
<body>
<div class="container mt-5">
    <div class="card custom-card p-4">

        <h3 class="text-center mb-4">CẬP NHẬT LỊCH TRÌNH</h3>

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?= htmlspecialchars($error_message) ?>
                <button class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <form method="post">
            <input type="hidden" name="id" value="<?= $id ?>">

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Mã lịch trình</label>
                    <input type="text" class="form-control" name="ma_lich_trinh"
                           value="<?= htmlspecialchars($ma_lich_trinh) ?>" required autofocus>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Tàu</label>
                    <select class="form-control" name="id_tau" required>
                        <option value="">-- Chọn tàu --</option>
                        <?php foreach ($tau_list as $tau): ?>
                            <option value="<?= $tau['id'] ?>" <?= $id_tau == $tau['id'] ? 'selected' : '' ?>>
                                <?= $tau['ma_tau'] . ' - ' . $tau['ten_tau'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Tuyến đường</label>
                    <select class="form-control" name="id_tuyen_duong" required>
                        <option value="">-- Chọn tuyến --</option>
                        <?php foreach ($tuyen_list as $tuyen): ?>
                            <option value="<?= $tuyen['id'] ?>" <?= $id_tuyen_duong == $tuyen['id'] ? 'selected' : '' ?>>
                                <?= $tuyen['ma_tuyen'] . ' - ' . $tuyen['ten_tuyen'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Trạng thái</label>
                    <select class="form-control" name="trang_thai">
                        <?php
                        $arr = ['Chưa chạy', 'Đang chạy', 'Đã hoàn thành', 'Hủy'];
                        foreach ($arr as $tt):
                        ?>
                            <option value="<?= $tt ?>" <?= $trang_thai == $tt ? 'selected' : '' ?>><?= $tt ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Ngày đi</label>
                    <input type="datetime-local" class="form-control" name="ngay_di"
                           value="<?= $ngay_di ?>" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Ngày đến</label>
                    <input type="datetime-local" class="form-control" name="ngay_den"
                           value="<?= $ngay_den ?>" required>
                </div>
            </div>

            <div class="text-center mt-4">
                <button class="btn btn-primary" name="btnEdit">Cập nhật</button>
                <a href="index.php" class="btn btn-secondary">Quay lại</a>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
require_once __DIR__. '/../../includes/footer.php';?>
