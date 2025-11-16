<?php
function getCategoryName($category_id, $conn) {
    if ($category_id) {
        $query = "SELECT name FROM categories WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$category_id]);
        $category = $stmt->fetch(PDO::FETCH_ASSOC);
        return $category ? $category['name'] : 'Uncategorized';
    }
    return 'Uncategorized';
}

function formatPrice($price) {
    return '$' . number_format($price, 2);
}

function getStockStatus($quantity) {
    if ($quantity == 0) {
        return '<span class="badge bg-danger">Out of Stock</span>';
    } elseif ($quantity < 10) {
        return '<span class="badge bg-warning">Low Stock</span>';
    } else {
        return '<span class="badge bg-success">In Stock</span>';
    }
}
?>