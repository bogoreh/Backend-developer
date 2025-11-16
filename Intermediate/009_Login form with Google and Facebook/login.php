<?php
require_once 'includes/functions.php';

// If user is already logged in, redirect to dashboard
if (isLoggedIn()) {
    redirect('dashboard.php');
}

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "SELECT * FROM users WHERE email = :email AND provider = 'local'";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    
    if ($stmt->rowCount() == 1) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (password_verify($password, $user['password'])) {
            loginUser($user);
            redirect('dashboard.php');
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "No account found with this email!";
    }
}

// Handle registration
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    if ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        $database = new Database();
        $db = $database->getConnection();
        
        // Check if email already exists
        $query = "SELECT id FROM users WHERE email = :email";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $error = "Email already registered!";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $query = "INSERT INTO users (name, email, password, provider) VALUES (:name, :email, :password, 'local')";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $hashed_password);
            
            if ($stmt->execute()) {
                $success = "Registration successful! Please login.";
            } else {
                $error = "Registration failed!";
            }
        }
    }
}

include 'includes/header.php';
?>

<div class="login-container">
    <div class="login-box">
        <h2>Welcome Back</h2>
        <p class="subtitle">Sign in to your account</p>
        
        <?php if ($error): ?>
            <div class="alert error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <!-- Social Login Buttons -->
        <div class="social-buttons">
            <a href="<?php echo generateGoogleAuthUrl(); ?>" class="btn-social btn-google">
                <i class="fab fa-google"></i>
                Continue with Google
            </a>
            <a href="<?php echo generateFacebookAuthUrl(); ?>" class="btn-social btn-facebook">
                <i class="fab fa-facebook-f"></i>
                Continue with Facebook
            </a>
        </div>
        
        <div class="divider">
            <span>or</span>
        </div>
        
        <!-- Login Form -->
        <form method="POST" class="login-form">
            <input type="hidden" name="login" value="1">
            <div class="form-group">
                <input type="email" name="email" placeholder="Email address" required>
                <i class="fas fa-envelope"></i>
            </div>
            <div class="form-group">
                <input type="password" name="password" placeholder="Password" required>
                <i class="fas fa-lock"></i>
            </div>
            <button type="submit" class="btn-login">Sign In</button>
        </form>
        
        <!-- Registration Form -->
        <div class="register-section">
            <h3>Don't have an account?</h3>
            <form method="POST" class="register-form">
                <input type="hidden" name="register" value="1">
                <div class="form-group">
                    <input type="text" name="name" placeholder="Full Name" required>
                    <i class="fas fa-user"></i>
                </div>
                <div class="form-group">
                    <input type="email" name="email" placeholder="Email address" required>
                    <i class="fas fa-envelope"></i>
                </div>
                <div class="form-group">
                    <input type="password" name="password" placeholder="Password" required>
                    <i class="fas fa-lock"></i>
                </div>
                <div class="form-group">
                    <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                    <i class="fas fa-lock"></i>
                </div>
                <button type="submit" class="btn-register">Create Account</button>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>