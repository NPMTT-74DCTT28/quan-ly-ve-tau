<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../includes/header.php';

requireLogin();
requireAdmin();

$conn = $db->getConnection();

/* =========================
   LẤY DỮ LIỆU DROPDOWN CHO FILTER
========================= */
$tau_list = $conn->query("SELECT id, ma_tau, ten_tau FROM tau ORDER BY ma_tau")->fetchAll(PDO::FETCH_ASSOC);
$loai_toa_list = $conn->query("SELECT id, ten_loai FROM loai_toa ORDER BY ten_loai")->fetchAll(PDO::FETCH_ASSOC);

/* =========================
   NHẬN GIÁ TRỊ TÌM KIẾM
========================= */
$ma_toa      = $_POST['ma_toa'] ?? '';
$id_tau      = $_POST['id_tau'] ?? '';
$id_loai_toa = $_POST['id_loai_toa'] ?? '';

/* =========================
   XÂY DỰNG SQL TÌM KIẾM
========================= */
$sql = "SELECT tt.*, t.ma_tau, t.ten_tau, lt.ten_loai 
        FROM toa_tau tt
        LEFT JOIN tau t ON tt.id_tau = t.id
        LEFT JOIN loai_toa lt ON tt.id_loai_toa = lt.id
        WHERE 1=1";
$params = [];

if ($ma_toa !== '') {
    $sql .= " AND tt.ma_toa LIKE ?";
    $params[] = "%$ma_toa%";
}
if ($id_tau !== '') {
    $sql .= " AND tt.id_tau = ?";
    $params[] = $id_tau;
}
if ($id_loai_toa !== '') {
    $sql .= " AND tt.id_loai_toa = ?";
    $params[] = $id_loai_toa;
}

$sql .= " ORDER BY t.ma_tau, tt.ma_toa";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$data_search = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="main-content">
    <h1>QUẢN LÝ TOA TÀU</h1>

    <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 25px; border: 1px solid #e9ecef;">
        <form method="post">
            <div class="row" style="display: flex; flex-wrap: wrap; gap: 15px; justify-content: center;">
                <div style="flex: 1; min-width: 150px;">
                    <label style="font-weight: 600; font-size: 14px; margin-bottom: 5px; display: block;">Mã toa</label>
                    <input type="text" class="form-control" name="ma_toa" value="<?= htmlspecialchars($ma_toa) ?>" placeholder="VD: Toa 1"
                        style="width: 100%; padding: 6px 10px; border: 1px solid #ced4da; border-radius: 4px;">
                </div>
                <div style="flex: 1; min-width: 200px;">
                    <label style="font-weight: 600; font-size: 14px; margin-bottom: 5px; display: block;">Thuộc tàu</label>
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
                    <label style="font-weight: 600; font-size: 14px; margin-bottom: 5px; display: block;">Loại toa</label>
                    <select class="form-control" name="id_loai_toa" style="width: 100%; padding: 6px 10px; border: 1px solid #ced4da; border-radius: 4px;">
                        <option value="">-- Tất cả --</option>
                        <?php foreach ($loai_toa_list as $loai): ?>
                            <option value="<?= $loai['id'] ?>" <?= $id_loai_toa == $loai['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($loai['ten_loai']) ?>
                            </option>
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
            </div>
        </form>
    </div>

    <h3 style="text-align: center; margin-bottom: 20px; color: #34495e; font-size: 20px;">DANH SÁCH TOA TÀU</h3>
    <div style="overflow-x: auto;">
        <table class="table" style="width: 100%; border-collapse: collapse; margin-bottom: 1rem;">
            <thead>
                <tr style="background-color: #4a90e2; color: white;">
                    <th style="padding: 12px; border: 1px solid #dee2e6; text-align: center;">STT</th>
                    <th style="padding: 12px; border: 1px solid #dee2e6; text-align: center;">Mã toa</th>
                    <th style="padding: 12px; border: 1px solid #dee2e6; text-align: center;">Thuộc tàu</th>
                    <th style="padding: 12px; border: 1px solid #dee2e6; text-align: center;">Loại toa</th>
                    <th style="padding: 12px; border: 1px solid #dee2e6; text-align: center;">Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($data_search): $i = 1;
                    foreach ($data_search as $row): ?>
                        <tr style="border-bottom: 1px solid #dee2e6;">
                            <td style="padding: 10px; border: 1px solid #dee2e6; text-align: center;"><?= $i++ ?></td>
                            <td style="padding: 10px; border: 1px solid #dee2e6; text-align: center;"><?= htmlspecialchars($row['ma_toa']) ?></td>
                            <td style="padding: 10px; border: 1px solid #dee2e6; text-align: center;"><?= htmlspecialchars($row['ma_tau'] . ' - ' . $row['ten_tau']) ?></td>
                            <td style="padding: 10px; border: 1px solid #dee2e6; text-align: center;">
                                <span style="background-color: #0dcaf0; color: #000; padding: 4px 10px; border-radius: 15px; font-size: 13px;">
                                    <?= htmlspecialchars($row['ten_loai']) ?>
                                </span>
                            </td>
                            <td style="padding: 10px; border: 1px solid #dee2e6; text-align: center;">
                                <a href="Edit.php?id=<?= $row['id'] ?>" title="Cập nhật" style="color: #0d6efd; margin-right: 10px; font-size: 18px;">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <a href="Delete.php?id=<?= $row['id'] ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa toa này?')" title="Xóa" style="color: #dc3545; font-size: 18px;">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach;
                else: ?>
                    <tr>
                        <td colspan="5" style="padding: 20px; text-align: center; color: #6c757d;">Không tìm thấy dữ liệu</td>
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