<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../includes/header.php';

requireLogin();
$conn = $db->getConnection();

// Khởi tạo các biến tìm kiếm
$cccd = isset($_POST['cccd']) ? $_POST['cccd'] : '';
$ho_ten = isset($_POST['ho_ten']) ? $_POST['ho_ten'] : '';
$ngay_sinh = isset($_POST['ngay_sinh']) ? $_POST['ngay_sinh'] : '';
$gioi_tinh = isset($_POST['gioi_tinh']) ? $_POST['gioi_tinh'] : '';
$sdt = isset($_POST['sdt']) ? $_POST['sdt'] : '';
$dia_chi = isset($_POST['dia_chi']) ? $_POST['dia_chi'] : '';

// Xử lý tìm kiếm
$sql = "SELECT * FROM khach_hang WHERE 1=1";
$params = [];

if (!empty($cccd)) {
    $sql .= " AND cccd LIKE ?";
    $params[] = "%$cccd%";
}
if (!empty($ho_ten)) {
    $sql .= " AND ho_ten LIKE ?";
    $params[] = "%$ho_ten%";
}
if (!empty($ngay_sinh)) {
    $sql .= " AND DATE(ngay_sinh) = ?";
    $params[] = $ngay_sinh;
}
if (!empty($gioi_tinh)) {
    $sql .= " AND gioi_tinh = ?";
    $params[] = $gioi_tinh;
}
if (!empty($sdt)) {
    $sql .= " AND sdt LIKE ?";
    $params[] = "%$sdt%";
}
if (!empty($dia_chi)) {
    $sql .= " AND dia_chi LIKE ?";
    $params[] = "%$dia_chi%";
}

$sql .= " ORDER BY id DESC";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$data_search = $stmt->fetchAll();
?>

