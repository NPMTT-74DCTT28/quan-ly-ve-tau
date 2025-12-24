<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../includes/header.php';

if (!isset($_SESSION['user'])) {
    header("Location: " . BASE_URL . "modules/auth/dang_nhap.php");
    exit();
}

$conn = $db->getConnection();

/* =========================
   LẤY DỮ LIỆU DROPDOWN
========================= */
$tau_list = $conn->query(
    "SELECT id, ma_tau, ten_tau FROM tau ORDER BY ma_tau"
)->fetchAll(PDO::FETCH_ASSOC);

$tuyen_duong_list = $conn->query(
    "SELECT id, ma_tuyen, ten_tuyen FROM tuyen_duong ORDER BY ma_tuyen"
)->fetchAll(PDO::FETCH_ASSOC);

/* =========================
   NHẬN GIÁ TRỊ TÌM KIẾM
========================= */
$ma_lich_trinh   = $_POST['ma_lich_trinh'] ?? '';
$id_tau          = $_POST['id_tau'] ?? '';
$id_tuyen_duong  = $_POST['id_tuyen_duong'] ?? '';
$ngay_di         = $_POST['ngay_di'] ?? '';
$ngay_den        = $_POST['ngay_den'] ?? '';
$trang_thai      = $_POST['trang_thai'] ?? '';

/* =========================
   XÂY DỰNG SQL TÌM KIẾM
========================= */
$sql = "SELECT l.*, t.ma_tau, t.ten_tau, td.ma_tuyen, td.ten_tuyen
        FROM lich_trinh l
        LEFT JOIN tau t ON l.id_tau = t.id
        LEFT JOIN tuyen_duong td ON l.id_tuyen_duong = td.id
        WHERE 1=1";
$params = [];

if ($ma_lich_trinh !== '') {
    $sql .= " AND l.ma_lich_trinh LIKE ?";
    $params[] = "%$ma_lich_trinh%";
}
if ($id_tau !== '') {
    $sql .= " AND l.id_tau = ?";
    $params[] = $id_tau;
}
if ($id_tuyen_duong !== '') {
    $sql .= " AND l.id_tuyen_duong = ?";
    $params[] = $id_tuyen_duong;
}
if ($ngay_di !== '') {
    $sql .= " AND DATE(l.ngay_di) = ?";
    $params[] = $ngay_di;
}
if ($ngay_den !== '') {
    $sql .= " AND DATE(l.ngay_den) = ?";
    $params[] = $ngay_den;
}
if ($trang_thai !== '') {
    $sql .= " AND l.trang_thai = ?";
    $params[] = $trang_thai;
}

