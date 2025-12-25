<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../includes/header.php';

if (!isset($_SESSION['user'])) {
    header("Location: " . BASE_URL . "modules/auth/dang_nhap.php");
    exit();
}

requireAdmin();

$conn = $db->getConnection();

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Kiểm tra xem vé tàu có tồn tại không
    $sql_check = "SELECT * FROM ve_tau WHERE id = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->execute([$id]);

    if ($stmt_check->rowCount() > 0) {
        $ve_tau = $stmt_check->fetch();

        // Kiểm tra trạng thái vé trước khi xóa
        // Nếu vé đã xác nhận hoặc hoàn thành, có thể không cho xóa
        if ($ve_tau['trang_thai'] == 'Đã xác nhận' || $ve_tau['trang_thai'] == 'Hoàn thành') {
            echo "<script>
                alert('Không thể xóa vé đang ở trạng thái \"' + '{$ve_tau['trang_thai']}' + '\"!');
                window.location.href = 'index.php';
            </script>";
        } else {
            // Xóa vé tàu
            $sql_delete = "DELETE FROM ve_tau WHERE id = ?";
            $stmt_delete = $conn->prepare($sql_delete);

            if ($stmt_delete->execute([$id])) {
                echo "<script>
                    alert('Xóa vé tàu thành công!');
                    window.location.href = 'index.php';
                </script>";
            } else {
                echo "<script>
                    alert('Xóa vé tàu thất bại!');
                    window.location.href = 'index.php';
                </script>";
            }
        }
    } else {
        echo "<script>
            alert('Vé tàu không tồn tại!');
            window.location.href = 'index.php';
        </script>";
    }
} else {
    echo "<script>
        alert('Không có ID vé tàu!');
        window.location.href = 'index.php';
    </script>";
}
