<?php
require_once '../../config/database.php';
require_once '../../includes/header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QL GA TAU</title>
    <link rel="stylesheet" href="../../modules/ga-tau/gataustyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<div class="page-wrapper">
    <form method="POST" class="search-form">
        <div class="form-row">
            <div class="form-group">
                <label>Mã Ga</label>
                <input type="text" name="txtmaga" placeholder="Nhập mã ga...">
            </div>

            <div class="form-group">
                <label>Tên Ga</label>
                <input type="text" name="txttenga" placeholder="Nhập tên ga...">
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" name="btnTimkiem" class="btn_timkiem">
                <i class="fa-solid fa-magnifying-glass"></i> Tìm kiếm
            </button>
            <a href="themga.php" class="btn_them">
                <i class="fa-solid fa-plus"></i> Thêm Ga
            </a>
        </div>
        
    </form>
    <div class="table-wrapper">
        <table class="data_bang">
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Mã Ga</th>
                    <th>Tên Ga</th>
                    <th>Địa chỉ</th>
                    <th>Thành Phố</th>
                    <th>Thao tác</th>
                </tr>
            </thead>

            <tbody>
            <?php
            if (isset($data) && mysqli_num_rows($data)) {
                $i = 0;
                while ($row = mysqli_fetch_array($data)) {
            ?>
                <tr>
                    <td><?php echo ++$i; ?></td>
                    <td><?php echo $row['ma_ga']; ?></td>
                    <td><?php echo $row['ten_ga']; ?></td>
                    <td><?php echo $row['dia_chi']; ?></td>
                    <td><?php echo $row['thanh_pho']; ?></td>
                    <td class="action-col">
                        <a href="sua.php?ma_ga=<?php echo $row['ma_ga']; ?>" class="btn_sua">
                            <i class="fa-solid fa-pen"></i> Sửa
                        </a>

                        <a href="xoa.php?ma_ga=<?php echo $row['ma_ga']; ?>"
                           class="btn_xoa"
                           onclick="return confirm('Bạn có muốn xóa ga <?php echo $row['ten_ga']; ?> không ?');">
                            <i class="fa-solid fa-trash-can"></i> Xóa
                        </a>
                    </td>
                </tr>
            <?php
                }
            }
            ?>
            </tbody>
        </table>
    </div>

</div>
</body>
</html>
<?php require_once '../../includes/footer.php'; ?>