$sql .= " ORDER BY l.ngay_di DESC";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$data_search = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="main-content">
    <h1>QUẢN LÝ LỊCH TRÌNH</h1>

    <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 25px; border: 1px solid #e9ecef;">
        <form method="post">
            <div class="row" style="display: flex; flex-wrap: wrap; gap: 15px;">
                <div style="flex: 1; min-width: 150px;">
                    <label style="font-weight: 600; font-size: 14px; margin-bottom: 5px; display: block;">Mã lịch trình</label>
                    <input type="text" class="form-control" name="ma_lich_trinh" value="<?= htmlspecialchars($ma_lich_trinh) ?>"
                        style="width: 100%; padding: 6px 10px; border: 1px solid #ced4da; border-radius: 4px;">
                </div>
                <div style="flex: 1; min-width: 200px;">
                    <label style="font-weight: 600; font-size: 14px; margin-bottom: 5px; display: block;">Tàu</label>
                    <select class="form-control" name="id_tau" style="width: 100%; padding: 6px 10px; border: 1px solid #ced4da; border-radius: 4px;">
                        <option value="">-- Tất cả --</option>
                        <?php foreach ($tau_list as $tau): ?>
                            <option value="<?= $tau['id'] ?>" <?= $id_tau == $tau['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($tau['ma_tau'] . ' - ' . $tau['ten_tau']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div style="flex: 1; min-width: 200px;">
                    <label style="font-weight: 600; font-size: 14px; margin-bottom: 5px; display: block;">Tuyến đường</label>
                    <select class="form-control" name="id_tuyen_duong" style="width: 100%; padding: 6px 10px; border: 1px solid #ced4da; border-radius: 4px;">
                        <option value="">-- Tất cả --</option>
                        <?php foreach ($tuyen_duong_list as $tuyen): ?>
                            <option value="<?= $tuyen['id'] ?>" <?= $id_tuyen_duong == $tuyen['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($tuyen['ma_tuyen'] . ' - ' . $tuyen['ten_tuyen']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div style="flex: 1; min-width: 150px;">
                    <label style="font-weight: 600; font-size: 14px; margin-bottom: 5px; display: block;">Ngày đi</label>
                    <input type="date" class="form-control" name="ngay_di" value="<?= htmlspecialchars($ngay_di) ?>"
                        style="width: 100%; padding: 6px 10px; border: 1px solid #ced4da; border-radius: 4px;">
                </div>
                <div style="flex: 1; min-width: 150px;">
                    <label style="font-weight: 600; font-size: 14px; margin-bottom: 5px; display: block;">Ngày đến</label>
                    <input type="date" class="form-control" name="ngay_den" value="<?= htmlspecialchars($ngay_den) ?>"
                        style="width: 100%; padding: 6px 10px; border: 1px solid #ced4da; border-radius: 4px;">
                </div>
                <div style="flex: 1; min-width: 150px;">
                    <label style="font-weight: 600; font-size: 14px; margin-bottom: 5px; display: block;">Trạng thái</label>
                    <select class="form-control" name="trang_thai" style="width: 100%; padding: 6px 10px; border: 1px solid #ced4da; border-radius: 4px;">
                        <option value="">Tất cả</option>
                        <?php foreach (['Chưa chạy', 'Đang chạy', 'Đã hoàn thành', 'Hủy'] as $tt): ?>
                            <option value="<?= $tt ?>" <?= $trang_thai == $tt ? 'selected' : '' ?>><?= $tt ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div style="margin-top: 20px; text-align: center;">
                <button type="submit" style="background: #0d6efd; color: white; padding: 8px 20px; border: none; border-radius: 4px; cursor: pointer; margin: 0 5px;">
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

    <h3 style="text-align: center; margin-bottom: 20px; color: #34495e; font-size: 20px;">DANH SÁCH LỊCH TRÌNH</h3>
    <div style="overflow-x: auto;">
        <table class="table" style="width: 100%; border-collapse: collapse; margin-bottom: 1rem;">
            <thead>
                <tr style="background-color: #4a90e2; color: white;">
                    <th style="padding: 12px; border: 1px solid #dee2e6; text-align: center;">Mã</th>
                    <th style="padding: 12px; border: 1px solid #dee2e6; text-align: center;">Tàu</th>
                    <th style="padding: 12px; border: 1px solid #dee2e6; text-align: center;">Tuyến</th>
                    <th style="padding: 12px; border: 1px solid #dee2e6; text-align: center;">Ngày đi</th>
                    <th style="padding: 12px; border: 1px solid #dee2e6; text-align: center;">Ngày đến</th>
                    <th style="padding: 12px; border: 1px solid #dee2e6; text-align: center;">Trạng thái</th>
                    <th style="padding: 12px; border: 1px solid #dee2e6; text-align: center;">Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($data_search): foreach ($data_search as $row): ?>
                        <tr style="border-bottom: 1px solid #dee2e6;">
                            <td style="padding: 10px; border: 1px solid #dee2e6; text-align: center;"><?= htmlspecialchars($row['ma_lich_trinh']) ?></td>
                            <td style="padding: 10px; border: 1px solid #dee2e6; text-align: center;"><?= htmlspecialchars($row['ma_tau'] . ' - ' . $row['ten_tau']) ?></td>
                            <td style="padding: 10px; border: 1px solid #dee2e6; text-align: center;"><?= htmlspecialchars($row['ma_tuyen'] . ' - ' . $row['ten_tuyen']) ?></td>
                            <td style="padding: 10px; border: 1px solid #dee2e6; text-align: center;"><?= date('d/m/Y H:i', strtotime($row['ngay_di'])) ?></td>
                            <td style="padding: 10px; border: 1px solid #dee2e6; text-align: center;"><?= date('d/m/Y H:i', strtotime($row['ngay_den'])) ?></td>
                            <td style="padding: 10px; border: 1px solid #dee2e6; text-align: center;">
                                <?php
                                $bg_color = match ($row['trang_thai']) {
                                    'Chưa chạy' => '#ffc107', // warning
                                    'Đang chạy' => '#198754', // success
                                    'Đã hoàn thành' => '#0dcaf0', // info
                                    'Hủy' => '#dc3545', // danger
                                    default => '#6c757d'
                                };
                                $text_color = ($row['trang_thai'] == 'Chưa chạy' || $row['trang_thai'] == 'Đã hoàn thành') ? '#000' : '#fff';
                                ?>
                                <span style="background-color: <?= $bg_color ?>; color: <?= $text_color ?>; padding: 4px 10px; border-radius: 15px; font-size: 13px;">
                                    <?= htmlspecialchars($row['trang_thai']) ?>
                                </span>
                            </td>
                            <td style="padding: 10px; border: 1px solid #dee2e6; text-align: center;">
                                <a href="Edit.php?id=<?= $row['id'] ?>" title="Cập nhật" style="color: #0d6efd; margin-right: 10px; font-size: 18px;">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <a href="Delete.php?id=<?= $row['id'] ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa?')" title="Xóa" style="color: #dc3545; font-size: 18px;">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach;
                else: ?>
                    <tr>
                        <td colspan="8" style="padding: 20px; text-align: center; color: #6c757d;">Không tìm thấy dữ liệu</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>