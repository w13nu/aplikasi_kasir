<?php
include 'Sidebar.php';
include 'config.php';

// Update akun sendiri
if (isset($_POST['update'])) {
    $id     = htmlspecialchars($_POST['id_login']);
    $user   = htmlspecialchars($_POST['username']);
    $toko   = htmlspecialchars($_POST['nama_toko']);
    $alamat = htmlspecialchars($_POST['alamat']);
    $telp   = htmlspecialchars($_POST['telepon']);
    $pass   = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $result = mysqli_query($conn, "UPDATE login SET 
        username='$user', 
        password='$pass',
        nama_toko='$toko',
        alamat='$alamat',
        telepon='$telp' 
        WHERE id_login = '$id'") or die(mysqli_error($conn));

    if ($result) {
        echo '<script>alert("Data berhasil diupdate");window.location="pengaturan.php"</script>';
    } else {
        echo '<script>alert("Data gagal diupdate");history.go(-1);</script>';
    }
}

// Tambah user baru
if (isset($_POST['tambah_user'])) {
    $user   = htmlspecialchars($_POST['new_username']);
    $pass   = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
    $toko   = htmlspecialchars($_POST['new_nama_toko']);
    $alamat = htmlspecialchars($_POST['new_alamat']);
    $telp   = htmlspecialchars($_POST['new_telepon']);
    $role   = htmlspecialchars($_POST['new_role']);

    $result = mysqli_query($conn, "INSERT INTO login (username, password, nama_toko, alamat, telepon, role) 
        VALUES ('$user', '$pass', '$toko', '$alamat', '$telp', '$role')") or die(mysqli_error($conn));

    if ($result) {
        echo '<script>alert("User baru berhasil ditambahkan");window.location="pengaturan.php"</script>';
    } else {
        echo '<script>alert("Gagal menambahkan user baru");history.go(-1);</script>';
    }
}

// Ambil data user login saat ini
$id = $_SESSION['id_login'];
$query = mysqli_query($conn, "SELECT * FROM login WHERE id_login = '$id'");
$data = mysqli_fetch_assoc($query);
?>

<br>
<div class="card">
    <div class="card-header">
        <div class="card-title"><i class="fa fa-cog me-2"></i> Account Settings</div>
    </div>
    <form method="post">
        <div class="card-body">
            <div class="row">
                <input type="hidden" name="id_login" value="<?= $data['id_login'] ?>">
                <div class="col-sm-6 mb-2">
                    <label>Nama Toko</label>
                    <input name="nama_toko" type="text" class="form-control" value="<?= $data['nama_toko'] ?>" required>
                </div>
                <div class="col-sm-6 mb-2">
                    <label>Username</label>
                    <input name="username" type="text" class="form-control" value="<?= $data['username'] ?>" required>
                </div>
                <div class="col-sm-6 mb-2">
                    <label>Telepon</label>
                    <input name="telepon" type="text" class="form-control" value="<?= $data['telepon'] ?>" required>
                </div>
                <div class="col-sm-6 mb-2">
                    <label>Password (baru)</label>
                    <input name="password" type="password" class="form-control" placeholder="Password baru" required>
                </div>
                <div class="col-sm-12">
                    <label>Alamat</label>
                    <textarea name="alamat" class="form-control" rows="3" required><?= $data['alamat'] ?></textarea>
                </div>
            </div>
        </div>
        <div class="card-footer text-right">
            <button type="submit" name="update" class="btn btn-primary">Update Akun</button>
        </div>
    </form>
</div>

<br>

<?php if ($_SESSION['role'] == 'admin'): ?>
<div class="card mt-4">
    <div class="card-header bg-success text-white">
        <div class="card-title"><i class="fa fa-user-plus me-2"></i> Tambah User Baru</div>
    </div>
    <form method="post">
        <div class="card-body">
            <div class="row">
                <div class="col-sm-6 mb-2">
                    <label>Username</label>
                    <input name="new_username" type="text" class="form-control" required>
                </div>
                <div class="col-sm-6 mb-2">
                    <label>Password</label>
                    <input name="new_password" type="password" class="form-control" required>
                </div>
                <div class="col-sm-6 mb-2">
                    <label>Nama Toko</label>
                    <input name="new_nama_toko" type="text" class="form-control" required>
                </div>
                <div class="col-sm-6 mb-2">
                    <label>Telepon</label>
                    <input name="new_telepon" type="text" class="form-control" required>
                </div>
                <div class="col-sm-12 mb-2">
                    <label>Alamat</label>
                    <textarea name="new_alamat" class="form-control" rows="2" required></textarea>
                </div>
                <div class="col-sm-6 mb-2">
                    <label>Role</label>
                    <select name="new_role" class="form-control" required>
                        <option value="kasir">Kasir</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="card-footer text-right">
            <button type="submit" name="tambah_user" class="btn btn-success">Tambah User</button>
        </div>
    </form>
</div>
<?php endif; ?>

<?php include 'template/footer.php'; ?>
