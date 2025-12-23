<?php
include_once '../../config/database.php';
require_once '../../includes/header.php';


if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Kiểm tra xem lịch trình có tồn tại không
    $sql_check = "SELECT * FROM lich_trinh WHERE id = ?";
    $stmt_check = $pdo->prepare($sql_check);
    $stmt_check->execute([$id]);
    
    if ($stmt_check->rowCount() > 0) {
        // Xóa lịch trình
        $sql_delete = "DELETE FROM lich_trinh WHERE id = ?";
        $stmt_delete = $pdo->prepare($sql_delete);
        
        if ($stmt_delete->execute([$id])) {
            echo "<script>alert('Xóa lịch trình thành công!'); window.location='index.php';</script>";
        } else {
            echo "<script>alert('Xóa thất bại! Vui lòng thử lại.'); window.location='index.php';</script>";
        }
    } else {
        echo "<script>alert('Lịch trình không tồn tại!'); window.location='index.php';</script>";
    }
} else {
    header('location:index.php');
}
?>