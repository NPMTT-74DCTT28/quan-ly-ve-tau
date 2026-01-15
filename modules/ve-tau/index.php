<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../includes/header.php';

requireLogin();

$conn = $db->getConnection();

// Khởi tạo các biến tìm kiếm
$ma_ve = $_POST['ma_ve'] ?? '';
$ten_khach = $_POST['ten_khach'] ?? '';
$ma_lich_trinh = $_POST['ma_lich_trinh'] ?? '';
$trang_thai = $_POST['trang_thai'] ?? '';
$ngay_dat_tu = $_POST['ngay_dat_tu'] ?? '';
$ngay_dat_den = $_POST['ngay_dat_den'] ?? '';

// Xử lý tìm kiếm
$sql = "SELECT vt.*, kh.ho_ten as ten_khach_hang, kh.sdt, lt.ma_lich_trinh,
            CONCAT(g.so_ghe, ' (', t.ma_toa, ')') as ten_ghe, nv.ho_ten as ten_nhan_vien
        FROM ve_tau vt
        LEFT JOIN khach_hang kh ON vt.id_khach_hang = kh.id
        LEFT JOIN lich_trinh lt ON vt.id_lich_trinh = lt.id
        LEFT JOIN ghe g ON vt.id_ghe = g.id
        LEFT JOIN toa_tau t ON g.id_toa_tau = t.id
        LEFT JOIN nhan_vien nv ON vt.id_nhan_vien = nv.id
        WHERE 1=1";

$params = [];

if (!empty($ma_ve)) {
    $sql .= " AND vt.ma_ve LIKE ?";
    $params[] = "%$ma_ve%";
}
if (!empty($ten_khach)) {
    $sql .= " AND kh.ho_ten LIKE ?";
    $params[] = "%$ten_khach%";
}
if (!empty($ma_lich_trinh)) {
    $sql .= " AND lt.ma_lich_trinh LIKE ?";
    $params[] = "%$ma_lich_trinh%";
}
if (!empty($trang_thai)) {
    $sql .= " AND vt.trang_thai = ?";
    $params[] = $trang_thai;
}
if (!empty($ngay_dat_tu)) {
    $sql .= " AND DATE(vt.ngay_dat) >= ?";
    $params[] = $ngay_dat_tu;
}
if (!empty($ngay_dat_den)) {
    $sql .= " AND DATE(vt.ngay_dat) <= ?";
    $params[] = $ngay_dat_den;
}

$sql .= " ORDER BY vt.ngay_dat DESC";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$data_search = $stmt->fetchAll();
?>

