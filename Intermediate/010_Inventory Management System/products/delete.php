<?php
include '../config/database.php';
include '../includes/header.php';

$database = new Database();
$db = $database->getConnection();

if(isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Get product details for confirmation message
    $query = "SELECT name FROM products WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if($product) {
        if($_POST && isset($_POST['confirm_delete'])) {
            // Delete the product
            $delete_query = "DELETE FROM products WHERE id = ?";
            $delete_stmt = $db->prepare($delete_query);
            
            if($delete_stmt->execute([$id])) {
                echo "<script>
                    alert('Product deleted successfully!');
                    window.location.href = 'index.php';
                </script>";
            } else {
                echo "<div class='alert alert-danger'>Unable to delete product.</div>";
            }
        }
    } else {
        echo "<div class='alert alert-danger'>Product not found.</div>";
        include '../includes/footer.php';
        exit();
    }
} else {
    echo "<div class='alert alert-danger'>No product ID specified.</div>";
    include '../includes/footer.php';
    exit();
}
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-danger text-white">
                <h4 class="mb-0"><i class="fas fa-trash"></i> Delete Product</h4>
            </div>
            <div class="card-body text-center">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                    <h5>Are you sure you want to delete this product?</h5>
                    <p class="mb-0"><strong>Product:</strong> <?php echo htmlspecialchars($product['name']); ?></p>
                    <p class="text-muted">This action cannot be undone.</p>
                </div>
                
                <form method="post">
                    <div class="d-grid gap-2">
                        <button type="submit" name="confirm_delete" class="btn btn-danger btn-lg">
                            <i class="fas fa-trash"></i> Yes, Delete Product
                        </button>
                        <a href="index.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>