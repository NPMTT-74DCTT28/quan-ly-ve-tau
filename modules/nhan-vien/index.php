<?php
require_once __DIR__ . '/../../bootstrap.php';

if (!isset($_SESSION['user'])) {
    header("Location: " . BASE_URL . "modules/auth/dang_nhap.php");
    exit();
}

$conn = $db->getConnection();

$keyword = $_GET['keyword'] ?? '';
$vai_tro = $_GET['vai_tro'] ?? '';

$sql = "SELECT * FROM nhan_vien WHERE 1=1";
$params = [];

if (!empty($keyword)) {
    $sql .= " AND (ma_nhan_vien LIKE ? OR ho_ten LIKE ? OR sdt LIKE ? OR email LIKE ?)";
    $search_term = "%$keyword%";
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
}

if (!empty($vai_tro)) {
    $sql .= " AND vai_tro = ?";
    $params[] = $vai_tro;
}

$sql .= " ORDER BY id DESC";
$stmt = $conn->prepare($sql);
$stmt->execute($params);
$nhan_viens = $stmt->fetchAll();

require_once __DIR__ . '/../../includes/header.php';
?>

<style>
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .search-container {
        background: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
    }

    .search-form {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
    }

    .form-control {
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        min-width: 200px;
    }

    .btn {
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        text-decoration: none;
        font-weight: 500;
        display: inline-block;
    }

    .btn-primary {
        background-color: #007bff;
        color: white;
    }

    .btn-primary:hover {
        background-color: #0056b3;
    }

    .btn-success {
        background-color: #28a745;
        color: white;
    }

    .btn-warning {
        background-color: #ffc107;
        color: #000;
        font-size: 0.9em;
        padding: 5px 10px;
    }

    .btn-danger {
        background-color: #dc3545;
        color: white;
        font-size: 0.9em;
        padding: 5px 10px;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .data-table th,
    .data-table td {
        padding: 12px 15px;
        text-align: left;
        border-bottom: 1px solid #eee;
    }

    .data-table th {
        background-color: #f8f9fa;
        font-weight: 600;
        color: #333;
    }

    .data-table tr:hover {
        background-color: #f1f1f1;
    }

    .action-links {
        display: flex;
        gap: 5px;
    }
</style>

<div class="container" style="padding: 20px;">

    <div class="page-header">
        <h2>Quản lý Nhân viên</h2>
        <a href="them.php" class="btn btn-success">+ Thêm nhân viên</a>
    </div>

    <div class="search-container">
        <form method="GET" action="" class="search-form">
            <input type="text" name="keyword" class="form-control"
                placeholder="Tìm theo Mã, Tên, SĐT, Email..."
                value="<?php echo htmlspecialchars($keyword); ?>"
                style="flex: 2;">

            <select name="vai_tro" class="form-control">
                <option value="">-- Tất cả vai trò --</option>

                <option value="<?php echo ROLE_ADMIN; ?>"
                    <?php echo $vai_tro === ROLE_ADMIN ? 'selected' : ''; ?>>
                    <?php echo ROLE_ADMIN; ?>
                </option>

                <option value="<?php echo ROLE_NHAN_VIEN; ?>"
                    <?php echo $vai_tro === ROLE_NHAN_VIEN ? 'selected' : ''; ?>>
                    <?php echo ROLE_NHAN_VIEN; ?>
                </option>
            </select>

            <button type="submit" class="btn btn-primary">Tìm kiếm</button>
            <a href="index.php" class="btn" style="background: #6c757d; color: white;">Đặt lại</a>
        </form>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>STT</th>
                <th>Mã NV</th>
                <th>Họ tên</th>
                <th>SĐT</th>
                <th>Email</th>
                <th>Vai trò</th>
                <th width="150">Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($nhan_viens) > 0): ?>
                <?php foreach ($nhan_viens as $nv): ?>
                    <tr>
                        <td><?php echo $nv['id']; ?></td>
                        <td><strong><?php echo $nv['ma_nhan_vien']; ?></strong></td>
                        <td><?php echo $nv['ho_ten']; ?></td>
                        <td><?php echo $nv['sdt']; ?></td>
                        <td><?php echo $nv['email']; ?></td>
                        <td>
                            <span style="padding: 3px 8px; border-radius: 4px; background: #e9ecef; font-size: 0.9em;">
                                <?php echo htmlspecialchars($nv['vai_tro']); ?>
                            </span>
                        </td>
                        <td class="action-links">
                            <a href="sua.php?id=<?php echo $nv['id']; ?>" class="btn btn-warning">Sửa</a>

                            <?php if ($_SESSION['user']['id'] != $nv['id']): ?>
                                <a href="xoa.php?id=<?php echo $nv['id']; ?>"
                                    class="btn btn-danger"
                                    onclick="return confirm('Bạn có chắc chắn muốn xóa nhân viên <?php echo $nv['ho_ten']; ?> không?');">
                                    Xóa
                                </a>
                            <?php else: ?>
                                <span class="btn" style="background:#ddd; cursor:not-allowed; padding:5px 10px; font-size:0.9em;">Xóa</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" style="text-align: center; padding: 20px;">Không tìm thấy nhân viên nào.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>