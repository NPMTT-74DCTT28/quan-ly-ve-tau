<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../includes/header.php';

if (!isset($_SESSION['user'])) {
    header("Location: " . BASE_URL . "modules/auth/dang_nhap.php");
    exit();
}

$conn = $db->getConnection();

// Lấy danh sách dữ liệu cho dropdowns
$sql_khach_hang = "SELECT id, ho_ten, sdt FROM khach_hang ORDER BY ho_ten";
$stmt_khach_hang = $conn->query($sql_khach_hang);
$khach_hang_list = $stmt_khach_hang->fetchAll();

$sql_lich_trinh = "SELECT id, ma_lich_trinh, ngay_di FROM lich_trinh WHERE ngay_di > NOW() ORDER BY ngay_di";
$stmt_lich_trinh = $conn->query($sql_lich_trinh);
$lich_trinh_list = $stmt_lich_trinh->fetchAll();

$sql_nhan_vien = "SELECT id, ho_ten FROM nhan_vien ORDER BY ho_ten";
$stmt_nhan_vien = $conn->query($sql_nhan_vien);
$nhan_vien_list = $stmt_nhan_vien->fetchAll();

// Biến lưu dữ liệu và thông báo
$ma_ve = $id_khach_hang = $id_lich_trinh = $id_ghe = $id_nhan_vien = $gia_ve = $trang_thai = '';
$show_success = $show_error = false;
$success_message = $error_message = '';
$ghe_list = [];

// Lấy danh sách ghế khi chọn lịch trình
if (isset($_POST['get_ghe'])) {
    $id_lich_trinh = $_POST['id_lich_trinh'];
    $sql_ghe = "SELECT g.id, g.so_ghe, t.ma_toa 
                FROM ghe g 
                JOIN toa_tau t ON g.id_toa_tau = t.id 
                JOIN tau tau ON t.id_tau = tau.id 
                JOIN lich_trinh lt ON lt.id_tau = tau.id 
                WHERE lt.id = ? 
                AND g.id NOT IN (SELECT id_ghe FROM ve_tau WHERE id_lich_trinh = ? AND trang_thai NOT IN ('Đã hủy'))";
    $stmt_ghe = $conn->prepare($sql_ghe);
    $stmt_ghe->execute([$id_lich_trinh, $id_lich_trinh]);
    $ghe_list = $stmt_ghe->fetchAll();
}

