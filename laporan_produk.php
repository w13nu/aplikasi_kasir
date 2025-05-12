<?php 
include 'Sidebar.php'; 
include 'config.php'; // pastikan koneksi tersedia

// Ambil filter bulan dan tahun
$bulan = isset($_GET['bulan']) ? (int)$_GET['bulan'] : date('m');
$tahun = isset($_GET['tahun']) ? (int)$_GET['tahun'] : date('Y');
?>

<br>
<div class="page-title">
                <h1><i class="fas fa-box-open me-2 text-primary"></i>Laporan Produk</h1>
                <nav aria-label="breadcrumb" class="breadcrumb-container">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-content="page">Data Laporan Produk</li>
                    </ol>
                </nav>
            </div>
<div class="card">
    <div class="card-header">
        <h5 class="card-title">Laporan Produk (Stok, Penjualan & Laba)</h5>
        <form method="GET" class="row g-2 mt-3">
            <div class="col-md-3">
                <select name="bulan" class="form-control" required>
                    <option value="">Pilih Bulan</option>
                    <?php for($i=1; $i<=12; $i++): ?>
                        <option value="<?= $i ?>" <?= ($i == $bulan) ? 'selected' : '' ?>>
                            <?= date('F', mktime(0, 0, 0, $i, 1)) ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-2">
                <input type="number" name="tahun" class="form-control" placeholder="Tahun" value="<?= $tahun ?>" required>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">Tampilkan</button>
            </div>
        </form>
        <!-- Tombol cetak diperbaiki -->
        <a href="cetak_laporan_produk.php?bulan=<?= $bulan ?>&tahun=<?= $tahun ?>" class="btn btn-success btn-sm mt-2">
            <i class="fas fa-print"></i> Cetak Laporan
        </a>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-sm table-striped" id="table" width="100%">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode Produk</th>
                    <th>Nama Produk</th>
                    <th>Stok Awal</th>
                    <th>Terjual</th>
                    <th>Stok Akhir</th>
                    <th>Harga Modal</th>
                    <th>Harga Jual</th>
                    <th>Total Pembelian</th>
                    <th>Total Penjualan</th>
                    <th>Laba</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
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
                        tb_nota n 
                        ON p.idproduk = n.idproduk 
                        AND MONTH(n.tgl_nota) = '$bulan' 
                        AND YEAR(n.tgl_nota) = '$tahun'
                    GROUP BY 
                        p.idproduk
                ";
                $data = mysqli_query($conn, $query);
                
                while($d = mysqli_fetch_array($data)){
                    $stok_akhir = (int) $d['stock'];
                    $terjual = (int) $d['terjual'];
                    $stok_awal = $stok_akhir + $terjual;

                    $total_pembelian = $stok_awal * $d['harga_modal'];
                    $total_penjualan = $terjual * $d['harga_jual'];

                    $laba = ($terjual > 0) ? ($d['harga_jual'] - $d['harga_modal']) * $terjual : 0;
                    
                    // Menambahkan total ke variabel akumulasi
                    $total_semua_pembelian += $total_pembelian;
                    $total_semua_penjualan += $total_penjualan;
                    $total_semua_laba += $laba;
                ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($d['kode_produk']) ?></td>
                    <td><?= htmlspecialchars($d['nama_produk']) ?></td>
                    <td><?= $stok_awal ?></td>
                    <td><?= $terjual ?></td>
                    <td><?= $stok_akhir ?></td>
                    <td>Rp.<?= ribuan($d['harga_modal']) ?></td>
                    <td>Rp.<?= ribuan($d['harga_jual']) ?></td>
                    <td>Rp.<?= ribuan($total_pembelian) ?></td>
                    <td>Rp.<?= ribuan($total_penjualan) ?></td>
                    <td>Rp.<?= ribuan($laba) ?></td>
                </tr>
                <?php } ?>
            </tbody>
            <tfoot>
                <tr class="table-primary">
                    <th colspan="8">TOTAL</th>
                    <th>Rp.<?= ribuan($total_semua_pembelian) ?></th>
                    <th>Rp.<?= ribuan($total_semua_penjualan) ?></th>
                    <th>Rp.<?= ribuan($total_semua_laba) ?></th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<?php include 'template/footer.php'; ?>