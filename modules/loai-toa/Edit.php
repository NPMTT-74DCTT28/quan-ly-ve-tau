<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../includes/header.php';

requireLogin();
requireAdmin();

$conn = $db->getConnection();

$id = $ten_loai = $he_so_gia = '';
$error_message = '';

// LẤY THÔNG TIN CŨ
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql_select = "SELECT * FROM loai_toa WHERE id = ?";
    $stmt = $conn->prepare($sql_select);
    $stmt->execute([$id]);
    $row = $stmt->fetch();

    if ($row) {
        $ten_loai = $row['ten_loai'];
        $he_so_gia = $row['he_so_gia'];
    } else {
        echo "<script>alert('Không tìm thấy thông tin loại toa!'); window.location='index.php';</script>";
        exit;
    }
} else {
    header('location:index.php');
    exit;
}

// XỬ LÝ UPDATE
if (isset($_POST['btnEdit'])) {
    $id = $_POST['id'];
    $ten_loai = trim($_POST['ten_loai']);
    $he_so_gia = trim($_POST['he_so_gia']);

    if (empty($ten_loai) || empty($he_so_gia)) {
        $error_message = "Vui lòng nhập đầy đủ tên loại và hệ số giá!";
    } else {
        try {
            $sql_check = "SELECT id FROM loai_toa WHERE ten_loai = ? AND id <> ?";
            $stmt_check = $conn->prepare($sql_check);
            $stmt_check->execute([$ten_loai, $id]);

            if ($stmt_check->rowCount() > 0) {
                $error_message = "Tên loại '$ten_loai' đã tồn tại! Vui lòng chọn tên khác.";
            } else {
                $sql_update = "UPDATE loai_toa SET ten_loai = ?, he_so_gia = ? WHERE id = ?";
                $stmt_update = $conn->prepare($sql_update);

                if ($stmt_update->execute([$ten_loai, $he_so_gia, $id])) {
                    echo "<script>alert('Cập nhật thông tin thành công!'); window.location='index.php';</script>";
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
    <h1>CẬP NHẬT LOẠI TOA</h1>

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
                    <label style="font-weight: 600; color: #34495e; display: block; margin-bottom: 5px;">Tên Loại (*):</label>
                    <input type="text" class="form-control" name="ten_loai"
                        value="<?php echo htmlspecialchars($ten_loai); ?>" required
                        style="width: 100%; padding: 8px 12px; border: 1px solid #ced4da; border-radius: 4px;">
                </div>
            </div>

            <div class="col-md-6" style="flex: 0 0 50%; max-width: 50%; padding-right: 15px; padding-left: 15px; box-sizing: border-box;">
                <div class="form-group mb-20">
                    <label style="font-weight: 600; color: #34495e; display: block; margin-bottom: 5px;">Hệ Số Giá (*):</label>
                    <input type="number" step="0.01" class="form-control" name="he_so_gia"
                        value="<?php echo htmlspecialchars($he_so_gia); ?>" required
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