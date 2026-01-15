<?php
require_once __DIR__ . '/../../bootstrap.php';

requireLogin();
requireAdmin();

$conn = $db->getConnection();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = (int) $_GET['id'];

// Thực hiện xóa
// Lưu ý: Do có ON DELETE CASCADE trong SQL cho bảng toa_tau (liên quan đến ghế), 
// nên nếu xóa toa, các ghế trong toa đó cũng có thể bị xóa theo (tùy config foreign key của bảng ghế).
// Ở đây ta chỉ xóa toa.

$stmt = $conn->prepare("DELETE FROM toa_tau WHERE id = ?");
$stmt->execute([$id]);

if ($stmt->rowCount() > 0) {
    echo "<script>
        alert('Xóa toa tàu thành công!');
        window.location.href = 'index.php';
    </script>";
} else {
    echo "<script>
        alert('Toa tàu không tồn tại hoặc lỗi!');
        window.location.href = 'index.php';
    </script>";
}
?>