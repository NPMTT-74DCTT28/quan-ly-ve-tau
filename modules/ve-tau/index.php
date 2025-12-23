<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../includes/header.php';
$conn = $db->getConnection();

// Khởi tạo các biến tìm kiếm
$ma_ve = isset($_POST['ma_ve']) ? $_POST['ma_ve'] : '';
$ten_khach = isset($_POST['ten_khach']) ? $_POST['ten_khach'] : '';
$ma_lich_trinh = isset($_POST['ma_lich_trinh']) ? $_POST['ma_lich_trinh'] : '';
$trang_thai = isset($_POST['trang_thai']) ? $_POST['trang_thai'] : '';
$ngay_dat_tu = isset($_POST['ngay_dat_tu']) ? $_POST['ngay_dat_tu'] : '';
$ngay_dat_den = isset($_POST['ngay_dat_den']) ? $_POST['ngay_dat_den'] : '';

// Xử lý tìm kiếm
$sql = "SELECT 
            vt.*,
            kh.ho_ten as ten_khach_hang,
            kh.sdt,
            lt.ma_lich_trinh,
            CONCAT(g.so_ghe, ' (', t.ma_toa, ')') as ten_ghe,
            nv.ho_ten as ten_nhan_vien
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

if (isset($_POST['btnAdd'])) {
    header('location:them_ve_tau.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Vé Tàu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .container {
            max-width: 1600px;
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
        .table-container {
            background-color: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-top: 20px;
        }
        .table th {
            background-color: #28a745;
            color: white;
            text-align: center;
            vertical-align: middle;
        }
        .table td {
            vertical-align: middle;
        }
        h2.text-center {
            color: #2c3e50;
            margin-bottom: 30px;
            font-weight: 700;
        }
        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
        }
        .status-confirmed {
            background-color: #d4edda;
            color: #155724;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }
        .status-completed {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        .search-form {
            background-color: #a8e6cf;
        }
        .table-container {
            background-color: #a8e6cf;
        }
        .price-cell {
            font-weight: bold;
            color: #d35400;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center mb-4">QUẢN LÝ VÉ TÀU</h2>
        
        <div class="search-form">
            <form method="post" action="">
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="ma_ve" class="form-label">Mã vé:</label>
                            <input type="text" class="form-control" name="ma_ve" value="<?php echo htmlspecialchars($ma_ve); ?>" placeholder="Nhập mã vé">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="ten_khach" class="form-label">Tên khách hàng:</label>
                            <input type="text" class="form-control" name="ten_khach" value="<?php echo htmlspecialchars($ten_khach); ?>" placeholder="Nhập tên khách">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="ma_lich_trinh" class="form-label">Mã lịch trình:</label>
                            <input type="text" class="form-control" name="ma_lich_trinh" value="<?php echo htmlspecialchars($ma_lich_trinh); ?>" placeholder="Nhập mã lịch trình">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="trang_thai" class="form-label">Trạng thái:</label>
                            <select class="form-control" name="trang_thai">
                                <option value="">Tất cả</option>
                                <option value="Đã xác nhận" <?php echo $trang_thai == 'Đã xác nhận' ? 'selected' : ''; ?>>Đã xác nhận</option>
                                <option value="Chờ xác nhận" <?php echo $trang_thai == 'Chờ xác nhận' ? 'selected' : ''; ?>>Chờ xác nhận</option>
                                <option value="Đã hủy" <?php echo $trang_thai == 'Đã hủy' ? 'selected' : ''; ?>>Đã hủy</option>
                                <option value="Hoàn thành" <?php echo $trang_thai == 'Hoàn thành' ? 'selected' : ''; ?>>Hoàn thành</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="ngay_dat_tu" class="form-label">Ngày đặt từ:</label>
                            <input type="date" class="form-control" name="ngay_dat_tu" value="<?php echo htmlspecialchars($ngay_dat_tu); ?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="ngay_dat_den" class="form-label">Ngày đặt đến:</label>
                            <input type="date" class="form-control" name="ngay_dat_den" value="<?php echo htmlspecialchars($ngay_dat_den); ?>">
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
                    <a href="export_ve_tau.php" class="btn btn-info text-white">
                        <i class="bi bi-file-earmark-excel"></i> Xuất Excel
                    </a>
                    <a href="import_ve_tau.php" class="btn btn-warning">
                        <i class="bi bi-upload"></i> Nhập Excel
                    </a>
                </div>
            </form>
        </div>
        
        <div class="table-container">
            <h3 class="text-center mb-4">DANH SÁCH VÉ TÀU</h3>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-success">
                        <tr>
                            <th width="8%">Mã vé</th>
                            <th width="15%">Khách hàng</th>
                            <th width="10%">Số điện thoại</th>
                            <th width="10%">Mã lịch trình</th>
                            <th width="10%">Ghế</th>
                            <th width="12%">Ngày đặt</th>
                            <th width="10%">Giá vé</th>
                            <th width="10%">Trạng thái</th>
                            <th width="10%">Nhân viên</th>
                            <th width="5%">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (count($data_search) > 0) {
                            foreach ($data_search as $row) {
                                $status_class = '';
                                switch($row['trang_thai']) {
                                    case 'Đã xác nhận': $status_class = 'status-confirmed'; break;
                                    case 'Chờ xác nhận': $status_class = 'status-pending'; break;
                                    case 'Đã hủy': $status_class = 'status-cancelled'; break;
                                    case 'Hoàn thành': $status_class = 'status-completed'; break;
                                }
                        ?>
                                <tr>
                                    <td class="text-center"><?php echo htmlspecialchars($row['ma_ve']); ?></td>
                                    <td><?php echo htmlspecialchars($row['ten_khach_hang']); ?></td>
                                    <td><?php echo htmlspecialchars($row['sdt']); ?></td>
                                    <td class="text-center"><?php echo htmlspecialchars($row['ma_lich_trinh']); ?></td>
                                    <td class="text-center"><?php echo htmlspecialchars($row['ten_ghe']); ?></td>
                                    <td class="text-center"><?php echo !empty($row['ngay_dat']) ? htmlspecialchars(date('d/m/Y H:i', strtotime($row['ngay_dat']))) : ''; ?></td>
                                    <td class="text-center price-cell"><?php echo number_format($row['gia_ve'], 0, ',', '.') . ' đ'; ?></td>
                                    <td class="text-center">
                                        <span class="status-badge <?php echo $status_class; ?>">
                                            <?php echo htmlspecialchars($row['trang_thai']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['ten_nhan_vien']); ?></td>
                                    <td>
                                        <div class="btn-action d-flex justify-content-center gap-2">
                                            <a class="btn btn-sm btn-primary" href="sua.php?id=<?php echo $row['id']; ?>" title="Sửa">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a class="btn btn-sm btn-danger" href="xoa.php?id=<?php echo $row['id']; ?>" 
                                               onclick="return confirm('Bạn có chắc chắn muốn xóa vé tàu này?')" title="Xóa">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                        <?php
                            }
                        } else {
                            echo "<tr><td colspan='10' class='text-center text-muted'>Không tìm thấy dữ liệu</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btnAdd = document.querySelector('button[name="btnAdd"]');
            if (btnAdd) {
                btnAdd.addEventListener('click', function(e) {
                    e.preventDefault();
                    window.location.href = 'them.php';
                });
            }
        });
    </script>
</body>
<?php
require_once '../../includes/footer.php';
?>
</html>