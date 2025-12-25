<?php
require_once __DIR__ . '/../../bootstrap.php';

requireAdmin();

$conn = $db->getConnection();

if (isset($_GET['ma_tuyen'])) {
    $ma_tuyen_xoa = $_GET['ma_tuyen'];
    $sql = "DELETE FROM tuyen_duong WHERE ma_tuyen = ?";

    try {
        $stmt = $conn->prepare($sql);
        $stmt->execute([$ma_tuyen_xoa]);
        echo "<script>
                alert('Đã xóa tuyến đường thành công!');
                window.location.href = 'index.php';
              </script>";
        exit();

    } catch (PDOException $e) {
        echo "<script>
                alert('Không thể xóa tuyến đường này (đang được sử dụng).');
                window.location.href = 'index.php';
              </script>";
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}
?>
