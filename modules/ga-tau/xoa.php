<?php
require_once __DIR__ . '/../../bootstrap.php';
requireAdmin();
$conn = $db->getConnection();
if (isset($_GET['ma_ga'])) {
    $ma_ga_xoa = $_GET['ma_ga'];
    $sql = "DELETE FROM ga_tau WHERE ma_ga = ?";
    try {
        $stmt = $conn->prepare($sql);
        $stmt->execute([$ma_ga_xoa]);
        echo "<script>
                alert('Đã xóa ga tàu thành công!');
                window.location.href = 'index.php';
              </script>";
        exit();

    } catch (PDOException $e) {
        echo "<script>
                alert('Lỗi! Không thể xóa ga này (có thể do ràng buộc dữ liệu).');
                window.location.href = 'index.php';
              </script>";
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}
?>