<div class="container" style="padding: 20px; max-width: 1400px; margin: 0 auto;">
    <h2 style="color: #2c3e50; text-align: center; margin-bottom: 20px;">QUẢN LÝ KHÁCH HÀNG</h2>

    <div style="background: #fff; padding: 25px; border-radius: 12px; box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08); margin-bottom: 30px;">
        <form method="post" action="">
            <div style="display: flex; flex-wrap: wrap; gap: 15px;">
                <div style="flex: 1; min-width: 200px;">
                    <label style="font-weight: 600; color: #34495e;">CCCD:</label>
                    <input type="text" name="cccd" value="<?php echo htmlspecialchars($cccd); ?>" placeholder="Số CCCD"
                        class="form-control" style="width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #ccc; border-radius: 4px;">
                </div>
                <div style="flex: 1; min-width: 200px;">
                    <label style="font-weight: 600; color: #34495e;">Họ và Tên:</label>
                    <input type="text" name="ho_ten" value="<?php echo htmlspecialchars($ho_ten); ?>" placeholder="Họ tên"
                        class="form-control" style="width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #ccc; border-radius: 4px;">
                </div>
                <div style="flex: 1; min-width: 150px;">
                    <label style="font-weight: 600; color: #34495e;">SĐT:</label>
                    <input type="text" name="sdt" value="<?php echo htmlspecialchars($sdt); ?>" placeholder="Số điện thoại"
                        class="form-control" style="width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #ccc; border-radius: 4px;">
                </div>
                <div style="flex: 1; min-width: 150px;">
                    <label style="font-weight: 600; color: #34495e;">Ngày sinh:</label>
                    <input type="date" name="ngay_sinh" value="<?php echo htmlspecialchars($ngay_sinh); ?>"
                        class="form-control" style="width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #ccc; border-radius: 4px;">
                </div>
                <div style="flex: 1; min-width: 150px;">
                    <label style="font-weight: 600; color: #34495e;">Giới tính:</label>
                    <select name="gioi_tinh" class="form-control" style="width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #ccc; border-radius: 4px;">
                        <option value="">Tất cả</option>
                        <option value="Nam" <?php echo $gioi_tinh == 'Nam' ? 'selected' : ''; ?>>Nam</option>
                        <option value="Nữ" <?php echo $gioi_tinh == 'Nữ' ? 'selected' : ''; ?>>Nữ</option>
                        <option value="Khác" <?php echo $gioi_tinh == 'Khác' ? 'selected' : ''; ?>>Khác</option>
                    </select>
                </div>
                <div style="flex: 2; min-width: 300px;">
                    <label style="font-weight: 600; color: #34495e;">Địa chỉ:</label>
                    <input type="text" name="dia_chi" value="<?php echo htmlspecialchars($dia_chi); ?>" placeholder="Nhập địa chỉ"
                        class="form-control" style="width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #ccc; border-radius: 4px;">
                </div>
            </div>

            <div style="margin-top: 25px; display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;">
                <button type="submit" name="btnSearch" style="background: #0d6efd; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; display: inline-flex; align-items: center; gap: 5px;">
                    <i class="bi bi-search"></i> Tìm kiếm
                </button>

                <a href="them.php" style="background: #198754; color: white; padding: 10px 20px; border: none; border-radius: 4px; text-decoration: none; display: inline-flex; align-items: center; gap: 5px;">
                    <i class="bi bi-plus-circle"></i> Thêm mới
                </a>
            </div>
        </form>
    </div>

    <div style="background: #fff; padding: 25px; border-radius: 12px; box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);">
        <h3 style="text-align: center; margin-bottom: 20px; color: #34495e;">DANH SÁCH KHÁCH HÀNG</h3>
        <div class="table-responsive">
            <table class="table table-bordered table-hover" style="width: 100%; margin-bottom: 0;">
                <thead style="background-color: #4a90e2; color: white;">
                    <tr>
                        <th style="padding: 12px; text-align: center;">CCCD</th>
                        <th style="padding: 12px; text-align: center;">Họ và Tên</th>
                        <th style="padding: 12px; text-align: center;">Ngày sinh</th>
                        <th style="padding: 12px; text-align: center;">Giới tính</th>
                        <th style="padding: 12px; text-align: center;">SĐT</th>
                        <th style="padding: 12px; text-align: center;">Địa chỉ</th>
                        <th style="padding: 12px; text-align: center;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (count($data_search) > 0) {
                        foreach ($data_search as $row) {
                    ?>
                            <tr>
                                <td style="text-align: center; vertical-align: middle;"><?php echo htmlspecialchars($row['cccd']); ?></td>
                                <td style="vertical-align: middle;"><?php echo htmlspecialchars($row['ho_ten']); ?></td>
                                <td style="text-align: center; vertical-align: middle;">
                                    <?php echo !empty($row['ngay_sinh']) ? htmlspecialchars(date('d/m/Y', strtotime($row['ngay_sinh']))) : ''; ?>
                                </td>
                                <td style="text-align: center; vertical-align: middle;">
                                    <?php
                                    $bg_color = '#6c757d'; // Default grey
                                    if ($row['gioi_tinh'] == 'Nam') $bg_color = '#0d6efd';
                                    if ($row['gioi_tinh'] == 'Nữ') $bg_color = '#d63384';
                                    ?>
                                    <span style="background-color: <?php echo $bg_color; ?>; color: white; padding: 4px 10px; border-radius: 15px; font-size: 14px;">
                                        <?php echo htmlspecialchars($row['gioi_tinh']); ?>
                                    </span>
                                </td>
                                <td style="text-align: center; vertical-align: middle;"><?php echo htmlspecialchars($row['sdt']); ?></td>
                                <td style="vertical-align: middle;"><?php echo htmlspecialchars($row['dia_chi']); ?></td>
                                <td style="text-align: center; vertical-align: middle;">
                                    <div style="display: flex; gap: 5px; justify-content: center;">
                                        <a href="sua.php?id=<?php echo $row['id']; ?>" title="Cập nhật"
                                            style="background: #0d6efd; color: white; padding: 5px 10px; border-radius: 4px; text-decoration: none;">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="xoa.php?id=<?php echo $row['id']; ?>"
                                            onclick="return confirm('Bạn có chắc chắn muốn xóa khách hàng này?')" title="Xóa"
                                            style="background: #dc3545; color: white; padding: 5px 10px; border-radius: 4px; text-decoration: none;">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                    <?php
                        }
                    } else {
                        echo "<tr><td colspan='8' class='text-center text-muted' style='padding: 20px;'>Không tìm thấy dữ liệu</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

<?php
require_once '../../includes/footer.php';
?>