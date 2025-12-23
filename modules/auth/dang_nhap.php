<?php
require_once __DIR__ . '/../../bootstrap.php';
$conn = $db->getConnection();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ma_nhan_vien = $_POST['ma_nhan_vien'];
    $mat_khau = $_POST['mat_khau'];

    $stmt = $conn->prepare("SELECT * FROM nhan_vien WHERE ma_nhan_vien = ?");
    $stmt->execute([$ma_nhan_vien]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($mat_khau, $user['mat_khau'])) {
        session_start();
        $_SESSION['user'] = $user;
        echo "<script> alert('Đăng nhập thành công!'); window.location.href = '" . BASE_URL . "';</script>";
        exit();
    } else {
        $error_message = "Sai tên đăng nhập hoặc mật khẩu!";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-container {
            background-color: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-header h2 {
            color: #333;
            margin: 0;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #666;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            box-sizing: border-box;
            transition: border-color 0.3s;
        }

        .form-group input:focus {
            border-color: #007bff;
            outline: none;
        }

        .btn-submit {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn-submit:hover {
            background-color: #0056b3;
        }

        .error-msg {
            color: #dc3545;
            background-color: #f8d7da;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
            font-size: 14px;
        }

        .footer-link {
            text-align: center;
            margin-top: 15px;
            font-size: 14px;
        }

        .footer-link a {
            color: #007bff;
            text-decoration: none;
        }
    </style>
</head>

<body>

    <div class="login-container">
        <div class="login-header">
            <h2>Đăng nhập</h2>
        </div>

        <?php if (!empty($error_message)): ?>
            <div class="error-msg"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="form-group">
                <label for="ma_nhan_vien">Mã nhân viên</label>
                <input type="text" id="ma_nhan_vien" name="ma_nhan_vien" required placeholder="Nhập mã NV">
            </div>

            <div class="form-group">
                <label for="mat_khau">Mật khẩu</label>
                <input type="password" id="mat_khau" name="mat_khau" required placeholder="Nhập mật khẩu">
            </div>

            <button type="submit" class="btn-submit">Đăng nhập</button>
        </form>

        <div class="footer-link">
            <p>Chưa có tài khoản? <a href="<?php echo BASE_URL ?>modules/auth/dang_ky.php">Đăng ký ngay</a></p>
        </div>
    </div>

</body>

</html>