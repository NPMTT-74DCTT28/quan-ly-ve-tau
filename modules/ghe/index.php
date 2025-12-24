<?php
require_once __DIR__ . '/../../bootstrap.php';

if (!isset($_SESSION['user'])) {
    header("Location: " . BASE_URL . "modules/auth/dang_nhap.php");
    exit();
}

requireAdmin();

$conn = $db->getConnection();

$so_ghe = isset($_POST['so_ghe']) ? trim($_POST['so_ghe']) : '';

try {
    $sql = "SELECT ghe.*, toa_tau.ma_toa 
            FROM ghe 
            INNER JOIN toa_tau ON ghe.id_toa_tau = toa_tau.id 
            WHERE 1=1";
    $params = [];

    if (!empty($so_ghe)) {
        $sql .= " AND (ghe.so_ghe LIKE ? OR toa_tau.ma_toa LIKE ?)";
        $params[] = "%$so_ghe%";
        $params[] = "%$so_ghe%";
    }

    $sql .= " ORDER BY ghe.id DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $data_ghe = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Lỗi truy vấn: " . $e->getMessage());
}

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="container" style="padding: 20px; max-width: 1200px; margin: 0 auto;">
    <h2 style="margin-bottom: 20px; border-bottom: 2px solid #f0f0f0; padding-bottom: 10px;">Quản Lý Ghế</h2>

    <div style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); margin-bottom: 20px;">
        <form method="post" style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center;">
            <input type="text" name="so_ghe" value="<?= htmlspecialchars($so_ghe) ?>"
                placeholder="Nhập số ghế hoặc mã toa..."
                style="padding: 8px; border: 1px solid #ccc; border-radius: 4px; flex: 1; min-width: 200px;">

            <button type="submit" style="background: #007bff; color: white; padding: 8px 15px; border: none; border-radius: 4px; cursor: pointer;">
                Tìm kiếm
            </button>

            <a href="Add.php" style="background: #28a745; color: white; padding: 8px 15px; text-decoration: none; border-radius: 4px;">
                + Thêm mới
            </a>

            <a href="Export.php" style="background: #17a2b8; color: white; padding: 8px 15px; text-decoration: none; border-radius: 4px;">
                Nhập Excel
            </a>
            <a href="Import.php" style="background: #ffc107; color: #000; padding: 8px 15px; text-decoration: none; border-radius: 4px;">
                Xuất Excel
            </a>
        </form>
    </div>

    <div style="background: #fff; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; min-width: 600px;">
            <thead>
                <tr style="background-color: #2c3e50; color: white; text-align: left;">
                    <th style="padding: 12px; border-bottom: 1px solid #ddd;">STT</th>
                    <th style="padding: 12px; border-bottom: 1px solid #ddd;">Số Ghế</th>
                    <th style="padding: 12px; border-bottom: 1px solid #ddd;">Thuộc Toa Tàu</th>
                    <th style="padding: 12px; border-bottom: 1px solid #ddd; text-align: center;">Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($data_ghe):
                    $i = 1;
                    foreach ($data_ghe as $row): ?>
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 12px;"><?= $i++ ?></td>
                            <td style="padding: 12px; font-weight: bold;"><?= htmlspecialchars($row['so_ghe']) ?></td>
                            <td style="padding: 12px;"><?= htmlspecialchars($row['ma_toa']) ?></td>
                            <td style="padding: 12px; text-align: center;">
                                <a href="Edit.php?id=<?= $row['id'] ?>" style="color: #007bff; text-decoration: none; margin-right: 10px;">Sửa</a>
                                <a href="Delete.php?id=<?= $row['id'] ?>" onclick="return confirm('Bạn có chắc muốn xóa ghế này không?')" style="color: #dc3545; text-decoration: none;">Xóa</a>
                            </td>
                        </tr>
                    <?php endforeach;
                else: ?>
                    <tr>
                        <td colspan="4" style="padding: 20px; text-align: center; color: #777;">
                            Không tìm thấy dữ liệu ghế nào.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>