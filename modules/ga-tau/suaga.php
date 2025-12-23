<?php
require_once '../../config/database.php';
if (isset($_GET['ma_ga'])) {
    $ma_ga_edit = $_GET['ma_ga'];
    $sql_get = "SELECT * FROM ga_tau WHERE ma_ga = '$ma_ga_edit'";
    $stmt_get = $pdo->query($sql_get);
    $row = $stmt_get->fetch();
    if (!$row) {
        header('Location: index.php');
        exit();
    }
}
if (isset($_POST['btnthem'])) {
    $maGa = $_POST['masv'];    
    $tenGa = $_POST['hotensv']; 
    $diaChi = $_POST['dc'];
    $thanhPho = $_POST['lop'];
    $sql_update = "UPDATE ga_tau 
                   SET ten_ga = '$tenGa', dia_chi = '$diaChi', thanh_pho = '$thanhPho' 
                   WHERE ma_ga = '$maGa'";

    try {
        $pdo->query($sql_update);
        echo "<script>
                alert('Sửa ga thành công!');
                window.location.href = 'index.php'; 
              </script>";
    } catch (PDOException $e) {
        echo "<script>alert('Lỗi! sửa thất bại. ')</script>";
    }
}

require_once '../../includes/header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa Ga</title>
    <link rel="stylesheet" href="../../modules/ga-tau/stylethemga.css">
</head>
<body>

<div class="add-wrapper">

    <h2 class="add-title">SỬA GA</h2>

    <form method="POST" class="add-form">

        <div class="form-row">
            <label>Mã Ga</label>
            <input type="text" name="masv" value="<?php echo $row['ma_ga']; ?>" required readonly>
        </div>

        <div class="form-row">
            <label>Tên Ga</label>
            <input type="text" name="hotensv" value="<?php echo $row['ten_ga']; ?>" required>
        </div>

        <div class="form-row">
            <label>Địa chỉ</label>
            <input type="text" name="dc" value="<?php echo $row['dia_chi']; ?>" required>
        </div>

        <div class="form-row">
            <label>Thành phố</label>
            <input type="text" name="lop" value="<?php echo $row['thanh_pho']; ?>" required>
        </div>

        <div class="form-actions">
            <button type="submit" name="btnthem" class="btn-submit">
                Sửa Ga
            </button>
        </div>

    </form>

</div>

</body>
</html>
<?php require_once '../../includes/footer.php'; ?>