<?php
require_once __DIR__ . '/../../bootstrap.php';

requireLogin();
requireAdmin();

$conn = $db->getConnection();

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql_check = "SELECT * FROM khach_hang WHERE id = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->execute([$id]);

    if ($stmt_check->rowCount() > 0) {
        $sql_check_ve = "SELECT * FROM ve_tau WHERE id_khach_hang = ?";
        $stmt_check_ve = $conn->prepare($sql_check_ve);
        $stmt_check_ve->execute([$id]);

        if ($stmt_check_ve->rowCount() > 0) {
            echo "<script>
                alert('Không thể xóa khách hàng vì đã có vé đặt!');
                window.location.href = 'index.php';
            </script>";
        } else {
            $sql_delete = "DELETE FROM khach_hang WHERE id = ?";
            $stmt_delete = $conn->prepare($sql_delete);

            if ($stmt_delete->execute([$id])) {
                echo "<script>
                    alert('Xóa khách hàng thành công!');
                    window.location.href = 'index.php';
                </script>";
            } else {
                echo "<script>
                    alert('Xóa khách hàng thất bại!');
                    window.location.href = 'index.php';
                </script>";
            }
        }
    } else {
        echo "<script>
            alert('Khách hàng không tồn tại!');
            window.location.href = 'index.php';
        </script>";
    }
} else {
    echo "<script>
        alert('Không có ID khách hàng!');
        window.location.href = 'index.php';
    </script>";
}
