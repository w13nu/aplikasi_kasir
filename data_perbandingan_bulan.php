<?php
include 'config.php';

$bulan1 = (int)($_GET['bulan1'] ?? 0);
$bulan2 = (int)($_GET['bulan2'] ?? 0);
$tahun = date('Y');

function getData($conn, $bulan, $tahun) {
    // Penjualan total dari tabel 'laporan' berdasarkan tgl_sub
    $q1 = mysqli_query($conn, "SELECT SUM(totalbeli) as total FROM laporan WHERE MONTH(tgl_sub) = $bulan AND YEAR(tgl_sub) = $tahun");
    $r1 = mysqli_fetch_assoc($q1);
    $penjualan = (int)($r1['total'] ?? 0);

    // Laba dari tb_nota dan produk berdasarkan tgl_nota
    $q2 = mysqli_query($conn, "SELECT t.quantity, p.harga_modal, p.harga_jual 
        FROM tb_nota t 
        JOIN produk p ON t.idproduk = p.idproduk 
        WHERE MONTH(t.tgl_nota) = $bulan AND YEAR(t.tgl_nota) = $tahun");

    $totalLaba = 0;
    while ($r2 = mysqli_fetch_assoc($q2)) {
        $totalLaba += ($r2['harga_jual'] - $r2['harga_modal']) * $r2['quantity'];
    }

    return [$penjualan, $totalLaba];
}

$data1 = getData($conn, $bulan1, $tahun);
$data2 = getData($conn, $bulan2, $tahun);

echo json_encode([
    'penjualan' => [$data1[0], $data2[0]],
    'laba' => [$data1[1], $data2[1]]
]);
