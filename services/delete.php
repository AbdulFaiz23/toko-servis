<?php
session_start();
include_once '../config/database.php';

if (isset($_GET['id'])) {
    $database = new Database();
    $db = $database->getConnection();

    $id = $_GET['id'];
    
    // Periksa apakah layanan digunakan dalam tabel Orders
    $check_query = "SELECT COUNT(*) FROM Orders WHERE service_id = :id";
    $check_stmt = $db->prepare($check_query);
    $check_stmt->bindParam(':id', $id);
    $check_stmt->execute();
    $count = $check_stmt->fetchColumn();

    if ($count > 0) {
        $_SESSION['message'] = "Layanan tidak dapat dihapus karena masih digunakan dalam pesanan";
        $_SESSION['message_type'] = "danger";
    } else {
        $query = "DELETE FROM servis WHERE service_id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id);

        if($stmt->execute()) {
            $_SESSION['message'] = "Layanan berhasil dihapus";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Gagal menghapus layanan";
            $_SESSION['message_type'] = "danger";
        }
    }
}

header("Location: index.php");
exit(); 