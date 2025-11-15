<?php
include 'config/database.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    try {
        $query = "DELETE FROM schedules WHERE id = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$id]);
        
        header("Location: view_schedules.php?message=Schedule deleted successfully");
        exit();
    } catch(PDOException $e) {
        header("Location: view_schedules.php?error=Error deleting schedule");
        exit();
    }
} else {
    header("Location: view_schedules.php");
    exit();
}
?>