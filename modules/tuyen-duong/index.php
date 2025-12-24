<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../includes/header.php';

if (!isset($_SESSION['user'])) {
    header("Location: " . BASE_URL . "modules/auth/dang_nhap.php");
    exit();
}

requireAdmin();

$conn = $db->getConnection();

$ma_tuyen = isset($_POST['txtmatuyen']) ? trim($_POST['txtmatuyen']) : '';
$ten_tuyen = isset($_POST['txttentuyen']) ? trim($_POST['txttentuyen']) : '';

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

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="container" style="padding: 20px;">
    <h2>Quản Lý Tuyến Đường</h2>

    <div style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); margin-bottom: 20px;">
        <form method="POST" action="" style="display: flex; gap: 15px; flex-wrap: wrap; align-items: flex-end;">
            <div style="flex: 1; min-width: 200px;">
                <label style="font-weight: bold;">Mã Tuyến</label>
                <input type="text" name="txtmatuyen" value="<?= htmlspecialchars($ma_tuyen) ?>" placeholder="Nhập mã tuyến..." style="width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #ced4da; border-radius: 4px;">
            </div>

            <div style="flex: 1; min-width: 200px;">
                <label style="font-weight: bold;">Tên Tuyến</label>
                <input type="text" name="txttentuyen" value="<?= htmlspecialchars($ten_tuyen) ?>" placeholder="Nhập tên tuyến..." style="width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #ced4da; border-radius: 4px;">
            </div>

            <div style="display: flex; gap: 10px;">
                <button type="submit" name="btnTimkiem" style="background: #17a2b8; color: white; padding: 8px 15px; border: none; border-radius: 4px; cursor: pointer;">
                    <i class="fa-solid fa-magnifying-glass"></i> Tìm kiếm
                </button>
                <a href="themtuyen.php" style="background: #28a745; color: white; padding: 8px 15px; border: none; border-radius: 4px; text-decoration: none; display: inline-block;">
                    <i class="fa-solid fa-plus"></i> Thêm mới
                </a>
            </div>
        </form>
    </div>

    <div style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; min-width: 800px;">
            <thead>
                <tr style="background-color: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                    <th style="padding: 12px; text-align: left; border-bottom: 1px solid #dee2e6;">STT</th>
                    <th style="padding: 12px; text-align: left; border-bottom: 1px solid #dee2e6;">Mã Tuyến</th>
                    <th style="padding: 12px; text-align: left; border-bottom: 1px solid #dee2e6;">Tên Tuyến</th>
                    <th style="padding: 12px; text-align: left; border-bottom: 1px solid #dee2e6;">Ga đi</th>
                    <th style="padding: 12px; text-align: left; border-bottom: 1px solid #dee2e6;">Ga đến</th>
                    <th style="padding: 12px; text-align: left; border-bottom: 1px solid #dee2e6;">Khoảng cách</th>
                    <th style="padding: 12px; text-align: left; border-bottom: 1px solid #dee2e6;">Giá cơ bản</th>
                    <th style="padding: 12px; text-align: center; border-bottom: 1px solid #dee2e6; width: 150px;">Thao tác</th>
                </tr>
            </thead>

            <tbody>
                <?php if (!empty($data)): ?>
                    <?php $i = 0;
                    foreach ($data as $row): ?>
                        <tr style="border-bottom: 1px solid #dee2e6;">
                            <td style="padding: 12px;"><?= ++$i; ?></td>
                            <td style="padding: 12px; font-weight: bold;"><?= htmlspecialchars($row['ma_tuyen']); ?></td>
                            <td style="padding: 12px;"><?= htmlspecialchars($row['ten_tuyen']); ?></td>
                            <td style="padding: 12px;"><?= htmlspecialchars($row['ten_ga_di']); ?></td>
                            <td style="padding: 12px;"><?= htmlspecialchars($row['ten_ga_den']); ?></td>
                            <td style="padding: 12px;"><?= htmlspecialchars($row['khoang_cach_km']); ?> km</td>
                            <td style="padding: 12px;"><?= number_format($row['gia_co_ban'], 0, ',', '.'); ?> đ</td>
                            <td style="padding: 12px; text-align: center;">
                                <a href="suatuyen.php?ma_tuyen=<?= $row['ma_tuyen']; ?>" style="color: #ffc107; margin-right: 10px;" title="Sửa">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <a href="xoa.php?ma_tuyen=<?= $row['ma_tuyen']; ?>"
                                    onclick="return confirm('Bạn có muốn xóa tuyến <?= htmlspecialchars($row['ten_tuyen']); ?> không?');"
                                    style="color: #dc3545;" title="Xóa">
                                    <i class="fa-solid fa-trash-can"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" style="padding: 20px; text-align: center; color: #666;">Không có tuyến đường nào...</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>