<?php
require_once __DIR__ . '/../../bootstrap.php';

requireLogin();
requireAdmin();

require_once __DIR__ . '/../../includes/header.php';

$conn = $db->getConnection();
$error_message = '';
$success_message = '';

// 1. Lấy dữ liệu cũ
$current_tuyen = null;
if (isset($_GET['ma_tuyen'])) {
    $ma_edit = $_GET['ma_tuyen'];
    $stmt = $conn->prepare("SELECT * FROM tuyen_duong WHERE ma_tuyen = ?");
    $stmt->execute([$ma_edit]);
    $current_tuyen = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$current_tuyen) {
        header("Location: index.php");
        exit();
    }
}

// Lấy danh sách ga
$sqlGa = "SELECT id, ten_ga FROM ga_tau";
$stmtGa = $conn->query($sqlGa);
$dsGa = $stmtGa->fetchAll(PDO::FETCH_ASSOC);

// 2. Xử lý sửa
if (isset($_POST['btnsua'])) {
    $ma    = $_POST['txtmatuyen']; // Readonly
    $ten   = trim($_POST['txttentuyen']);
    $gadi  = (int)$_POST['txtgadi'];
    $gaden = (int)$_POST['txtgaden'];
    $kc    = trim($_POST['txtkhoangcach']);
    $gia   = trim($_POST['txtgiacb']);

    if (empty($ten) || empty($kc) || empty($gia)) {
        $error_message = "Vui lòng nhập đầy đủ thông tin!";
    } elseif ($gadi === $gaden) {
        $error_message = "Ga đi và ga đến không được trùng nhau!";
    } else {
        // Kiểm tra trùng tuyến đường (trừ chính nó)
        $checkSql = "SELECT COUNT(*) FROM tuyen_duong
                     WHERE id_ga_di = ? AND id_ga_den = ?
                     AND ma_tuyen <> ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->execute([$gadi, $gaden, $ma]);

        if ($checkStmt->fetchColumn() > 0) {
            $error_message = "Tuyến đường với cặp Ga đi - Ga đến này đã tồn tại!";
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

                $success_message = "Cập nhật thành công!";
                // Cập nhật lại biến hiển thị
                $current_tuyen['ten_tuyen'] = $ten;
                $current_tuyen['id_ga_di'] = $gadi;
                $current_tuyen['id_ga_den'] = $gaden;
                $current_tuyen['khoang_cach_km'] = $kc;
                $current_tuyen['gia_co_ban'] = $gia;
            } catch (PDOException $e) {
                $error_message = "Lỗi cập nhật: " . $e->getMessage();
            }
        }
    }
}
?>

<div class="container" style="padding: 20px; max-width: 800px; margin: 0 auto;">
    <h2>Sửa Tuyến Đường</h2>

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
            <label>Mã Tuyến (Không thể sửa)</label>
            <input type="text" name="txtmatuyen" class="form-control"
                value="<?= htmlspecialchars($current_tuyen['ma_tuyen']) ?>" readonly
                style="width: 100%; padding: 8px; margin-top: 5px; background-color: #e9ecef;">
        </div>

        <div class="form-group" style="margin-bottom: 15px;">
            <label>Tên Tuyến</label>
            <input type="text" name="txttentuyen" class="form-control"
                value="<?= htmlspecialchars($current_tuyen['ten_tuyen']) ?>" required
                style="width: 100%; padding: 8px; margin-top: 5px;">
        </div>

        <div class="form-group" style="margin-bottom: 15px;">
            <label>Ga đi</label>
            <select name="txtgadi" class="form-control" required style="width: 100%; padding: 8px; margin-top: 5px;">
                <?php foreach ($dsGa as $ga): ?>
                    <option value="<?= $ga['id'] ?>"
                        <?= ($ga['id'] == $current_tuyen['id_ga_di']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($ga['ten_ga']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group" style="margin-bottom: 15px;">
            <label>Ga đến</label>
            <select name="txtgaden" class="form-control" required style="width: 100%; padding: 8px; margin-top: 5px;">
                <?php foreach ($dsGa as $ga): ?>
                    <option value="<?= $ga['id'] ?>"
                        <?= ($ga['id'] == $current_tuyen['id_ga_den']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($ga['ten_ga']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group" style="margin-bottom: 15px;">
            <label>Khoảng cách (km)</label>
            <input type="number" step="0.01" name="txtkhoangcach" class="form-control"
                value="<?= htmlspecialchars($current_tuyen['khoang_cach_km']) ?>" required
                style="width: 100%; padding: 8px; margin-top: 5px;">
        </div>

        <div class="form-group" style="margin-bottom: 20px;">
            <label>Giá cơ bản</label>
            <input type="number" step="1000" name="txtgiacb" class="form-control"
                value="<?= htmlspecialchars($current_tuyen['gia_co_ban']) ?>" required
                style="width: 100%; padding: 8px; margin-top: 5px;">
        </div>

        <button type="submit" name="btnsua" style="background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;">Lưu thay đổi</button>
        <a href="index.php" style="margin-left: 10px; color: #666; text-decoration: none;">Quay lại</a>
    </form>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>