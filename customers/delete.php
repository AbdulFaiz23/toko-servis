<?php
session_start();
include_once '../config/database.php';

if (isset($_GET['id'])) {
    $database = new Database();
    $db = $database->getConnection();

    $id = $_GET['id'];
    
    // Periksa apakah pelanggan memiliki pesanan
    $check_query = "SELECT COUNT(*) FROM Orders WHERE customer_id = :id";
    $check_stmt = $db->prepare($check_query);
    $check_stmt->bindParam(':id', $id);
    $check_stmt->execute();
    $count = $check_stmt->fetchColumn();

    if ($count > 0) {
        $_SESSION['message'] = "Pelanggan tidak dapat dihapus karena memiliki riwayat pesanan";
        $_SESSION['message_type'] = "danger";
    } else {
        $query = "DELETE FROM Customers WHERE customer_id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id);

        if($stmt->execute()) {
            $_SESSION['message'] = "Pelanggan berhasil dihapus";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Gagal menghapus pelanggan";
            $_SESSION['message_type'] = "danger";
        }
    }
}

header("Location: index.php");
exit(); 