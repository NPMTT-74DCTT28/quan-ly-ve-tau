<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../includes/header.php';

$conn = $db->getConnection();

// Khởi tạo các biến lưu thông báo cho Cách 2
$msg = "";
$msg_type = "";

// 1. Lấy dữ liệu cũ để hiển thị lên Form
$current_tuyen = null;
if (isset($_GET['ma_tuyen'])) {
    $ma_edit = trim($_GET['ma_tuyen']);

    $stmt = $conn->prepare("SELECT * FROM tuyen_duong WHERE ma_tuyen = ?");
    $stmt->execute([$ma_edit]);
    $current_tuyen = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$current_tuyen) {
        header("Location: index.php");
        exit();
    }
}

// Lấy danh sách ga tàu để đổ vào Select Box
$sqlGa = "SELECT id, ten_ga FROM ga_tau";
$stmtGa = $conn->query($sqlGa);
$dsGa = $stmtGa->fetchAll(PDO::FETCH_ASSOC);

// 2. Xử lý khi nhấn nút "Sửa Tuyến"
if (isset($_POST['btnsua'])) {
    $ma    = trim($_POST['txtmatuyen']);
    $ten   = trim($_POST['txttentuyen']);
    $gadi  = (int)$_POST['txtgadi'];
    $gaden = (int)$_POST['txtgaden'];
    $kc    = (float)$_POST['txtkhoangcach'];
    $gia   = (float)$_POST['txtgiacb'];

    // Kiểm tra các điều kiện logic dữ liệu
    if ($gadi === $gaden) {
        $msg = "Ga đi và ga đến không được trùng nhau!";
        $msg_type = "error";
    } else if ($kc <= 0) {
        $msg = "Khoảng cách phải là số dương lớn hơn 0!";
        $msg_type = "error";
    } else if ($gia <= 0) {
        $msg = "Giá cơ bản phải là số dương lớn hơn 0!";
        $msg_type = "error";
    } else {
        // Kiểm tra trùng lặp cặp ga đi - ga đến ở các tuyến khác
        $checkSql = "SELECT COUNT(*) FROM tuyen_duong WHERE id_ga_di = ? AND id_ga_den = ? AND ma_tuyen <> ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->execute([$gadi, $gaden, $ma]);

        if ($checkStmt->fetchColumn() > 0) {
            $msg = "Tuyến đường nối giữa hai ga này đã tồn tại trong hệ thống!";
            $msg_type = "error";
        } else {
            try {
                $sql = "UPDATE tuyen_duong
                        SET ten_tuyen = ?, 
                            id_ga_di = ?, 
                            id_ga_den = ?, 
                            khoang_cach_km = ?, 
                            gia_co_ban = ?
                        WHERE ma_tuyen = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$ten, $gadi, $gaden, $kc, $gia, $ma]);

                $msg = "Cập nhật tuyến đường thành công!";
                $msg_type = "success";

                // Cập nhật lại mảng $current_tuyen để form hiển thị ngay giá trị mới vừa sửa
                $current_tuyen['ten_tuyen'] = $ten;
                $current_tuyen['id_ga_di'] = $gadi;
                $current_tuyen['id_ga_den'] = $gaden;
                $current_tuyen['khoang_cach_km'] = $kc;
                $current_tuyen['gia_co_ban'] = $gia;

            } catch (PDOException $e) {
                $msg = "Lỗi cập nhật dữ liệu: " . $e->getMessage();
                $msg_type = "error";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sửa Tuyến Đường</title>
    <link rel="stylesheet" href="../../modules/ga-tau/stylethemga.css">
</head>
<body>

<div class="add-wrapper">
    <h2 class="add-title">SỬA TUYẾN ĐƯỜNG</h2>

    <?php if (!empty($msg)): ?>
        <div class="alert-box <?= $msg_type ?>" style="padding: 12px; margin-bottom: 20px; border-radius: 4px; text-align: center; font-weight: bold; <?= $msg_type === 'success' ? 'background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb;' : 'background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb;' ?>">
            <?= htmlspecialchars($msg) ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="add-form">

        <div class="form-row">
            <label>Mã Tuyến</label>
            <input type="text" name="txtmatuyen" value="<?= htmlspecialchars($current_tuyen['ma_tuyen'] ?? '') ?>" readonly style="background-color: #e9ecef; cursor: not-allowed;">
        </div>

        <div class="form-row">
            <label>Tên Tuyến</label>
            <input type="text" name="txttentuyen" value="<?= htmlspecialchars($current_tuyen['ten_tuyen'] ?? '') ?>" required>
        </div>

        <div class="form-row">
            <label>Ga đi</label>
            <select name="txtgadi" required>
                <?php foreach ($dsGa as $ga): ?>
                    <option value="<?= $ga['id']; ?>" <?= ($ga['id'] == ($current_tuyen['id_ga_di'] ?? '')) ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($ga['ten_ga']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-row">
            <label>Ga đến</label>
            <select name="txtgaden" required>
                <?php foreach ($dsGa as $ga): ?>
                    <option value="<?= $ga['id']; ?>" <?= ($ga['id'] == ($current_tuyen['id_ga_den'] ?? '')) ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($ga['ten_ga']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-row">
            <label>Khoảng cách (km)</label>
            <input type="number" step="0.1" name="txtkhoangcach" value="<?= htmlspecialchars($current_tuyen['khoang_cach_km'] ?? '') ?>" required>
        </div>

        <div class="form-row">
            <label>Giá cơ bản</label>
            <input type="number" step="0.01" name="txtgiacb" value="<?= htmlspecialchars($current_tuyen['gia_co_ban'] ?? '') ?>" required>
        </div>

        <div class="form-actions">
            <button type="submit" name="btnsua" class="btn-submit">
                Sửa Tuyến
            </button>
        </div>

    </form>
</div>

</body>
</html>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>