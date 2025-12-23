<?php
require_once '../../config/database.php';
require_once '../../includes/header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa Ga</title>
    <link rel="stylesheet" href="../../modules/ga-tau/stylethemga.css">
</head>
<body>

<div class="add-wrapper">

    <h2 class="add-title">SỬA GA</h2>

    <form method="POST" class="add-form">

        <div class="form-row">
            <label>Mã Ga</label>
            <input type="text" name="masv" required readonly>
        </div>

        <div class="form-row">
            <label>Tên Ga</label>
            <input type="text" name="hotensv" required>
        </div>

        <div class="form-row">
            <label>Địa chỉ</label>
            <input type="text" name="dc" required>
        </div>

        <div class="form-row">
            <label>Thành phố</label>
            <input type="text" name="lop" required>
        </div>

        <div class="form-actions">
            <button type="submit" name="btnthem" class="btn-submit">
                Sửa Ga
            </button>
        </div>

    </form>

</div>

</body>
</html>
<?php require_once '../../includes/footer.php'; ?>