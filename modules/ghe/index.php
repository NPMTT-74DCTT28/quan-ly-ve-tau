<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__. '/../../includes/header.php';

if (!isset($_SESSION['user'])) {
    header("Location: " . BASE_URL . "modules/auth/dang_nhap.php");
    exit();
}

$conn = $db->getConnection();

$so_ghe = isset($_POST['so_ghe']) ? trim($_POST['so_ghe']) : '';
$id_toa_tau = isset($_POST['id_toa_tau']) ? trim($_POST['id_toa_tau']) : '';

try {
    $sql = "SELECT * FROM ghe WHERE 1=1";
    $params = [];

    if (!empty($so_ghe)) {
        $sql .= " OR so_ghe LIKE ?";
        $params[] = "%$so_ghe%";
    }

    if(!empty($id_toa_tau)){
        $sql .= "OR id_toa_tau LIKE ?";
        $params[] = "%$id_toa_tau%";
    }

    $sql .= " ORDER BY id DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $ghe = $stmt->fetchAll();
    
} catch (PDOException $e) {
    die("Lỗi truy vấn: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Quản Lý Ghế</title>
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
    </style>
</head>

<body>
    <div class="container">
        <h2 class="text-center mb-4">QUẢN LÝ GHẾ</h2>
        <div class="search-form">
            <form method="post">
                <div class="row justify-content-center mb-3">
                    <div class="col-md-6">
                        <input type="text" class="form-control text-center" name="so_ghe"
                            value="<?= htmlspecialchars($so_ghe) ?>"
                            placeholder="Nhập số ghế cần tìm...">
                    </div>
                </div>

                <div class="row justify-content-center">
                    <div class="col-auto d-flex gap-2 flex-wrap justify-content-center">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Tìm kiếm</button>
                        <a href="Add.php" class="btn btn-success"><i class="bi bi-plus-circle"></i> Thêm mới</a>
                        <a href="Export.php" class="btn btn-info text-white"><i class="bi bi-file-earmark-excel"></i>  Nhập Excel</a>
                        <a href="Import.php" class="btn btn-warning"><i class="bi bi-upload"></i> Xuất Excel</a>
                    </div>
                </div>
            </form>
        </div>

        <div class="table-container">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Số Ghế</th>
                        <th>Thuộc Toa Tàu</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($ghe): $i = 1;
                        foreach ($ghe as $row): ?>
                            <tr>
                                <td><?= $i++ ?></td>
                                <td><?= htmlspecialchars($row['so_ghe']) ?></td>
                                <td><?= htmlspecialchars($row['id_toa_tau']) ?></td>
                                <td>
                                    <a class="btn btn-sm btn-primary" href="Edit.php?id=<?= $row['id'] ?>"><i class="bi bi-pencil"></i></a>
                                    <a class="btn btn-sm btn-danger" href="Delete.php?id=<?= $row['id'] ?>" onclick="return confirm('Xóa ghế này?')"><i class="bi bi-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach;
                    else: ?>
                        <tr>
                            <td colspan="4" class="text-center">Không tìm thấy dữ liệu</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
</html>