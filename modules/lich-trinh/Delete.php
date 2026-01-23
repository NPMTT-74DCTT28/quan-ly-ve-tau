<?php
require_once __DIR__ . '/../../bootstrap.php';

requireLogin();
requireAdmin();

$conn = $db->getConnection();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit;
}

try {
    $id = (int) $_GET['id'];

$stmt = $conn->prepare("DELETE FROM lich_trinh WHERE id = ?");
$stmt->execute([$id]);

if ($stmt->rowCount() > 0) {
    echo "<script>
        alert('Xóa lịch trình thành công!');
        window.location.href = 'index.php';
    </script>";
} else {
    echo "<script>
        alert('Lịch trình không tồn tại hoặc đã bị xóa!');
        window.location.href = 'index.php';
    </script>";
}
} catch (PDOException $e) {
    if ($e->getCode() == '23000') {
        echo "<script>
            alert('Không thể xóa lich trinh này vì đã có dữ liệu liên quan trong hệ thống .');
            window.location.href = 'index.php';
        </script>";
    } else {
        echo "Lỗi hệ thống: " . $e->getMessage();
    }
}
