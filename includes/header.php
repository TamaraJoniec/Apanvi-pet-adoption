<?php
// Start the session if it hasn't been started yet
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pet Adoption System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <nav class="bg-blue-600 text-white shadow-lg">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <a href="/pages/index.php" class="text-2xl font-bold">Pet Adoption</a>
                <div class="space-x-4">
                    <a href="/pages/index.php" class="hover:text-blue-200">Home</a>
                    <a href="/pages/available-pets.php" class="hover:text-blue-200">Available Pets</a>
                    <a href="/pages/about.php" class="hover:text-blue-200">About</a>
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <a href="/pages/dashboard.php" class="hover:text-blue-200">Dashboard</a>
                        <a href="/pages/logout.php" class="hover:text-blue-200">Logout</a>
                    <?php else: ?>
                        <a href="/pages/login.php" class="hover:text-blue-200">Login</a>
                        <a href="/pages/register.php" class="hover:text-blue-200">Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
    <main class="container mx-auto px-4 py-8">
    <?php if(isset($_SESSION['message'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <?php 
            echo htmlspecialchars($_SESSION['message']); 
            unset($_SESSION['message']);
            ?>
        </div>
    <?php endif; ?> 