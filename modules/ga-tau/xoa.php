<?php
// 1. Nhúng file cấu hình và kết nối
require_once __DIR__ . '/../../bootstrap.php';

requireAdmin();

// 2. Khởi tạo kết nối từ đối tượng $db
$conn = $db->getConnection();

// 3. Kiểm tra nếu có mã ga trên URL
if (isset($_GET['ma_ga'])) {
    $ma_ga_xoa = $_GET['ma_ga'];

    // 4. Sử dụng câu lệnh DELETE với dấu ? để bảo mật
    $sql = "DELETE FROM ga_tau WHERE ma_ga = ?";
    
    try {
        $stmt = $conn->prepare($sql);
        
        // 5. Thực thi việc xóa
        $stmt->execute([$ma_ga_xoa]);

        // 6. Xóa thành công thì hiện thông báo và quay về trang danh sách
        echo "<script>
                alert('Đã xóa ga tàu thành công!');
                window.location.href = 'index.php';
              </script>";
        exit();

    } catch (PDOException $e) {
        // Trường hợp không xóa được (ví dụ: ga đang có lịch trình tàu đi qua)
        echo "<script>
                alert('Lỗi! Không thể xóa ga này (có thể do ràng buộc dữ liệu).');
                window.location.href = 'index.php';
              </script>";
        exit();
    }
} else {
    // Nếu không có mã ga thì đẩy về trang chủ ngay
    header("Location: index.php");
    exit();
}
?>