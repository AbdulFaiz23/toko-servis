<?php
session_start();

// Cek apakah user sudah login
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

// Mengambil statistik untuk dashboard
$stats = [
    'total_customers' => 0,
    'total_services' => 0,
    'pending_orders' => 0,
    'completed_orders' => 0
];

// Total Customers
$query = "SELECT COUNT(*) as total FROM Customers";
$stmt = $db->prepare($query);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);
$stats['total_customers'] = $result['total'];

// Total Services
$query = "SELECT COUNT(*) as total FROM servis";
$stmt = $db->prepare($query);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);
$stats['total_services'] = $result['total'];

// Pending Orders
$query = "SELECT COUNT(*) as total FROM Orders WHERE status = 'Pending'";
$stmt = $db->prepare($query);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);
$stats['pending_orders'] = $result['total'];

// Completed Orders
$query = "SELECT COUNT(*) as total FROM Orders WHERE status = 'Completed'";
$stmt = $db->prepare($query);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);
$stats['completed_orders'] = $result['total'];

// Mengambil pesanan terbaru
$recent_orders_query = "SELECT o.*, c.name as customer_name, s.service_name 
                       FROM Orders o 
                       LEFT JOIN Customers c ON o.customer_id = c.customer_id 
                       LEFT JOIN servis s ON o.service_id = s.service_id 
                       ORDER BY o.order_date DESC LIMIT 5";
$recent_orders_stmt = $db->prepare($recent_orders_query);
$recent_orders_stmt->execute();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Toko Servis Faiz</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <!-- Font Awesome untuk ikon -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>
<body>
    <!-- Modifikasi navbar untuk menampilkan user info dan tombol logout -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">Toko Servis Faiz</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="services/index.php">Layanan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="customers/index.php">Pelanggan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="orders/index.php">Pesanan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="categories/index.php">Kategori</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" 
                           data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user"></i> <?= htmlspecialchars($_SESSION['name']) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><span class="dropdown-item-text">Role: <?= ucfirst($_SESSION['role']) ?></span></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h1 class="mb-4">Dashboard</h1>

        <!-- Statistik Cards -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-users"></i> Total Pelanggan
                        </h5>
                        <h2 class="card-text"><?= $stats['total_customers'] ?></h2>
                        <a href="customers/index.php" class="text-white">Lihat Detail →</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-tools"></i> Total Layanan
                        </h5>
                        <h2 class="card-text"><?= $stats['total_services'] ?></h2>
                        <a href="services/index.php" class="text-white">Lihat Detail →</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card bg-warning text-dark">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-clock"></i> Pesanan Pending
                        </h5>
                        <h2 class="card-text"><?= $stats['pending_orders'] ?></h2>
                        <a href="orders/index.php" class="text-dark">Lihat Detail →</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-check-circle"></i> Pesanan Selesai
                        </h5>
                        <h2 class="card-text"><?= $stats['completed_orders'] ?></h2>
                        <a href="orders/index.php" class="text-white">Lihat Detail →</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pesanan Terbaru -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Pesanan Terbaru</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Pelanggan</th>
                                <th>Layanan</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($order = $recent_orders_stmt->fetch(PDO::FETCH_ASSOC)): ?>
                                <tr>
                                    <td><?= $order['order_id'] ?></td>
                                    <td><?= htmlspecialchars($order['customer_name']) ?></td>
                                    <td><?= htmlspecialchars($order['service_name']) ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($order['order_date'])) ?></td>
                                    <td>
                                        <span class="badge bg-<?php 
                                            switch($order['status']) {
                                                case 'Pending': echo 'warning'; break;
                                                case 'Process': echo 'info'; break;
                                                case 'Completed': echo 'success'; break;
                                                case 'Cancelled': echo 'danger'; break;
                                            }
                                        ?>"><?= $order['status'] ?></span>
                                    </td>
                                    <td>
                                        <a href="orders/edit.php?id=<?= $order['order_id'] ?>" 
                                           class="btn btn-sm btn-warning">Edit</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Tombol Aksi Cepat -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="d-flex gap-2">
                    <a href="orders/create.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Pesanan Baru
                    </a>
                    <a href="customers/create.php" class="btn btn-success">
                        <i class="fas fa-user-plus"></i> Pelanggan Baru
                    </a>
                    <a href="services/create.php" class="btn btn-info text-white">
                        <i class="fas fa-tools"></i> Layanan Baru
                    </a>
                </div>
            </div>
        </div>
    </div>

    <?php include_once 'includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html> 