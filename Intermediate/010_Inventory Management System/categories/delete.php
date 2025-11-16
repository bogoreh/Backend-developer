<?php
include '../config/database.php';
include '../includes/header.php';

$database = new Database();
$db = $database->getConnection();

if(isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Get category details for confirmation message
    $query = "SELECT c.*, COUNT(p.id) as product_count 
              FROM categories c 
              LEFT JOIN products p ON c.id = p.category_id 
              WHERE c.id = ? 
              GROUP BY c.id";
    $stmt = $db->prepare($query);
    $stmt->execute([$id]);
    $category = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if($category) {
        if($_POST && isset($_POST['confirm_delete'])) {
            // First, set products in this category to NULL
            $update_products = "UPDATE products SET category_id = NULL WHERE category_id = ?";
            $update_stmt = $db->prepare($update_products);
            $update_stmt->execute([$id]);
            
            // Then delete the category
            $delete_query = "DELETE FROM categories WHERE id = ?";
            $delete_stmt = $db->prepare($delete_query);
            
            if($delete_stmt->execute([$id])) {
                echo "<script>
                    alert('Category deleted successfully! Products have been set to uncategorized.');
                    window.location.href = 'index.php';
                </script>";
            } else {
                echo "<div class='alert alert-danger'>Unable to delete category.</div>";
            }
        }
    } else {
        echo "<div class='alert alert-danger'>Category not found.</div>";
        include '../includes/footer.php';
        exit();
    }
} else {
    echo "<div class='alert alert-danger'>No category ID specified.</div>";
    include '../includes/footer.php';
    exit();
}
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-danger text-white">
                <h4 class="mb-0"><i class="fas fa-trash"></i> Delete Category</h4>
            </div>
            <div class="card-body text-center">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                    <h5>Are you sure you want to delete this category?</h5>
                    <p class="mb-1"><strong>Category:</strong> <?php echo htmlspecialchars($category['name']); ?></p>
                    <p class="mb-1"><strong>Products in this category:</strong> <?php echo $category['product_count']; ?></p>
                    <p class="text-muted mb-0">Products in this category will be set to uncategorized.</p>
                </div>
                
                <form method="post">
                    <div class="d-grid gap-2">
                        <button type="submit" name="confirm_delete" class="btn btn-danger btn-lg">
                            <i class="fas fa-trash"></i> Yes, Delete Category
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