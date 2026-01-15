<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../includes/header.php';

$conn = $db->getConnection();

$sqlGa = "SELECT id, ten_ga FROM ga_tau";
$stmtGa = $conn->query($sqlGa);
$dsGa = $stmtGa->fetchAll(PDO::FETCH_ASSOC);

if (isset($_POST['btnthem'])) {
    $ma    = trim($_POST['txtmatuyen']);
    $ten   = trim($_POST['txttentuyen']);
    $gadi  = (int)$_POST['txtgadi'];
    $gaden = (int)$_POST['txtgaden'];
    $kc    = (float)$_POST['txtkhoangcach'];
    $gia   = (float)$_POST['txtgiacb'];

    if ($gadi === $gaden) {
        echo "<script>alert('Ga đi và ga đến không được trùng nhau');</script>";
    } else {
        $sql = "INSERT INTO tuyen_duong
                (ma_tuyen, ten_tuyen, id_ga_di, id_ga_den, Khoang_cach_km, gia_co_ban)
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        try {
            $stmt->execute([$ma, $ten, $gadi, $gaden, $kc, $gia]);
            echo "<script>
                    alert('Thêm tuyến đường thành công!');
                    window.location.href = 'themtuyen.php';
                  </script>";
            exit();
        } catch (PDOException $e) {
            echo "<script>alert('Thêm tuyến đường thất bại');</script>";
            echo $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Tuyến Đường</title>
    <link rel="stylesheet" href="../../modules/ga-tau/stylethemga.css">
</head>
<body>

<div class="add-wrapper">

    <h2 class="add-title">THÊM TUYẾN ĐƯỜNG</h2>

    <form method="POST" class="add-form">

        <div class="form-row">
            <label>Mã Tuyến</label>
            <input type="text" name="txtmatuyen" required>
        </div>

        <div class="form-row">
            <label>Tên Tuyến</label>
            <input type="text" name="txttentuyen" required>
        </div>

        <div class="form-row">
            <label>Ga đi</label>
            <select name="txtgadi" required>
                <option value="">-- Chọn ga đi --</option>
                <?php foreach ($dsGa as $ga): ?>
                    <option value="<?= $ga['id'] ?>">
                        <?= $ga['ten_ga'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-row">
            <label>Ga đến</label>
            <select name="txtgaden" required>
                <option value="">-- Chọn ga đến --</option>
                <?php foreach ($dsGa as $ga): ?>
                    <option value="<?= $ga['id'] ?>">
                        <?= $ga['ten_ga'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-row">
            <label>Khoảng cách (km)</label>
            <input type="number" name="txtkhoangcach" required>
        </div>

        <div class="form-row">
            <label>Giá cơ bản</label>
            <input type="number" name="txtgiacb" required>
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