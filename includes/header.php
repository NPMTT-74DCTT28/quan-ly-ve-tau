<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL ?>assets/css/layout.css">
</head>

<body>
    <header>
        <a href="<?php echo BASE_URL ?>">
            <img src="<?php echo BASE_URL ?>assets/images/logo.png" alt="logo" class="logo">
        </a>
        <ul class="menu">
            <?php if (isset($_SESSION['user'])): ?>
                <?php if (isAdmin()): ?>
                    <li><a href="<?php echo BASE_URL ?>modules/nhan-vien">Nhân viên</a></li>
                    <li><a href="<?php echo BASE_URL ?>modules/tau">Tàu</a></li>
                    <li><a href="<?php echo BASE_URL ?>modules/loai-toa">Loại toa</a></li>
                    <li><a href="<?php echo BASE_URL ?>modules/ghe">Ghế</a></li>
                    <li><a href="<?php echo BASE_URL ?>modules/toa-tau">Toa tàu</a></li>
                    <li><a href="<?php echo BASE_URL ?>modules/ga-tau">Ga tàu</a></li>
                    <li><a href="<?php echo BASE_URL ?>modules/tuyen-duong">Tuyến đường</a></li>
                <?php endif ?>
                <li><a href="<?php echo BASE_URL ?>modules/lich-trinh">Lịch trình</a></li>
                <li><a href="<?php echo BASE_URL ?>modules/khach-hang">Khách hàng</a></li>
                <li><a href="<?php echo BASE_URL ?>modules/ve-tau">Vé tàu</a></li>
                <li class="dropdown-container">
                    <a href="javascript:void(0)" class="dropdown-trigger">
                        Xin chào, <?php echo $_SESSION['user']['ho_ten'] ?> <span class="arrow"> ▼</span>
                    </a>
                    <ul class="personal-menu">
                        <li>
                            <a href="<?php echo BASE_URL ?>modules/nhan-vien/sua.php?id=<?php echo $_SESSION['user']['id'] ?>">
                                Cập nhật thông tin
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo BASE_URL ?>modules/auth/doi_mat_khau.php?id=<?php echo $_SESSION['user']['id'] ?>">
                                Đổi mật khẩu
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo BASE_URL ?>modules/auth/dang_xuat.php"
                                onclick="return confirm('Bạn có chắc chắn muốn đăng xuất không?');"
                                class="logout-btn">
                                Đăng xuất
                            </a>
                        </li>
                    </ul>
                </li>
            <?php else: ?>
                <li><a href="<?php echo BASE_URL ?>modules/auth/dang_nhap.php">Đăng nhập</a></li>
            <?php endif; ?>
        </ul>
    </header>
    <div class="main-content">