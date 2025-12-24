<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../includes/header.php';

if (!isset($_SESSION['user'])) {
    header("Location: " . BASE_URL . "modules/auth/dang_nhap.php");
    exit();
}

requireAdmin();

$conn = $db->getConnection();

$id = $ma_tau = $ten_tau = '';
$error_message = '';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql_select = "SELECT * FROM tau WHERE id = ?";
    $stmt = $conn->prepare($sql_select);
    $stmt->execute([$id]);
    $row = $stmt->fetch();

    if ($row) {
        $ma_tau = $row['ma_tau'];
        $ten_tau = $row['ten_tau'];
    } else {
        echo "<script>alert('Không tìm thấy thông tin tàu!'); window.location='index.php';</script>";
        exit;
    }
} else {
    header('location:index.php');
    exit;
}

if (isset($_POST['btnEdit'])) {
    $id = $_POST['id'];
    $ma_tau = trim($_POST['ma_tau']);
    $ten_tau = trim($_POST['ten_tau']);

    if (empty($ma_tau) || empty($ten_tau)) {
        $error_message = "Vui lòng nhập đầy đủ mã tàu và tên tàu!";
    } else {
        try {
            $sql_check = "SELECT id FROM tau WHERE ma_tau = ? AND id <> ?";
            $stmt_check = $conn->prepare($sql_check);
            $stmt_check->execute([$ma_tau, $id]);

            if ($stmt_check->rowCount() > 0) {
                $error_message = "Mã tàu '$ma_tau' đã tồn tại ở một tàu khác! Vui lòng chọn mã khác.";
            } else {
                $sql_update = "UPDATE tau SET ma_tau = ?, ten_tau = ? WHERE id = ?";
                $stmt_update = $conn->prepare($sql_update);

                if ($stmt_update->execute([$ma_tau, $ten_tau, $id])) {
                    echo "<script>alert('Cập nhật thông tin tàu thành công!'); window.location='index.php';</script>";
                } else {
                    $error_message = "Cập nhật thất bại! Vui lòng thử lại.";
                }
            }
        } catch (PDOException $e) {
            $error_message = "Lỗi hệ thống: " . $e->getMessage();
        }
    }
}
?>

<div class="main-content">
    <h1>CẬP NHẬT THÔNG TIN TÀU</h1>

    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger" style="color: #721c24; background-color: #f8d7da; border-color: #f5c6cb; padding: 15px; margin-bottom: 20px; border-radius: 4px;">
            <?= htmlspecialchars($error_message) ?>
        </div>
    <?php endif; ?>

    <form method="post" action="">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">

        <div class="row" style="display: flex; flex-wrap: wrap; margin-right: -15px; margin-left: -15px;">
            <div class="col-md-6" style="flex: 0 0 50%; max-width: 50%; padding-right: 15px; padding-left: 15px; box-sizing: border-box;">
                <div class="form-group mb-20">
                    <label style="font-weight: 600; color: #34495e; display: block; margin-bottom: 5px;">Mã Tàu (*):</label>
                    <input type="text" class="form-control" name="ma_tau"
                        value="<?php echo htmlspecialchars($ma_tau); ?>" required
                        style="width: 100%; padding: 8px 12px; border: 1px solid #ced4da; border-radius: 4px;">
                </div>
            </div>

            <div class="col-md-6" style="flex: 0 0 50%; max-width: 50%; padding-right: 15px; padding-left: 15px; box-sizing: border-box;">
                <div class="form-group mb-20">
                    <label style="font-weight: 600; color: #34495e; display: block; margin-bottom: 5px;">Tên Tàu (*):</label>
                    <input type="text" class="form-control" name="ten_tau"
                        value="<?php echo htmlspecialchars($ten_tau); ?>" required
                        style="width: 100%; padding: 8px 12px; border: 1px solid #ced4da; border-radius: 4px;">
                </div>
            </div>
        </div>

        <div style="text-align: center; margin-top: 20px;">
            <button type="submit" class="btn btn-primary" name="btnEdit" style="background: #007bff; color: white; padding: 10px 25px; border: none; border-radius: 4px; cursor: pointer; font-weight: 600;">
                <i class="bi bi-save"></i> Lưu thay đổi
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