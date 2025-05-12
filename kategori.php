<?php include 'Sidebar.php'; ?>
<br>

<?php
// Tambah kategori
if (isset($_POST['addkategori'])) {
    $namakategori = htmlspecialchars($_POST['nama_kategori']);
    $tambahkat = mysqli_query($conn, "INSERT INTO kategori (nama_kategori) VALUES ('$namakategori')");

    if ($tambahkat) {
        $_SESSION['pesan'] = ['success', 'Berhasil!', 'Kategori berhasil ditambahkan'];
    } else {
        $_SESSION['pesan'] = ['error', 'Gagal!', 'Kategori gagal ditambahkan'];
    }

    echo "<script>window.location='kategori.php';</script>";
    exit;
}

// Update kategori
if (isset($_POST['update'])) {
    $idkategori = htmlspecialchars($_POST['idkategori']);
    $namakategori = htmlspecialchars($_POST['nama_kategori']);
    $editup = mysqli_query($conn, "UPDATE kategori SET nama_kategori='$namakategori' WHERE idkategori='$idkategori'");

    if ($editup) {
        $_SESSION['pesan'] = ['success', 'Berhasil!', 'Kategori berhasil diperbarui'];
    } else {
        $_SESSION['pesan'] = ['error', 'Gagal!', 'Kategori gagal diperbarui'];
    }

    echo "<script>window.location='kategori.php';</script>";
    exit;
}
?>
<div class="page-title">
                <h1><i class="fas fa-box-open me-2 text-primary"></i>Kategori</h1>
                <nav aria-label="breadcrumb" class="breadcrumb-container">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-content="page">Kategori</li>
                    </ol>
                </nav>
            </div>

<div class="card">
    <div class="card-header">
        <div class="card-title">
            <i class="fa fa-table me-2 d-none d-sm-inline-block d-md-inline-block"></i> Data Kategori 
            <?php 
            if (!empty($_GET['edit'])):
                $idkategori = $_GET['edit'];
                $edit = mysqli_query($conn, "SELECT * FROM kategori WHERE idkategori='$idkategori'");
                $e = mysqli_fetch_array($edit);
                if ($e):
                    $namo = $e['nama_kategori'];
            ?>
                <form method="POST" class="float-right">
                    <input type="hidden" name="idkategori" value="<?= $idkategori ?>">
                    <div class="input-group">
                        <input type="text" name="nama_kategori" class="form-control form-control-sm bg-white"
                            style="border-radius:0.428rem 0px 0px 0.428rem;" placeholder="Masukan Kategori" 
                            value="<?= $namo ?>" required>
                        <div class="input-group-append">
                            <button class="btn btn-success btn-xs p-1" name="update"
                                style="border-radius: 0px 0.428rem 0.428rem 0px;" type="submit">
                                <i class="fas fa-check"></i>
                                <span class="d-none d-sm-inline-block d-md-inline-block ml-1">Update</span>
                            </button>
                            <a href="kategori.php" class="btn btn-danger btn-xs py-1 px-2 ml-1">
                                <i class="fas fa-times"></i>
                                <span class="d-none d-sm-inline-block d-md-inline-block ml-1">Batal</span>
                            </a>
                        </div>
                    </div>
                </form>
            <?php endif; else: ?>
                <form method="POST" class="float-right">
                    <div class="input-group">
                        <input type="text" name="nama_kategori" class="form-control form-control-sm bg-white"
                            style="border-radius:0.428rem 0px 0px 0.428rem;" placeholder="Masukan Kategori" required>
                        <div class="input-group-append">
                            <button class="btn btn-primary btn-xs p-1" name="addkategori"
                                style="border-radius: 0px 0.428rem 0.428rem 0px;" type="submit">
                                <i class="fa fa-plus"></i>
                                <span class="d-none d-sm-inline-block d-md-inline-block ml-1">Tambah</span>
                            </button>
                        </div>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <div class="card-body">
        <table class="table table-striped table-sm table-bordered dt-responsive nowrap" id="table" width="100%">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Nama Kategori</th>
                    <th>Qty</th>
                    <th>Tanggal</th>
                    <th>Opsi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                $query = "SELECT * FROM kategori ORDER BY idkategori ASC";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                while ($d = mysqli_fetch_array($result)):
                    $idkategori = $d['idkategori'];
                ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $d['nama_kategori'] ?></td>
                    <td>
                        <?php 
                        $result1 = mysqli_query($conn, "SELECT COUNT(idproduk) AS count FROM produk WHERE idkategori='$idkategori'");
                        $row1 = mysqli_fetch_assoc($result1);
                        echo ribuan($row1['count']);
                        ?>
                    </td>
                    <td><?= $d['tgl_dibuat'] ?></td>
                    <td>
                        <a href="?edit=<?= $idkategori ?>" class="btn btn-primary btn-xs">
                            <i class="fa fa-pen fa-xs mr-1"></i>Edit
                        </a>
                        <a class="btn btn-danger btn-xs" href="?hapus=<?= $idkategori ?>" data-nama="<?= $d['nama_kategori'] ?>">
                            <i class="fa fa-trash fa-xs mr-1"></i>Hapus
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Script SweetAlert2 Hapus -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const hapusButtons = document.querySelectorAll('.btn-danger');
        hapusButtons.forEach(button => {
            button.addEventListener('click', function (event) {
                event.preventDefault();
                const id = this.getAttribute('href').split('=')[1];
                const nama = this.getAttribute('data-nama');

                Swal.fire({
                    title: 'Yakin ingin menghapus?',
                    text: "Data kategori '" + nama + "' akan dihapus!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "kategori.php?hapus=" + id;
                    }
                });
            });
        });
    });
</script>

<?php 
// Hapus kategori
if (!empty($_GET['hapus'])) {
    $idkategori = $_GET['hapus'];
    $hapus_data = mysqli_query($conn, "DELETE FROM kategori WHERE idkategori='$idkategori'");

    if ($hapus_data) {
        $_SESSION['pesan'] = ['success', 'Berhasil!', 'Kategori berhasil dihapus'];
    } else {
        $_SESSION['pesan'] = ['error', 'Gagal!', 'Kategori gagal dihapus'];
    }

    echo "<script>
            Swal.fire({
                icon: '".$_SESSION['pesan'][0]."',
                title: '".$_SESSION['pesan'][1]."',
                text: '".$_SESSION['pesan'][2]."',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location = 'kategori.php';
            });
        </script>";
    exit;
}
?>

<?php if (isset($_SESSION['pesan'])): ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        Swal.fire({
            icon: '<?= $_SESSION['pesan'][0] ?>',
            title: '<?= $_SESSION['pesan'][1] ?>',
            text: '<?= $_SESSION['pesan'][2] ?>',
            confirmButtonText: 'OK'
        }).then(() => {
            <?php unset($_SESSION['pesan']); ?> // Pastikan pesan hanya muncul sekali
        });
    });
</script>
<?php endif; ?>

<?php include 'template/footer.php'; ?>
