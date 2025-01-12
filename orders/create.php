<?php
session_start();
include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

// Ambil daftar pelanggan untuk dropdown
$customer_query = "SELECT * FROM Customers ORDER BY name";
$customer_stmt = $db->prepare($customer_query);
$customer_stmt->execute();

// Ambil daftar layanan untuk dropdown
$service_query = "SELECT s.*, c.category_name 
                 FROM servis s 
                 LEFT JOIN Categories c ON s.category_id = c.category_id 
                 ORDER BY c.category_name, s.service_name";
$service_stmt = $db->prepare($service_query);
$service_stmt->execute();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $customer_id = $_POST['customer_id'];
    $service_id = $_POST['service_id'];
    $status = $_POST['status'];
    $notes = $_POST['notes'];

    $query = "INSERT INTO Orders (customer_id, service_id, status, notes) 
              VALUES (:customer_id, :service_id, :status, :notes)";
    $stmt = $db->prepare($query);

    $stmt->bindParam(':customer_id', $customer_id);
    $stmt->bindParam(':service_id', $service_id);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':notes', $notes);

    if($stmt->execute()) {
        $_SESSION['message'] = "Pesanan berhasil ditambahkan";
        $_SESSION['message_type'] = "success";
        header("Location: index.php");
        exit();
    } else {
        $_SESSION['message'] = "Gagal menambahkan pesanan";
        $_SESSION['message_type'] = "danger";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pesanan - Toko Servis Aril</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include_once '../includes/navbar.php'; ?>

    <div class="container mt-4">
        <h2>Tambah Pesanan Baru</h2>
        <form action="create.php" method="POST" onsubmit="return validateForm()">
            <div class="mb-3">
                <label for="customer_id" class="form-label">Pelanggan</label>
                <select class="form-select" id="customer_id" name="customer_id" required>
                    <option value="">Pilih Pelanggan</option>
                    <?php while ($customer = $customer_stmt->fetch(PDO::FETCH_ASSOC)): ?>
                        <option value="<?= $customer['customer_id'] ?>">
                            <?= htmlspecialchars($customer['name']) ?> - 
                            <?= htmlspecialchars($customer['phone']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="service_id" class="form-label">Layanan</label>
                <select class="form-select" id="service_id" name="service_id" required>
                    <option value="">Pilih Layanan</option>
                    <?php 
                    $current_category = '';
                    while ($service = $service_stmt->fetch(PDO::FETCH_ASSOC)): 
                        if ($current_category != $service['category_name']): 
                            if ($current_category != '') echo '</optgroup>';
                            $current_category = $service['category_name'];
                            echo '<optgroup label="' . htmlspecialchars($current_category) . '">';
                        endif;
                    ?>
                        <option value="<?= $service['service_id'] ?>">
                            <?= htmlspecialchars($service['service_name']) ?> - 
                            Rp <?= number_format($service['price'], 0, ',', '.') ?>
                        </option>
                    <?php 
                    endwhile;
                    if ($current_category != '') echo '</optgroup>';
                    ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status" required>
                    <option value="Pending">Pending</option>
                    <option value="Process">Process</option>
                    <option value="Completed">Completed</option>
                    <option value="Cancelled">Cancelled</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="notes" class="form-label">Catatan</label>
                <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="index.php" class="btn btn-secondary">Batal</a>
        </form>
    </div>

    <?php include_once '../includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/main.js"></script>
</body>
</html> 