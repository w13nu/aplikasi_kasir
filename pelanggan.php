<?php include 'Sidebar.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<br>

<?php
if (isset($_POST['tambahPelanggan'])) {
    $nama_pelanggan = htmlspecialchars($_POST['nama_pelanggan']);
    $telepon_pelanggan = htmlspecialchars($_POST['telepon_pelanggan']);
    $alamat_pelanggan = htmlspecialchars($_POST['alamat_pelanggan']);

    $tambah = mysqli_query($conn, "INSERT INTO pelanggan (nama_pelanggan, telepon_pelanggan, alamat_pelanggan)
                                   VALUES ('$nama_pelanggan', '$telepon_pelanggan', '$alamat_pelanggan')");
    if ($tambah) {
        echo '<script>
            Swal.fire({
                icon: "success",
                title: "Berhasil",
                text: "Data pelanggan berhasil ditambahkan!",
                showConfirmButton: false,
                timer: 2000
            }).then(() => {
                window.location.href = "pelanggan.php";
            });
        </script>';
    } else {
        echo '<script>
            Swal.fire({
                icon: "error",
                title: "Gagal",
                text: "Gagal menambahkan data pelanggan!",
                showConfirmButton: false,
                timer: 2000
            }).then(() => {
                history.back();
            });
        </script>';
    }
}

if (isset($_POST['updatePelanggan'])) {
    $id = htmlspecialchars($_POST['idpelanggan']);
    $nama = htmlspecialchars($_POST['nama_pelanggan']);
    $telepon = htmlspecialchars($_POST['telepon_pelanggan']);
    $alamat = htmlspecialchars($_POST['alamat_pelanggan']);

    $update = mysqli_query($conn, "UPDATE pelanggan SET
                                    nama_pelanggan='$nama',
                                    telepon_pelanggan='$telepon',
                                    alamat_pelanggan='$alamat'
                                    WHERE idpelanggan='$id'");
    if ($update) {
        echo '<script>
            Swal.fire({
                icon: "success",
                title: "Berhasil",
                text: "Data pelanggan berhasil diperbarui!",
                showConfirmButton: false,
                timer: 2000
            }).then(() => {
                window.location.href = "pelanggan.php";
            });
        </script>';
    }
}

if (!empty($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $hapus = mysqli_query($conn, "DELETE FROM pelanggan WHERE idpelanggan='$id'");
    if ($hapus) {
        echo '<script>
            Swal.fire({
                icon: "success",
                title: "Berhasil",
                text: "Data pelanggan berhasil dihapus!",
                showConfirmButton: false,
                timer: 2000
            }).then(() => {
                window.location.href = "pelanggan.php";
            });
        </script>';
    } else {
        echo '<script>
            Swal.fire({
                icon: "error",
                title: "Gagal",
                text: "Gagal menghapus data pelanggan!",
                showConfirmButton: false,
                timer: 2000
            }).then(() => {
                history.back();
            });
        </script>';
    }
}
?>

<div class="page-title">
                <h1><i class="fas fa-box-open me-2 text-primary"></i>Data Pelanggan</h1>
                <nav aria-label="breadcrumb" class="breadcrumb-container">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-content="page">Data Pelanggan</li>
                    </ol>
                </nav>
            </div>
<div class="card">
    <div class="card-header">
        <div class="card-title">
            <i class="fa fa-table me-2"></i> Data Pelanggan
            <button type="button" class="btn btn-primary btn-xs p-2 float-right" data-toggle="modal" data-target="#addpelanggan">
                <i class="fa fa-plus fa-xs mr-1"></i> Tambah Data
            </button>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-striped table-sm table-bordered dt-responsive nowrap" id="table" width="100%">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Nama Pelanggan</th>
                    <th>Telepon</th>
                    <th>Alamat</th>
                    <th>Opsi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                $pelanggan = mysqli_query($conn, "SELECT * FROM pelanggan ORDER BY idpelanggan ASC");
                while ($d = mysqli_fetch_array($pelanggan)) {
                    $id = $d['idpelanggan'];
                ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $d['nama_pelanggan'] ?></td>
                        <td><?= $d['telepon_pelanggan'] ?></td>
                        <td><?= $d['alamat_pelanggan'] ?></td>
                        <td>
                            <button type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#editP<?= $id ?>">
                                <i class="fa fa-pen fa-xs mr-1"></i>Edit
                            </button>
                            <button type="button" class="btn btn-danger btn-xs hapusPelanggan"
                                data-id="<?= $id ?>"
                                data-nama="<?= htmlspecialchars($d['nama_pelanggan'], ENT_QUOTES) ?>">
                                <i class="fa fa-trash fa-xs mr-1"></i>Hapus
                            </button>
                        </td>
                    </tr>

                    <!-- Modal Edit -->
                    <div class="modal fade" id="editP<?= $id ?>" tabindex="-1" role="dialog" aria-labelledby="ModalEditTitle" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <form method="post">
                                    <div class="modal-header">
                                        <h5 class="modal-title"><i class="fa fa-user mr-1 text-muted"></i> Edit Pelanggan</h5>
                                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="idpelanggan" value="<?= $id ?>">
                                        <div class="form-group mb-2">
                                            <label>Nama Pelanggan:</label>
                                            <input type="text" name="nama_pelanggan" class="form-control" value="<?= $d['nama_pelanggan'] ?>" required>
                                        </div>
                                        <div class="form-group mb-2">
                                            <label>Telepon:</label>
                                            <input type="number" name="telepon_pelanggan" class="form-control" value="<?= $d['telepon_pelanggan'] ?>" required>
                                        </div>
                                        <div class="form-group mb-2">
                                            <label>Alamat:</label>
                                            <input type="text" name="alamat_pelanggan" class="form-control" value="<?= $d['alamat_pelanggan'] ?>" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light btn-xs p-2" data-dismiss="modal">
                                            <i class="fa fa-times mr-1"></i> Batal
                                        </button>
                                        <button type="submit" class="btn btn-primary btn-xs p-2" name="updatePelanggan">
                                            <i class="fa fa-save mr-1"></i> Simpan
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- End Modal Edit -->
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="addpelanggan" tabindex="-1" role="dialog" aria-labelledby="ModalTambah" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="post">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fa fa-user-plus mr-1 text-muted"></i> Tambah Pelanggan</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-2">
                        <label>Nama Pelanggan:</label>
                        <input type="text" name="nama_pelanggan" class="form-control" required>
                    </div>
                    <div class="form-group mb-2">
                        <label>Telepon:</label>
                        <input type="number" name="telepon_pelanggan" class="form-control" required>
                    </div>
                    <div class="form-group mb-2">
                        <label>Alamat:</label>
                        <input type="text" name="alamat_pelanggan" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light btn-xs p-2" data-dismiss="modal">
                        <i class="fa fa-times mr-1"></i> Batal
                    </button>
                    <button type="reset" class="btn btn-danger btn-xs p-2">
                        <i class="fa fa-trash-restore-alt mr-1"></i> Reset
                    </button>
                    <button type="submit" class="btn btn-primary btn-xs p-2" name="tambahPelanggan">
                        <i class="fa fa-plus-circle mr-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Script SweetAlert2 Hapus -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const hapusButtons = document.querySelectorAll('.hapusPelanggan');
        hapusButtons.forEach(button => {
            button.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                const nama = this.getAttribute('data-nama');

                Swal.fire({
                    title: 'Yakin ingin menghapus?',
                    text: "Data pelanggan '" + nama + "' akan dihapus!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "pelanggan.php?hapus=" + id;
                    }
                });
            });
        });
    });
</script>

<?php include 'template/footer.php'; ?>
