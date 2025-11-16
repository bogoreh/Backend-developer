<?php
include '../config/database.php';
include '../includes/header.php';
include '../includes/functions.php';

$database = new Database();
$db = $database->getConnection();

$search = $_GET['search'] ?? '';
$category_filter = $_GET['category'] ?? '';

$query = "SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE 1=1";
$params = [];

if (!empty($search)) {
    $query .= " AND (p.name LIKE ? OR p.description LIKE ? OR p.sku LIKE ?)";
    $search_term = "%$search%";
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
}

if (!empty($category_filter)) {
    $query .= " AND p.category_id = ?";
    $params[] = $category_filter;
}

$query .= " ORDER BY p.created_at DESC";

$stmt = $db->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get categories for filter
$categories = $db->query("SELECT * FROM categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-boxes"></i> Product Management</h2>
    <a href="add.php" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add New Product
    </a>
</div>

<!-- Search and Filter Form -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Search products..." value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="col-md-4">
                <select name="category" class="form-select">
                    <option value="">All Categories</option>
                    <?php foreach($categories as $category): ?>
                        <option value="<?php echo $category['id']; ?>" <?php echo ($category_filter == $category['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($category['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary me-2">Filter</button>
                <a href="index.php" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>SKU</th>
                        <th>Product Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($products) > 0): ?>
                        <?php foreach($products as $product): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($product['sku']); ?></strong></td>
                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                            <td><?php echo $product['category_name'] ?: 'Uncategorized'; ?></td>
                            <td class="fw-bold text-success">$<?php echo number_format($product['price'], 2); ?></td>
                            <td>
                                <span class="badge bg-secondary"><?php echo $product['quantity']; ?></span>
                            </td>
                            <td><?php echo getStockStatus($product['quantity']); ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="edit.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="delete.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this product?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="fas fa-box-open fa-3x mb-3"></i>
                                <p>No products found. <a href="add.php">Add your first product</a></p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>