<?php
include '../config/database.php';
include '../includes/header.php';

$database = new Database();
$db = $database->getConnection();

if($_POST){
    $name = $_POST['name'];
    $description = $_POST['description'];

    $query = "INSERT INTO categories SET name=?, description=?";
    $stmt = $db->prepare($query);
    
    if($stmt->execute([$name, $description])){
        echo "<div class='alert alert-success'>Category was added successfully.</div>";
    } else {
        echo "<div class='alert alert-danger'>Unable to add category.</div>";
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="fas fa-plus"></i> Add New Category</h4>
            </div>
            <div class="card-body">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="mb-3">
                        <label for="name" class="form-label">Category Name *</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save"></i> Add Category
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