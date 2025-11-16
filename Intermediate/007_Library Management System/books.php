<?php
include 'config/database.php';
include 'includes/header.php';

// Handle form submission
if ($_POST) {
    try {
        $title = $_POST['title'];
        $author = $_POST['author'];
        $isbn = $_POST['isbn'];
        $publication_year = $_POST['publication_year'];
        $category = $_POST['category'];
        $total_copies = $_POST['total_copies'];
        
        $sql = "INSERT INTO books (title, author, isbn, publication_year, category, total_copies, available_copies) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$title, $author, $isbn, $publication_year, $category, $total_copies, $total_copies]);
        
        $success = "Book added successfully!";
    } catch(PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Get all books
$books = $pdo->query("SELECT * FROM books ORDER BY title")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="row">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Add New Book</h3>
        </div>
        <div class="card-body">
            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            <?php if (isset($error)): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label class="form-label">Title</label>
                    <input type="text" class="form-control" name="title" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Author</label>
                    <input type="text" class="form-control" name="author" required>
                </div>
                <div class="form-group">
                    <label class="form-label">ISBN</label>
                    <input type="text" class="form-control" name="isbn" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Publication Year</label>
                    <input type="number" class="form-control" name="publication_year" min="1000" max="2023" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Category</label>
                    <input type="text" class="form-control" name="category" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Total Copies</label>
                    <input type="number" class="form-control" name="total_copies" min="1" value="1" required>
                </div>
                <button type="submit" class="btn btn-primary">Add Book</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">All Books</h3>
        </div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Author</th>
                        <th>ISBN</th>
                        <th>Category</th>
                        <th>Available</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($books as $book): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($book['title']); ?></td>
                        <td><?php echo htmlspecialchars($book['author']); ?></td>
                        <td><?php echo htmlspecialchars($book['isbn']); ?></td>
                        <td><?php echo htmlspecialchars($book['category']); ?></td>
                        <td><?php echo $book['available_copies']; ?>/<?php echo $book['total_copies']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>