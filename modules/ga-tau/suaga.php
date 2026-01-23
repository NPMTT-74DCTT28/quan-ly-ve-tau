<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../includes/header.php';

$conn = $db->getConnection();

$current_ga = null;
if (isset($_GET['ma_ga'])) {
    $ma_edit = $_GET['ma_ga'];
    $stmt = $conn->prepare("SELECT * FROM ga_tau WHERE ma_ga = ?");
    $stmt->execute([$ma_edit]);
    $current_ga = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$current_ga) {
        header("Location: index.php");
        exit();
    }
}

if (isset($_POST['btnsua'])) {
    $ma  = $_POST['txtmaga']; 
    $ten = $_POST['txttenga'];
    $dc  = $_POST['txtdiachi'];
    $tp  = $_POST['txtthanhpho'];

    try {
        $sql = "UPDATE ga_tau SET ten_ga = ?, dia_chi = ?, thanh_pho = ? WHERE ma_ga = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$ten, $dc, $tp, $ma]);
        
        echo "<script>alert('Cập nhật thành công!'); window.location.href='index.php';</script>";
        exit();
    } catch (PDOException $e) {
        echo "<script>alert('Lỗi cập nhật: " . $e->getMessage() . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sửa Ga</title>
    <link rel="stylesheet" href="../../modules/ga-tau/stylethemga.css">
</head>
<body>

<div class="add-wrapper">
    <h2 class="add-title">SỬA GA</h2>

    <form method="POST" class="add-form">

        <div class="form-row">
            <label>Mã Ga</label>
            <input type="text" name="txtmaga" value="<?php echo $current_ga['ma_ga']; ?>" required readonly>
        </div>

        <div class="form-row">
            <label>Tên Ga</label>
            <input type="text" name="txttenga" value="<?php echo $current_ga['ten_ga']; ?>" required>
        </div>

        <div class="form-row">
            <label>Địa chỉ</label>
            <input type="text" name="txtdiachi" value="<?php echo $current_ga['dia_chi']; ?>" required>
        </div>

        <div class="form-row">
            <label>Thành phố</label>
            <input type="text" name="txtthanhpho" value="<?php echo $current_ga['thanh_pho']; ?>" required>
        </div>

        <div class="form-actions">
            <button type="submit" name="btnsua" class="btn-submit">Sửa Ga</button>
        </div>

    </form>
</div>

</body>
</html>