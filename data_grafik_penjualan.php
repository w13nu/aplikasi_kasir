<?php
include 'config.php';

$tahun = date('Y');
$bulan = [];
$penjualan = [];
$laba = [];

for ($i = 1; $i <= 12; $i++) {
    $bulanLabel = date('F', mktime(0, 0, 0, $i, 1));
    $bulan[] = $bulanLabel;

    // Penjualan total
    $q1 = mysqli_query($conn, "SELECT SUM(totalbeli) as total FROM laporan WHERE MONTH(tgl_sub) = $i AND YEAR(tgl_sub) = $tahun");
    $r1 = mysqli_fetch_assoc($q1);
    $penjualan[] = (int)($r1['total'] ?? 0);

    // Laba dari tb_nota dan produk
    $q2 = mysqli_query($conn, "SELECT t.quantity, p.harga_modal, p.harga_jual 
        FROM tb_nota t 
        JOIN produk p ON t.idproduk = p.idproduk 
        WHERE MONTH(t.tgl_nota) = $i AND YEAR(t.tgl_nota) = $tahun");

    $totalLaba = 0;
    while ($r2 = mysqli_fetch_assoc($q2)) {
        $totalLaba += ($r2['harga_jual'] - $r2['harga_modal']) * $r2['quantity'];
    }
    $laba[] = $totalLaba;
}

echo json_encode([
    'bulan' => $bulan,
    'penjualan' => $penjualan,
    'laba' => $laba
]);
