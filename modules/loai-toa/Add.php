<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../includes/header.php';

if (!isset($_SESSION['user'])) {
    header("Location: " . BASE_URL . "modules/auth/dang_nhap.php");
    exit();
}

requireAdmin();

$conn = $db->getConnection();

$ten_loai = $he_so_gia = '';
$show_success = false;
$show_error = false;
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['btnAdd'])) {
        $ten_loai = trim($_POST['ten_loai']);
        $he_so_gia = trim($_POST['he_so_gia']);

        if (empty($ten_loai) || empty($he_so_gia)) {
            $error_message = "Vui lòng điền đầy đủ thông tin tên loại và hệ số giá!";
            $show_error = true;
        } else {
            $sql_check = "SELECT * FROM loai_toa WHERE ten_loai = ?";
            $stmt_check = $conn->prepare($sql_check);
            $stmt_check->execute([$ten_loai]);

            if ($stmt_check->rowCount() > 0) {
                $error_message = "Tên loại này đã tồn tại!";
                $show_error = true;
            } else {
                $sql_insert = "INSERT INTO loai_toa (ten_loai, he_so_gia) VALUES (?, ?)";
                $stmt_insert = $conn->prepare($sql_insert);

                if ($stmt_insert->execute([$ten_loai, $he_so_gia])) {
                    $success_message = "Thêm mới loại toa thành công!";
                    $show_success = true;
                    $ten_loai = $he_so_gia = '';
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
    <h1>THÊM LOẠI TOA MỚI</h1>

    <?php if ($show_error): ?>
        <div class="alert alert-danger" style="color: #721c24; background-color: #f8d7da; border-color: #f5c6cb; padding: 15px; margin-bottom: 20px; border-radius: 4px;">
            <?= htmlspecialchars($error_message) ?>
        </div>
    <?php endif; ?>

    <?php if ($show_success): ?>
        <div class="alert alert-success" style="color: #155724; background-color: #d4edda; border-color: #c3e6cb; padding: 15px; margin-bottom: 20px; border-radius: 4px;">
            <?= htmlspecialchars($success_message) ?>
        </div>
    <?php endif; ?>

    <form method="post" action="">
        <div class="row" style="display: flex; flex-wrap: wrap; margin-right: -15px; margin-left: -15px;">
            <div class="col-md-6" style="flex: 0 0 50%; max-width: 50%; padding-right: 15px; padding-left: 15px; box-sizing: border-box;">
                <div class="form-group mb-20">
                    <label style="font-weight: 600; color: #34495e; display: block; margin-bottom: 5px;">Tên Loại (*):</label>
                    <input type="text" class="form-control" name="ten_loai"
                        value="<?php echo htmlspecialchars($ten_loai); ?>"
                        required placeholder="VD: Ngồi mềm điều hòa..."
                        style="width: 100%; padding: 8px 12px; border: 1px solid #ced4da; border-radius: 4px;">
                </div>
            </div>

            <div class="col-md-6" style="flex: 0 0 50%; max-width: 50%; padding-right: 15px; padding-left: 15px; box-sizing: border-box;">
                <div class="form-group mb-20">
                    <label style="font-weight: 600; color: #34495e; display: block; margin-bottom: 5px;">Hệ Số Giá (*):</label>
                    <input type="number" step="0.01" class="form-control" name="he_so_gia"
                        value="<?php echo htmlspecialchars($he_so_gia); ?>"
                        required placeholder="VD: 1.25"
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