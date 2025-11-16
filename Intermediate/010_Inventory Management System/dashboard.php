<?php
include 'config/database.php';
include 'includes/header.php';

$database = new Database();
$db = $database->getConnection();

// Get statistics
$total_products = $db->query("SELECT COUNT(*) as count FROM products")->fetch()['count'];
$total_categories = $db->query("SELECT COUNT(*) as count FROM categories")->fetch()['count'];
$low_stock = $db->query("SELECT COUNT(*) as count FROM products WHERE quantity < 10 AND quantity > 0")->fetch()['count'];
$out_of_stock = $db->query("SELECT COUNT(*) as count FROM products WHERE quantity = 0")->fetch()['count'];

// Get recent products
$recent_products = $db->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="row dashboard-stats">
    <div class="col-md-3 mb-4">
        <div class="card stat-card total-products">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="card-title"><?php echo $total_products; ?></h4>
                        <p class="card-text">Total Products</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-box fa-2x text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card stat-card categories">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="card-title"><?php echo $total_categories; ?></h4>
                        <p class="card-text">Categories</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-tags fa-2x text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card stat-card low-stock">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="card-title"><?php echo $low_stock; ?></h4>
                        <p class="card-text">Low Stock</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-exclamation-triangle fa-2x text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card stat-card out-of-stock">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="card-title"><?php echo $out_of_stock; ?></h4>
                        <p class="card-text">Out of Stock</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-times-circle fa-2x text-danger"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-clock"></i> Recent Products</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Category</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($recent_products as $product): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($product['name']); ?></td>
                                <td>$<?php echo number_format($product['price'], 2); ?></td>
                                <td><?php echo $product['quantity']; ?></td>
                                <td><?php echo $product['category_name'] ?: 'Uncategorized'; ?></td>
                                <td>
                                    <?php
                                    if ($product['quantity'] == 0) {
                                        echo '<span class="badge bg-danger">Out of Stock</span>';
                                    } elseif ($product['quantity'] < 10) {
                                        echo '<span class="badge bg-warning">Low Stock</span>';
                                    } else {
                                        echo '<span class="badge bg-success">In Stock</span>';
                                    }
                                    ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-rocket"></i> Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="products/add.php" class="btn btn-primary btn-lg">
                        <i class="fas fa-plus"></i> Add New Product
                    </a>
                    <a href="categories/add.php" class="btn btn-success btn-lg">
                        <i class="fas fa-tag"></i> Add New Category
                    </a>
                    <a href="products/index.php" class="btn btn-info btn-lg">
                        <i class="fas fa-list"></i> View All Products
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>