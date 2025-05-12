<?php include 'Sidebar.php';?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php
$bulan = isset($_GET['bulan']) ? (int)$_GET['bulan'] : date('n');
$tahun = isset($_GET['tahun']) ? (int)$_GET['tahun'] : date('Y');
?>
<br>
<div class="page-title">
                <h1><i class="fas fa-box-open me-2 text-primary"></i>Laporan Transaksi</h1>
                <nav aria-label="breadcrumb" class="breadcrumb-container">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="index-asli.php">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-content="page">Data Laporan Transaksi</li>
                    </ol>
                </nav>
            </div>
<div class="row">
    <div class="col-6 col-sm-6 col-md-6 col-lg-3 mb-3 pr-0">
        <div class="card-body bg-white py-2 px-1 border-laporan">
            <div class="row mx-auto align-items-center">
                <div class="col-auto m-pr-1">
                    <div class="bg-icon">
                        <i class="fa fa-user"></i>
                    </div>
                </div>
                <div class="col-auto pl-0 pt-2">
                    <div class="text-muted" style="font-size:11px;">
                        Pelanggan
                    </div>
                    <h4 class="1"><?php 
                    $itungpelanggan = mysqli_query($conn,"SELECT COUNT(idpelanggan) as jumlahpelanggan FROM pelanggan");
                    $cekrow1 = mysqli_num_rows($itungpelanggan);
                    $itungpelanggan1 = mysqli_fetch_assoc($itungpelanggan);
                    $itungpelanggan2 = $itungpelanggan1['jumlahpelanggan'];
                    if($cekrow1 > 0){
                        echo  $itungpelanggan2;
                        } ?></h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-sm-6 col-md-6 col-lg-3 mb-3 m-pr-0">
        <div class="card-body bg-white py-2 px-1 border-laporan">
            <div class="row mx-auto align-items-center">
                <div class="col-auto m-pr-1">
                    <div class="bg-icon">
                        <i class="fa fa-shopping-cart"></i>
                    </div>
                </div>
                <div class="col-auto pl-0 pt-2">
                    <div class="text-muted" style="font-size:11px;">
                        Terjual
                    </div>
                    <h4 class="1"><?php $itungpeterjual = mysqli_query($conn,"SELECT SUM(quantity) as jumlahterjual FROM tb_nota");
                    $cekrow = mysqli_num_rows($itungpeterjual);
                    $itungpeterjual1 = mysqli_fetch_assoc($itungpeterjual);
                    $itungpeterjual2 = $itungpeterjual1['jumlahterjual'];
                    if($cekrow > 0){
                        echo $itungpeterjual2;
                        } ?></h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-sm-6 col-md-6 col-lg-3 mb-3 pr-0">
        <div class="card-body bg-white py-2 px-1 border-laporan">
            <div class="row mx-auto align-items-center">
                <div class="col-auto m-pr-1">
                    <div class="bg-icon">
                        <i class="fa fa-dollar-sign"></i>
                    </div>
                </div>
                <div class="col-auto pl-0 pt-2">
                    <div class="text-muted" style="font-size:11px;">
                        Laba
                    </div>
                    <h4 class="1">Rp.<?php 
                    $data_produk=mysqli_query($conn,"SELECT * FROM tb_nota t, produk p
                    WHERE p.idproduk=t.idproduk ORDER BY idnota ASC");
                    $subtotaldiskon = 0;
                    $x = mysqli_num_rows($data_produk);
                    if($x>0){
                    while($b=mysqli_fetch_array($data_produk)){
                        $totalharga += $b['harga_jual'] * $b['quantity'];
                        $totaldiskon += $b['harga_modal'] * $b['quantity'];
                        $subtotaldiskon = $totalharga - $totaldiskon;
                    }
                } 
                echo ribuan($subtotaldiskon)?></h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-sm-6 col-md-6 col-lg-3 mb-3">
        <div class="card-body bg-white py-2 px-1 border-laporan">
            <div class="row mx-auto align-items-center">
                <div class="col-auto m-pr-1">
                    <div class="bg-icon">
                        <i class="fa fa-file-invoice-dollar"></i>
                    </div>
                </div>
                <div class="col-auto pl-0 pt-2">
                    <div class="text-muted" style="font-size:11px;">
                        Jumlah Transaksi
                    </div>
                    <h4 class="1">Rp.<?php 
                    $itungtotal = mysqli_query($conn,"SELECT SUM(totalbeli) as jumlahtotal FROM laporan");
                    $cekrow3 = mysqli_num_rows($itungtotal);
                    $itungtotal1 = mysqli_fetch_assoc($itungtotal);
                    $itungtotal2 = $itungtotal1['jumlahtotal'];
                    if($cekrow3 > 0){
                        echo ribuan($itungtotal2);
                        } ?></h4>
                </div>
            </div>
        </div>
    </div>
</div><!-- end row -->

<div class="card">
    <div class="card-header">
        <div class="card-title">
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

            <!-- Tombol cetak tetap muncul, meski belum submit -->
            <a href="cetak_laporan.php?bulan=<?= $bulan ?>&tahun=<?= $tahun ?>" class="btn btn-success btn-sm mt-2">
                <i class="fas fa-print"></i> Cetak Laporan
            </a>
        </div>
    </div>

    <div class="card-body">
        <?php
        // Menyiapkan variabel untuk menampung data filter
        $bulan = isset($_GET['bulan']) ? $_GET['bulan'] : '';
        $tahun = isset($_GET['tahun']) ? $_GET['tahun'] : '';
        $ada_filter = ($bulan != '' && $tahun != '');
        ?>

        <table class="table table-striped table-sm table-bordered dt-responsive nowrap" id="table" width="100%">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>No. Nota</th>
                    <th>Pelanggan</th>
                    <th>Qty</th>
                    <th>Catatan</th>
                    <th>SubTotal</th>
                    <th>Pembayaran</th>
                    <th>Kembalian</th>
                    <th>Metode</th>
                    <th>Tanggal</th>
                    <th>Opsi</th>
                </tr>
            </thead>
            <tbody>
            <?php
                $no=1;
                
                // Query dasar
                $query = "SELECT * FROM laporan l, pelanggan e WHERE e.idpelanggan=l.idpelanggan";
                
                // Tambahkan filter bulan dan tahun jika ada
                if ($ada_filter) {
                    $query .= " AND MONTH(l.tgl_sub)='$bulan' AND YEAR(l.tgl_sub)='$tahun'";
                }
                
                $query .= " ORDER BY idlaporan ASC";
                $data_produk = mysqli_query($conn, $query);
                
                // Cek apakah ada data
                if (mysqli_num_rows($data_produk) > 0) {
                    while($d=mysqli_fetch_array($data_produk)){
                        $idlaporan = $d['idlaporan'];
                        $nota = $d['no_nota'];
                ?>
                <tr>
                    <td><?php echo $no++ ?></td>
                    <td><?php echo $nota ?></td>
                    <td><?php echo $d['nama_pelanggan'] ?></td>
                    <td>
                        <?php 
                        $itungtrans = mysqli_query($conn,"SELECT SUM(quantity) as jumlahtrans
                            FROM tb_nota where no_nota='$nota'");
                        $itungtrans2 = mysqli_fetch_assoc($itungtrans);
                        echo $itungtrans2['jumlahtrans'];
                        ?>
                    </td>
                    <td class="catatan"><?php echo $d['catatan'] ?></td>
                    <td>Rp.<?php echo ribuan($d['totalbeli']) ?></td>
                    <td>Rp.<?php echo ribuan($d['pembayaran']) ?></td>
                    <td>Rp.<?php echo ribuan($d['kembalian']) ?></td>
                    <td><?php echo $d['metode'] ?></td>
                    <td><?php echo $d['tgl_sub'] ?></td>
                    <td>
                        <a class="btn btn-primary btn-xs" href="detail.php?invoice=<?php echo $nota ?>">
                            <i class="fa fa-eye fa-xs mr-1"></i>View</a>
                        <button class="btn btn-danger btn-xs hapusLaporan" data-id="<?= $nota ?>" data-nama="<?= htmlspecialchars($d['no_nota'], ENT_QUOTES) ?>">
                            <i class="fa fa-trash fa-xs mr-1"></i>Hapus
                        </button>
                    </td>
                </tr>        
                <?php 
                    }
                } else if ($ada_filter) {
                    // Tampilkan pesan jika filter aktif tapi tidak ada data
                    echo '<tr><td colspan="11" class="text-center">Tidak ada data transaksi untuk bulan ' . 
                         date('F', mktime(0, 0, 0, $bulan, 1)) . ' ' . $tahun . '</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Script SweetAlert2 Hapus -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const hapusButtons = document.querySelectorAll('.hapusLaporan');
        hapusButtons.forEach(button => {
            button.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                const nama = this.getAttribute('data-nama');

                Swal.fire({
                    title: 'Yakin ingin menghapus?',
                    text: "Data laporan '" + nama + "' akan dihapus!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "laporan.php?hapus=" + id;
                    }
                });
            });
        });
    });
</script>

<?php 
    if(!empty($_GET['hapus'])){
        $nota = $_GET['hapus'];
        $hapus_data = mysqli_query($conn, "DELETE FROM laporan WHERE no_nota='$nota'");
        $hapus_data1 = mysqli_query($conn, "DELETE FROM tb_nota WHERE no_nota='$nota'");
        if($hapus_data && $hapus_data1){
            // Mempertahankan parameter filter setelah penghapusan
            $redirect = "laporan.php";
            if (isset($_GET['bulan']) && isset($_GET['tahun'])) {
                $redirect .= "?bulan=" . $_GET['bulan'] . "&tahun=" . $_GET['tahun'];
            }
            
            echo '<script>
                    Swal.fire("Berhasil", "Data laporan telah dihapus!", "success").then(() => {
                        window.location="' . $redirect . '";
                    });
                  </script>';
        } else {
            echo '<script>
                    Swal.fire("Gagal", "Data laporan gagal dihapus.", "error");
                  </script>';
        }
    };
?>
<?php include 'template/footer.php';?>