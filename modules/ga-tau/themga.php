<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../includes/header.php';

// 1. Khởi tạo kết nối
$conn = $db->getConnection();

// 2. Xử lý khi người dùng nhấn nút Lưu (btnLuu)
if (isset($_POST['btnthem'])) {
    // Lấy dữ liệu từ các ô input
    $ma = $_POST['txtmaga'];
    $ten = $_POST['txttenga'];
    $dc = $_POST['txtdiachi'];
    $tp = $_POST['txtthanhpho'];

    // Câu lệnh INSERT với dấu ?
    $sql = "INSERT INTO ga_tau (ma_ga, ten_ga, dia_chi, thanh_pho) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    try {
        // Thực thi với mảng tham số tương ứng 4 dấu ?
        $stmt->execute([$ma, $ten, $dc, $tp]);

        echo "<script>
                alert('Thêm ga tàu thành công!');
                window.location.href = 'index.php';
              </script>";
        exit();
    } catch (PDOException $e) {
        // Trường hợp trùng khóa chính (ma_ga) hoặc lỗi DB
        echo "<script>alert('Lỗi: Không thể thêm ga. Có thể mã ga đã tồn tại!');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Ga</title>
    <link rel="stylesheet" href="../../modules/ga-tau/stylethemga.css">
</head>
<body>

<div class="add-wrapper">

    <h2 class="add-title">THÊM GA</h2>

    <form method="POST" class="add-form">

        <div class="form-row">
            <label>Mã Ga</label>
            <input type="text" name="txtmaga" required>
        </div>

        <div class="form-row">
            <label>Tên Ga</label>
            <input type="text" name="txttenga" required>
        </div>

        <div class="form-row">
            <label>Địa chỉ</label>
            <input type="text" name="txtdiachi" required>
        </div>

        <div class="form-row">
            <label>Thành phố</label>
            <input type="text" name="txtthanhpho" required>
        </div>

        <div class="form-actions">
            <button type="submit" name="btnthem" class="btn-submit">
                Thêm Ga
            </button>
        </div>

    </form>

</div>

</body>
</html>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>