<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../includes/header.php';

$conn = $db->getConnection();

// Khởi tạo các biến lưu thông báo cho Cách 2
$msg = "";
$msg_type = "";

$sqlGa = "SELECT id, ten_ga FROM ga_tau";
$stmtGa = $conn->query($sqlGa);
$dsGa = $stmtGa->fetchAll(PDO::FETCH_ASSOC);

if (isset($_POST['btnthem'])) {
    $ma    = trim($_POST['txtmatuyen']);
    $ten   = trim($_POST['txttentuyen']);
    $gadi  = (int)$_POST['txtgadi'];
    $gaden = (int)$_POST['txtgaden'];
    $kc    = (float)$_POST['txtkhoangcach'];
    $gia   = (float)$_POST['txtgiacb'];

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
        $sql = "INSERT INTO tuyen_duong
                (ma_tuyen, ten_tuyen, id_ga_di, id_ga_den, Khoang_cach_km, gia_co_ban)
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        try {
            $stmt->execute([$ma, $ten, $gadi, $gaden, $kc, $gia]);
            $msg = "Thêm tuyến đường thành công!";
            $msg_type = "success";
            
            // Xóa dữ liệu cũ sau khi thêm thành công để form trống sạch
            $ma = $ten = $gadi = $gaden = $kc = $gia = "";
        } catch (PDOException $e) {
            $msg = "Thêm tuyến đường thất bại. Có thể mã tuyến đã tồn tại!";
            $msg_type = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Tuyến Đường</title>
    <link rel="stylesheet" href="../../modules/ga-tau/stylethemga.css">
</head>
<body>

<div class="add-wrapper">

    <h2 class="add-title">THÊM TUYẾN ĐƯỜNG</h2>

    <?php if (!empty($msg)): ?>
        <div class="alert-box <?= $msg_type ?>" style="padding: 12px; margin-bottom: 20px; border-radius: 4px; text-align: center; font-weight: bold; <?= $msg_type === 'success' ? 'background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb;' : 'background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb;' ?>">
            <?= htmlspecialchars($msg) ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="add-form">

        <div class="form-row">
            <label>Mã Tuyến</label>
            <input type="text" name="txtmatuyen" value="<?= isset($ma) ? htmlspecialchars($ma) : '' ?>" required>
        </div>

        <div class="form-row">
            <label>Tên Tuyến</label>
            <input type="text" name="txttentuyen" value="<?= isset($ten) ? htmlspecialchars($ten) : '' ?>" required>
        </div>

        <div class="form-row">
            <label>Ga đi</label>
            <select name="txtgadi" required>
                <option value="">-- Chọn ga đi --</option>
                <?php foreach ($dsGa as $ga): ?>
                    <option value="<?= $ga['id'] ?>" <?= (isset($gadi) && $gadi == $ga['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($ga['ten_ga']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-row">
            <label>Ga đến</label>
            <select name="txtgaden" required>
                <option value="">-- Chọn ga đến --</option>
                <?php foreach ($dsGa as $ga): ?>
                    <option value="<?= $ga['id'] ?>" <?= (isset($gaden) && $gaden == $ga['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($ga['ten_ga']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-row">
            <label>Khoảng cách (km)</label>
            <input type="number" step="0.1" name="txtkhoangcach" value="<?= isset($kc) ? htmlspecialchars($kc) : '' ?>" required>
        </div>

        <div class="form-row">
            <label>Giá cơ bản</label>
            <input type="number" step="0.01" name="txtgiacb" value="<?= isset($gia) ? htmlspecialchars($gia) : '' ?>" required>
        </div>

        <div class="form-actions">
            <button type="submit" name="btnthem" class="btn-submit">
                Thêm Tuyến Đường
            </button>
        </div>

    </form>

</div>

</body>
</html>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>