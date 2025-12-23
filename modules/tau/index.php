<?php
include_once '../../config/database.php';
require_once '../../includes/header.php';

$conn = getConnection();

$ma_tau = isset($_POST['ma_tau']) ? trim($_POST['ma_tau']) : '';
$ten_tau = isset($_POST['ten_tau']) ? trim($_POST['ten_tau']) : '';

if (isset($_POST['btnAdd'])) {
    header('location:Add.php');
    exit;
}

try {
    $sql = "SELECT * FROM tau WHERE 1=1";
    $params = [];

    if (!empty($ma_tau)) {
        $sql .= " AND ma_tau LIKE ?";
        $params[] = "%$ma_tau%";
    }

    if (!empty($ten_tau)) {
        $sql .= " AND ten_tau LIKE ?";
        $params[] = "%$ten_tau%";
    }

    $sql .= " ORDER BY id DESC";

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $data_search = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Lỗi truy vấn: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Tàu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .container {
            max-width: 1000px;
            margin-top: 30px;
        }

        .search-form,
        .table-container {
            background-color: #81aad3ff;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .form-label {
            font-weight: 600;
            color: #34495e;
        }

        .action-buttons {
            display: flex;
            gap: 12px;
            justify-content: center;
            margin-top: 20px;
        }

        .table th {
            background-color: #4a90e2;
            color: white;
            text-align: center;
        }

        .table td {
            background-color: white;
            vertical-align: middle;
            text-align: center;
        }

        h2.text-center,
        h3.text-center {
            color: #2c3e50;
            font-weight: 700;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2 class="text-center mb-4">QUẢN LÝ TÀU</h2>

        <div class="search-form">
            <form method="post" action="">
                <div class="row g-3 justify-content-center">
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="ma_tau" class="form-label">Mã Tàu:</label>
                            <input type="text" class="form-control" name="ma_tau" value="<?php echo htmlspecialchars($ma_tau); ?>" placeholder="Nhập mã tàu">
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="ten_tau" class="form-label">Tên Tàu:</label>
                            <input type="text" class="form-control" name="ten_tau" value="<?php echo htmlspecialchars($ten_tau); ?>" placeholder="Nhập tên tàu">
                        </div>
                    </div>
                </div>

                <div class="action-buttons">
                    <button type="submit" class="btn btn-primary" name="btnSearch"><i class="bi bi-search"></i> Tìm kiếm</button>
                    <button type="submit" class="btn btn-success" name="btnAdd"><i class="bi bi-plus-circle"></i> Thêm mới</button>
                    <a href="Export.php" class="btn btn-info text-white"><i class="bi bi-file-earmark-excel"></i> Xuất Excel</a>
                    <a href="Import.php" class="btn btn-warning"><i class="bi bi-upload"></i> Nhập Excel</a>
                </div>
            </form>
        </div>

        <div class="table-container">
            <h3 class="text-center mb-4">DANH SÁCH TÀU</h3>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th width="10%">STT</th>
                            <th width="35%">Mã Tàu</th>
                            <th width="35%">Tên Tàu</th>
                            <th width="20%">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!empty($data_search)) {
                            $i = 1;
                            foreach ($data_search as $row) {
                        ?>
                                <tr>
                                    <td><?php echo $i++; ?></td>
                                    <td><?php echo htmlspecialchars($row['ma_tau']); ?></td>
                                    <td><?php echo htmlspecialchars($row['ten_tau']); ?></td>
                                    <td>
                                        <div class="btn-action">
                                            <a class="btn btn-sm btn-primary" href="Edit.php?id=<?php echo $row['id']; ?>"><i class="bi bi-pencil"></i></a>
                                            <a class="btn btn-sm btn-danger" href="Delete.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Xóa tàu này?')"><i class="bi bi-trash"></i></a>
                                        </div>
                                    </td>
                                </tr>
                        <?php
                            }
                        } else {
                            echo "<tr><td colspan='4' class='text-center text-muted'>Không tìm thấy mã tàu hoặc tên tàu</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
<?php require_once '../../includes/footer.php'; ?>

</html>