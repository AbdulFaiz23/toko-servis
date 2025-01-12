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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category_name = $_POST['category_name'];
    $description = $_POST['description'];

    $query = "UPDATE Categories SET category_name = :category_name, description = :description WHERE category_id = :id";
    $stmt = $db->prepare($query);

    $stmt->bindParam(':category_name', $category_name);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':id', $id);

    if($stmt->execute()) {
        $_SESSION['message'] = "Kategori berhasil diperbarui";
        $_SESSION['message_type'] = "success";
        header("Location: index.php");
        exit();
    } else {
        $_SESSION['message'] = "Gagal memperbarui kategori";
        $_SESSION['message_type'] = "danger";
    }
}

$query = "SELECT * FROM Categories WHERE category_id = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $id);
$stmt->execute();
$category = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$category) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Kategori - Toko Servis Aril</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include_once '../includes/navbar.php'; ?>

    <div class="container mt-4">
        <h2>Edit Kategori</h2>
        <form action="edit.php?id=<?= $id ?>" method="POST" onsubmit="return validateForm()">
            <div class="mb-3">
                <label for="category_name" class="form-label">Nama Kategori</label>
                <input type="text" class="form-control" id="category_name" name="category_name" 
                       value="<?= htmlspecialchars($category['category_name']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Deskripsi</label>
                <textarea class="form-control" id="description" name="description" rows="3"><?= htmlspecialchars($category['description']) ?></textarea>
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