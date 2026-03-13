<?php

require_once '../middleware/Auth.php';
require_once '../config/Database.php';
require_once '../models/Complaint.php';

Auth::check();

$db = (new Database())->getConnection();
$complaint = new Complaint($db);

$id = $_GET['id'] ?? null;

if(!$id){
    header("Location: tambah_pengaduan.php");
    exit;
}

$result = $complaint->getById($id);

if($result->num_rows == 0){
    header("Location: tambah_pengaduan.php");
    exit;
}

$data = $result->fetch_assoc();

?>

<!DOCTYPE html>
<html>
<head>
    <title>Detail Pengaduan</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f5f5f5;
            padding: 20px 0;
        }
        .card {
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .card-header {
            background-color: #007bff;
            color: white;
            font-weight: bold;
        }
        .detail-label {
            font-weight: bold;
            color: #555;
        }
        .kategori-badge {
            display: inline-block;
            padding: 8px 15px;
            border-radius: 5px;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .kategori-akademik { background-color: #e3f2fd; color: #1565c0; }
        .kategori-fasilitas { background-color: #f3e5f5; color: #6a1b9a; }
        .kategori-dosen { background-color: #fff3e0; color: #e65100; }
        .kategori-administrasi { background-color: #fce4ec; color: #c2185b; }
        .kategori-keamanan { background-color: #e8f5e9; color: #1b5e20; }
        .kategori-lainnya { background-color: #f1f1f1; color: #424242; }
        .status-pending {
            display: inline-block;
            padding: 8px 15px;
            border-radius: 5px;
            background-color: #ffc107;
            color: black;
            font-weight: bold;
        }
        .status-approved {
            display: inline-block;
            padding: 8px 15px;
            border-radius: 5px;
            background-color: #28a745;
            color: white;
            font-weight: bold;
        }
        .status-rejected {
            display: inline-block;
            padding: 8px 15px;
            border-radius: 5px;
            background-color: #dc3545;
            color: white;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header">Detail Pengaduan</div>
                    <div class="card-body">
                        <div class="mb-4">
                            <p class="detail-label">ID Pengaduan</p>
                            <p><?php echo $data['id']; ?></p>
                        </div>

                        <div class="mb-4">
                            <p class="detail-label">Judul</p>
                            <p><?php echo htmlspecialchars($data['judul']); ?></p>
                        </div>

                        <div class="mb-4">
                            <p class="detail-label">Kategori</p>
                            <p>
                                <span class="kategori-badge kategori-<?php echo strtolower(str_replace(' ', '-', $data['kategori'])); ?>">
                                    <?php echo $data['kategori']; ?>
                                </span>
                            </p>
                        </div>

                        <div class="mb-4">
                            <p class="detail-label">Status</p>
                            <p>
                                <?php
                                $status_class = 'status-pending';
                                if($data['status'] == 'Disetujui') $status_class = 'status-approved';
                                elseif($data['status'] == 'Ditolak') $status_class = 'status-rejected';
                                ?>
                                <span class="<?php echo $status_class; ?>">
                                    <?php echo $data['status']; ?>
                                </span>
                            </p>
                        </div>

                        <div class="mb-4">
                            <p class="detail-label">Pelapor</p>
                            <p><?php echo $data['nama']; ?></p>
                        </div>

                        <div class="mb-4">
                            <p class="detail-label">Tanggal Laporan</p>
                            <p><?php echo date('d-m-Y H:i:s', strtotime($data['tanggal'])); ?></p>
                        </div>

                        <div class="mb-4">
                            <p class="detail-label">Deskripsi</p>
                            <div style="background-color: #f9f9f9; padding: 15px; border-radius: 5px; border-left: 4px solid #007bff;">
                                <?php echo nl2br(htmlspecialchars($data['deskripsi'])); ?>
                            </div>
                        </div>

                        <?php if($data['foto']): ?>
                            <div class="mb-4">
                                <p class="detail-label">Foto Bukti</p>
                                <div style="text-align: center;">
                                    <img src="../uploads/bukti/<?php echo $data['foto']; ?>" alt="Bukti Pengaduan" 
                                         style="max-width: 100%; max-height: 500px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.2);">
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                            <a href="tambah_pengaduan.php" class="btn btn-secondary">Kembali</a>
                            <a href="tambah_pengaduan.php?edit=<?php echo $data['id']; ?>" class="btn btn-info">Edit</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
