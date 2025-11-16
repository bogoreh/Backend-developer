<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Management System</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-logo">
                <i class="fas fa-book"></i>
                Library System
            </div>
            <ul class="nav-menu">
                <li><a href="index.php"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="books.php"><i class="fas fa-book"></i> Books</a></li>
                <li><a href="members.php"><i class="fas fa-users"></i> Members</a></li>
                <li><a href="borrow.php"><i class="fas fa-hand-holding"></i> Borrow</a></li>
                <li><a href="return.php"><i class="fas fa-undo"></i> Return</a></li>
            </ul>
        </div>
    </nav>
    <div class="container">