<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../includes/header.php';

if (!isset($_SESSION['user'])) {
    header("Location: " . BASE_URL . "modules/auth/dang_nhap.php");
    exit();
}

requireAdmin();

$conn = $db->getConnection();

$so_ghe = isset($_POST['so_ghe']) ? trim($_POST['so_ghe']) : '';

// Xử lý nút Thêm mới nếu dùng form submit (theo phong cách code 1)
if (isset($_POST['btnAdd'])) {
    header('location:Add.php');
    exit;
}

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
?>

<div class="main-content">
    <h1>QUẢN LÝ GHẾ</h1>

    <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 25px; border: 1px solid #e9ecef;">
        <form method="post" action="">
            <div class="row" style="display: flex; flex-wrap: wrap; gap: 15px; justify-content: center;">
                <div style="flex: 1; min-width: 200px; max-width: 400px;">
                    <label style="font-weight: 600; font-size: 14px; margin-bottom: 5px; display: block;">Tìm kiếm ghế/toa:</label>
                    <input type="text" class="form-control" name="so_ghe" value="<?php echo htmlspecialchars($so_ghe); ?>" placeholder="Nhập số ghế hoặc mã toa"
                        style="width: 100%; padding: 6px 10px; border: 1px solid #ced4da; border-radius: 4px;">
                </div>
            </div>

            <div style="margin-top: 20px; text-align: center;">
                <button type="submit" class="btn btn-primary" name="btnSearch" style="background: #0d6efd; color: white; padding: 8px 20px; border: none; border-radius: 4px; cursor: pointer; margin: 0 5px;">
                    <i class="bi bi-search"></i> Tìm kiếm
                </button>
                <a href="Add.php" style="background: #198754; color: white; padding: 8px 20px; border: none; border-radius: 4px; text-decoration: none; margin: 0 5px; display: inline-block;">
                    <i class="bi bi-plus-circle"></i> Thêm mới
                </a>
                <a href="Export.php" style="background: #0dcaf0; color: white; padding: 8px 20px; border: none; border-radius: 4px; text-decoration: none; margin: 0 5px; display: inline-block;">
                    <i class="bi bi-file-earmark-excel"></i> Xuất Excel
                </a>
                <a href="Import.php" style="background: #ffc107; color: #000; padding: 8px 20px; border: none; border-radius: 4px; text-decoration: none; margin: 0 5px; display: inline-block;">
                    <i class="bi bi-upload"></i> Nhập Excel
                </a>
            </div>
        </form>
    </div>

    <h3 style="text-align: center; margin-bottom: 20px; color: #34495e; font-size: 20px;">DANH SÁCH GHẾ</h3>
    <div style="overflow-x: auto;">
        <table class="table" style="width: 100%; border-collapse: collapse; margin-bottom: 1rem;">
            <thead>
                <tr style="background-color: #FEE1E8; color: black;">
                    <th style="padding: 12px; border: 1px solid #dee2e6; text-align: center; width: 10%;">STT</th>
                    <th style="padding: 12px; border: 1px solid #dee2e6; text-align: center; width: 35%;">Số Ghế</th>
                    <th style="padding: 12px; border: 1px solid #dee2e6; text-align: center; width: 35%;">Mã Toa Tàu</th>
                    <th style="padding: 12px; border: 1px solid #dee2e6; text-align: center; width: 20%;">Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!empty($data_ghe)) {
                    $i = 1;
                    foreach ($data_ghe as $row) {
                ?>
                        <tr style="border-bottom: 1px solid #dee2e6;">
                            <td style="padding: 10px; border: 1px solid #dee2e6; text-align: center;"><?php echo $i++; ?></td>
                            <td style="padding: 10px; border: 1px solid #dee2e6; text-align: center;"><?php echo htmlspecialchars($row['so_ghe']); ?></td>
                            <td style="padding: 10px; border: 1px solid #dee2e6; text-align: center;"><?php echo htmlspecialchars($row['ma_toa']); ?></td>
                            <td style="padding: 10px; border: 1px solid #dee2e6; text-align: center;">
                                <a href="Edit.php?id=<?php echo $row['id']; ?>" title="Cập nhật" style="color: #0d6efd; margin-right: 10px; font-size: 18px;">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <a href="Delete.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa ghế này?')" title="Xóa" style="color: #dc3545; font-size: 18px;">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                <?php
                    }
                } else {
                    echo "<tr><td colspan='4' style='padding: 20px; text-align: center; color: #6c757d;'>Không tìm thấy dữ liệu ghế</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>