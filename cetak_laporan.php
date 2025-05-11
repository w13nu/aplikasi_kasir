<?php
include 'config.php';
require 'vendor/autoload.php';
session_start(); // penting agar $_SESSION tersedia
$role = $_SESSION['role'] ?? '';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

// Fungsi untuk menambahkan pemisah ribuan
function ribuan($angka) {
    return number_format($angka, 0, ',', '.');
}

$filter = $_GET['filter'] ?? '';
$tanggal = $_GET['tanggal'] ?? '';
$bulan = $_GET['bulan'] ?? '';

// Validasi filter
if (($filter === 'harian' && empty($tanggal)) || ($filter === 'bulanan' && empty($bulan))) {
    die("Filter atau parameter tidak valid. Pastikan Anda memilih filter harian dengan tanggal atau filter bulanan dengan bulan dan tahun.");
}

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Menentukan judul berdasarkan filter
if ($filter === 'harian' && !empty($tanggal)) {
    $judul = "Laporan Penjualan Harian Tanggal " . date('d F Y', strtotime($tanggal));
    $filename = "Laporan_Harian_" . date('d-m-Y', strtotime($tanggal)) . ".xlsx";
} elseif ($filter === 'bulanan' && !empty($bulan)) {
    $bulan_tahun = date('F Y', strtotime($bulan . "-01"));
    $judul = "Laporan Penjualan Bulanan " . $bulan_tahun;
    $filename = "Laporan_Bulanan_" . date('F_Y', strtotime($bulan . "-01")) . ".xlsx";
} else {
    $currentMonthYear = date('F Y');
    $judul = "Laporan Penjualan Keseluruhan Bulan " . $currentMonthYear;
    $filename = "Laporan_Keseluruhan_" . date('F_Y') . ".xlsx";
}

// Judul Laporan
$sheet->setCellValue('A1', $judul);
$sheet->mergeCells('A1:I1');
$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
$sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Header Kolom
$header = ['No', 'No. Nota', 'Pelanggan', 'Qty', 'Catatan', 'Metode', 'SubTotal', 'Pembayaran', 'Kembalian', 'Tanggal'];
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
$sheet->getStyle('A3:J3')->applyFromArray($headerStyle);

// Query untuk data
$query = "SELECT * FROM laporan l, pelanggan e WHERE e.idpelanggan=l.idpelanggan";
if ($filter === 'harian' && $tanggal) {
    $query .= " AND DATE(tgl_sub) = '$tanggal'";
} elseif ($filter === 'bulanan' && $bulan) {
    $query .= " AND DATE_FORMAT(tgl_sub, '%Y-%m') = '$bulan'";
}
$query .= " ORDER BY idlaporan ASC";

$result = mysqli_query($conn, $query);
$rowNum = 4; // Mulai dari baris keempat (setelah header)
$no = 1;

// Variabel untuk total
$total_subtotal = 0;
$total_pembayaran = 0;
$total_kembalian = 0;

// Menulis data ke Excel
while ($rowData = mysqli_fetch_array($result)) {
    $nota = $rowData['no_nota'];
    
    // Query untuk mendapatkan Qty
    $qty_query = mysqli_query($conn, "SELECT SUM(quantity) as jumlahtrans FROM tb_nota WHERE no_nota='$nota'");
    $qty_result = mysqli_fetch_assoc($qty_query);
    $qty = $qty_result['jumlahtrans'] ?? 0;

    // Menambahkan ke total
    $total_subtotal += $rowData['totalbeli'];
    $total_pembayaran += $rowData['pembayaran'];
    $total_kembalian += $rowData['kembalian'];

    // Menuliskan data ke file Excel
    $sheet->setCellValue('A' . $rowNum, $no++);
    $sheet->setCellValue('B' . $rowNum, $rowData['no_nota']);
    $sheet->setCellValue('C' . $rowNum, $rowData['nama_pelanggan']);
    $sheet->setCellValue('D' . $rowNum, $qty);
    $sheet->setCellValue('E' . $rowNum, $rowData['catatan']);
    $sheet->setCellValue('F' . $rowNum, $rowData['metode']);
    $sheet->setCellValue('G' . $rowNum, $rowData['totalbeli']);
    $sheet->setCellValue('H' . $rowNum, $rowData['pembayaran']);
    $sheet->setCellValue('I' . $rowNum, $rowData['kembalian']);
    $sheet->setCellValue('J' . $rowNum, date('d-m-Y', strtotime($rowData['tgl_sub'])));
    
    $rowNum++;
}

// Total row
$totalRow = $rowNum;
$sheet->setCellValue('A' . $totalRow, 'TOTAL');
$sheet->mergeCells('A' . $totalRow . ':F' . $totalRow);
$sheet->setCellValue('G' . $totalRow, $total_subtotal);
$sheet->setCellValue('H' . $totalRow, $total_pembayaran);
$sheet->setCellValue('I' . $totalRow, $total_kembalian);

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
$sheet->getStyle('A' . $totalRow . ':J' . $totalRow)->applyFromArray($totalStyle);

// Border untuk semua data
$borderStyle = [
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
        ],
    ],
];
$sheet->getStyle('A3:J' . $totalRow)->applyFromArray($borderStyle);

// Format kolom nilai uang dengan format rupiah
foreach (range('F', 'H') as $col) {
    // Format angka dengan pemisah ribuan
    $sheet->getStyle($col . '4:' . $col . $totalRow)->getNumberFormat()
        ->setFormatCode('#,##0');
    
    // Rata kanan untuk kolom nilai uang
    $sheet->getStyle($col . '4:' . $col . $totalRow)->getAlignment()
        ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
}

// Rata tengah untuk kolom nomor, no nota, dan qty
$sheet->getStyle('A4:A' . ($totalRow-1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('B4:B' . ($totalRow-1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('D4:D' . ($totalRow-1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('I4:I' . ($totalRow-1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('F4:F' . ($totalRow - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);


// Atur lebar kolom agar konten terlihat dengan baik
$sheet->getColumnDimension('A')->setWidth(5);
$sheet->getColumnDimension('B')->setWidth(15);
$sheet->getColumnDimension('C')->setWidth(25);
$sheet->getColumnDimension('D')->setWidth(8);
$sheet->getColumnDimension('E')->setWidth(30);
$sheet->getColumnDimension('F')->setWidth(15);
$sheet->getColumnDimension('G')->setWidth(15);
$sheet->getColumnDimension('H')->setWidth(15);
$sheet->getColumnDimension('I')->setWidth(15);
$sheet->getColumnDimension('J')->setWidth(15);

// Bersihkan buffer output
ob_clean();

// Set header untuk download Excel
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: max-age=0');

if ($role === 'kasir') {
    $protection = $sheet->getProtection();
    $protection->setSheet(true);
    $protection->setPassword('readonly123');
    $sheet->getStyle('A1:J' . $totalRow)->getProtection()->setLocked(true);
}

// Simpan file Excel ke output
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;