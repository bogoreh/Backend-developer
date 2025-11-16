<?php
include '../config/database.php';
include '../includes/header.php';

$database = new Database();
$db = $database->getConnection();

$search = $_GET['search'] ?? '';

$query = "SELECT c.*, COUNT(p.id) as product_count 
          FROM categories c 
          LEFT JOIN products p ON c.id = p.category_id 
          WHERE 1=1";
$params = [];

if (!empty($search)) {
    $query .= " AND (c.name LIKE ? OR c.description LIKE ?)";
    $search_term = "%$search%";
    $params[] = $search_term;
    $params[] = $search_term;
}

$query .= " GROUP BY c.id ORDER BY c.name";

$stmt = $db->prepare($query);
$stmt->execute($params);
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-tags"></i> Category Management</h2>
    <a href="add.php" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add New Category
    </a>
</div>

<!-- Search Form -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-8">
                <input type="text" name="search" class="form-control" placeholder="Search categories..." value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary me-2">Search</button>
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
                        <th>Category Name</th>
                        <th>Description</th>
                        <th>Products Count</th>
                        <th>Created Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($categories) > 0): ?>
                        <?php foreach($categories as $category): ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($category['name']); ?></strong>
                            </td>
                            <td><?php echo htmlspecialchars($category['description'] ?: 'No description'); ?></td>
                            <td>
                                <span class="badge bg-primary"><?php echo $category['product_count']; ?> products</span>
                            </td>
                            <td><?php echo date('M j, Y', strtotime($category['created_at'])); ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="edit.php?id=<?php echo $category['id']; ?>" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="delete.php?id=<?php echo $category['id']; ?>" class="btn btn-sm btn-danger" 
                                       onclick="return confirm('Are you sure? This will set products in this category to uncategorized.')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="fas fa-tags fa-3x mb-3"></i>
                                <p>No categories found. <a href="add.php">Add your first category</a></p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>