<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];
    
    $sql = "INSERT INTO kontak (nama, email, pesan) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $name, $email, $message);
    
    if ($stmt->execute()) {
        header('Location: contact.php?status=success');
    } else {
        header('Location: contact.php?status=error');
    }
    
    $stmt->close();
    $conn->close();
} else {
    header('Location: contact.php');
}
?>