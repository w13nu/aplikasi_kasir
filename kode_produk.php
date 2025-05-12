<?php
include 'config.php';

if (isset($_GET['awalan'])) {
    $awalan = strtoupper($_GET['awalan']);

    // Validasi agar hanya huruf A-Z
    if (!preg_match('/^[A-Z]$/', $awalan)) {
        echo '';
        exit;
    }

    // Cari kode terakhir yang diawali dengan huruf tersebut
    $queryKode = mysqli_query($conn, "SELECT kode_produk FROM produk WHERE kode_produk LIKE '$awalan%' ORDER BY kode_produk DESC LIMIT 1");
    $kodeBaru = $awalan . "001"; // Default jika belum ada

    if ($row = mysqli_fetch_assoc($queryKode)) {
        $lastKode = $row['kode_produk'];
        // Ekstrak bagian angka dari kode (semua karakter setelah huruf pertama)
        $lastNumber = (int)substr($lastKode, 1);
        // Buat nomor baru dengan padding 3 digit
        $newNumber = $lastNumber + 1;
        $kodeBaru = $awalan . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    echo $kodeBaru;
} else {
    // Jika tidak ada parameter awalan, kembalikan string kosong
    echo '';
}
?>