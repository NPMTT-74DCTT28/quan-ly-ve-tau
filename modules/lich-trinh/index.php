<?php
include_once '../../config/database.php';
require_once '../../includes/header.php';


// Lấy danh sách tàu cho dropdown
$sql_tau = "SELECT id, ma_tau, ten_tau FROM tau ORDER BY ma_tau";
$stmt_tau = $pdo->query($sql_tau);
$tau_list = $stmt_tau->fetchAll(PDO::FETCH_ASSOC);

// Lấy danh sách tuyến đường cho dropdown
$sql_tuyen_duong = "SELECT id, ma_tuyen, ten_tuyen FROM tuyen_duong ORDER BY ma_tuyen";
$stmt_tuyen_duong = $pdo->query($sql_tuyen_duong);
$tuyen_duong_list = $stmt_tuyen_duong->fetchAll(PDO::FETCH_ASSOC);

// Khởi tạo các biến tìm kiếm
$id = isset($_POST['id']) ? $_POST['id'] : '';
$ma_lich_trinh = isset($_POST['ma_lich_trinh']) ? $_POST['ma_lich_trinh'] : '';
$id_tau = isset($_POST['id_tau']) ? $_POST['id_tau'] : '';
$id_tuyen_duong = isset($_POST['id_tuyen_duong']) ? $_POST['id_tuyen_duong'] : '';
$ngay_di = isset($_POST['ngay_di']) ? $_POST['ngay_di'] : '';
$ngay_den = isset($_POST['ngay_den']) ? $_POST['ngay_den'] : '';
$trang_thai = isset($_POST['trang_thai']) ? $_POST['trang_thai'] : '';

// Xử lý tìm kiếm
$sql = "SELECT l.*, t.ma_tau, t.ten_tau, td.ma_tuyen, td.ten_tuyen 
        FROM lich_trinh l
        LEFT JOIN tau t ON l.id_tau = t.id
        LEFT JOIN tuyen_duong td ON l.id_tuyen_duong = td.id
        WHERE 1=1";
$params = [];

if (!empty($id)) {
    $sql .= " AND l.id LIKE ?";
    $params[] = "%$id%";
}
if (!empty($ma_lich_trinh)) {
    $sql .= " AND l.ma_lich_trinh LIKE ?";
    $params[] = "%$ma_lich_trinh%";
}
if (!empty($id_tau)) {
    $sql .= " AND l.id_tau = ?";
    $params[] = $id_tau;
}
if (!empty($id_tuyen_duong)) {
    $sql .= " AND l.id_tuyen_duong = ?";
    $params[] = $id_tuyen_duong;
}
if (!empty($ngay_di)) {
    $sql .= " AND DATE(l.ngay_di) = ?";
    $params[] = $ngay_di;
}
if (!empty($ngay_den)) {
    $sql .= " AND DATE(l.ngay_den) = ?";
    $params[] = $ngay_den;
}
if (!empty($trang_thai)) {
    $sql .= " AND l.trang_thai LIKE ?";
    $params[] = "%$trang_thai%";
}

$sql .= " ORDER BY l.ngay_di DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$data_search = $stmt->fetchAll();

