<?php
require_once __DIR__ . '/../../bootstrap.php';

requireLogin();
requireAdmin();

$id = $_GET['id'] ?? 0;

if ($id == $_SESSION['user']['id']) {
    echo "<script>alert('Bạn không thể xóa chính mình!'); window.location.href='index.php';</script>";
    exit();
}

try {
    $conn = $db->getConnection();
    $stmt = $conn->prepare("DELETE FROM nhan_vien WHERE id = ?");
    $stmt->execute([$id]);

    echo "<script>
        alert('Xóa thành công!');
        window.location.href = 'index.php';</script>";
    exit();
} catch (PDOException $e) {
    if ($e->getCode() == '23000') {
        echo "<script>
            alert('Không thể xóa nhân viên này vì họ đã có dữ liệu liên quan trong hệ thống (Vé tàu, Lịch trình...).');
            window.location.href = 'index.php';
        </script>";
    } else {
        echo "Lỗi hệ thống: " . $e->getMessage();
    }
}
