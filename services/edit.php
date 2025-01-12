<?php
session_start();
include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];

// Ambil daftar kategori untuk dropdown
$category_query = "SELECT * FROM Categories ORDER BY category_name";
$category_stmt = $db->prepare($category_query);
$category_stmt->execute();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category_id = $_POST['category_id'];
    $service_name = $_POST['service_name'];
    $description = $_POST['description'];
    $price = $_POST['price'];

    $query = "UPDATE servis 
              SET category_id = :category_id, 
                  service_name = :service_name, 
                  description = :description, 
                  price = :price 
              WHERE service_id = :id";
    $stmt = $db->prepare($query);

    $stmt->bindParam(':category_id', $category_id);
    $stmt->bindParam(':service_name', $service_name);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':price', $price);
    $stmt->bindParam(':id', $id);

    if($stmt->execute()) {
        $_SESSION['message'] = "Layanan berhasil diperbarui";
        $_SESSION['message_type'] = "success";
        header("Location: index.php");
        exit();
    } else {
        $_SESSION['message'] = "Gagal memperbarui layanan";
        $_SESSION['message_type'] = "danger";
    }
}

$query = "SELECT * FROM servis WHERE service_id = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $id);
$stmt->execute();
$service = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$service) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Layanan - Toko Servis Aril</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include_once '../includes/navbar.php'; ?>

    <div class="container mt-4">
        <h2>Edit Layanan</h2>
        <form action="edit.php?id=<?= $id ?>" method="POST" onsubmit="return validateForm()">
            <div class="mb-3">
                <label for="category_id" class="form-label">Kategori</label>
                <select class="form-select" id="category_id" name="category_id" required>
                    <option value="">Pilih Kategori</option>
                    <?php while ($category = $category_stmt->fetch(PDO::FETCH_ASSOC)): ?>
                        <option value="<?= $category['category_id'] ?>" 
                                <?= ($category['category_id'] == $service['category_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($category['category_name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="service_name" class="form-label">Nama Layanan</label>
                <input type="text" class="form-control" id="service_name" name="service_name" 
                       value="<?= htmlspecialchars($service['service_name']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Deskripsi</label>
                <textarea class="form-control" id="description" name="description" rows="3"><?= htmlspecialchars($service['description']) ?></textarea>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Harga</label>
                <div class="input-group">
                    <span class="input-group-text">Rp</span>
                    <input type="number" class="form-control" id="price" name="price" 
                           value="<?= $service['price'] ?>" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            <a href="index.php" class="btn btn-secondary">Batal</a>
        </form>
    </div>

    <?php include_once '../includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/main.js"></script>
</body>
</html> 