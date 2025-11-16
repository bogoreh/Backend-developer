<?php
session_start();

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'login_system');
define('DB_USER', 'root');
define('DB_PASS', '');

// Google OAuth Configuration (You need to get these from Google Cloud Console)
define('GOOGLE_CLIENT_ID', 'your-google-client-id');
define('GOOGLE_CLIENT_SECRET', 'your-google-client-secret');
define('GOOGLE_REDIRECT_URI', 'http://localhost/login-app/login.php');

// Facebook OAuth Configuration (You need to get these from Facebook Developer)
define('FACEBOOK_APP_ID', 'your-facebook-app-id');
define('FACEBOOK_APP_SECRET', 'your-facebook-app-secret');
define('FACEBOOK_REDIRECT_URI', 'http://localhost/login-app/login.php');

// Base URL
define('BASE_URL', 'http://localhost/login-app/');
?>