<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../includes/header.php';

requireLogin();
requireAdmin();

$conn = $db->getConnection();

$ma_tau = $ten_tau = '';
$show_success = false;
$show_error = false;
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['btnAdd'])) {
        $ma_tau = trim($_POST['ma_tau']);
        $ten_tau = trim($_POST['ten_tau']);

        if (empty($ma_tau) || empty($ten_tau)) {
            $error_message = "Vui lòng điền đầy đủ thông tin mã tàu và tên tàu!";
            $show_error = true;
        } else {
            $sql_check = "SELECT * FROM tau WHERE ma_tau = ?";
            $stmt_check = $conn->prepare($sql_check);
            $stmt_check->execute([$ma_tau]);

            if ($stmt_check->rowCount() > 0) {
                $error_message = "Mã tàu này đã tồn tại! Vui lòng nhập mã khác.";
                $show_error = true;
            } else {
                $sql_insert = "INSERT INTO tau (ma_tau, ten_tau) VALUES (?, ?)";
                $stmt_insert = $conn->prepare($sql_insert);

                if ($stmt_insert->execute([$ma_tau, $ten_tau])) {
                    $success_message = "Thêm mới tàu thành công!";
                    $show_success = true;
                    $ma_tau = $ten_tau = '';
                } else {
                    $error_message = "Thêm thông tin thất bại! Vui lòng thử lại.";
                    $show_error = true;
                }
            }
        }
    }
}
?>

<div class="main-content">
    <h1>THÊM TÀU MỚI</h1>

    <?php if ($show_success): ?>
        <div class="alert alert-success" style="color: #155724; background-color: #d4edda; border-color: #c3e6cb; padding: 15px; margin-bottom: 20px; border-radius: 4px;">
            <?= htmlspecialchars($success_message) ?>
        </div>
    <?php endif; ?>

    <?php if ($show_error): ?>
        <div class="alert alert-danger" style="color: #721c24; background-color: #f8d7da; border-color: #f5c6cb; padding: 15px; margin-bottom: 20px; border-radius: 4px;">
            <?= htmlspecialchars($error_message) ?>
        </div>
    <?php endif; ?>

    <form method="post" action="">
        <div class="row" style="display: flex; flex-wrap: wrap; margin-right: -15px; margin-left: -15px;">
            <div class="col-md-6" style="flex: 0 0 50%; max-width: 50%; padding-right: 15px; padding-left: 15px; box-sizing: border-box;">
                <div class="form-group mb-20">
                    <label style="font-weight: 600; color: #34495e; display: block; margin-bottom: 5px;">Mã Tàu (*):</label>
                    <input type="text" class="form-control" name="ma_tau"
                        value="<?php echo htmlspecialchars($ma_tau); ?>"
                        required placeholder="VD: SE1, TN1..."
                        style="width: 100%; padding: 8px 12px; border: 1px solid #ced4da; border-radius: 4px;">
                </div>
            </div>

            <div class="col-md-6" style="flex: 0 0 50%; max-width: 50%; padding-right: 15px; padding-left: 15px; box-sizing: border-box;">
                <div class="form-group mb-20">
                    <label style="font-weight: 600; color: #34495e; display: block; margin-bottom: 5px;">Tên Tàu (*):</label>
                    <input type="text" class="form-control" name="ten_tau"
                        value="<?php echo htmlspecialchars($ten_tau); ?>"
                        required placeholder="VD: Tàu Thống Nhất..."
                        style="width: 100%; padding: 8px 12px; border: 1px solid #ced4da; border-radius: 4px;">
                </div>
            </div>
        </div>

        <div style="text-align: center; margin-top: 20px;">
            <button type="submit" class="btn btn-success" name="btnAdd" style="background: #28a745; color: white; padding: 10px 25px; border: none; border-radius: 4px; cursor: pointer; font-weight: 600;">
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