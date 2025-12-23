<?php
require_once '../../config/database.php';

if (isset($_GET['ma_ga'])) {
    $ma_ga = $_GET['ma_ga'];

    $sql_xoa = "DELETE FROM ga_tau WHERE ma_ga = '$ma_ga'";

    try {
        $pdo->query($sql_xoa);
        echo "<script>
                alert('Đã xóa ga thành công!');
                window.location.href = 'index.php';
              </script>";
    } catch (PDOException $e) {
        echo "<script>
                alert('Xóa ga thất bại!');
                window.location.href = 'index.php';
              </script>";
    }
} else {
    header('Location: index.php');
    exit();
}
?>