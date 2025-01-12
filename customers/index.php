<?php
session_start();
include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$query = "SELECT * FROM Customers ORDER BY customer_id DESC";
$stmt = $db->prepare($query);
$stmt->execute();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pelanggan - Toko Servis Faiz</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include_once '../includes/navbar.php'; ?>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Daftar Pelanggan</h2>
            <a href="create.php" class="btn btn-primary">Tambah Pelanggan</a>
        </div>

        <?php if(isset($_SESSION['message'])): ?>
            <div class="alert alert-<?= $_SESSION['message_type'] ?>">
                <?= $_SESSION['message'] ?>
            </div>
            <?php 
            unset($_SESSION['message']);
            unset($_SESSION['message_type']);
            endif; 
        ?>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Telepon</th>
                        <th>Alamat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td><?= $row['customer_id'] ?></td>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= htmlspecialchars($row['phone']) ?></td>
                            <td><?= htmlspecialchars($row['address']) ?></td>
                            <td>
                                <a href="edit.php?id=<?= $row['customer_id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                <a href="delete.php?id=<?= $row['customer_id'] ?>" 
                                   class="btn btn-sm btn-danger" 
                                   onclick="return confirmDelete()">Hapus</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php include_once '../includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/main.js"></script>
</body>
</html> 