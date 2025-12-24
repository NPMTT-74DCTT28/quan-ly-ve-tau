<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../includes/header.php';

if (!isset($_SESSION['user'])) {
    header("Location: " . BASE_URL . "modules/auth/dang_nhap.php");
    exit();
}

requireAdmin();

$conn = $db->getConnection();

$ma_ga = isset($_POST['txtmaga']) ? trim($_POST['txtmaga']) : '';
$ten_ga = isset($_POST['txttenga']) ? trim($_POST['txttenga']) : '';

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

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="container" style="padding: 20px;">
    <h2>Quản Lý Ga Tàu</h2>

    <div style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); margin-bottom: 20px;">
        <form method="POST" action="" style="display: flex; gap: 15px; flex-wrap: wrap; align-items: flex-end;">
            <div style="flex: 1; min-width: 200px;">
                <label style="font-weight: bold;">Mã Ga</label>
                <input type="text" name="txtmaga" value="<?= htmlspecialchars($ma_ga) ?>" placeholder="Nhập mã ga..." style="width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #ced4da; border-radius: 4px;">
            </div>

            <div style="flex: 1; min-width: 200px;">
                <label style="font-weight: bold;">Tên Ga</label>
                <input type="text" name="txttenga" value="<?= htmlspecialchars($ten_ga) ?>" placeholder="Nhập tên ga..." style="width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #ced4da; border-radius: 4px;">
            </div>

            <div style="display: flex; gap: 10px;">
                <button type="submit" name="btnTimkiem" style="background: #17a2b8; color: white; padding: 8px 15px; border: none; border-radius: 4px; cursor: pointer;">
                    <i class="fa-solid fa-magnifying-glass"></i> Tìm kiếm
                </button>
                <a href="themga.php" style="background: #28a745; color: white; padding: 8px 15px; border: none; border-radius: 4px; text-decoration: none; display: inline-block;">
                    <i class="fa-solid fa-plus"></i> Thêm Ga
                </a>
            </div>
        </form>
    </div>

    <div style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; min-width: 600px;">
            <thead>
                <tr style="background-color: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                    <th style="padding: 12px; text-align: left; border-bottom: 1px solid #dee2e6;">STT</th>
                    <th style="padding: 12px; text-align: left; border-bottom: 1px solid #dee2e6;">Mã Ga</th>
                    <th style="padding: 12px; text-align: left; border-bottom: 1px solid #dee2e6;">Tên Ga</th>
                    <th style="padding: 12px; text-align: left; border-bottom: 1px solid #dee2e6;">Địa chỉ</th>
                    <th style="padding: 12px; text-align: left; border-bottom: 1px solid #dee2e6;">Thành Phố</th>
                    <th style="padding: 12px; text-align: center; border-bottom: 1px solid #dee2e6; width: 150px;">Thao tác</th>
                </tr>
            </thead>

            <tbody>
                <?php if (!empty($data)): ?>
                    <?php $i = 0;
                    foreach ($data as $row): ?>
                        <tr style="border-bottom: 1px solid #dee2e6;">
                            <td style="padding: 12px;"><?= ++$i; ?></td>
                            <td style="padding: 12px; font-weight: bold;"><?= htmlspecialchars($row['ma_ga']); ?></td>
                            <td style="padding: 12px;"><?= htmlspecialchars($row['ten_ga']); ?></td>
                            <td style="padding: 12px;"><?= htmlspecialchars($row['dia_chi']); ?></td>
                            <td style="padding: 12px;"><?= htmlspecialchars($row['thanh_pho']); ?></td>
                            <td style="padding: 12px; text-align: center;">
                                <a href="suaga.php?ma_ga=<?= $row['ma_ga']; ?>" style="color: #ffc107; margin-right: 10px;" title="Sửa">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <a href="xoa.php?ma_ga=<?= $row['ma_ga']; ?>"
                                    onclick="return confirm('Bạn có muốn xóa <?= htmlspecialchars($row['ten_ga']); ?> không?');"
                                    style="color: #dc3545;" title="Xóa">
                                    <i class="fa-solid fa-trash-can"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="padding: 20px; text-align: center; color: #666;">Không có dữ liệu ga tàu nào...</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>