<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../includes/header.php';

requireLogin();
requireAdmin();

$conn = $db->getConnection();

$tau_list = $conn->query(
    "SELECT id, ma_tau, ten_tau FROM tau ORDER BY ma_tau"
)->fetchAll(PDO::FETCH_ASSOC);

$tuyen_duong_list = $conn->query(
    "SELECT id, ma_tuyen, ten_tuyen FROM tuyen_duong ORDER BY ma_tuyen"
)->fetchAll(PDO::FETCH_ASSOC);

$id              = $_POST['id'] ?? '';
$ma_lich_trinh   = $_POST['ma_lich_trinh'] ?? '';
$id_tau          = $_POST['id_tau'] ?? '';
$id_tuyen_duong  = $_POST['id_tuyen_duong'] ?? '';
$ngay_di         = $_POST['ngay_di'] ?? '';
$ngay_den        = $_POST['ngay_den'] ?? '';
$trang_thai      = $_POST['trang_thai'] ?? '';

$sql = "SELECT l.*, t.ma_tau, t.ten_tau, td.ma_tuyen, td.ten_tuyen
        FROM lich_trinh l
        LEFT JOIN tau t ON l.id_tau = t.id
        LEFT JOIN tuyen_duong td ON l.id_tuyen_duong = td.id
        WHERE 1=1";
$params = [];

if ($id !== '' && is_numeric($id)) {
    $sql .= " AND l.id = ?";
    $params[] = $id;
}
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

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Quản lý lịch trình</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .container {
            max-width: 1400px;
            margin-top: 30px;
        }

        .search-form,
        .table-container {
            background-color: #81aad3ff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .1);
            margin-bottom: 30px;
        }

        h2,
        h3 {
            font-weight: 700;
            color: #2c3e50;
        }

        .form-label {
            font-weight: 600;
        }

        .action-buttons {
            display: flex;
            gap: 12px;
            justify-content: center;
            margin-top: 20px;
            flex-wrap: wrap;
        }

        .table th {
            background: #4a90e2;
            color: #fff;
            text-align: center;
        }

        .table td {
            text-align: center;
            vertical-align: middle;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2 class="text-center mb-4">QUẢN LÝ LỊCH TRÌNH</h2>

        <div class="search-form">
            <form method="post">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Tàu</label>
                        <select class="form-control" name="id_tau">
                            <option value="">-- Tất cả --</option>
                            <?php foreach ($tau_list as $tau): ?>
                                <option value="<?= $tau['id'] ?>" <?= $id_tau == $tau['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($tau['ma_tau'] . ' - ' . $tau['ten_tau']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tuyến đường</label>
                        <select class="form-control" name="id_tuyen_duong">
                            <option value="">-- Tất cả --</option>
                            <?php foreach ($tuyen_duong_list as $tuyen): ?>
                                <option value="<?= $tuyen['id'] ?>" <?= $id_tuyen_duong == $tuyen['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($tuyen['ma_tuyen'] . ' - ' . $tuyen['ten_tuyen']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Ngày đi</label>
                        <input type="date" class="form-control" name="ngay_di" value="<?= htmlspecialchars($ngay_di) ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Ngày đến</label>
                        <input type="date" class="form-control" name="ngay_den" value="<?= htmlspecialchars($ngay_den) ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Trạng thái</label>
                        <select class="form-control" name="trang_thai">
                            <option value="">Tất cả</option>
                            <?php foreach (['Chưa chạy', 'Đang chạy', 'Đã hoàn thành', 'Hủy'] as $tt): ?>
                                <option value="<?= $tt ?>" <?= $trang_thai == $tt ? 'selected' : '' ?>><?= $tt ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="action-buttons">
                    <button class="btn btn-primary"><i class="bi bi-search"></i> Tìm kiếm</button>
                    <a href="Add.php" class="btn btn-success"><i class="bi bi-plus-circle"></i> Thêm mới</a>
                </div>
            </form>
        </div>

        <div class="table-container">
            <h3 class="text-center mb-3">DANH SÁCH LỊCH TRÌNH</h3>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Mã</th>
                            <th>Tàu</th>
                            <th>Tuyến</th>
                            <th>Ngày đi</th>
                            <th>Ngày đến</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($data_search): $i = 1;
                            foreach ($data_search as $row): ?>
                                <tr>

                                    <td><?= $row['id'] ?></td>
                                    <td><?= htmlspecialchars($row['ma_lich_trinh']) ?></td>
                                    <td><?= htmlspecialchars($row['ma_tau'] . ' - ' . $row['ten_tau']) ?></td>
                                    <td><?= htmlspecialchars($row['ma_tuyen'] . ' - ' . $row['ten_tuyen']) ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($row['ngay_di'])) ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($row['ngay_den'])) ?></td>
                                    <td>
                                        <?php
                                        $color = match ($row['trang_thai']) {
                                            'Chưa chạy' => 'warning',
                                            'Đang chạy' => 'success',
                                            'Đã hoàn thành' => 'info',
                                            'Hủy' => 'danger',
                                            default => 'secondary'
                                        };
                                        ?>
                                        <span class="badge bg-<?= $color ?>"><?= $row['trang_thai'] ?></span>
                                    </td>
                                    <td>
                                        <a class="btn btn-sm btn-primary" href="Edit.php?id=<?= $row['id'] ?>"><i class="bi bi-pencil"></i></a>
                                        <a class="btn btn-sm btn-danger"
                                            href="Delete.php?id=<?= $row['id'] ?>"
                                            onclick="return confirm('Bạn có chắc chắn muốn xóa?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach;
                        else: ?>
                            <tr>
                                <td colspan="9" class="text-center text-muted">Không có dữ liệu</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
<?php
require_once __DIR__ . '/../../includes/footer.php'; ?>