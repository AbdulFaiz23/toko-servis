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
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    $query = "UPDATE Customers SET name = :name, email = :email, phone = :phone, address = :address WHERE customer_id = :id";
    $stmt = $db->prepare($query);

    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':phone', $phone);
    $stmt->bindParam(':address', $address);
    $stmt->bindParam(':id', $id);

    if($stmt->execute()) {
        $_SESSION['message'] = "Data pelanggan berhasil diperbarui";
        $_SESSION['message_type'] = "success";
        header("Location: index.php");
        exit();
    } else {
        $_SESSION['message'] = "Gagal memperbarui data pelanggan";
        $_SESSION['message_type'] = "danger";
    }
}

$query = "SELECT * FROM Customers WHERE customer_id = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $id);
$stmt->execute();
$customer = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$customer) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pelanggan - Toko Servis Aril</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include_once '../includes/navbar.php'; ?>

    <div class="container mt-4">
        <h2>Edit Data Pelanggan</h2>
        <form action="edit.php?id=<?= $id ?>" method="POST" onsubmit="return validateForm()">
            <div class="mb-3">
                <label for="name" class="form-label">Nama Pelanggan</label>
                <input type="text" class="form-control" id="name" name="name" 
                       value="<?= htmlspecialchars($customer['name']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" 
                       value="<?= htmlspecialchars($customer['email']) ?>">
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Telepon</label>
                <input type="tel" class="form-control" id="phone" name="phone" 
                       value="<?= htmlspecialchars($customer['phone']) ?>">
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Alamat</label>
                <textarea class="form-control" id="address" name="address" rows="3"><?= htmlspecialchars($customer['address']) ?></textarea>
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