<?php
session_start();
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = filter_var($_POST['username'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    

    if (!filter_var($username, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Username harus berupa email yang valid.";
    } elseif (!preg_match('/^(?=.*[A-Z])(?=.*[\W_]).{8,}$/', $password)) {
        $error_message = "Password harus memiliki minimal 8 karakter, mengandung huruf kapital, dan simbol.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: dashboard.php");
            exit();
        } else {
            $error_message = "Email atau password salah.";
        }
    }
}
?>

<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>WELCOME TO WEB'S SHORTLINK</title>
        <link rel="stylesheet" href="css/style.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="bg">
    <nav id="navbar" class="navbar navbar-expand-lg bg-white border-bottom border-2 shadow-sm fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">
                <div class="brand d-none d-lg-block">
                    <img src="assets/svg/brand.svg" alt="hayuJoin">
                </div>
                <div class="d-block d-lg-none">
                    <img src="assets/icon/iconColored.png" alt="hayuJoin" width="30" height="30">
                    <span class="text-black">HayuJoin</span>
                </div>
            </a>
            <button class="navbar-toggler border-0 bg-primary text-white rounded-0" type="button" data-bs-toggle="collapse" data-bs-target="#collapsibleNavbar">
            <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="collapsibleNavbar">
                <ul class="navbar-nav nav-pills ms-auto mb-2 mb-lg-0">
                    <li class="nav-item mx-4 my-2 rounded-0">
                        <form class="d-flex" role="search" id="userNav">
                            <a href="login.php" class="btn btn-outline-secondary fw-semibold rounded-5 px-4 me-2">Login</a>
                            <a href="register.php" class="btn btn-primary fw-semibold rounded-5 px-4">SignUp</a>
                         </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container py-5 my-5">
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong>Login gagal:</strong> <?php echo $error_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header text-center">
                            Halaman Login
                        </div>
                        <form method="post">
                            <div class="card-body">
                                <label for="username" class="form-label">Email</label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text">@</span>
                                    <input type="email" class="form-control" id="username" name="username" required placeholder="Masukan email">
                                </div>
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group mb-3">
                                    <input type="password" class="form-control" id="password" name="password" required minlength="8" placeholder="Masukan password">
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                <div class="row">
                                    <button type="submit" class="btn btn-primary">Login</button>
                                </div>
                                <div class="text-center">
                                    Belum punya akun? <a href="register.php">Daftar</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card-footer bg-dark text-light text-center fixed-bottom">
        Copyright © Indonesia 2024. Contact: <a href="mailto:dzikriamrillah86@gmail.com" class="text-light">dzikriamrillah86@gmail.com</a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
    <script>
        document.getElementById("togglePassword").addEventListener("click", function() {
            var passwordField = document.getElementById("password");
            if (passwordField.type === "password") {
                passwordField.type = "text";
                this.innerHTML = '<i class="bi bi-eye-slash"></i>';
            } else {
                passwordField.type = "password";
                this.innerHTML = '<i class="bi bi-eye"></i>';
            }
        });
    </script>
</body>
</html>
