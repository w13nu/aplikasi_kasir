<?php include 'Sidebar.php';?>
<br>
<?php
            if (isset($_POST['tambahProduk'])) {
                $idkategori = htmlspecialchars($_POST['idkategori']);
                $kodeproduk = htmlspecialchars($_POST['kode_produk']);
                $namaproduk = htmlspecialchars($_POST['nama_produk']);
                $stock = htmlspecialchars($_POST['stock']);
                $harga_modal = htmlspecialchars($_POST['harga_modal']);
                $harga_jual = htmlspecialchars($_POST['harga_jual']);
            
                $tambah = mysqli_query($conn, "INSERT INTO produk (idkategori,kode_produk,nama_produk,stock,harga_modal,harga_jual,satuan)
                VALUES ('$idkategori', '$kodeproduk', '$namaproduk', '$stock', '$harga_modal', '$harga_jual')");
            
                if ($tambah) {
                    $_SESSION['pesan'] = ['success', 'Berhasil!', 'Berhasil Menambahkan Data Produk'];
                } else {
                    $_SESSION['pesan'] = ['error', 'Gagal!', 'Gagal Menambahkan Data Produk'];
                }
            
                echo "<script>window.location='produk.php';</script>";
                exit;
            }
            
            // Update Produk
            if (isset($_POST['updateProduk'])) {
                $idproduk = htmlspecialchars($_POST['idproduk']);
                $idkategori = htmlspecialchars($_POST['idkategori']);
                $kodeproduk = htmlspecialchars($_POST['kode_produk']);
                $namaproduk = htmlspecialchars($_POST['nama_produk']);
                $stock = htmlspecialchars($_POST['stock']);
                $harga_modal = htmlspecialchars($_POST['harga_modal']);
                $harga_jual = htmlspecialchars($_POST['harga_jual']);
            
                $update = mysqli_query($conn, "UPDATE produk SET
                    idkategori='$idkategori',
                    nama_produk='$namaproduk',
                    kode_produk='$kodeproduk',
                    stock='$stock',
                    harga_modal='$harga_modal',
                    harga_jual='$harga_jual',
                    WHERE idproduk='$idproduk'");
            
                if ($update) {
                    $_SESSION['pesan'] = ['success', 'Berhasil!', 'Produk berhasil diupdate'];
                } else {
                    $_SESSION['pesan'] = ['error', 'Gagal!', 'Gagal update produk'];
                }
            
                echo "<script>window.location='produk.php';</script>";
                exit;
            }
            ?>
            <div class="page-title">
                <h1><i class="fas fa-box-open me-2 text-primary"></i>Data Produk</h1>
                <nav aria-label="breadcrumb" class="breadcrumb-container">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="index-asli.php">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-content="page">Produk</li>
                    </ol>
                </nav>
            </div>
<div class="card">
    <div class="card-header">
        <div class="card-tittle"><i class="fa fa-table me-2"></i> Data Produk 
        <button type="button" class="btn btn-primary btn-xs p-2 float-right" data-toggle="modal" data-target="#addproduk">
            <i class="fa fa-plus fa-xs mr-1"></i> Tambah Produk</button></div>
    </div>
        <div class="card-body">
            <table class="table table-striped table-sm table-bordered dt-responsive nowrap" id="table" width="100%">
                        <thead>
                        <tr>
                            <th>No.</th>
                            <th>Kode Produk</th>
                            <th>Nama Produk</th>
                            <th>Kategori</th>
                            <th>Stock</th>
                            <th>Harga Modal</th>
                            <th>Harga Jual</th>
                            <th>Tanggal Input</th>
                            <th>Opsi</th>
                        </tr>
                        </thead>
                        <tbody>
                            <?php
                                $no=1;
                                $data_produk=mysqli_query($conn,"SELECT * FROM kategori k, produk p WHERE k.idkategori=p.idkategori order by idproduk ASC");
                                while($d=mysqli_fetch_array($data_produk)){
                                    $idproduk = $d['idproduk'];
                                    ?>
                                    
                                    <tr>
                                        <td><?php echo $no++ ?></td>
                                        <td><?php echo $d['kode_produk'] ?></td>
                                        <td><?php echo $d['nama_produk'] ?></td>
                                        <td><?php echo $d['nama_kategori'] ?></td>
                                        <td><?php echo $d['stock'] ?></td>
                                        <td>Rp.<?php echo ribuan($d['harga_modal']) ?></td>
                                        <td>Rp.<?php echo ribuan($d['harga_jual']) ?></td>
                                        <td><?php echo $d['tgl_input'] ?></td>
                                        <td>
                                            <button type="button" class="btn btn-primary btn-xs"
                                             data-toggle="modal" data-target="#editP<?php echo $idproduk ?>">
                                             <i class="fa fa-pen fa-xs mr-1"></i>Edit</button>
                                            <a class="btn btn-danger btn-xs" href="?hapus=<?php echo $idproduk ?>" 
                                            onclick="javascript:return confirm('Hapus Data produk - <?php echo $d['nama_produk'] ?> ?');">
                                            <i class="fa fa-trash fa-xs mr-1"></i>Hapus</a>
                                        </td>
                                    </tr>
                                    
                                    <!-- modal edit -->
                                    <div class="modal fade" id="editP<?php echo $idproduk ?>" tabindex="-1" role="dialog" aria-labelledby="ModalTittle2" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                            <form method="post">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="ModalTittle2"><i class="fa fa-shopping-bag mr-1 text-muted"></i> Edit Produk</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="form-group mb-2">
                                                    <label>Kode Produk :</label>
                                                    <input type="hidden" name="idproduk" class="form-control" value="<?php echo $d['idproduk'] ?>">
                                                    <input type="text" name="kode_produk" class="form-control" value="<?php echo $d['kode_produk'] ?>" readonly>
                                                </div>
                                                <div class="form-group mb-2">
                                                    <label>Nama Produk :</label>
                                                    <input type="text" name="nama_produk" class="form-control" value="<?php echo $d['nama_produk'] ?>" required>
                                                </div>
                                                <div class="form-group mb-2">
                                                    <label>Kategori Produk :</label>
                                                        <select name="idkategori" class="form-control" required>
                                                            <option value="<?php echo $d['idkategori'] ?>" class="small"><?php echo $d['nama_kategori'] ?></option>
                                                        <?php
                                                        $dataK=mysqli_query($conn,"SELECT * FROM kategori ORDER BY nama_kategori ASC")or die(mysqli_error());
                                                        while($dk=mysqli_fetch_array($dataK)){
                                                        ?>
                                                            <option value="<?php echo $dk['idkategori'] ?>" class="small"><?php echo $dk['nama_kategori'] ?></option>
                                                            <?php } ?>
                                                        </select>
                                                </div>
                                                <div class="row mb-2">
                                                    <div class="col-2 col-md-2 pr-0">
                                                        <label>Stock :</label>
                                                        <input type="number" name="stock" class="form-control" value="<?php echo $d['stock'] ?>" required>
                                                    </div>
                                                    <div class="col-5 col-md-5 pr-0">
                                                        <label>Harga Modal :</label>
                                                        <input type="number" name="harga_modal" value="<?php echo $d['harga_modal'] ?>" class="form-control" required>
                                                    </div>
                                                    <div class="col-5 col-md-5">
                                                        <label>Harga Jual :</label>
                                                        <input type="number" name="harga_jual" value="<?php echo $d['harga_jual'] ?>" class="form-control" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-light btn-xs p-2" data-dismiss="modal">
                                                    <i class="fa fa-times mr-1"></i> Batal</button>
                                                <button type="submit" class="btn btn-primary btn-xs p-2" name="updateProduk">
                                                <i class="fa fa-plus-circle mr-1"></i> Simpan</button>
                                            </div>
                                            </form>
                                            </div>
                                        </div>
                                        </div>
                                    <!-- end modal edit -->
                        <?php }?>
					</tbody>
                </table>
        </div>
</div>
<?php 
	if (!empty($_GET['hapus'])) {
        $idproduk = $_GET['hapus'];
        $hapus = mysqli_query($conn, "DELETE FROM produk WHERE idproduk='$idproduk'");
    
        if ($hapus) {
            $_SESSION['pesan'] = ['success', 'Berhasil!', 'Produk berhasil dihapus'];
        } else {
            $_SESSION['pesan'] = ['error', 'Gagal!', 'Gagal menghapus produk'];
        }
    
        echo "<script>window.location='produk.php';</script>";
        exit;
    }
    ?>
<!-- Modal -->
<div class="modal fade" id="addproduk" tabindex="-1" role="dialog" aria-labelledby="ModalTittle" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="post">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fa fa-plus-circle mr-1 text-muted"></i> Tambah Produk</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-2">
                        <label>Kode Produk:</label>
                        <input type="text" name="kode_produk" class="form-control" placeholder="Otomatis berdasarkan kategori" readonly required>
                    </div>
                    <div class="form-group mb-2">
                        <label>Nama Produk:</label>
                        <input type="text" name="nama_produk" class="form-control" placeholder="Nama Produk" required>
                    </div>
                    <div class="form-group mb-2">
                        <label>Kategori:</label>
                        <select name="idkategori" class="form-control" required>
                            <option value="" disabled selected>Pilih Kategori</option>
                            <?php
                            $dataK = mysqli_query($conn, "SELECT * FROM kategori ORDER BY nama_kategori ASC");
                            while ($dk = mysqli_fetch_array($dataK)) {
                                echo "<option value='" . $dk['idkategori'] . "'>" . $dk['nama_kategori'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-4">
                            <label>Stock:</label>
                            <input type="number" name="stock" class="form-control" placeholder="Stock" required>
                        </div>
                        <div class="col-4">
                            <label>Harga Modal:</label>
                            <input type="number" name="harga_modal" class="form-control" placeholder="Harga Modal" required>
                        </div>
                        <div class="col-4">
                            <label>Harga Jual:</label>
                            <input type="number" name="harga_jual" class="form-control" placeholder="Harga Jual" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light btn-xs p-2" data-dismiss="modal">
                        <i class="fa fa-times mr-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary btn-xs p-2" name="tambahProduk">
                        <i class="fa fa-plus-circle mr-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.querySelector('#addproduk');
    const kategoriSelect = modal.querySelector('select[name="idkategori"]');
    const kodeProdukInput = modal.querySelector('input[name="kode_produk"]');

    kategoriSelect.addEventListener('change', function () {
        const idkategori = this.value;
        if (idkategori) {
            fetch('kode_produk.php?idkategori=' + idkategori)
                .then(response => response.text())
                .then(data => {
                    kodeProdukInput.value = data;
                });
        } else {
            kodeProdukInput.value = '';
        }
    });
});
</script>

<?php if (isset($_SESSION['pesan'])): ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        Swal.fire({
            icon: '<?= $_SESSION['pesan'][0] ?>',
            title: '<?= $_SESSION['pesan'][1] ?>',
            text: '<?= $_SESSION['pesan'][2] ?>',
            confirmButtonText: 'OK'
        });
    });
</script>
<?php unset($_SESSION['pesan']); ?>
<?php endif; ?>

<?php include 'template/footer.php';?>
