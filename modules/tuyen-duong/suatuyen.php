<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../includes/header.php';

$conn = $db->getConnection();

$current_tuyen = null;
if (isset($_GET['ma_tuyen'])) {
    $ma_edit = $_GET['ma_tuyen'];

    $stmt = $conn->prepare("SELECT * FROM tuyen_duong WHERE ma_tuyen = ?");
    $stmt->execute([$ma_edit]);
    $current_tuyen = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$current_tuyen) {
        header("Location: index.php");
        exit();
    }
}

$sqlGa = "SELECT id, ten_ga FROM ga_tau";
$stmtGa = $conn->query($sqlGa);
$dsGa = $stmtGa->fetchAll(PDO::FETCH_ASSOC);

if (isset($_POST['btnsua'])) {

    $ma    = $_POST['txtmatuyen'];
    $ten   = $_POST['txttentuyen'];
    $gadi  = (int)$_POST['txtgadi'];
    $gaden = (int)$_POST['txtgaden'];
    $kc    = (float)$_POST['txtkhoangcach'];
    $gia   = (float)$_POST['txtgiacb'];

    if ($gadi === $gaden) {
        echo "<script>alert('Ga đi và ga đến không được trùng nhau');</script>";
    } else {

        $checkSql = "SELECT COUNT(*) FROM tuyen_duong
                     WHERE id_ga_di = ? AND id_ga_den = ?
                     AND ma_tuyen <> ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->execute([$gadi, $gaden, $ma]);

        if ($checkStmt->fetchColumn() > 0) {
            echo "<script>alert('Tuyến đường này đã tồn tại');</script>";
        } else {
            try {
                $sql = "UPDATE tuyen_duong
                        SET ten_tuyen = ?, 
                            id_ga_di = ?, 
                            id_ga_den = ?, 
                            khoang_cach_km = ?, 
                            gia_co_ban = ?
                        WHERE ma_tuyen = ?";
                $stmt = $conn->prepare($sql);

                $stmt->execute([$ten, $gadi, $gaden, $kc, $gia, $ma]);

                echo "<script>
                        alert('Cập nhật tuyến đường thành công!');
                        window.location.href='index.php';
                      </script>";
                exit();
            } catch (PDOException $e) {
                echo "<script>alert('Lỗi cập nhật: " . $e->getMessage() . "');</script>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sửa Tuyến Đường</title>
    <link rel="stylesheet" href="../../modules/ga-tau/stylethemga.css">
</head>
<body>

<div class="add-wrapper">
    <h2 class="add-title">SỬA TUYẾN ĐƯỜNG</h2>

    <form method="POST" class="add-form">

        <div class="form-row">
            <label>Mã Tuyến</label>
            <input type="text" name="txtmatuyen" value="<?php echo $current_tuyen['ma_tuyen']; ?>" readonly>
        </div>

        <div class="form-row">
            <label>Tên Tuyến</label>
            <input type="text" name="txttentuyen" value="<?php echo $current_tuyen['ten_tuyen']; ?>" required>
        </div>

        <div class="form-row">
            <label>Ga đi</label>
            <select name="txtgadi" required>
                <?php foreach ($dsGa as $ga): ?>
                    <option value="<?php echo $ga['id']; ?>"
                        <?php if ($ga['id'] == $current_tuyen['id_ga_di']) echo 'selected'; ?>>
                        <?php echo $ga['ten_ga']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-row">
            <label>Ga đến</label>
            <select name="txtgaden" required>
                <?php foreach ($dsGa as $ga): ?>
                    <option value="<?php echo $ga['id']; ?>"
                        <?php if ($ga['id'] == $current_tuyen['id_ga_den']) echo 'selected'; ?>>
                        <?php echo $ga['ten_ga']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-row">
            <label>Khoảng cách (km)</label>
            <input type="text" name="txtkhoangcach" value="<?php echo $current_tuyen['khoang_cach_km']; ?>" required>
        </div>

        <div class="form-row">
            <label>Giá cơ bản</label>
            <input type="text" name="txtgiacb" value="<?php echo $current_tuyen['gia_co_ban']; ?>" required>
        </div>

        <div class="form-actions">
            <button type="submit" name="btnsua" class="btn-submit">
                Sửa Tuyến
            </button>
        </div>

    </form>
</div>

</body>
</html>
