<?php
require_once '../../config/database.php';
require_once '../../includes/header.php';


// Khởi tạo các biến tìm kiếm
$id = isset($_POST['id']) ? $_POST['id'] : '';
$cccd = isset($_POST['cccd']) ? $_POST['cccd'] : '';
$ho_ten = isset($_POST['ho_ten']) ? $_POST['ho_ten'] : '';
$ngay_sinh = isset($_POST['ngay_sinh']) ? $_POST['ngay_sinh'] : '';
$gioi_tinh = isset($_POST['gioi_tinh']) ? $_POST['gioi_tinh'] : '';
$sdt = isset($_POST['sdt']) ? $_POST['sdt'] : '';
$dia_chi = isset($_POST['dia_chi']) ? $_POST['dia_chi'] : '';

// Xử lý tìm kiếm
$sql = "SELECT * FROM khach_hang WHERE 1=1";
$params = [];

if (!empty($id)) {
    $sql .= " AND id LIKE ?";
    $params[] = "%$id%";
}
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
    <title>Quản lý Khách hàng</title>
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
        .gender-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
        }
        .gender-male {
            background-color: #e3f2fd;
            color: #1976d2;
        }
        .gender-female {
            background-color: #fce4ec;
            color: #c2185b;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center mb-4">QUẢN LÝ KHÁCH HÀNG</h2>
        
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
                            <label for="cccd" class="form-label">CCCD:</label>
                            <input type="text" class="form-control" name="cccd" value="<?php echo htmlspecialchars($cccd); ?>" placeholder="Nhập số CCCD">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="ho_ten" class="form-label">Họ và Tên:</label>
                            <input type="text" class="form-control" name="ho_ten" value="<?php echo htmlspecialchars($ho_ten); ?>" placeholder="Nhập họ tên">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="sdt" class="form-label">Số điện thoại:</label>
                            <input type="text" class="form-control" name="sdt" value="<?php echo htmlspecialchars($sdt); ?>" placeholder="Nhập số điện thoại">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="ngay_sinh" class="form-label">Ngày sinh:</label>
                            <input type="date" class="form-control" name="ngay_sinh" value="<?php echo htmlspecialchars($ngay_sinh); ?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="gioi_tinh" class="form-label">Giới tính:</label>
                            <select class="form-control" name="gioi_tinh">
                                <option value="">Tất cả</option>
                                <option value="Nam" <?php echo $gioi_tinh == 'Nam' ? 'selected' : ''; ?>>Nam</option>
                                <option value="Nữ" <?php echo $gioi_tinh == 'Nữ' ? 'selected' : ''; ?>>Nữ</option>
                                <option value="Khác" <?php echo $gioi_tinh == 'Khác' ? 'selected' : ''; ?>>Khác</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="dia_chi" class="form-label">Địa chỉ:</label>
                            <input type="text" class="form-control" name="dia_chi" value="<?php echo htmlspecialchars($dia_chi); ?>" placeholder="Nhập địa chỉ">
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
            <h3 class="text-center mb-4">DANH SÁCH KHÁCH HÀNG</h3>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-primary">
                        <tr>
                            <th width="5%">STT</th>
                            <th width="5%">ID</th>
                            <th width="15%">CCCD</th>
                            <th width="20%">Họ và Tên</th>
                            <th width="10%">Ngày sinh</th>
                            <th width="10%">Giới tính</th>
                            <th width="12%">Số điện thoại</th>
                            <th width="18%">Địa chỉ</th>
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
                                    <td><?php echo htmlspecialchars($row['cccd']); ?></td>
                                    <td><?php echo htmlspecialchars($row['ho_ten']); ?></td>
                                    <td><?php echo !empty($row['ngay_sinh']) ? htmlspecialchars(date('d/m/Y', strtotime($row['ngay_sinh']))) : ''; ?></td>
                                    <td>
                                        <?php
                                        $gender_class = '';
                                        switch($row['gioi_tinh']) {
                                            case 'Nam': $gender_class = 'gender-male'; break;
                                            case 'Nữ': $gender_class = 'gender-female'; break;
                                            default: $gender_class = 'bg-secondary text-white';
                                        }
                                        ?>
                                        <span class="gender-badge <?php echo $gender_class; ?>">
                                            <?php echo htmlspecialchars($row['gioi_tinh']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['sdt']); ?></td>
                                    <td><?php echo htmlspecialchars($row['dia_chi']); ?></td>
                                    <td>
                                        <div class="btn-action">
                                            <a class="btn btn-sm btn-primary" href="sua.php?id=<?php echo $row['id']; ?>" title="Cập nhật">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a class="btn btn-sm btn-danger" href="xoa.php?id=<?php echo $row['id']; ?>" 
                                               onclick="return confirm('Bạn có chắc chắn muốn xóa khách hàng này?')" title="Xóa">
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