<?php
require_once 'config/database.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

$database = new Database();
$db = $database->getConnection();
$auth = new Auth($db);

$error = '';

if($_POST) {
    $email = sanitize($_POST['email']);
    $password = sanitize($_POST['password']);

    if($auth->login($email, $password)) {
        redirect('dashboard.php');
    } else {
        $error = 'Invalid email or password!';
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="container">
    <div class="form-wrapper">
        <div class="form-card">
            <div class="form-header">
                <h2><i class="fas fa-sign-in-alt"></i> Login to Your Account</h2>
                <p>Enter your credentials to access your dashboard</p>
            </div>

            <?php if($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="form-group">
                    <label for="email"><i class="fas fa-envelope"></i> Email Address</label>
                    <input type="email" id="email" name="email" required placeholder="Enter your email">
                </div>

                <div class="form-group">
                    <label for="password"><i class="fas fa-lock"></i> Password</label>
                    <input type="password" id="password" name="password" required placeholder="Enter your password">
                </div>

                <button type="submit" class="btn btn-primary btn-full">Login</button>
            </form>

            <div class="form-footer">
                <p>Don't have an account? <a href="register.php">Create one here</a></p>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>