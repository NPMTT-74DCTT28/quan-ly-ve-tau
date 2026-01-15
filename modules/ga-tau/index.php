<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../includes/header.php';

$conn = $db -> getConnection();

$ma_ga = isset($_POST['txtmaga']) ? $_POST['txtmaga'] : '';
$ten_ga = isset($_POST['txttenga']) ? $_POST['txttenga'] : '';

$sql = "SELECT * FROM ga_tau WHERE 1=1";
$params = [];

if (!empty($ma_ga)) {
    $sql .= " AND ma_ga LIKE ?";
    $params[] = "%$ma_ga%";
}

if (!empty($ten_ga)) {
    $sql .= " AND ten_ga LIKE ?";
    $params[] = "%$ten_ga%";
}

$sql .= " ORDER BY ma_ga ASC";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QL GA TAU</title>
    <link rel="stylesheet" href="../../modules/ga-tau/gataustyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<div class="page-wrapper">
    <form method="POST" class="search-form">
        <div class="form-row">
            <div class="form-group">
                <label>Mã Ga</label>
                <input type="text" name="txtmaga" placeholder="Nhập mã ga...">
            </div>

            <div class="form-group">
                <label>Tên Ga</label>
                <input type="text" name="txttenga" placeholder="Nhập tên ga...">
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" name="btnTimkiem" class="btn_timkiem">
                <i class="fa-solid fa-magnifying-glass"></i> Tìm kiếm
            </button>
            <a href="themga.php" class="btn_them">
                <i class="fa-solid fa-plus"></i> Thêm Ga
            </a>
        </div>
        
    </form>
    <div class="table-wrapper">
        <table class="data_bang">
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Mã Ga</th>
                    <th>Tên Ga</th>
                    <th>Địa chỉ</th>
                    <th>Thành Phố</th>
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
                    <td><?php echo htmlspecialchars($row['ma_ga']); ?></td>
                    <td><?php echo htmlspecialchars($row['ten_ga']); ?></td>
                    <td><?php echo htmlspecialchars($row['dia_chi']); ?></td>
                    <td><?php echo htmlspecialchars($row['thanh_pho']); ?></td>
                    <td class="action-col">
                    <a href="suaga.php?ma_ga=<?php echo $row['ma_ga']; ?>" class="btn_sua">
                        <i class="fa-solid fa-pen"></i> Sửa
                    </a>

                    <a href="xoa.php?ma_ga=<?php echo $row['ma_ga']; ?>"
                    class="btn_xoa"
                        onclick="return confirm('Bạn có muốn xóa <?php echo htmlspecialchars($row['ten_ga']); ?> không ?');">
                    <i class="fa-solid fa-trash-can"></i> Xóa
                    </a>
                    </td>
                </tr>
            <?php
                }
                } else {
                    echo "<tr><td colspan='6'>Không có ga nào...</td></tr>";
                }
            ?>
            </tbody>
        </table>
    </div>

</div>
</body>
</html>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>