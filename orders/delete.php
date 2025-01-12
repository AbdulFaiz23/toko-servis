<?php
session_start();
include_once '../config/database.php';

if (isset($_GET['id'])) {
    $database = new Database();
    $db = $database->getConnection();

    $id = $_GET['id'];

    $query = "DELETE FROM Orders WHERE order_id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id);

    if($stmt->execute()) {
        $_SESSION['message'] = "Pesanan berhasil dihapus";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Gagal menghapus pesanan";
        $_SESSION['message_type'] = "danger";
    }
}

header("Location: index.php");
exit(); 