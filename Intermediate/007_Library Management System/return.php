<?php
include 'config/database.php';
include 'includes/header.php';

// Handle form submission
if ($_POST) {
    try {
        $transaction_id = $_POST['transaction_id'];
        $return_date = $_POST['return_date'];
        
        // Get transaction details
        $transaction_sql = "SELECT * FROM transactions WHERE id = ? AND status = 'borrowed'";
        $transaction_stmt = $pdo->prepare($transaction_sql);
        $transaction_stmt->execute([$transaction_id]);
        $transaction = $transaction_stmt->fetch();
        
        if ($transaction) {
            // Calculate fine if overdue
            $due_date = new DateTime($transaction['due_date']);
            $return_date_obj = new DateTime($return_date);
            $fine_amount = 0;
            
            if ($return_date_obj > $due_date) {
                $days_overdue = $return_date_obj->diff($due_date)->days;
                $fine_amount = $days_overdue * 1.00; // $1 per day fine
            }
            
            // Update transaction
            $update_sql = "UPDATE transactions SET return_date = ?, status = 'returned', fine_amount = ? WHERE id = ?";
            $update_stmt = $pdo->prepare($update_sql);
            $update_stmt->execute([$return_date, $fine_amount, $transaction_id]);
            
            // Update book available copies
            $book_sql = "UPDATE books SET available_copies = available_copies + 1 WHERE id = ?";
            $book_stmt = $pdo->prepare($book_sql);
            $book_stmt->execute([$transaction['book_id']]);
            
            $success = "Book returned successfully!" . ($fine_amount > 0 ? " Fine amount: $" . number_format($fine_amount, 2) : "");
        } else {
            $error = "Invalid transaction or book already returned.";
        }
    } catch(PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Get borrowed books
$borrowed_books = $pdo->query("
    SELECT t.*, b.title, b.isbn, m.name 
    FROM transactions t 
    JOIN books b ON t.book_id = b.id 
    JOIN members m ON t.member_id = m.id 
    WHERE t.status = 'borrowed'
    ORDER BY t.due_date
")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Return Book</h3>
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
                <label class="form-label">Select Borrowed Book</label>
                <select class="form-control" name="transaction_id" required>
                    <option value="">Choose a borrowed book...</option>
                    <?php foreach($borrowed_books as $book): ?>
                    <option value="<?php echo $book['id']; ?>">
                        <?php echo htmlspecialchars($book['title']); ?> - Borrowed by <?php echo htmlspecialchars($book['name']); ?>
                        (Due: <?php echo $book['due_date']; ?>)
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Return Date</label>
                <input type="date" class="form-control" name="return_date" required value="<?php echo date('Y-m-d'); ?>">
            </div>
            <button type="submit" class="btn btn-primary">Return Book</button>
        </form>
        
        <div class="mt-4">
            <h4>Currently Borrowed Books</h4>
            <table class="table">
                <thead>
                    <tr>
                        <th>Book</th>
                        <th>Member</th>
                        <th>Borrow Date</th>
                        <th>Due Date</th>
                        <th>Days Overdue</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($borrowed_books as $book): 
                        $due_date = new DateTime($book['due_date']);
                        $today = new DateTime();
                        $is_overdue = $today > $due_date;
                        $days_overdue = $is_overdue ? $today->diff($due_date)->days : 0;
                    ?>
                    <tr class="<?php echo $is_overdue ? 'table-danger' : ''; ?>">
                        <td><?php echo htmlspecialchars($book['title']); ?></td>
                        <td><?php echo htmlspecialchars($book['name']); ?></td>
                        <td><?php echo $book['borrow_date']; ?></td>
                        <td><?php echo $book['due_date']; ?></td>
                        <td>
                            <?php if ($is_overdue): ?>
                                <span class="text-danger"><?php echo $days_overdue; ?> days</span>
                            <?php else: ?>
                                <span class="text-success">On time</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>