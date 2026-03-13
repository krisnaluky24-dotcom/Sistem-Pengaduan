<?php

require_once '../middleware/Auth.php';
require_once '../config/Database.php';
require_once '../models/Complaint.php';

Auth::check();

$db = (new Database())->getConnection();
$complaint = new Complaint($db);

$message = "";
$edit_data = null;

$kategori_list = ['Akademik', 'Fasilitas', 'Dosen', 'Administrasi', 'Keamanan', 'Lainnya'];

// CREATE
if(isset($_POST['kirim'])){
    $judul = $_POST['judul'];
    $deskripsi = $_POST['deskripsi'];
    $kategori = $_POST['kategori'];
    $user_id = $_SESSION['id'];
    $foto = null;

    // Handle file upload
    if(isset($_FILES['foto']) && $_FILES['foto']['error'] == 0){
        $file_name = $_FILES['foto']['name'];
        $file_tmp = $_FILES['foto']['tmp_name'];
        $file_size = $_FILES['foto']['size'];
        $file_type = $_FILES['foto']['type'];

        // Validasi file
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 5 * 1024 * 1024; // 5MB

        if(!in_array($file_type, $allowed_types)){
            $message = "<div class='alert alert-danger'>Tipe file harus JPG, PNG, atau GIF!</div>";
        } elseif($file_size > $max_size){
            $message = "<div class='alert alert-danger'>Ukuran file maksimal 5MB!</div>";
        } else {
            // Buat nama file unik
            $new_filename = 'bukti_' . $user_id . '_' . time() . '_' . basename($file_name);
            $upload_path = '../uploads/bukti/' . $new_filename;

            // Buat folder jika belum ada
            if(!is_dir('../uploads/bukti')){
                mkdir('../uploads/bukti', 0777, true);
            }

            if(move_uploaded_file($file_tmp, $upload_path)){
                $foto = $new_filename;
            } else {
                $message = "<div class='alert alert-danger'>Gagal upload file!</div>";
            }
        }
    }

    if(empty($judul) || empty($deskripsi) || empty($kategori)){
        $message = "<div class='alert alert-danger'>Semua field harus diisi!</div>";
    } else {
        if($complaint->create($judul, $deskripsi, $kategori, $user_id, $foto)){
            $message = "<div class='alert alert-success'>Pengaduan berhasil ditambahkan!</div>";
        } else {
            $message = "<div class='alert alert-danger'>Gagal menambahkan pengaduan!</div>";
        }
    }
}

// UPDATE
if(isset($_POST['update'])){
    $id = $_POST['id'];
    $judul = $_POST['judul'];
    $deskripsi = $_POST['deskripsi'];
    $kategori = $_POST['kategori'];
    $foto = null;

    // Handle file upload
    if(isset($_FILES['foto']) && $_FILES['foto']['error'] == 0){
        $file_name = $_FILES['foto']['name'];
        $file_tmp = $_FILES['foto']['tmp_name'];
        $file_size = $_FILES['foto']['size'];
        $file_type = $_FILES['foto']['type'];

        // Validasi file
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 5 * 1024 * 1024; // 5MB

        if(!in_array($file_type, $allowed_types)){
            $message = "<div class='alert alert-danger'>Tipe file harus JPG, PNG, atau GIF!</div>";
        } elseif($file_size > $max_size){
            $message = "<div class='alert alert-danger'>Ukuran file maksimal 5MB!</div>";
        } else {
            // Buat nama file unik
            $new_filename = 'bukti_' . $_SESSION['id'] . '_' . time() . '_' . basename($file_name);
            $upload_path = '../uploads/bukti/' . $new_filename;

            // Buat folder jika belum ada
            if(!is_dir('../uploads/bukti')){
                mkdir('../uploads/bukti', 0777, true);
            }

            if(move_uploaded_file($file_tmp, $upload_path)){
                $foto = $new_filename;
            } else {
                $message = "<div class='alert alert-danger'>Gagal upload file!</div>";
            }
        }
    }

    if(empty($judul) || empty($deskripsi) || empty($kategori)){
        $message = "<div class='alert alert-danger'>Semua field harus diisi!</div>";
    } else {
        if($complaint->update($id, $judul, $deskripsi, $kategori, $foto)){
            $message = "<div class='alert alert-success'>Pengaduan berhasil diperbarui!</div>";
        } else {
            $message = "<div class='alert alert-danger'>Gagal memperbarui pengaduan!</div>";
        }
    }
}

// DELETE
if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    if($complaint->delete($id)){
        $message = "<div class='alert alert-success'>Pengaduan berhasil dihapus!</div>";
    } else {
        $message = "<div class='alert alert-danger'>Gagal menghapus pengaduan!</div>";
    }
}

// EDIT
if(isset($_GET['edit'])){
    $id = $_GET['edit'];
    $result = $complaint->getById($id);
    if($result->num_rows > 0){
        $edit_data = $result->fetch_assoc();
    }
}

