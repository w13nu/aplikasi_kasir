<?php
// login.php
@ob_start();
session_start();
include 'config.php';  // pastikan di sini ada koneksi $conn ke MySQL

// Pastikan koneksi database berhasil
if (!isset($conn) || !$conn) {
    die("Koneksi database gagal. Periksa file config.php");
}

// Jika sudah login, langsung arahkan sesuai role
if (isset($_SESSION['status']) && $_SESSION['status'] === 'login') {
    if ($_SESSION['role'] === 'admin') {
        header('Location: dashboard.php');
    } else {
        header('Location: index.php');
    }
    exit;
}

// Variabel untuk menampung pesan error
$error_message = '';

if (isset($_POST['login'])) {
    // 1. Ambil input dan escape
    $user = mysqli_real_escape_string($conn, $_POST['username']);
    $pass = mysqli_real_escape_string($conn, $_POST['password']);

    // 2. Query user beserta kolom role
    $query = mysqli_query($conn, "SELECT * FROM login WHERE username='$user'");
    if (!$query) {
        $error_message = "Error database: " . mysqli_error($conn);
    } else if (mysqli_num_rows($query) !== 1) {
        $error_message = "Username tidak ditemukan";
    } else {
        $cari = mysqli_fetch_assoc($query);

        // 3. Verifikasi password
        if (password_verify($pass, $cari['password'])) {
            // 4. Simpan session
            $_SESSION['id_login']  = $cari['id_login'];
            $_SESSION['username']  = $cari['username'];
            $_SESSION['nama_toko'] = $cari['nama_toko'];
            $_SESSION['alamat']    = $cari['alamat'];
            $_SESSION['telepon']   = $cari['telepon'];
            $_SESSION['role']      = $cari['role'];      // ← penting!
            $_SESSION['status']    = 'login';

            // Pastikan tidak ada output sebelum redirect
            ob_end_clean();
            
            // 5. Redirect sesuai role
            if ($cari['role'] === 'admin') {
                header('Location: dashboard.php');
            } else {
                header('Location: index.php');
            }
            exit;
        } else {
            // Coba alternatif jika mungkin password disimpan dengan metode lain (contoh: MD5)
            if (md5($pass) === $cari['password']) {
                // Set session
                $_SESSION['id_login']  = $cari['id_login'];
                $_SESSION['username']  = $cari['username'];
                $_SESSION['nama_toko'] = $cari['nama_toko'];
                $_SESSION['alamat']    = $cari['alamat'];
                $_SESSION['telepon']   = $cari['telepon'];
                $_SESSION['role']      = $cari['role'];
                $_SESSION['status']    = 'login';

                // Pastikan tidak ada output sebelum redirect
                ob_end_clean();
                
                if ($cari['role'] === 'admin') {
                    header('Location: index-asli.php');
                } else {
                    header('Location: index.php');
                }
                exit;
            } else {
                $error_message = "Password salah";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Login Aplikasi Kasir</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" href="assets/img/logo-dark.jpg" type="image/jpg">
  <link rel="stylesheet" href="assets/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/fontawesome/css/all.min.css">
  <style>
    :root {
      --primary-color: #2e7d32;     /* Green - representing agriculture */
      --secondary-color: #558b2f;   /* Light green accent */
      --tertiary-color: #c8e6c9;    /* Very light green for subtle elements */
      --text-color: #33691e;        /* Dark green for text */
      --light-color: #f1f8e9;       /* Very light background */
      --dark-color: #1b5e20;        /* Dark green for emphasis */
    }

    body {
      background: linear-gradient(135deg, var(--light-color), var(--tertiary-color));
      min-height: 100vh;
      display: flex;
      align-items: center;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .logo-container {
      margin-bottom: 1.5rem;
      transition: transform 0.3s ease;
    }

    .logo-container:hover {
      transform: scale(1.05);
    }

    .card {
      border: none;
      border-radius: 15px;
      box-shadow: 0 10px 30px rgba(46, 125, 50, 0.15);
      overflow: hidden;
    }

    .card-header {
      background-color: var(--primary-color);
      color: white;
      text-align: center;
      padding: 1.2rem;
      font-size: 1.5rem;
      font-weight: 600;
    }

    .card-body {
      padding: 2rem;
      background-color: white;
    }

    .form-group {
      margin-bottom: 1.5rem;
    }

    .form-control {
      border-radius: 8px;
      padding: 0.8rem 1rem;
      border: 1px solid #ddd;
      box-shadow: none;
      transition: all 0.3s;
    }

    .form-control:focus {
      border-color: var(--primary-color);
      box-shadow: 0 0 0 0.2rem rgba(46, 125, 50, 0.25);
    }

    label {
      font-weight: 500;
      color: var(--text-color);
      margin-bottom: 0.5rem;
    }

    .btn {
      padding: 0.6rem 1rem;
      font-weight: 500;
      border-radius: 8px;
      transition: all 0.3s;
    }

    .btn-primary {
      background-color: var(--primary-color);
      border-color: var(--primary-color);
    }

    .btn-primary:hover, .btn-primary:focus {
      background-color: var(--dark-color);
      border-color: var(--dark-color);
      transform: translateY(-2px);
    }

    .btn-danger {
      background-color: #d32f2f;
      border-color: #d32f2f;
    }

    .btn-danger:hover, .btn-danger:focus {
      background-color: #b71c1c;
      border-color: #b71c1c;
      transform: translateY(-2px);
    }

    .text-muted {
      color: #757575 !important;
    }

    .input-group-text {
      background-color: var(--tertiary-color);
      border-color: #ddd;
      color: var(--text-color);
    }

    .alert {
      border-radius: 8px;
      margin-bottom: 1.5rem;
    }

    /* Responsive adjustments */
    @media (max-width: 576px) {
      .card-body {
        padding: 1.5rem;
      }
    }
  </style>
</head>
<body>
<div class="container">
  <div class="row justify-content-center">
    <div class="col-sm-10 col-md-8 col-lg-5">
      <div class="text-center logo-container">
        <img src="assets/img/logoanugerahtani.png" alt="Anugerah Tani Logo" width="220" class="img-fluid">
      </div>
      <div class="card">
        <div class="card-header">
          <i class="fas fa-leaf mr-2"></i> Login Aplikasi Kasir
        </div>
        <div class="card-body">
          <?php if (!empty($error_message)): ?>
          <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
          </div>
          <?php endif; ?>
          
          <form method="POST" novalidate>
            <div class="form-group">
              <label for="user"><i class="fa fa-user"></i> Username</label>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text"><i class="fa fa-user"></i></span>
                </div>
                <input type="text" class="form-control" id="user" name="username" placeholder="Masukkan username" required>
              </div>
            </div>
            <div class="form-group">
              <label for="pass"><i class="fa fa-lock"></i> Password</label>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text"><i class="fa fa-lock"></i></span>
                </div>
                <input type="password" class="form-control" id="pass" name="password" placeholder="Masukkan password" required>
              </div>
            </div>
            <div class="row">
              <div class="col-6">
                <button type="reset" class="btn btn-danger btn-block">
                  <i class="fa fa-trash-restore-alt"></i> Reset
                </button>
              </div>
              <div class="col-6">
                <button type="submit" name="login" class="btn btn-primary btn-block">
                  <i class="fa fa-sign-in-alt"></i> Login
                </button>
              </div>
            </div>
          </form>
          <div class="text-center mt-4">
            <small class="text-muted">Aplikasi Kasir Anugerah Tani © <?php echo date('Y'); ?> - By One.Click</small>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="assets/js/jquery.slim.min.js"></script>
<script src="assets/js/popper.min.js"></script>
<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>