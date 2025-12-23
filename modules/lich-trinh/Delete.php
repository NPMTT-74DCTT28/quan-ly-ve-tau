<?php
require_once __DIR__ . '/../../bootstrap.php';

$conn = $db->getConnection();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit;
}

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
