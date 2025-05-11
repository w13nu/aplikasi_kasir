<?php
require 'vendor/autoload.php';
include 'config.php';
session_start();
$role = $_SESSION['role'] ?? '';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

$bulan = isset($_GET['bulan']) ? (int)$_GET['bulan'] : date('m');
$tahun = isset($_GET['tahun']) ? (int)$_GET['tahun'] : date('Y');

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle("Laporan Produk");

// Header
$sheet->setCellValue('A1', 'Laporan Produk Bulan ' . date('F', mktime(0, 0, 0, $bulan, 1)) . " $tahun");
$sheet->mergeCells('A1:K1'); // perbaikan sampai K
$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
$sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Header tabel
$header = ['No', 'Kode Produk', 'Nama Produk', 'Stok Awal', 'Terjual', 'Stok Akhir', 'Harga Modal', 'Harga Jual', 'Total Pembelian', 'Total Penjualan', 'Laba'];
$sheet->fromArray($header, NULL, 'A3');

// Format header
$headerStyle = [
    'font' => [
        'bold' => true,
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER,
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
        ],
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => [
            'rgb' => 'CCCCCC',
        ],
    ],
];
$sheet->getStyle('A3:K3')->applyFromArray($headerStyle);

// Data
$no = 1;
$row = 4;
$total_semua_pembelian = 0;
$total_semua_penjualan = 0;
$total_semua_laba = 0;

$query = "
    SELECT 
        p.*, 
        IFNULL(SUM(n.quantity), 0) AS terjual
    FROM 
        produk p
    LEFT JOIN 
        tb_nota n ON p.idproduk = n.idproduk 
        AND MONTH(n.tgl_nota) = '$bulan' 
        AND YEAR(n.tgl_nota) = '$tahun'
    GROUP BY p.idproduk
";

$data = mysqli_query($conn, $query);
while ($d = mysqli_fetch_array($data)) {
    $stok_akhir = (int)$d['stock'];
    $terjual = (int)$d['terjual'];
    $stok_awal = $stok_akhir + $terjual;

    $total_pembelian = $stok_awal * $d['harga_modal'];
    $total_penjualan = $terjual * $d['harga_jual'];
    $laba = ($terjual > 0) ? ($d['harga_jual'] - $d['harga_modal']) * $terjual : 0;
    
    // Tambahkan ke total keseluruhan
    $total_semua_pembelian += $total_pembelian;
    $total_semua_penjualan += $total_penjualan;
    $total_semua_laba += $laba;

    $sheet->setCellValue('A' . $row, $no++);
    $sheet->setCellValue('B' . $row, $d['kode_produk']);
    $sheet->setCellValue('C' . $row, $d['nama_produk']);
    $sheet->setCellValue('D' . $row, $stok_awal);
    $sheet->setCellValue('E' . $row, $terjual);
    $sheet->setCellValue('F' . $row, $stok_akhir);
    $sheet->setCellValue('G' . $row, $d['harga_modal']);
    $sheet->setCellValue('H' . $row, $d['harga_jual']);
    $sheet->setCellValue('I' . $row, $total_pembelian);
    $sheet->setCellValue('J' . $row, $total_penjualan);
    $sheet->setCellValue('K' . $row, $laba);
    
    $row++;
}

// Total row
$totalRow = $row;
$sheet->setCellValue('A' . $totalRow, 'TOTAL');
$sheet->mergeCells('A' . $totalRow . ':G' . $totalRow);
$sheet->setCellValue('I' . $totalRow, $total_semua_pembelian);
$sheet->setCellValue('J' . $totalRow, $total_semua_penjualan);
$sheet->setCellValue('K' . $totalRow, $total_semua_laba);

// Style for total row
$totalStyle = [
    'font' => [
        'bold' => true,
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => [
            'rgb' => 'DDEBF7',
        ],
    ],
];
$sheet->getStyle('A' . $totalRow . ':K' . $totalRow)->applyFromArray($totalStyle);

// Border untuk semua data
$borderStyle = [
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
        ],
    ],
];
$sheet->getStyle('A3:K' . $totalRow)->applyFromArray($borderStyle);

// Format kolom nilai uang dengan format rupiah
foreach (range('G', 'K') as $col) {
    // Format angka dengan pemisah ribuan
    $sheet->getStyle($col . '4:' . $col . $totalRow)->getNumberFormat()
        ->setFormatCode('#,##0');
    
    // Rata kanan untuk kolom nilai uang
    $sheet->getStyle($col . '4:' . $col . $totalRow)->getAlignment()
        ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
}

// Rata tengah untuk kolom nomor
$sheet->getStyle('A4:A' . ($totalRow-1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
// Rata tengah untuk kolom stok dan terjual
$sheet->getStyle('D4:F' . ($totalRow-1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Atur lebar kolom agar konten terlihat dengan baik
$sheet->getColumnDimension('A')->setWidth(5);   // No
$sheet->getColumnDimension('B')->setWidth(15);  // Kode Produk
$sheet->getColumnDimension('C')->setWidth(25);  // Nama Produk
$sheet->getColumnDimension('D')->setWidth(10);  // Stok Awal
$sheet->getColumnDimension('E')->setWidth(10);  // Terjual
$sheet->getColumnDimension('F')->setWidth(10);  // Stok Akhir
$sheet->getColumnDimension('G')->setWidth(15);  // Harga Modal
$sheet->getColumnDimension('H')->setWidth(15);  // Harga Jual
$sheet->getColumnDimension('I')->setWidth(18);  // Total Pembelian
$sheet->getColumnDimension('J')->setWidth(18);  // Total Penjualan
$sheet->getColumnDimension('K')->setWidth(18);

// Set header untuk download Excel
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment;filename=\"Laporan_Produk_" . date('F', mktime(0, 0, 0, $bulan, 1)) . "_$tahun.xlsx\"");
header('Cache-Control: max-age=0');

if ($role === 'kasir') {
    $protection = $sheet->getProtection();
    $protection->setSheet(true);
    $protection->setPassword('readonly123');
    $sheet->getStyle('A1:K' . $totalRow)->getProtection()->setLocked(true);
}

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;