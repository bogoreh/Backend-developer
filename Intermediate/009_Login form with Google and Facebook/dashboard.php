<?php
require_once 'includes/functions.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

include 'includes/header.php';
?>

<div class="dashboard-container">
    <div class="dashboard-header">
        <h1>Welcome, <?php echo $_SESSION['user_name']; ?>!</h1>
        <p>You have successfully logged in via <?php echo $_SESSION['provider']; ?>.</p>
        <a href="logout.php" class="btn-logout">Logout</a>
    </div>
    
    <div class="dashboard-content">
        <div class="info-card">
            <h3>Account Information</h3>
            <p><strong>Email:</strong> <?php echo $_SESSION['user_email']; ?></p>
            <p><strong>Login Method:</strong> <?php echo ucfirst($_SESSION['provider']); ?></p>
            <p><strong>Session Active:</strong> Yes</p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>