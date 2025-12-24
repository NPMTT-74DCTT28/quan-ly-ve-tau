<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../includes/header.php';

$conn = $db -> getConnection();

$ma_tuyen = isset($_POST['txtmatuyen']) ? $_POST['txtmatuyen'] : '';
$ten_tuyen = isset($_POST['txttentuyen']) ? $_POST['txttentuyen'] : '';

$sql = "SELECT 
    td.ma_tuyen,
    td.ten_tuyen,
    gd.ten_ga AS ten_ga_di,
    gden.ten_ga AS ten_ga_den,
    td.khoang_cach_km,
    td.gia_co_ban
FROM tuyen_duong td
JOIN ga_tau gd   ON td.id_ga_di  = gd.id
JOIN ga_tau gden ON td.id_ga_den = gden.id
WHERE 1=1";
$params = [];

if (!empty($ma_tuyen)) {
    $sql .= " AND td.ma_tuyen LIKE ?";
    $params[] = "%$ma_tuyen%";
}

if (!empty($ten_tuyen)) {
    $sql .= " AND td.ten_tuyen LIKE ?";
    $params[] = "%$ten_tuyen%";
}

$sql .= " ORDER BY td.ma_tuyen ASC";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QL TUYEN DUONG</title>
    <link rel="stylesheet" href="../../modules/tuyen-duong/tuyenduongstyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<div class="page-wrapper">
    <form method="POST" class="search-form">
        <div class="form-row">
            <div class="form-group">
                <label>Mã Tuyến</label>
                <input type="text" name="txtmatuyen" placeholder="Nhập mã tuyến...">
            </div>

            <div class="form-group">
                <label>Tên Tuyến</label>
                <input type="text" name="txttentuyen" placeholder="Nhập tên tuyến...">
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" name="btnTimkiem" class="btn_timkiem">
                <i class="fa-solid fa-magnifying-glass"></i> Tìm kiếm
            </button>
            <a href="themtuyen.php" class="btn_them">
                <i class="fa-solid fa-plus"></i> Thêm tuyến đường
            </a>
        </div>
        
    </form>
    <div class="table-wrapper">
        <table class="data_bang">
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Mã Tuyến</th>
                    <th>Tên Tuyến</th>
                    <th>Ga đi</th>
                    <th>Ga đến</th>
                    <th>Khoảng cách (km)</th>
                    <th>Giá cơ bản</th>
                    <th>Thao tác</th>
                </tr>
            </thead>

            <tbody>
                <?php
                    if (!empty($data)) {
                    $i = 0;
                    foreach ($data as $row) {
                ?>
                <tr>
                    <td><?php echo ++$i; ?></td>
                    <td><?php echo htmlspecialchars($row['ma_tuyen']); ?></td>
                    <td><?php echo htmlspecialchars($row['ten_tuyen']); ?></td>
                    <td><?php echo htmlspecialchars($row['ten_ga_di']); ?></td>
                    <td><?php echo htmlspecialchars($row['ten_ga_den']); ?></td>
                    <td><?php echo htmlspecialchars($row['khoang_cach_km']); ?></td>
                    <td><?php echo htmlspecialchars($row['gia_co_ban']); ?></td>
                    <td class="action-col">
                    <a href="suatuyen.php?ma_tuyen=<?php echo $row['ma_tuyen']; ?>" class="btn_sua">
                        <i class="fa-solid fa-pen"></i> Sửa
                    </a>

                    <a href="xoa.php?ma_tuyen=<?php echo $row['ma_tuyen']; ?>"
                    class="btn_xoa"
                        onclick="return confirm('Bạn có muốn xóa <?php echo htmlspecialchars($row['ten_tuyen']); ?> không ?');">
                    <i class="fa-solid fa-trash-can"></i> Xóa
                    </a>
                    </td>
                </tr>
            <?php
                }
                } else {
                    echo "<tr><td colspan='8'>Không có tuyến đường nào...</td></tr>";
                }
            ?>
            </tbody>
        </table>
    </div>

</div>
</body>
</html>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>