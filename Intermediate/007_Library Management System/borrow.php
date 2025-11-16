<?php
include 'config/database.php';
include 'includes/header.php';

// Handle form submission
if ($_POST) {
    try {
        $book_id = $_POST['book_id'];
        $member_id = $_POST['member_id'];
        $borrow_date = $_POST['borrow_date'];
        $due_date = $_POST['due_date'];
        
        // Check if book is available
        $book_check = $pdo->prepare("SELECT available_copies FROM books WHERE id = ?");
        $book_check->execute([$book_id]);
        $book = $book_check->fetch();
        
        if ($book['available_copies'] > 0) {
            // Create transaction
            $sql = "INSERT INTO transactions (book_id, member_id, borrow_date, due_date) 
                    VALUES (?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$book_id, $member_id, $borrow_date, $due_date]);
            
            // Update available copies
            $update_sql = "UPDATE books SET available_copies = available_copies - 1 WHERE id = ?";
            $update_stmt = $pdo->prepare($update_sql);
            $update_stmt->execute([$book_id]);
            
            $success = "Book borrowed successfully!";
        } else {
            $error = "Sorry, this book is not available for borrowing.";
        }
    } catch(PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Get available books and active members
$books = $pdo->query("SELECT * FROM books WHERE available_copies > 0 ORDER BY title")->fetchAll(PDO::FETCH_ASSOC);
$members = $pdo->query("SELECT * FROM members WHERE status='active' ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Borrow Book</h3>
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
                <label class="form-label">Select Book</label>
                <select class="form-control" name="book_id" required>
                    <option value="">Choose a book...</option>
                    <?php foreach($books as $book): ?>
                    <option value="<?php echo $book['id']; ?>">
                        <?php echo htmlspecialchars($book['title']); ?> by <?php echo htmlspecialchars($book['author']); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Select Member</label>
                <select class="form-control" name="member_id" required>
                    <option value="">Choose a member...</option>
                    <?php foreach($members as $member): ?>
                    <option value="<?php echo $member['id']; ?>">
                        <?php echo htmlspecialchars($member['name']); ?> (<?php echo htmlspecialchars($member['email']); ?>)
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Borrow Date</label>
                <input type="date" class="form-control" name="borrow_date" required value="<?php echo date('Y-m-d'); ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Due Date</label>
                <input type="date" class="form-control" name="due_date" required value="<?php echo date('Y-m-d', strtotime('+14 days')); ?>">
            </div>
            <button type="submit" class="btn btn-primary">Borrow Book</button>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>