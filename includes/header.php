<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <a href="<?php echo BASE_URL; ?>">
            <img src="<?php echo BASE_URL; ?>/assets/images/logo.png" alt="logo"
                style="height: 100px; width: 150px; background-color: white; margin-left: 5px;">
        </a>
        <div class="container">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>/modules/nhan-vien/">Nhân viên</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>/modules/tau/">Tàu</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>/modules/loai-toa/">Loại toa</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>/modules/toa-tau/">Toa tàu</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>/modules/ghe/">Ghế</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>/modules/ga-tau/">Ga tàu</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>/modules/tuyen-duong/">Tuyến đường</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>/modules/lich-trinh/">Lịch trình</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>/modules/khach-hang/">Khách hàng</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>/modules/ve-tau/">Vé tàu</a></li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>/modules/auth/login.php">Đăng nhập</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-4">