<?php
include 'config/database.php';
include 'includes/header.php';

// Handle form submission
if ($_POST) {
    try {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $address = $_POST['address'];
        $membership_date = $_POST['membership_date'];
        
        $sql = "INSERT INTO members (name, email, phone, address, membership_date) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name, $email, $phone, $address, $membership_date]);
        
        $success = "Member added successfully!";
    } catch(PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Get all members
$members = $pdo->query("SELECT * FROM members ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="row">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Add New Member</h3>
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
                    <label class="form-label">Full Name</label>
                    <input type="text" class="form-control" name="name" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Phone</label>
                    <input type="tel" class="form-control" name="phone">
                </div>
                <div class="form-group">
                    <label class="form-label">Address</label>
                    <textarea class="form-control" name="address" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Membership Date</label>
                    <input type="date" class="form-control" name="membership_date" required value="<?php echo date('Y-m-d'); ?>">
                </div>
                <button type="submit" class="btn btn-primary">Add Member</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">All Members</h3>
        </div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Membership Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($members as $member): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($member['name']); ?></td>
                        <td><?php echo htmlspecialchars($member['email']); ?></td>
                        <td><?php echo htmlspecialchars($member['phone']); ?></td>
                        <td><?php echo $member['membership_date']; ?></td>
                        <td>
                            <span class="badge <?php echo $member['status'] == 'active' ? 'badge-success' : 'badge-danger'; ?>">
                                <?php echo ucfirst($member['status']); ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>