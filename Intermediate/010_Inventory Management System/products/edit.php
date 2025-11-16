<?php
include '../config/database.php';
include '../includes/header.php';

$database = new Database();
$db = $database->getConnection();

// Get categories for dropdown
$categories = $db->query("SELECT * FROM categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

if(isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Get product details
    $query = "SELECT * FROM products WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if(!$product) {
        echo "<div class='alert alert-danger'>Product not found.</div>";
        include '../includes/footer.php';
        exit();
    }
} else {
    echo "<div class='alert alert-danger'>No product ID specified.</div>";
    include '../includes/footer.php';
    exit();
}

if($_POST){
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    $category_id = $_POST['category_id'];
    $sku = $_POST['sku'];

    $query = "UPDATE products SET name=?, description=?, price=?, quantity=?, category_id=?, sku=?, updated_at=NOW() WHERE id=?";
    $stmt = $db->prepare($query);
    
    if($stmt->execute([$name, $description, $price, $quantity, $category_id, $sku, $id])){
        echo "<div class='alert alert-success'>Product was updated successfully.</div>";
        // Refresh product data
        $stmt = $db->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        echo "<div class='alert alert-danger'>Unable to update product.</div>";
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-warning text-white">
                <h4 class="mb-0"><i class="fas fa-edit"></i> Edit Product</h4>
            </div>
            <div class="card-body">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $id); ?>" method="post">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="sku" class="form-label">SKU *</label>
                            <input type="text" class="form-control" id="sku" name="sku" 
                                   value="<?php echo htmlspecialchars($product['sku']); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Product Name *</label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="<?php echo htmlspecialchars($product['name']); ?>" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($product['description']); ?></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="price" class="form-label">Price *</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="price" name="price" 
                                       step="0.01" min="0" value="<?php echo htmlspecialchars($product['price']); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="quantity" class="form-label">Quantity *</label>
                            <input type="number" class="form-control" id="quantity" name="quantity" 
                                   min="0" value="<?php echo htmlspecialchars($product['quantity']); ?>" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="category_id" class="form-label">Category</label>
                            <select class="form-select" id="category_id" name="category_id">
                                <option value="">Select Category</option>
                                <?php foreach($categories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>" 
                                        <?php echo ($product['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($category['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="card bg-light mb-3">
                        <div class="card-body">
                            <h6 class="card-title"><i class="fas fa-info-circle"></i> Product Information</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <small class="text-muted">Created: <?php echo date('M j, Y g:i A', strtotime($product['created_at'])); ?></small>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted">Last Updated: <?php echo date('M j, Y g:i A', strtotime($product['updated_at'])); ?></small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-warning btn-lg">
                            <i class="fas fa-save"></i> Update Product
                        </button>
                        <a href="index.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Products
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>