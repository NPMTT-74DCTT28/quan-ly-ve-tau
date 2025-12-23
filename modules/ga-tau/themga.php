<?php
require_once '../../config/database.php';

if (isset($_POST['btnthem'])) {
    $maGa = $_POST['masv'];
    $tenGa = $_POST['hotensv'];
    $diaChi = $_POST['dc'];
    $thanhPho = $_POST['lop'];
    $sql = "INSERT INTO ga_tau (ma_ga, ten_ga, dia_chi, thanh_pho) 
            VALUES ('$maGa', '$tenGa', '$diaChi', '$thanhPho')";

    try {
        $pdo->query($sql);

        echo "<script>
                alert('Thêm ga thành công!');
                window.location.href = 'themga.php'; 
              </script>";
    } catch (PDOException $e) {
        echo "<script>alert('Thêm ga thất bại!');</script>";
    }
}

require_once '../../includes/header.php';
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
            <input type="text" name="masv" required>
        </div>

        <div class="form-row">
            <label>Tên Ga</label>
            <input type="text" name="hotensv" required>
        </div>

        <div class="form-row">
            <label>Địa chỉ</label>
            <input type="text" name="dc" required>
        </div>

        <div class="form-row">
            <label>Thành phố</label>
            <input type="text" name="lop" required>
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
<?php require_once '../../includes/footer.php'; ?>