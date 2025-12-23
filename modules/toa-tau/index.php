<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../includes/header.php';

if (!isset($_SESSION['user'])) {
    header("Location: " . BASE_URL . "modules/auth/dang_nhap.php");
    exit();
}

$conn = $db->getConnection();

/* =========================
   LẤY DỮ LIỆU DROPDOWN CHO FILTER
========================= */
// Lấy danh sách tàu

$tau_list = $conn->query("SELECT id, ma_tau, ten_tau FROM tau ORDER BY ma_tau")->fetchAll(PDO::FETCH_ASSOC);

// Lấy danh sách loại toa
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

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Quản lý toa tàu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .container {
            max-width: 1200px;
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
        <h2 class="text-center mb-4">QUẢN LÝ TOA TÀU</h2>

        <div class="search-form">
            <form method="post">
                <div class="row g-3 justify-content-center">
                    <div class="col-md-3">
                        <label class="form-label">Mã toa</label>
                        <input type="text" class="form-control" name="ma_toa" value="<?= htmlspecialchars($ma_toa) ?>" placeholder="VD: Toa 1">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Thuộc tàu</label>
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
                        <label class="form-label">Loại toa</label>
                        <select class="form-control" name="id_loai_toa">
                            <option value="">-- Tất cả --</option>
                            <?php foreach ($loai_toa_list as $loai): ?>
                                <option value="<?= $loai['id'] ?>" <?= $id_loai_toa == $loai['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($loai['ten_loai']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button class="btn btn-primary w-100"><i class="bi bi-search"></i> Tìm kiếm</button>
                    </div>
                </div>

                <div class="text-center mt-3">
                    <a href="Add.php" class="btn btn-success"><i class="bi bi-plus-circle"></i> Thêm toa mới</a>
                </div>
            </form>
        </div>

        <div class="table-container">
            <h3 class="text-center mb-3">DANH SÁCH TOA TÀU</h3>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Mã toa</th>
                            <th>Thuộc tàu</th>
                            <th>Loại toa</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($data_search): $i = 1;
                            foreach ($data_search as $row): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['ma_toa']) ?></td>
                                    <td><?= htmlspecialchars($row['ma_tau'] . ' - ' . $row['ten_tau']) ?></td>
                                    <td>
                                        <span class="badge bg-info text-dark">
                                            <?= htmlspecialchars($row['ten_loai']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a class="btn btn-sm btn-primary" href="Edit.php?id=<?= $row['id'] ?>"><i class="bi bi-pencil"></i></a>
                                        <a class="btn btn-sm btn-danger"
                                            href="Delete.php?id=<?= $row['id'] ?>"
                                            onclick="return confirm('Bạn có chắc chắn muốn xóa toa này?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach;
                        else: ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted">Không có dữ liệu</td>
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