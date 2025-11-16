<?php
include '../config/database.php';
include '../includes/header.php';

$database = new Database();
$db = $database->getConnection();

if(isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Get category details
    $query = "SELECT * FROM categories WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$id]);
    $category = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if(!$category) {
        echo "<div class='alert alert-danger'>Category not found.</div>";
        include '../includes/footer.php';
        exit();
    }
} else {
    echo "<div class='alert alert-danger'>No category ID specified.</div>";
    include '../includes/footer.php';
    exit();
}

if($_POST){
    $name = $_POST['name'];
    $description = $_POST['description'];

    $query = "UPDATE categories SET name=?, description=? WHERE id=?";
    $stmt = $db->prepare($query);
    
    if($stmt->execute([$name, $description, $id])){
        echo "<div class='alert alert-success'>Category was updated successfully.</div>";
        // Refresh category data
        $stmt = $db->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        $category = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        echo "<div class='alert alert-danger'>Unable to update category.</div>";
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-warning text-white">
                <h4 class="mb-0"><i class="fas fa-edit"></i> Edit Category</h4>
            </div>
            <div class="card-body">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $id); ?>" method="post">
                    <div class="mb-3">
                        <label for="name" class="form-label">Category Name *</label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="<?php echo htmlspecialchars($category['name']); ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($category['description']); ?></textarea>
                    </div>
                    
                    <div class="card bg-light mb-3">
                        <div class="card-body">
                            <h6 class="card-title"><i class="fas fa-info-circle"></i> Category Information</h6>
                            <small class="text-muted">Created: <?php echo date('M j, Y g:i A', strtotime($category['created_at'])); ?></small>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-warning btn-lg">
                            <i class="fas fa-save"></i> Update Category
                        </button>
                        <a href="index.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Categories
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>