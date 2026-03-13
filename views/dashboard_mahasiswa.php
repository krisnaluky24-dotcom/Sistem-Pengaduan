<?php

require_once '../middleware/Auth.php';
require_once '../config/Database.php';
require_once '../models/Complaint.php';

Auth::check();

// Cek user role
if($_SESSION['role'] != 'mahasiswa'){
    header("Location: dashboard_admin.php");
    exit;
}

$db = (new Database())->getConnection();
$complaint = new Complaint($db);

// Get all complaints
$all_complaints = $complaint->getByUserId($_SESSION['id']);

// Count by status
$query_pending = "SELECT COUNT(*) as total FROM complaints WHERE user_id='".$_SESSION['id']."' AND status='Menunggu'";
$query_approved = "SELECT COUNT(*) as total FROM complaints WHERE user_id='".$_SESSION['id']."' AND status='Disetujui'";
$query_rejected = "SELECT COUNT(*) as total FROM complaints WHERE user_id='".$_SESSION['id']."' AND status='Ditolak'";

$count_pending = $db->query($query_pending)->fetch_assoc()['total'];
$count_approved = $db->query($query_approved)->fetch_assoc()['total'];
$count_rejected = $db->query($query_rejected)->fetch_assoc()['total'];
$count_total = $all_complaints->num_rows;

?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Mahasiswa - Sistem Pengaduan</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }
        .navbar-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .navbar-custom .navbar-brand {
            font-weight: bold;
            font-size: 24px;
        }
        .navbar-custom .btn-outline-light:hover {
            background-color: white;
            color: #667eea;
        }
        .card-stat {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card-stat:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        .stat-icon {
            font-size: 40px;
            margin-bottom: 10px;
        }
        .stat-total { color: #667eea; }
        .stat-pending { color: #ffc107; }
        .stat-approved { color: #28a745; }
        .stat-rejected { color: #dc3545; }
        .page-title {
            color: white;
            margin-bottom: 30px;
            font-weight: bold;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }
        .main-content {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.2);
        }
        .table-title {
            color: #667eea;
            font-weight: bold;
            margin-bottom: 20px;
            font-size: 20px;
            border-left: 4px solid #667eea;
            padding-left: 10px;
        }
        .category-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        .kategori-akademik { background-color: #e3f2fd; color: #1565c0; }
        .kategori-fasilitas { background-color: #f3e5f5; color: #6a1b9a; }
        .kategori-dosen { background-color: #fff3e0; color: #e65100; }
        .kategori-administrasi { background-color: #fce4ec; color: #c2185b; }
        .kategori-keamanan { background-color: #e8f5e9; color: #1b5e20; }
        .kategori-lainnya { background-color: #f1f1f1; color: #424242; }
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-menunggu { background-color: #fff3cd; color: #856404; }
        .status-disetujui { background-color: #d4edda; color: #155724; }
        .status-ditolak { background-color: #f8d7da; color: #721c24; }
        .action-buttons a {
            margin-right: 5px;
            text-decoration: none;
        }
        .table {
            margin-top: 20px;
        }
        .table th {
            background-color: #f8f9fa;
            color: #667eea;
            font-weight: bold;
            border-bottom: 2px solid #667eea;
        }
        .table tbody tr:hover {
            background-color: #f8f9fa;
        }
        .btn-primary-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }
        .btn-primary-custom:hover {
            background: linear-gradient(135deg, #5568d3 0%, #6a3d8a 100%);
        }
        .no-data {
            text-align: center;
            padding: 40px 20px;
            color: #999;
        }
        .no-data i {
            font-size: 50px;
            margin-bottom: 20px;
            opacity: 0.5;
        }
    </style>
</head>
<body>
    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="fas fa-file-alt"></i> Sistem Pengaduan
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <span class="nav-link text-white">
                            <i class="fas fa-user"></i> <?php echo $_SESSION['user']; ?>
                        </span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="tambah_pengaduan.php">
                            <i class="fas fa-plus"></i> Buat Pengaduan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../Logout.php">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- MAIN CONTENT -->
    <div class="container mt-5">
        <h1 class="page-title text-center"><i class="fas fa-chart-line"></i> Dashboard Mahasiswa</h1>

        <!-- STATISTIK CARDS -->
        <div class="row mb-5">
            <div class="col-md-3 mb-3">
                <div class="card card-stat">
                    <div class="card-body text-center">
                        <div class="stat-icon stat-total">
                            <i class="fas fa-folder-open"></i>
                        </div>
                        <h5>Total Pengaduan</h5>
                        <h2><?php echo $count_total; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card card-stat">
                    <div class="card-body text-center">
                        <div class="stat-icon stat-pending">
                            <i class="fas fa-clock"></i>
                        </div>
                        <h5>Menunggu</h5>
                        <h2><?php echo $count_pending; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card card-stat">
                    <div class="card-body text-center">
                        <div class="stat-icon stat-approved">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h5>Disetujui</h5>
                        <h2><?php echo $count_approved; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card card-stat">
                    <div class="card-body text-center">
                        <div class="stat-icon stat-rejected">
                            <i class="fas fa-times-circle"></i>
                        </div>
                        <h5>Ditolak</h5>
                        <h2><?php echo $count_rejected; ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- DAFTAR PENGADUAN -->
        <div class="main-content">
            <div class="table-title">
                <i class="fas fa-list"></i> Daftar Pengaduan Anda
            </div>

            <?php if($all_complaints->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="25%">Judul</th>
                                <th width="15%">Kategori</th>
                                <th width="15%">Status</th>
                                <th width="15%">Tanggal</th>
                                <th width="25%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            while($row = $all_complaints->fetch_assoc()):
                                $kategori_class = 'kategori-' . strtolower(str_replace(' ', '-', $row['kategori']));
                                $status_class = 'status-' . strtolower($row['status']);
                            ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td><strong><?php echo substr($row['judul'], 0, 40); ?></strong></td>
                                    <td>
                                        <span class="category-badge <?php echo $kategori_class; ?>">
                                            <?php echo $row['kategori']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="status-badge <?php echo $status_class; ?>">
                                            <?php echo $row['status']; ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('d-m-Y', strtotime($row['tanggal'])); ?></td>
                                    <td class="action-buttons">
                                        <a href="detail.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> Lihat
                                        </a>
                                        <a href="tambah_pengaduan.php?edit=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="tambah_pengaduan.php?delete=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus?')">
                                            <i class="fas fa-trash"></i> Hapus
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="no-data">
                    <div><i class="fas fa-inbox"></i></div>
                    <p><strong>Belum ada pengaduan</strong></p>
                    <p>Anda belum membuat pengaduan apapun. <a href="tambah_pengaduan.php">Buat sekarang</a></p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- BOOTSTRAP JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>