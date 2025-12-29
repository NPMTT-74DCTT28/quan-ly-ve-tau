<?php
require_once __DIR__ . '/../../bootstrap.php';

if (!isset($_SESSION['user'])) {
    header("Location: " . BASE_URL . "modules/auth/dang_nhap.php");
    exit();
}

$error = '';
$conn = $db->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mat_khau_cu = $_POST['mat_khau_cu'] ?? '';
    $mat_khau_moi = $_POST['mat_khau_moi'] ?? '';
    $xac_nhan_mat_khau = $_POST['xac_nhan_mat_khau'] ?? '';
    $user_id = $_SESSION['user']['id'];

    if (empty($mat_khau_cu) || empty($mat_khau_moi) || empty($xac_nhan_mat_khau)) {
        $error = "Vui lòng điền đầy đủ thông tin.";
    } elseif ($mat_khau_moi !== $xac_nhan_mat_khau) {
        $error = "Mật khẩu mới và xác nhận mật khẩu không khớp.";
    } elseif (strlen($mat_khau_moi) < 6) {
        $error = "Mật khẩu mới phải có ít nhất 6 ký tự.";
    } else {
        $stmt_check = $conn->prepare("SELECT mat_khau FROM nhan_vien WHERE id = ?");
        $stmt_check->execute([$user_id]);
        $user_hien_tai = $stmt_check->fetch(PDO::FETCH_ASSOC);

        if (!$user_hien_tai || !password_verify($mat_khau_cu, $user_hien_tai['mat_khau'])) {
            $error = "Mật khẩu cũ không chính xác.";
        } else {
            $mat_khau_bam = password_hash($mat_khau_moi, PASSWORD_BCRYPT);
            $stmt = $conn->prepare("UPDATE nhan_vien SET mat_khau = ? WHERE id = ?");

            if ($stmt->execute([$mat_khau_bam, $user_id])) {
                session_destroy();
                echo "<script> alert('Đổi mật khẩu thành công, vui lòng đăng nhập lại!'); 
                window.location.href = '" . BASE_URL . "modules/auth/dang_nhap.php';</script>";
                exit();
            } else {
                $error = "Lỗi hệ thống: Không thể cập nhật mật khẩu.";
            }
        }
    }
}

require_once __DIR__ . '/../../includes/header.php';
?>

<style>
    .cpw-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .auth-container {
        max-width: 500px;
        margin: 0 auto;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        display: block;
        margin-bottom: 8px;
        color: #333;
        font-weight: 500;
    }

    .form-control {
        width: 100%;
        padding: 10px 15px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 14px;
        transition: border-color 0.3s;
    }

    .form-control:focus {
        border-color: #4a6fa5;
        outline: none;
        box-shadow: 0 0 0 3px rgba(74, 111, 165, 0.1);
    }

    .btn-submit {
        background-color: #4a6fa5;
        color: white;
        border: none;
        padding: 12px 24px;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
        width: 100%;
        transition: all 0.3s ease;
        font-size: 15px;
    }

    .btn-submit:hover {
        background-color: #34495e;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    .alert {
        padding: 12px;
        border-radius: 6px;
        margin-bottom: 20px;
        font-size: 14px;
    }

    .alert-danger {
        background-color: #ffeaea;
        color: #dc3545;
        border: 1px solid #f5c6cb;
    }
</style>

<h1 class="cpw-header">Đổi mật khẩu</h1>

<div class="auth-container">
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" action="" onsubmit="return confirm('Bạn có chắc chắn muốn đổi mật khẩu không?');">

        <div class="form-group">
            <label for="mat_khau_cu" class="form-label">Mật khẩu hiện tại</label>
            <input type="password" id="mat_khau_cu" name="mat_khau_cu" class="form-control" required placeholder="Nhập mật khẩu cũ">
        </div>

        <div class="form-group">
            <label for="mat_khau_moi" class="form-label">Mật khẩu mới</label>
            <input type="password" id="mat_khau_moi" name="mat_khau_moi" class="form-control" required placeholder="Nhập mật khẩu mới">
        </div>

        <div class="form-group">
            <label for="xac_nhan_mat_khau" class="form-label">Xác nhận mật khẩu mới</label>
            <input type="password" id="xac_nhan_mat_khau" name="xac_nhan_mat_khau" class="form-control" required placeholder="Nhập lại mật khẩu mới">
        </div>

        <div class="form-group" style="margin-top: 30px;">
            <button type="submit" class="btn-submit">Xác nhận</button>
        </div>

        <div style="text-align: center; margin-top: 15px;">
            <a href="<?php echo BASE_URL ?>" style="color: #666; text-decoration: none; font-size: 14px;">Quay lại trang chủ</a>
        </div>
    </form>
</div>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>