<div class="main-content">
    <h1>QUẢN LÝ VÉ TÀU</h1>

    <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 25px; border: 1px solid #e9ecef;">
        <form method="post" action="">
            <div class="row" style="display: flex; flex-wrap: wrap; gap: 15px;">
                <div style="flex: 1; min-width: 150px;">
                    <label style="font-weight: 600; font-size: 14px; margin-bottom: 5px; display: block;">Mã vé:</label>
                    <input type="text" class="form-control" name="ma_ve" value="<?php echo htmlspecialchars($ma_ve); ?>" placeholder="Mã vé"
                        style="width: 100%; padding: 6px 10px; border: 1px solid #ced4da; border-radius: 4px;">
                </div>
                <div style="flex: 1; min-width: 150px;">
                    <label style="font-weight: 600; font-size: 14px; margin-bottom: 5px; display: block;">Khách hàng:</label>
                    <input type="text" class="form-control" name="ten_khach" value="<?php echo htmlspecialchars($ten_khach); ?>" placeholder="Tên khách"
                        style="width: 100%; padding: 6px 10px; border: 1px solid #ced4da; border-radius: 4px;">
                </div>
                <div style="flex: 1; min-width: 150px;">
                    <label style="font-weight: 600; font-size: 14px; margin-bottom: 5px; display: block;">Lịch trình:</label>
                    <input type="text" class="form-control" name="ma_lich_trinh" value="<?php echo htmlspecialchars($ma_lich_trinh); ?>" placeholder="Mã lịch trình"
                        style="width: 100%; padding: 6px 10px; border: 1px solid #ced4da; border-radius: 4px;">
                </div>
                <div style="flex: 1; min-width: 150px;">
                    <label style="font-weight: 600; font-size: 14px; margin-bottom: 5px; display: block;">Trạng thái:</label>
                    <select class="form-control" name="trang_thai" style="width: 100%; padding: 6px 10px; border: 1px solid #ced4da; border-radius: 4px;">
                        <option value="">Tất cả</option>
                        <option value="Đã xác nhận" <?php echo $trang_thai == 'Đã xác nhận' ? 'selected' : ''; ?>>Đã xác nhận</option>
                        <option value="Chờ xác nhận" <?php echo $trang_thai == 'Chờ xác nhận' ? 'selected' : ''; ?>>Chờ xác nhận</option>
                        <option value="Đã hủy" <?php echo $trang_thai == 'Đã hủy' ? 'selected' : ''; ?>>Đã hủy</option>
                        <option value="Hoàn thành" <?php echo $trang_thai == 'Hoàn thành' ? 'selected' : ''; ?>>Hoàn thành</option>
                    </select>
                </div>
                <div style="flex: 1; min-width: 130px;">
                    <label style="font-weight: 600; font-size: 14px; margin-bottom: 5px; display: block;">Từ ngày:</label>
                    <input type="date" class="form-control" name="ngay_dat_tu" value="<?php echo htmlspecialchars($ngay_dat_tu); ?>"
                        style="width: 100%; padding: 6px 10px; border: 1px solid #ced4da; border-radius: 4px;">
                </div>
                <div style="flex: 1; min-width: 130px;">
                    <label style="font-weight: 600; font-size: 14px; margin-bottom: 5px; display: block;">Đến ngày:</label>
                    <input type="date" class="form-control" name="ngay_dat_den" value="<?php echo htmlspecialchars($ngay_dat_den); ?>"
                        style="width: 100%; padding: 6px 10px; border: 1px solid #ced4da; border-radius: 4px;">
                </div>
            </div>

            <div style="margin-top: 20px; text-align: center;">
                <button type="submit" name="btnSearch" style="background: #0d6efd; color: white; padding: 8px 20px; border: none; border-radius: 4px; cursor: pointer; margin: 0 5px;">
                    <i class="bi bi-search"></i> Tìm kiếm
                </button>
                <a href="them.php" style="background: #198754; color: white; padding: 8px 20px; border: none; border-radius: 4px; text-decoration: none; margin: 0 5px; display: inline-block;">
                    <i class="bi bi-plus-circle"></i> Thêm mới
                </a>
            </div>
        </form>
    </div>

    <h3 style="text-align: center; margin-bottom: 20px; color: #34495e; font-size: 20px;">DANH SÁCH VÉ TÀU</h3>
    <div style="overflow-x: auto;">
        <table class="table" style="width: 100%; border-collapse: collapse; margin-bottom: 1rem;">
            <thead>
                <tr style="background-color: #4a90e2; color: white;">
                    <th style="padding: 12px; border: 1px solid #dee2e6; text-align: center;">Mã vé</th>
                    <th style="padding: 12px; border: 1px solid #dee2e6; text-align: center;">Khách hàng</th>
                    <th style="padding: 12px; border: 1px solid #dee2e6; text-align: center;">SĐT</th>
                    <th style="padding: 12px; border: 1px solid #dee2e6; text-align: center;">Mã lịch trình</th>
                    <th style="padding: 12px; border: 1px solid #dee2e6; text-align: center;">Ghế</th>
                    <th style="padding: 12px; border: 1px solid #dee2e6; text-align: center;">Ngày đặt</th>
                    <th style="padding: 12px; border: 1px solid #dee2e6; text-align: center;">Giá vé</th>
                    <th style="padding: 12px; border: 1px solid #dee2e6; text-align: center;">Trạng thái</th>
                    <th style="padding: 12px; border: 1px solid #dee2e6; text-align: center;">Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($data_search) > 0): foreach ($data_search as $row): ?>
                        <tr style="border-bottom: 1px solid #dee2e6;">
                            <td style="padding: 10px; border: 1px solid #dee2e6; text-align: center;"><?php echo htmlspecialchars($row['ma_ve']); ?></td>
                            <td style="padding: 10px; border: 1px solid #dee2e6;"><?php echo htmlspecialchars($row['ten_khach_hang']); ?></td>
                            <td style="padding: 10px; border: 1px solid #dee2e6; text-align: center;"><?php echo htmlspecialchars($row['sdt']); ?></td>
                            <td style="padding: 10px; border: 1px solid #dee2e6; text-align: center;"><?php echo htmlspecialchars($row['ma_lich_trinh']); ?></td>
                            <td style="padding: 10px; border: 1px solid #dee2e6; text-align: center;"><?php echo htmlspecialchars($row['ten_ghe']); ?></td>
                            <td style="padding: 10px; border: 1px solid #dee2e6; text-align: center;"><?php echo !empty($row['ngay_dat']) ? htmlspecialchars(date('d/m/Y H:i', strtotime($row['ngay_dat']))) : ''; ?></td>
                            <td style="padding: 10px; border: 1px solid #dee2e6; text-align: center; font-weight: bold; color: #d35400;">
                                <?php echo number_format($row['gia_ve'], 0, ',', '.') . ' đ'; ?>
                            </td>
                            <td style="padding: 10px; border: 1px solid #dee2e6; text-align: center;">
                                <?php
                                $color = '#6c757d';
                                if ($row['trang_thai'] == 'Đã xác nhận') $color = '#198754';
                                if ($row['trang_thai'] == 'Chờ xác nhận') $color = '#ffc107';
                                if ($row['trang_thai'] == 'Đã hủy') $color = '#dc3545';
                                if ($row['trang_thai'] == 'Hoàn thành') $color = '#0dcaf0';
                                ?>
                                <span style="background-color: <?php echo $color; ?>; color: <?php echo ($row['trang_thai'] == 'Chờ xác nhận') ? '#000' : '#fff'; ?>; padding: 4px 10px; border-radius: 15px; font-size: 13px;">
                                    <?php echo htmlspecialchars($row['trang_thai']); ?>
                                </span>
                            </td>
                            <td style="padding: 10px; border: 1px solid #dee2e6; text-align: center;">
                                <a href="sua.php?id=<?php echo $row['id']; ?>" title="Cập nhật" style="color: #0d6efd; margin-right: 10px; font-size: 18px;"><i class="bi bi-pencil-square"></i></a>
                                <a href="xoa.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa vé này?')" title="Xóa" style="color: #dc3545; font-size: 18px;"><i class="bi bi-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach;
                else: ?>
                    <tr>
                        <td colspan="9" style="padding: 20px; text-align: center; color: #6c757d;">Không tìm thấy dữ liệu</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

<?php
require_once '../../includes/footer.php';
?>