<?php include 'includes/header.php'; ?>

<div class="container">
    <div class="hero-section">
        <div class="hero-content">
            <h1>Welcome to SecureApp</h1>
            <p>A secure and modern PHP login system with role-based authorization</p>
            <div class="hero-buttons">
                <?php if(isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                    <a href="dashboard.php" class="btn btn-primary">Go to Dashboard</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-primary">Login</a>
                    <a href="register.php" class="btn btn-secondary">Register</a>
                <?php endif; ?>
            </div>
        </div>
        <div class="hero-image">
            <i class="fas fa-lock"></i>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>