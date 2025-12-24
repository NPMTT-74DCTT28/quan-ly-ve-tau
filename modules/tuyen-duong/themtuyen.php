<?php
require_once __DIR__ . '/../../bootstrap.php';

if (!isset($_SESSION['user'])) {
    header("Location: " . BASE_URL . "modules/auth/dang_nhap.php");
    exit();
}

requireAdmin();

require_once __DIR__ . '/../../includes/header.php';

$conn = $db->getConnection();
$error_message = '';
$success_message = '';

// Lấy danh sách ga để đổ vào select box
$sqlGa = "SELECT id, ten_ga FROM ga_tau";
$stmtGa = $conn->query($sqlGa);
$dsGa = $stmtGa->fetchAll(PDO::FETCH_ASSOC);

// Biến giữ giá trị cũ
$ma = '';
$ten = '';
$gadi = '';
$gaden = '';
$kc = '';
$gia = '';

if (isset($_POST['btnthem'])) {
    $ma    = trim($_POST['txtmatuyen']);
    $ten   = trim($_POST['txttentuyen']);
    $gadi  = (int)$_POST['txtgadi'];
    $gaden = (int)$_POST['txtgaden'];
    $kc    = trim($_POST['txtkhoangcach']);
    $gia   = trim($_POST['txtgiacb']);

    if (empty($ma) || empty($ten) || empty($gadi) || empty($gaden) || empty($kc) || empty($gia)) {
        $error_message = "Vui lòng nhập đầy đủ thông tin!";
    } elseif ($gadi === $gaden) {
        $error_message = "Ga đi và ga đến không được trùng nhau!";
    } else {
        // Kiểm tra mã tuyến trùng
        $stmtCheck = $conn->prepare("SELECT COUNT(*) FROM tuyen_duong WHERE ma_tuyen = ?");
        $stmtCheck->execute([$ma]);
        if ($stmtCheck->fetchColumn() > 0) {
            $error_message = "Mã tuyến '$ma' đã tồn tại!";
        } else {
            $sql = "INSERT INTO tuyen_duong
                    (ma_tuyen, ten_tuyen, id_ga_di, id_ga_den, Khoang_cach_km, gia_co_ban)
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);

            try {
                if ($stmt->execute([$ma, $ten, $gadi, $gaden, $kc, $gia])) {
                    $success_message = "Thêm tuyến đường thành công!";
                    // Reset form
                    $ma = $ten = $gadi = $gaden = $kc = $gia = '';
                } else {
                    $error_message = "Thêm thất bại, vui lòng thử lại.";
                }
            } catch (PDOException $e) {
                $error_message = "Lỗi hệ thống: " . $e->getMessage();
            }
        }
    }
}
?>

<div class="container" style="padding: 20px; max-width: 800px; margin: 0 auto;">
    <h2>Thêm Tuyến Đường Mới</h2>

    <?php if ($error_message): ?>
        <div style="color: red; background: #f8d7da; padding: 10px; margin-bottom: 15px; border-radius: 4px;">
            <?= $error_message ?>
        </div>
    <?php endif; ?>

    <?php if ($success_message): ?>
        <div style="color: #155724; background-color: #d4edda; padding: 10px; margin-bottom: 15px; border-radius: 4px;">
            <?= $success_message ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="" style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">

        <div class="form-group" style="margin-bottom: 15px;">
            <label>Mã Tuyến (*)</label>
            <input type="text" name="txtmatuyen" class="form-control"
                value="<?= htmlspecialchars($ma) ?>" required
                style="width: 100%; padding: 8px; margin-top: 5px;">
        </div>

        <div class="form-group" style="margin-bottom: 15px;">
            <label>Tên Tuyến (*)</label>
            <input type="text" name="txttentuyen" class="form-control"
                value="<?= htmlspecialchars($ten) ?>" required
                style="width: 100%; padding: 8px; margin-top: 5px;">
        </div>

        <div class="form-group" style="margin-bottom: 15px;">
            <label>Ga đi (*)</label>
            <select name="txtgadi" class="form-control" required style="width: 100%; padding: 8px; margin-top: 5px;">
                <option value="">-- Chọn ga đi --</option>
                <?php foreach ($dsGa as $ga): ?>
                    <option value="<?= $ga['id'] ?>" <?= ($gadi == $ga['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($ga['ten_ga']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group" style="margin-bottom: 15px;">
            <label>Ga đến (*)</label>
            <select name="txtgaden" class="form-control" required style="width: 100%; padding: 8px; margin-top: 5px;">
                <option value="">-- Chọn ga đến --</option>
                <?php foreach ($dsGa as $ga): ?>
                    <option value="<?= $ga['id'] ?>" <?= ($gaden == $ga['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($ga['ten_ga']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group" style="margin-bottom: 15px;">
            <label>Khoảng cách (km) (*)</label>
            <input type="number" step="0.01" name="txtkhoangcach" class="form-control"
                value="<?= htmlspecialchars($kc) ?>" required
                style="width: 100%; padding: 8px; margin-top: 5px;">
        </div>

        <div class="form-group" style="margin-bottom: 20px;">
            <label>Giá cơ bản (VNĐ) (*)</label>
            <input type="number" step="1000" name="txtgiacb" class="form-control"
                value="<?= htmlspecialchars($gia) ?>" required
                style="width: 100%; padding: 8px; margin-top: 5px;">
        </div>

        <button type="submit" name="btnthem" style="background: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;">Thêm mới</button>
        <a href="index.php" style="margin-left: 10px; color: #666; text-decoration: none;">Quay lại</a>
    </form>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>