// Xử lý khi thêm vé
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnAdd'])) {
    $ma_ve = $_POST['ma_ve'];
    $id_khach_hang = $_POST['id_khach_hang'];
    $id_lich_trinh = $_POST['id_lich_trinh'];
    $id_ghe = $_POST['id_ghe'];
    $id_nhan_vien = $_POST['id_nhan_vien'];
    $gia_ve = $_POST['gia_ve'];
    $trang_thai = $_POST['trang_thai'];

    // Kiểm tra dữ liệu
    if (empty($ma_ve) || empty($id_khach_hang) || empty($id_lich_trinh) || empty($id_ghe) || empty($gia_ve) || empty($trang_thai)) {
        $error_message = "Vui lòng điền đầy đủ thông tin bắt buộc!";
        $show_error = true;
    } else {
        // Kiểm tra mã vé đã tồn tại chưa
        $sql_check = "SELECT id FROM ve_tau WHERE ma_ve = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->execute([$ma_ve]);

        if ($stmt_check->rowCount() > 0) {
            $error_message = "Mã vé đã tồn tại! Vui lòng nhập mã khác.";
            $show_error = true;
        } else {
            // Kiểm tra ghế đã được đặt chưa
            $sql_check_ghe = "SELECT id FROM ve_tau WHERE id_lich_trinh = ? AND id_ghe = ? AND trang_thai NOT IN ('Đã hủy')";
            $stmt_check_ghe = $conn->prepare($sql_check_ghe);
            $stmt_check_ghe->execute([$id_lich_trinh, $id_ghe]);

            if ($stmt_check_ghe->rowCount() > 0) {
                $error_message = "Ghế này đã được đặt cho lịch trình này!";
                $show_error = true;
            } else {
                // Thêm vé vào database
                $sql_insert = "INSERT INTO ve_tau (ma_ve, id_khach_hang, id_lich_trinh, id_ghe, id_nhan_vien, gia_ve, trang_thai, ngay_dat) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
                $stmt_insert = $conn->prepare($sql_insert);
                
                try {
                    if ($stmt_insert->execute([$ma_ve, $id_khach_hang, $id_lich_trinh, $id_ghe, $id_nhan_vien, $gia_ve, $trang_thai])) {
                        $success_message = "Thêm vé tàu thành công!";
                        $show_success = true;
                        
                        // Reset form
                        $ma_ve = $id_khach_hang = $id_lich_trinh = $id_ghe = $id_nhan_vien = $gia_ve = $trang_thai = '';
                        $ghe_list = [];
                    } else {
                        $error_message = "Thêm vé tàu thất bại! Vui lòng thử lại.";
                        $show_error = true;
                    }
                } catch (Exception $e) {
                    $error_message = "Lỗi: " . $e->getMessage();
                    $show_error = true;
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Vé Tàu Mới</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .container {
            max-width: 900px;
            margin-top: 30px;
        }
        .form-container {
            background-color: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        h2 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 30px;
            font-weight: 700;
        }
        .form-label {
            font-weight: 600;
            color: #34495e;
        }
        .btn-container {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 30px;
        }
        .btn-custom {
            min-width: 150px;
            padding: 10px 25px;
            font-weight: 600;
        }
        .alert {
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .required::after {
            content: " *";
            color: #dc3545;
        }
        .form-container {
            background-color: #a8e6cf;
        }
        .ghe-grid {
            display: grid;
            grid-template-columns: repeat(10, 1fr);
            gap: 8px;
            margin-top: 10px;
        }
        .ghe-item {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-align: center;
            cursor: pointer;
            background-color: #f8f9fa;
        }
        .ghe-item.selected {
            background-color: #28a745;
            color: white;
            border-color: #28a745;
        }
        .ghe-item.occupied {
            background-color: #dc3545;
            color: white;
            border-color: #dc3545;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h2>THÊM VÉ TÀU MỚI</h2>

            <?php if ($show_success && !empty($success_message)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($success_message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if ($show_error && !empty($error_message)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($error_message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form method="post" action="">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="ma_ve" class="form-label required">Mã vé:</label>
                            <input type="text" class="form-control" name="ma_ve" value="<?php echo htmlspecialchars($ma_ve); ?>" required placeholder="VD: VE20241223001">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="gia_ve" class="form-label required">Giá vé (VNĐ):</label>
                            <input type="number" class="form-control" name="gia_ve" value="<?php echo htmlspecialchars($gia_ve); ?>" required min="0" placeholder="VD: 500000">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="id_khach_hang" class="form-label required">Khách hàng:</label>
                            <select class="form-control" name="id_khach_hang" required>
                                <option value="">-- Chọn khách hàng --</option>
                                <?php foreach ($khach_hang_list as $kh): ?>
                                    <option value="<?php echo $kh['id']; ?>" <?php echo $id_khach_hang == $kh['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($kh['ho_ten'] . ' - ' . $kh['sdt']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="id_nhan_vien" class="form-label required">Nhân viên bán vé:</label>
                            <select class="form-control" name="id_nhan_vien" required>
                                <option value="">-- Chọn nhân viên --</option>
                                <?php foreach ($nhan_vien_list as $nv): ?>
                                    <option value="<?php echo $nv['id']; ?>" <?php echo $id_nhan_vien == $nv['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($nv['ho_ten']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="id_lich_trinh" class="form-label required">Lịch trình:</label>
                            <select class="form-control" name="id_lich_trinh" id="id_lich_trinh" required onchange="this.form.submit()">
                                <option value="">-- Chọn lịch trình --</option>
                                <?php foreach ($lich_trinh_list as $lt): ?>
                                    <option value="<?php echo $lt['id']; ?>" <?php echo $id_lich_trinh == $lt['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($lt['ma_lich_trinh'] . ' - ' . date('d/m/Y H:i', strtotime($lt['ngay_di']))); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <input type="hidden" name="get_ghe" value="1">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="trang_thai" class="form-label required">Trạng thái:</label>
                            <select class="form-control" name="trang_thai" required>
                                <option value="">-- Chọn trạng thái --</option>
                                <option value="Chờ xác nhận" <?php echo $trang_thai == 'Chờ xác nhận' ? 'selected' : ''; ?>>Chờ xác nhận</option>
                                <option value="Đã xác nhận" <?php echo $trang_thai == 'Đã xác nhận' ? 'selected' : ''; ?>>Đã xác nhận</option>
                                <option value="Hoàn thành" <?php echo $trang_thai == 'Hoàn thành' ? 'selected' : ''; ?>>Hoàn thành</option>
                                <option value="Đã hủy" <?php echo $trang_thai == 'Đã hủy' ? 'selected' : ''; ?>>Đã hủy</option>
                            </select>
                        </div>
                    </div>
                    
                    <?php if (!empty($ghe_list)): ?>
                    <div class="col-12">
                        <div class="form-group">
                            <label class="form-label required">Chọn ghế:</label>
                            <div class="ghe-grid">
                                <?php foreach ($ghe_list as $ghe): ?>
                                    <label class="ghe-item <?php echo $id_ghe == $ghe['id'] ? 'selected' : ''; ?>">
                                        <input type="radio" name="id_ghe" value="<?php echo $ghe['id']; ?>" 
                                               <?php echo $id_ghe == $ghe['id'] ? 'checked' : ''; ?> hidden>
                                        <?php echo htmlspecialchars($ghe['so_ghe']); ?>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <?php elseif (!empty($id_lich_trinh)): ?>
                    <div class="col-12">
                        <div class="alert alert-warning">
                            Không còn ghế trống cho lịch trình này.
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="btn-container">
                    <button type="submit" class="btn btn-success btn-custom" name="btnAdd">
                        <i class="bi bi-plus-circle"></i> Thêm vé
                    </button>
                    <a href="index_ve_tau.php" class="btn btn-secondary btn-custom">
                        <i class="bi bi-arrow-left"></i> Quay lại
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Xử lý chọn ghế
        document.addEventListener('DOMContentLoaded', function() {
            const gheItems = document.querySelectorAll('.ghe-item');
            gheItems.forEach(item => {
                item.addEventListener('click', function() {
                    if (!this.classList.contains('occupied')) {
                        // Bỏ chọn tất cả
                        gheItems.forEach(i => i.classList.remove('selected'));
                        // Chọn ghế này
                        this.classList.add('selected');
                        // Check radio button
                        const radio = this.querySelector('input[type="radio"]');
                        if (radio) radio.checked = true;
                    }
                });
            });
        });
    </script>
</body>
<?php
require_once '../../includes/footer.php';
?>
</html>