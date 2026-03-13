<?php
session_start();

require_once 'config/Database.php';
require_once 'models/User.php';

$db = (new Database())->getConnection();
$user = new User($db);

$error_message = "";
$success_message = "";

if(isset($_POST['login'])){
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Validasi input
    if(empty($email) || empty($password)){
        $error_message = "Email dan password harus diisi!";
    } else {
        $result = $user->login($email, $password);

        if($result->num_rows > 0){
            $data = $result->fetch_assoc();

            $_SESSION['user'] = $data['nama'];
            $_SESSION['role'] = $data['role'];
            $_SESSION['id'] = $data['id'];

            if($data['role'] == "admin"){
                header("Location: views/dashboard_admin.php");
            } else {
                header("Location: views/dashboard_mahasiswa.php");
            }
            exit;
        } else {
            $error_message = "Email atau password salah!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Pengaduan Mahasiswa</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-container {
            width: 100%;
            max-width: 400px;
            padding: 20px;
        }

        .card-login {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }

        .card-header-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 20px;
            text-align: center;
        }

        .card-header-login h1 {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .card-header-login p {
            font-size: 14px;
            opacity: 0.9;
            margin: 0;
        }

        .login-icon {
            font-size: 50px;
            margin-bottom: 15px;
            display: block;
        }

        .card-body-login {
            padding: 35px 25px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }

        .form-control-login {
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .form-control-login:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            outline: none;
        }

        .form-control-login::placeholder {
            color: #999;
        }

        .btn-login {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        .btn-login:hover {
            background: linear-gradient(135deg, #5568d3 0%, #6a3d8a 100%);
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
            color: white;
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .form-check {
            margin-top: 15px;
            margin-bottom: 0;
        }

        .form-check-input {
            cursor: pointer;
        }

        .form-check-label {
            cursor: pointer;
            font-size: 14px;
            color: #666;
        }

        .divider {
            text-align: center;
            margin: 20px 0;
            position: relative;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #e0e0e0;
        }

        .divider-text {
            background: white;
            padding: 0 10px;
            position: relative;
            color: #999;
            font-size: 14px;
        }

        .signup-link {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
        }

        .signup-link p {
            font-size: 14px;
            color: #666;
            margin: 0;
        }

        .signup-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .signup-link a:hover {
            color: #764ba2;
            text-decoration: underline;
        }

        .alert-login {
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            border: none;
        }

        .alert-danger-login {
            background-color: #fff5f5;
            color: #c53030;
            border-left: 4px solid #c53030;
        }

        .alert-success-login {
            background-color: #f0fff4;
            color: #22543d;
            border-left: 4px solid #22543d;
        }

        .input-group-icon {
            position: relative;
        }

        .input-group-icon i {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            z-index: 10;
        }

        .input-group-icon input {
            padding-right: 40px;
        }

        @media (max-width: 480px) {
            .card-header-login {
                padding: 30px 20px;
            }

            .card-header-login h1 {
                font-size: 24px;
            }

            .card-body-login {
                padding: 25px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="card card-login">
            <!-- Header -->
            <div class="card-header-login">
                <i class="fas fa-file-alt login-icon"></i>
                <h1>Sistem Pengaduan</h1>
                <p>Masuk untuk melanjutkan</p>
            </div>

            <!-- Body -->
            <div class="card-body-login">
                <!-- Error Message -->
                <?php if($error_message): ?>
                    <div class="alert alert-login alert-danger-login">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>

                <!-- Login Form -->
                <form method="POST" name="loginForm">
                    <!-- Email -->
                    <div class="form-group">
                        <label for="email"><i class="fas fa-envelope"></i> Email</label>
                        <div class="input-group-icon">
                            <input 
                                type="email" 
                                id="email" 
                                name="email" 
                                class="form-control form-control-login" 
                                placeholder="Masukkan email Anda"
                                required>
                            <i class="fas fa-envelope"></i>
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="form-group">
                        <label for="password"><i class="fas fa-lock"></i> Password</label>
                        <div class="input-group-icon">
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                class="form-control form-control-login" 
                                placeholder="Masukkan password Anda"
                                required>
                            <i class="fas fa-lock"></i>
                        </div>
                    </div>

                    <!-- Remember Me -->
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="remember" name="remember">
                        <label class="form-check-label" for="remember">
                            Ingat saya
                        </label>
                    </div>

                    <!-- Login Button -->
                    <button type="submit" name="login" class="btn btn-login">
                        <i class="fas fa-sign-in-alt"></i> Masuk
                    </button>
                </form>

                <!-- Divider -->
                <div class="divider">
                    <span class="divider-text">atau</span>
                </div>

                <!-- Sign Up Link -->
                <div class="signup-link">
                    <p>Belum punya akun?</p>
                    <a href="Register.php"><i class="fas fa-user-plus"></i> Daftar di sini</a>
                </div>
            </div>
        </div>

        <!-- Footer Info -->
        <div style="text-align: center; margin-top: 30px; color: white; font-size: 12px;">
            <p><i class="fas fa-lock"></i> Akses aman dan terpercaya</p>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>