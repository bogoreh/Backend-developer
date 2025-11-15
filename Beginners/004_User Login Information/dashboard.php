<?php
require_once 'config/database.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

$database = new Database();
$db = $database->getConnection();
$auth = new Auth($db);

// Redirect if not logged in
if(!$auth->isLoggedIn()) {
    redirect('login.php');
}

$user = $auth->getCurrentUser();
?>

<?php include 'includes/header.php'; ?>

<div class="container">
    <div class="dashboard-header">
        <h1><i class="fas fa-tachometer-alt"></i> Dashboard</h1>
        <p>Welcome to your personal dashboard</p>
    </div>

    <div class="dashboard-grid">
        <div class="dashboard-card">
            <div class="card-icon">
                <i class="fas fa-user"></i>
            </div>
            <h3>Profile Info</h3>
            <div class="user-info">
                <p><strong>Username:</strong> <?php echo $user['username']; ?></p>
                <p><strong>Email:</strong> <?php echo $user['email']; ?></p>
                <p><strong>Role:</strong> <span class="role-badge"><?php echo $user['role']; ?></span></p>
            </div>
        </div>

        <div class="dashboard-card">
            <div class="card-icon">
                <i class="fas fa-shield-alt"></i>
            </div>
            <h3>Security</h3>
            <p>Your account is secure and protected</p>
            <button class="btn btn-secondary">Change Password</button>
        </div>

        <div class="dashboard-card">
            <div class="card-icon">
                <i class="fas fa-chart-bar"></i>
            </div>
            <h3>Statistics</h3>
            <p>Account created: <?php echo date('F j, Y'); ?></p>
            <div class="stats">
                <div class="stat-item">
                    <span class="stat-number">0</span>
                    <span class="stat-label">Login Today</span>
                </div>
            </div>
        </div>

        <?php if($auth->hasRole('admin')): ?>
        <div class="dashboard-card admin-card">
            <div class="card-icon">
                <i class="fas fa-crown"></i>
            </div>
            <h3>Admin Panel</h3>
            <p>You have administrator privileges</p>
            <button class="btn btn-warning">Manage Users</button>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>