// GET DATA
$complaints = $complaint->getByUserId($_SESSION['id']);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Kelola Pengaduan</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f5f5f5;
            padding: 20px 0;
        }
        .card {
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .card-header {
            background-color: #007bff;
            color: white;
            font-weight: bold;
        }
        .btn-sm {
            margin-right: 5px;
        }
        .status-pending {
            background-color: #ffc107;
            color: black;
        }
        .status-approved {
            background-color: #28a745;
            color: white;
        }
        .status-rejected {
            background-color: #dc3545;
            color: white;
        }
        .kategori-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .kategori-akademik { background-color: #e3f2fd; color: #1565c0; }
        .kategori-fasilitas { background-color: #f3e5f5; color: #6a1b9a; }
        .kategori-dosen { background-color: #fff3e0; color: #e65100; }
        .kategori-administrasi { background-color: #fce4ec; color: #c2185b; }
        .kategori-keamanan { background-color: #e8f5e9; color: #1b5e20; }
        .kategori-lainnya { background-color: #f1f1f1; color: #424242; }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                
                <?php echo $message; ?>

                <!-- FORM TAMBAH/EDIT PENGADUAN -->
                <div class="card">
                    <div class="card-header">
                        <?php echo $edit_data ? 'Edit Pengaduan' : 'Buat Pengaduan Baru'; ?>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <?php if($edit_data): ?>
                                <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">
                            <?php endif; ?>

                            <div class="mb-3">
                                <label for="judul" class="form-label">Judul Pengaduan <span class="text-danger">*</span></label>
                                <input type="text" name="judul" id="judul" class="form-control" 
                                       placeholder="Masukkan judul pengaduan" 
                                       value="<?php echo $edit_data ? $edit_data['judul'] : ''; ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="kategori" class="form-label">Kategori <span class="text-danger">*</span></label>
                                <select name="kategori" id="kategori" class="form-control" required>
                                    <option value="">-- Pilih Kategori --</option>
                                    <?php foreach($kategori_list as $kat): ?>
                                        <option value="<?php echo $kat; ?>" 
                                            <?php echo ($edit_data && $edit_data['kategori'] == $kat) ? 'selected' : ''; ?>>
                                            <?php echo $kat; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="deskripsi" class="form-label">Deskripsi <span class="text-danger">*</span></label>
                                <textarea name="deskripsi" id="deskripsi" class="form-control" 
                                          placeholder="Deskripsikan pengaduan Anda secara detail" 
                                          rows="5" required><?php echo $edit_data ? $edit_data['deskripsi'] : ''; ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="foto" class="form-label">Foto Bukti</label>
                                <input type="file" name="foto" id="foto" class="form-control" accept="image/*">
                                <small class="text-muted">Format: JPG, PNG, GIF | Ukuran maksimal: 5MB</small>
                                <?php if($edit_data && $edit_data['foto']): ?>
                                    <div class="mt-2">
                                        <p><strong>Foto saat ini:</strong></p>
                                        <img src="../uploads/bukti/<?php echo $edit_data['foto']; ?>" alt="Bukti" style="max-width: 200px; border-radius: 5px;">
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <?php if($edit_data): ?>
                                    <a href="tambah_pengaduan.php" class="btn btn-secondary">Batal</a>
                                    <button type="submit" name="update" class="btn btn-warning">Perbarui</button>
                                <?php else: ?>
                                    <button type="submit" name="kirim" class="btn btn-primary">Kirim Pengaduan</button>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- DAFTAR PENGADUAN -->
                <div class="card">
                    <div class="card-header">
                        Daftar Pengaduan Anda (<?php echo $complaints->num_rows; ?>)
                    </div>
                    <div class="card-body">
                        <?php if($complaints->num_rows > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Judul</th>
                                            <th>Kategori</th>
                                            <th>Status</th>
                                            <th>Tanggal</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $no = 1;
                                        while($row = $complaints->fetch_assoc()):
                                            $status_class = 'status-pending';
                                            if($row['status'] == 'Disetujui') $status_class = 'status-approved';
                                            elseif($row['status'] == 'Ditolak') $status_class = 'status-rejected';
                                            
                                            $kategori_class = 'kategori-' . strtolower(str_replace(' ', '-', $row['kategori']));
                                        ?>
                                            <tr>
                                                <td><?php echo $no++; ?></td>
                                                <td><?php echo substr($row['judul'], 0, 30); ?>...</td>
                                                <td>
                                                    <span class="kategori-badge <?php echo $kategori_class; ?>">
                                                        <?php echo $row['kategori']; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge <?php echo $status_class; ?>">
                                                        <?php echo $row['status']; ?>
                                                    </span>
                                                </td>
                                                <td><?php echo date('d-m-Y H:i', strtotime($row['tanggal'])); ?></td>
                                                <td>
                                                    <a href="tambah_pengaduan.php?edit=<?php echo $row['id']; ?>" 
                                                       class="btn btn-sm btn-info">Edit</a>
                                                    <a href="tambah_pengaduan.php?delete=<?php echo $row['id']; ?>" 
                                                       class="btn btn-sm btn-danger" 
                                                       onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                                                    <a href="detail.php?id=<?php echo $row['id']; ?>" 
                                                       class="btn btn-sm btn-secondary">Lihat</a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">Anda belum membuat pengaduan apapun</div>
                        <?php endif; ?>
                        <div class="mb-3">
                        <a href="dashboard_mahasiswa.php" class="btn btn-secondary">← Kembali</a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</body>
</html>