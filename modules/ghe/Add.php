<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../includes/header.php';

$conn = $db->getConnection();

$toa_list = $conn->query("SELECT id, ma_toa FROM toa_tau")->fetchAll();

$error_message = '';
$success_message = '';
$so_ghe = '';
$id_toa_tau = '';

if (isset($_POST['btnAdd'])) {
    $so_ghe = trim($_POST['so_ghe']);
    $id_toa_tau = $_POST['id_toa_tau'];

    if (empty($so_ghe) || empty($id_toa_tau)) {
        $error_message = "Vui lòng điền đầy đủ thông tin số ghế và toa tàu!";
    } else {
        // Logic check trùng: Một toa tàu không thể có 2 ghế cùng số
        $sql_check = "SELECT * FROM ghe WHERE so_ghe = ? AND id_toa_tau = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->execute([$so_ghe, $id_toa_tau]);

        if ($stmt_check->rowCount() > 0) {
            $error_message = "Số ghế '$so_ghe' đã tồn tại trong toa này!";
        } else {
            // Thêm mới vào đúng bảng 'ghe'
            $sql_insert = "INSERT INTO ghe (so_ghe, id_toa_tau) VALUES (?, ?)";
            $stmt_insert = $conn->prepare($sql_insert);

            if ($stmt_insert->execute([$so_ghe, $id_toa_tau])) {
                $success_message = "Thêm mới ghế thành công!";
                $so_ghe = $id_toa_tau = ''; // Reset form
            } else {
                $error_message = "Thêm thông tin thất bại! Vui lòng thử lại.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Thêm Ghế Mới</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container { max-width: 600px; margin-top: 50px; }
        .form-container { background-color: #7ca3c9; padding: 40px; border-radius: 15px; }
    </style>
</head>

<body>
    <div class="container">
        <div class="form-container shadow">
            <h2 class="text-center mb-4 text-white">THÊM GHẾ MỚI</h2>

            <?php if ($error_message): ?>
                <div class="alert alert-danger"><?= $error_message ?></div>
            <?php endif; ?>

            <?php if ($success_message): ?>
                <div class="alert alert-success"><?= $success_message ?></div>
            <?php endif; ?>

            <form method="post">
                <div class="mb-3">
                    <label class="form-label fw-bold text-white">Chọn Toa Tàu:</label>
                    <select name="id_toa_tau" class="form-select" required>
                        <option value="">-- Chọn Toa --</option>
                        <?php foreach ($toa_list as $toa): ?>
                            <option value="<?= $toa['id'] ?>" <?= ($id_toa_tau == $toa['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($toa['ma_toa']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold text-white">Số Ghế:</label>
                    <input type="text" name="so_ghe" class="form-control" 
                           value="<?= htmlspecialchars($so_ghe) ?>" 
                           placeholder="VD: A01, 12..." required>
                </div>
                <div class="text-center">
                    <button type="submit" name="btnAdd" class="btn btn-success px-4">Lưu lại</button>
                    <a href="index.php" class="btn btn-secondary px-4">Quay lại</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>