if (isset($_POST['btnAdd'])) {
    header('location:Add.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý lịch trình</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        .container {
            max-width: 1400px;
            margin-top: 30px;
        }
        .search-form {
            background-color: #f8f9fa;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 15px;
        }
        .action-buttons {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            justify-content: center;
            margin-top: 20px;
        }
        .action-buttons .btn {
            min-width: 130px;
            padding: 10px 20px;
        }
        .table-container {
            margin-bottom: 20px;
            background-color: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-top: 20px;
        }
        .table th {
            background-color: #4a90e2;
            color: white;
            text-align: center;
            vertical-align: middle;
            font-weight: 600;
        }
        .table td {
            vertical-align: middle;
            text-align: center;
        }
        h2.text-center {
            color: #2c3e50;
            margin-bottom: 30px;
            font-weight: 700;
        }
        .form-label {
            font-weight: 600;
            color: #34495e;
        }
        .btn-action {
            display: flex;
            gap: 8px;
            justify-content: center;
        }
        .btn-sm {
            padding: 5px 12px;
            font-size: 14px;
        }
        .table-responsive {
            border-radius: 8px;
            overflow: hidden;
        }
        .search-form{
            background-color: #81aad3ff;
        }
        .table-container{
            background-color: #81aad3ff;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center mb-4">QUẢN LÝ LỊCH TRÌNH</h2>
        
        <div class="search-form">
            <form method="post" action="">
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="id" class="form-label">ID:</label>
                            <input type="text" class="form-control" name="id" value="<?php echo htmlspecialchars($id); ?>" placeholder="Nhập ID">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="ma_lich_trinh" class="form-label">Mã Lịch Trình:</label>
                            <input type="text" class="form-control" name="ma_lich_trinh" value="<?php echo htmlspecialchars($ma_lich_trinh); ?>" placeholder="Nhập mã lịch trình">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="id_tau" class="form-label">Tàu:</label>
                            <select class="form-control" name="id_tau">
                                <option value="">-- Tất cả tàu --</option>
                                <?php foreach ($tau_list as $tau): ?>
                                    <option value="<?php echo $tau['id']; ?>" <?php echo $id_tau == $tau['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($tau['ma_tau'] . ' - ' . $tau['ten_tau']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="id_tuyen_duong" class="form-label">Tuyến Đường:</label>
                            <select class="form-control" name="id_tuyen_duong">
                                <option value="">-- Tất cả tuyến đường --</option>
                                <?php foreach ($tuyen_duong_list as $tuyen): ?>
                                    <option value="<?php echo $tuyen['id']; ?>" <?php echo $id_tuyen_duong == $tuyen['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($tuyen['ma_tuyen'] . ' - ' . $tuyen['ten_tuyen']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="ngay_di" class="form-label">Ngày Đi:</label>
                            <input type="date" class="form-control" name="ngay_di" value="<?php echo htmlspecialchars($ngay_di); ?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="ngay_den" class="form-label">Ngày Đến:</label>
                            <input type="date" class="form-control" name="ngay_den" value="<?php echo htmlspecialchars($ngay_den); ?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="trang_thai" class="form-label">Trạng Thái:</label>
                            <select class="form-control" name="trang_thai">
                                <option value="">Tất cả</option>
                                <option value="Chưa chạy" <?php echo $trang_thai == 'Chưa chạy' ? 'selected' : ''; ?>>Chưa chạy</option>
                                <option value="Đang chạy" <?php echo $trang_thai == 'Đang chạy' ? 'selected' : ''; ?>>Đang chạy</option>
                                <option value="Đã hoàn thành" <?php echo $trang_thai == 'Đã hoàn thành' ? 'selected' : ''; ?>>Đã hoàn thành</option>
                                <option value="Hủy" <?php echo $trang_thai == 'Hủy' ? 'selected' : ''; ?>>Hủy</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="action-buttons">
                    <button type="submit" class="btn btn-primary" name="btnSearch">
                        <i class="bi bi-search"></i> Tìm kiếm
                    </button>
                    <button type="submit" class="btn btn-success" name="btnAdd">
                        <i class="bi bi-plus-circle"></i> Thêm mới
                    </button>
                    <a href="Export.php" class="btn btn-info text-white">
                        <i class="bi bi-file-earmark-excel"></i> Xuất Excel
                    </a>
                    <a href="Import.php" class="btn btn-warning">
                        <i class="bi bi-upload"></i> Nhập Excel
                    </a>
                </div>
            </form>
        </div>
        
        <div class="table-container">
            <h3 class="text-center mb-4">DANH SÁCH LỊCH TRÌNH</h3>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-primary">
                        <tr>
                            <th width="5%">STT</th>
                            <th width="10%">ID</th>
                            <th width="15%">Mã Lịch Trình</th>
                            <th width="15%">Tàu</th>
                            <th width="15%">Tuyến Đường</th>
                            <th width="12%">Ngày Đi</th>
                            <th width="12%">Ngày Đến</th>
                            <th width="10%">Trạng Thái</th>
                            <th width="10%">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (count($data_search) > 0) {
                            $i = 1;
                            foreach ($data_search as $row) {
                        ?>
                                <tr>
                                    <td class="text-center"><?php echo $i++; ?></td>
                                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                                    <td><?php echo htmlspecialchars($row['ma_lich_trinh']); ?></td>
                                    <td>
                                        <?php 
                                        echo htmlspecialchars($row['ma_tau'] . ' - ' . $row['ten_tau']);
                                        ?>
                                    </td>
                                    <td>
                                        <?php 
                                        echo htmlspecialchars($row['ma_tuyen'] . ' - ' . $row['ten_tuyen']);
                                        ?>
                                    </td>
                                    <td><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($row['ngay_di']))); ?></td>
                                    <td><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($row['ngay_den']))); ?></td>
                                    <td>
                                        <?php
                                        $badge_color = '';
                                        switch($row['trang_thai']) {
                                            case 'Chưa chạy': $badge_color = 'warning'; break;
                                            case 'Đang chạy': $badge_color = 'success'; break;
                                            case 'Đã hoàn thành': $badge_color = 'info'; break;
                                            case 'Hủy': $badge_color = 'danger'; break;
                                            default: $badge_color = 'secondary';
                                        }
                                        ?>
                                        <span class="badge bg-<?php echo $badge_color; ?>">
                                            <?php echo htmlspecialchars($row['trang_thai']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-action">
                                            <a class="btn btn-sm btn-primary" href="Edit.php?id=<?php echo $row['id']; ?>" title="Cập nhật">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a class="btn btn-sm btn-danger" href="Delete.php?id=<?php echo $row['id']; ?>" 
                                               onclick="return confirm('Bạn có chắc chắn muốn xóa lịch trình này?')" title="Xóa">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                        <?php
                            }
                        } else {
                            echo "<tr><td colspan='9' class='text-center text-muted'>Không tìm thấy dữ liệu</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Thêm Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btnAdd = document.querySelector('button[name="btnAdd"]');
            if (btnAdd) {
                btnAdd.addEventListener('click', function(e) {
                    e.preventDefault();
                    window.location.href = 'Add.php';
                });
            }
        });
    </script>
</body>
<?php
require_once '../../includes/footer.php';
?>
</html>