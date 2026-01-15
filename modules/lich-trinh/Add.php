<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../includes/header.php';

requireLogin();
requireAdmin();

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
        if (
            empty($ma_lich_trinh) || empty($id_tau) ||
            empty($id_tuyen_duong) || empty($ngay_di) ||
            empty($ngay_den) || empty($trang_thai)
        ) {
            $error_message = "Vui lòng nhập đầy đủ thông tin!";
        }

        /* 2. Kiểm tra ngày */ elseif (strtotime($ngay_den) < strtotime($ngay_di)) {
            $error_message = "Ngày đến không được trước ngày đi!";
        }

        /* 3. Kiểm tra trùng mã */ else {
            $stmt = $conn->prepare(
                "SELECT 1 FROM lich_trinh WHERE ma_lich_trinh = ?"
            );
            $stmt->execute([$ma_lich_trinh]);

            if ($stmt->fetch()) {
                $error_message = "Mã lịch trình đã tồn tại! Vui lòng nhập mã khác.";
            } else {

                /* 4. Insert */
                $stmt = $conn->prepare(
                    "INSERT INTO lich_trinh 
                    (ma_lich_trinh, id_tau, id_tuyen_duong, ngay_di, ngay_den, trang_thai)
                    VALUES (?, ?, ?, ?, ?, ?)"
                );

                if ($stmt->execute([
                    $ma_lich_trinh,
                    $id_tau,
                    $id_tuyen_duong,
                    $ngay_di,
                    $ngay_den,
                    $trang_thai
                ])) {
                    $success_message = "Thêm lịch trình thành công!";
                    // reset form
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

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Thêm lịch trình</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f4f7fb;
        }

        .form-box {
            max-width: 850px;
            margin: 50px auto;
            background-color: #7fa9d6;
            /* gần #81aad3ff */
            padding: 35px;
            border-radius: 16px;
        }

        h2 {
            text-align: center;
            font-weight: 700;
            color: #1f2d3d;
            margin-bottom: 30px;
        }

        .form-label {
            font-weight: 600;
        }

        .required::after {
            content: " *";
            color: red;
        }
    </style>
</head>

<body>
    <div class="form-box">

        <h2>THÊM LỊCH TRÌNH</h2>

        <?php if ($error_message): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
        <?php endif; ?>

        <?php if ($success_message): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success_message) ?></div>
        <?php endif; ?>

        <form method="post">

            <div class="row g-3">

                <div class="col-md-6">
                    <label class="form-label required">Mã lịch trình</label>
                    <input type="text" name="ma_lich_trinh" class="form-control"
                        value="<?= htmlspecialchars($ma_lich_trinh) ?>" placeholder="VD: LT001">
                </div>

                <div class="col-md-6">
                    <label class="form-label required">Tàu</label>
                    <select name="id_tau" class="form-select">
                        <option value="">-- Chọn tàu --</option>
                        <?php foreach ($tau_list as $t): ?>
                            <option value="<?= $t['id'] ?>" <?= $id_tau == $t['id'] ? 'selected' : '' ?>>
                                <?= $t['ma_tau'] ?> - <?= $t['ten_tau'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label required">Tuyến đường</label>
                    <select name="id_tuyen_duong" class="form-select">
                        <option value="">-- Chọn tuyến --</option>
                        <?php foreach ($tuyen_duong_list as $td): ?>
                            <option value="<?= $td['id'] ?>" <?= $id_tuyen_duong == $td['id'] ? 'selected' : '' ?>>
                                <?= $td['ma_tuyen'] ?> - <?= $td['ten_tuyen'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label required">Trạng thái</label>
                    <select name="trang_thai" class="form-select">
                        <option value="">-- Chọn --</option>
                        <option <?= $trang_thai == 'Chưa chạy' ? 'selected' : '' ?>>Chưa chạy</option>
                        <option <?= $trang_thai == 'Đang chạy' ? 'selected' : '' ?>>Đang chạy</option>
                        <option <?= $trang_thai == 'Đã hoàn thành' ? 'selected' : '' ?>>Đã hoàn thành</option>
                        <option <?= $trang_thai == 'Hủy' ? 'selected' : '' ?>>Hủy</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label required">Ngày đi</label>
                    <input type="datetime-local" class="form-control" name="ngay_di"
                        value="<?= $ngay_di ?>" required
                        min="<?= date('Y-m-d\TH:i') ?>">
                </div>

                <div class="col-md-6">
                    <label class="form-label required">Ngày đến</label>
                    <input type="datetime-local" class="form-control" name="ngay_den"
                        value="<?= $ngay_den ?>" required
                        min="<?= date('Y-m-d\TH:i') ?>">
                </div>

            </div>

            <div class="text-center mt-4">
                <button name="btnAdd" class="btn btn-success px-4">Thêm mới</button>
                <a href="index.php" class="btn btn-secondary px-4">Quay lại</a>
            </div>

        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
<?php
require_once __DIR__ . '/../../includes/footer.php'; ?>