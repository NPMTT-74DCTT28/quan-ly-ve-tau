<?php
require_once __DIR__ . '/../../bootstrap.php';

requireLogin();
requireAdmin();

$conn = $db->getConnection();

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    try {
        $stmt = $conn->prepare("DELETE FROM ghe WHERE id = ?");
        if ($stmt->execute([$id])) {
            echo "<script>alert('Xóa ghế thành công!'); window.location='index.php';</script>";
        }
    } catch (PDOException $e) {
        // Trường hợp ghế đã được bán vé (có liên kết khóa ngoại ở bảng ve_tau)
        echo "<script>alert('Không thể xóa ghế này vì đã có dữ liệu vé liên quan!'); window.location='index.php';</script>";
    }
} else {
    header('Location: index.php');
}
?>