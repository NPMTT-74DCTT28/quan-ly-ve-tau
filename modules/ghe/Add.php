<?php
require_once __DIR__ . '/../../bootstrap.php';

requireLogin();
requireAdmin();

$conn = $db->getConnection();
$toa_list = $conn->query("SELECT toa_tau.id, toa_tau.ma_toa, tau.ten_tau 
                          FROM toa_tau 
                          INNER JOIN tau ON toa_tau.id_tau = tau.id")->fetchAll();

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
        $sql_check = "SELECT * FROM ghe WHERE so_ghe = ? AND id_toa_tau = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->execute([$so_ghe, $id_toa_tau]);

        if ($stmt_check->rowCount() > 0) {
            $error_message = "Số ghế '$so_ghe' đã tồn tại trong toa này!";
        } else {
            $sql_insert = "INSERT INTO ghe (so_ghe, id_toa_tau) VALUES (?, ?)";
            $stmt_insert = $conn->prepare($sql_insert);

            if ($stmt_insert->execute([$so_ghe, $id_toa_tau])) {
                $success_message = "Thêm mới ghế thành công!";
                $show_success = true;
                $so_ghe = '';
            } else {
                $error_message = "Thêm thông tin thất bại! Vui lòng thử lại.";
            }
        }
    }
}

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="container" style="padding: 20px; max-width: 800px; margin: 0 auto;">
    <h2>Thêm Ghế Mới</h2>

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
            <label>Chọn Toa Tàu (*)</label>
            <select name="id_toa_tau" class="form-control" required style="width: 100%; padding: 8px; margin-top: 5px;">
                <option value="">-- Chọn Toa --</option>
                <?php foreach ($toa_list as $toa): ?>
                    <option value="<?= $toa['id'] ?>" <?= ($id_toa_tau == $toa['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($toa['ma_toa'] . " (" . $toa['ten_tau'] . ")") ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group" style="margin-bottom: 20px;">
            <label>Số Ghế (*)</label>
            <input type="text" name="so_ghe" class="form-control"
                value="<?= htmlspecialchars($so_ghe) ?>"
                placeholder="VD: A01, 12..." required
                style="width: 100%; padding: 8px; margin-top: 5px;">
        </div>

        <button type="submit" name="btnAdd" style="background: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;">Lưu lại</button>
        <a href="index.php" style="margin-left: 10px; color: #666; text-decoration: none;">Quay lại</a>
    </form>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>