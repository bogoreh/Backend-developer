<?php
include 'config/database.php';
include 'includes/header.php';

// Get statistics
$total_books = $pdo->query("SELECT COUNT(*) FROM books")->fetchColumn();
$total_members = $pdo->query("SELECT COUNT(*) FROM members WHERE status='active'")->fetchColumn();
$borrowed_books = $pdo->query("SELECT COUNT(*) FROM transactions WHERE status='borrowed'")->fetchColumn();
$overdue_books = $pdo->query("SELECT COUNT(*) FROM transactions WHERE status='borrowed' AND due_date < CURDATE()")->fetchColumn();

// Get recent transactions
$recent_transactions = $pdo->query("
    SELECT t.*, b.title, m.name 
    FROM transactions t 
    JOIN books b ON t.book_id = b.id 
    JOIN members m ON t.member_id = m.id 
    ORDER BY t.created_at DESC 
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-number"><?php echo $total_books; ?></div>
        <div class="stat-label">Total Books</div>
    </div>
    <div class="stat-card">
        <div class="stat-number"><?php echo $total_members; ?></div>
        <div class="stat-label">Active Members</div>
    </div>
    <div class="stat-card">
        <div class="stat-number"><?php echo $borrowed_books; ?></div>
        <div class="stat-label">Borrowed Books</div>
    </div>
    <div class="stat-card">
        <div class="stat-number"><?php echo $overdue_books; ?></div>
        <div class="stat-label">Overdue Books</div>
    </div>
</div>

<div class="row">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Recent Transactions</h3>
            <a href="borrow.php" class="btn btn-primary">New Transaction</a>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th>Book</th>
                    <th>Member</th>
                    <th>Borrow Date</th>
                    <th>Due Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($recent_transactions as $transaction): ?>
                <tr>
                    <td><?php echo htmlspecialchars($transaction['title']); ?></td>
                    <td><?php echo htmlspecialchars($transaction['name']); ?></td>
                    <td><?php echo $transaction['borrow_date']; ?></td>
                    <td><?php echo $transaction['due_date']; ?></td>
                    <td>
                        <span class="badge <?php echo $transaction['status'] == 'borrowed' ? 'badge-warning' : 'badge-success'; ?>">
                            <?php echo ucfirst($transaction['status']); ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>