<?php
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM links WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $_SESSION['user_id']]);

    if ($stmt->rowCount() > 0) {
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Failed to delete the link or you don't have permission to delete it.";
    }
} else {
    echo "Invalid request.";
}
?>
