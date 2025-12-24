<?php
require_once __DIR__ . '/../../bootstrap.php';

if (!isset($_SESSION['user'])) {
    header("Location: " . BASE_URL . "modules/auth/dang_nhap.php");
    exit();
}

requireAdmin();

$conn = $db->getConnection();

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql_check = "SELECT * FROM tau WHERE id = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->execute([$id]);

    if ($stmt_check->rowCount() > 0) {
        try {
            $sql_delete = "DELETE FROM tau WHERE id = ?";
            $stmt_delete = $conn->prepare($sql_delete);

            if ($stmt_delete->execute([$id])) {
                echo "<script>alert('Xóa thông tin tàu thành công!'); window.location='index.php';</script>";
            } else {
                echo "<script>alert('Xóa thất bại! Vui lòng thử lại.'); window.location='index.php';</script>";
            }
        } catch (PDOException $e) {
            echo "<script>alert('Không thể xóa! Tàu này đang có dữ liệu liên quan ở các bảng khác.'); window.location='index.php';</script>";
        }
    } else {
        echo "<script>alert('Tàu không tồn tại!'); window.location='index.php';</script>";
    }
} else {
    header('location:index.php');
}
?>