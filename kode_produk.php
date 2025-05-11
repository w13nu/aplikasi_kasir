<?php
include 'config.php'; // atau file koneksi Anda

if (isset($_GET['idkategori'])) {
    $idkategori = $_GET['idkategori'];

    // Ambil data kategori
    $queryKategori = mysqli_query($conn, "SELECT * FROM kategori WHERE idkategori='$idkategori'");
    $dataKategori = mysqli_fetch_assoc($queryKategori);
    $namaKategori = strtolower($dataKategori['nama_kategori']);

    // Mapping awalan
    if (strpos($namaKategori, 'elektronik') !== false) {
        $kode_awal = 'ELK';
    } elseif (strpos($namaKategori, 'barang') !== false) {
        $kode_awal = 'BRG';
    } elseif (strpos($namaKategori, 'sembako') !== false) {
        $kode_awal = 'SMB';
    } else {
        $kode_awal = 'PRD';
    }

    // Ambil kode terakhir
    $queryKode = mysqli_query($conn, "SELECT kode_produk FROM produk WHERE kode_produk LIKE '$kode_awal%' ORDER BY kode_produk DESC LIMIT 1");
    $kodeBaru = $kode_awal . "001"; // default

    if ($row = mysqli_fetch_assoc($queryKode)) {
        $lastKode = $row['kode_produk'];
        $lastNumber = (int)substr($lastKode, 3); // Ambil angka
        $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        $kodeBaru = $kode_awal . $newNumber;
    }

    echo $kodeBaru;
}
?>
