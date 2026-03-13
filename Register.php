<?php
session_start();

require_once 'config/Database.php';
require_once 'models/User.php';

$db = (new Database())->getConnection();
$user = new User($db);

$error_message = "";
$success_message = "";

if(isset($_POST['register'])){

    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];

    // Validasi input
    if(empty($nama) || empty($email) || empty($password)){
        $error_message = "Semua field harus diisi";
    }
    elseif($password !== $password_confirm){
        $error_message = "Password tidak cocok";
    }
    elseif(strlen($password) < 6){
        $error_message = "Password minimal 6 karakter";
    }
    elseif($user->checkEmail($email)){
        $error_message = "Email sudah terdaftar";
    }
    else{
        if($user->register($nama, $email, $password)){
            $success_message = "Registrasi berhasil! Silakan login.";
            // Redirect ke login page setelah 2 detik
            header("refresh:2; url=Index.php");
        } else {
            $error_message = "Registrasi gagal, silakan coba lagi";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registrasi - Sistem Pengaduan</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f5f5f5;
        }
        .register-container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="register-container">
                    <h2 class="mb-4">Registrasi Akun Mahasiswa</h2>

                    <?php if($error_message): ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo $error_message; ?>
                        </div>
                    <?php endif; ?>

                    <?php if($success_message): ?>
                        <div class="alert alert-success" role="alert">
                            <?php echo $success_message; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama Lengkap</label>
                            <input type="text" name="nama" id="nama" class="form-control" placeholder="Masukkan nama lengkap" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" id="email" class="form-control" placeholder="Masukkan email" required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" name="password" id="password" class="form-control" placeholder="Masukkan password (minimal 6 karakter)" required>
                        </div>

                        <div class="mb-3">
                            <label for="password_confirm" class="form-label">Konfirmasi Password</label>
                            <input type="password" name="password_confirm" id="password_confirm" class="form-control" placeholder="Konfirmasi password" required>
                        </div>

                        <button type="submit" name="register" class="btn btn-primary w-100">Daftar</button>
                    </form>

                    <p class="mt-3 text-center">Sudah punya akun? <a href="Index.php">Login di sini</a></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
