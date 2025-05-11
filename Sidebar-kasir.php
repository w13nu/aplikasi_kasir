<?php
include "config.php";
session_start();
  if($_SESSION['status']!="login"){
    header("location:login.php");
  }
  function ribuan ($nilai){
    return number_format ($nilai, 0, ',', '.');
}
$result1 = mysqli_query($conn, "SELECT * FROM login");
while($data = mysqli_fetch_array($result1))
{
    $user = $data['username'];
    $id = $data['id_login'];
    $toko = $data['nama_toko'];
    $alamat = $data['alamat'];
    $telp = $data['telepon'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>APLIKASI KASIR</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" type="image/jpg" href="assets/img/logo-dark.jpg">
  <link rel="stylesheet" href="assets/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <link href="assets/fontawesome/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="assets/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
  <link href="assets/vendor/datatables/responsive.bootstrap4.min.css" rel="stylesheet">

  <style>
    /* Sidebar styles */
    .sidebar {
      position: fixed;
      top: 0;
      left: 0;
      height: 100%;
      width: 250px;
      z-index: 1000;
      background-color: #343a40;
      transition: all 0.3s;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      overflow-y: auto;
    }
    
    .sidebar-header {
      padding: 15px;
      background-color: #212529;
      text-align: center;
    }
    
    .sidebar-brand {
      color: #fff;
      font-weight: 600;
      font-size: 1.2rem;
      margin-bottom: 5px;
    }
    
    .sidebar-brand-sub {
      color: #ffc107;
      font-size: 0.9rem;
      display: block;
    }
    
    .sidebar-menu {
      padding: 20px 0;
    }
    
    .sidebar-menu .nav-item {
      margin-bottom: 5px;
    }
    
    .sidebar-menu .nav-link {
      color: #ced4da;
      padding: 12px 15px;
      border-radius: 4px;
      margin: 0 8px;
      transition: 0.3s;
      display: flex;
      align-items: center;
    }
    
    .sidebar-menu .nav-link:hover {
      background-color: rgba(255,255,255,0.1);
      color: #fff;
    }
    
    .sidebar-menu .nav-link.active {
      background-color: #ffc107;
      color: #212529;
    }
    
    .sidebar-menu .nav-link i {
      margin-right: 10px;
      width: 20px;
      text-align: center;
    }
    
    .sidebar-menu .nav-link span {
      font-size: 0.9rem;
    }
    
    .sidebar-separator {
      height: 1px;
      background-color: rgba(255,255,255,0.1);
      margin: 10px 15px;
    }
    
    .content-wrapper {
      margin-left: 250px;
      transition: all 0.3s;
      min-height: 100vh;
    }
    
    .top-navbar {
      background-color: #fff;
      box-shadow: 0 2px 5px rgba(0,0,0,0.05);
      padding: 15px 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    
    .menu-toggler {
      display: none;
      background: none;
      border: none;
      color: #343a40;
      font-size: 1.5rem;
      cursor: pointer;
    }
    
    .user-info {
      display: flex;
      align-items: center;
    }
    
    .user-info span {
      margin-left: 10px;
    }
    
    .main-content {
      padding: 20px;
    }
    
    /* Responsive styles */
    @media (max-width: 991.98px) {
      .sidebar {
        width: 0;
        padding: 0;
      }
      
      .sidebar.show {
        width: 250px;
        padding: initial;
      }
      
      .content-wrapper {
        margin-left: 0;
      }
      
      .menu-toggler {
        display: block;
      }
      
      .overlay {
        display: none;
        position: fixed;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.4);
        z-index: 999;
        opacity: 0;
        transition: all 0.5s ease-in-out;
      }
      
      .overlay.show {
        display: block;
        opacity: 1;
      }
    }
  </style>
  <script>
  document.addEventListener('DOMContentLoaded', function () {
    const toggleButton = document.getElementById('menu-toggle');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');

    toggleButton.addEventListener('click', function () {
      sidebar.classList.toggle('show');
      overlay.classList.toggle('show');
    });

    overlay.addEventListener('click', function () {
      sidebar.classList.remove('show');
      overlay.classList.remove('show');
    });
  });
</script>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
  <div class="sidebar-header">
    <div class="sidebar-brand">APLIKASI KASIR</div>
    <div class="sidebar-brand-sub">ANUGERAH TANI</div>
  </div>
  <div class="sidebar-menu">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'index-asli.php' || basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>" href="index.php">
          <i class="fa fa-desktop"></i>
          <span>Transaksi</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'produk-kasir.php') ? 'active' : ''; ?>" href="produk-kasir.php">
          <i class="fa fa-shopping-bag"></i>
          <span>Data Produk</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'laporan_kasir.php') ? 'active' : ''; ?>" href="laporan_kasir.php">
          <i class="fa fa-chart-bar"></i>
          <span>Laporan Transaksi</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'laporan_produk_kasir.php') ? 'active' : ''; ?>" href="laporan_produk_kasir.php">
          <i class="fa fa-folder-open"></i>
          <span>Laporan Produk</span>
        </a>
      </li>
      <div class="sidebar-separator"></div>
      <li class="nav-item">
        <a class="nav-link" href="logout.php" onclick="javascript:return confirm('Anda yakin ingin keluar ?');">
          <i class="fa fa-sign-out-alt"></i>
          <span>Keluar</span>
        </a>
      </li>
    </ul>
  </div>
</div>

<!-- Overlay for mobile -->
<div class="overlay" id="sidebar-overlay"></div>

<!-- Main content -->
<div class="content-wrapper">
  <div class="top-navbar">
    <div class="d-flex align-items-center">
      <button class="menu-toggler" id="menu-toggle">
        <i class="fa fa-bars"></i>
      </button>
      <div class="ml-3">
        <h5 class="mb-0">KASIR TOKO</h5>
      </div>
    </div>
  </div>
  
  <div class="main-content">
    <!-- Your main content goes here -->