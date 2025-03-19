<?php
session_start();
require_once '../config/database.php';

$new_password = $confirm_password = "";
$new_password_err = $confirm_password_err = $token_err = "";

// Verify token
if (empty($_GET["token"])) {
    header("location: login.php");
    exit();
}

$token = $_GET["token"];
$token_hash = hash('sha256', $token);

// Check if token exists and is not expired
$sql = "SELECT id FROM admin_users WHERE reset_token = :token AND reset_expiry > NOW()";
$stmt = $conn->prepare($sql);
$stmt->bindParam(":token", $token_hash, PDO::PARAM_STR);
$stmt->execute();

if ($stmt->rowCount() != 1) {
    $token_err = "Invalid or expired reset link.";
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && empty($token_err)) {
    // Validate new password
    if (empty(trim($_POST["new_password"]))) {
        $new_password_err = "Please enter the new password.";     
    } elseif (strlen(trim($_POST["new_password"])) < 8) {
        $new_password_err = "Password must have at least 8 characters.";
    } else {
        $new_password = trim($_POST["new_password"]);
    }
    
    // Validate confirm password
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Please confirm the password.";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if (empty($new_password_err) && ($new_password != $confirm_password)) {
            $confirm_password_err = "Passwords did not match.";
        }
    }
    
    // Check input errors before updating the database
    if (empty($new_password_err) && empty($confirm_password_err)) {
        $sql = "UPDATE admin_users SET password = :password, reset_token = NULL, reset_expiry = NULL WHERE reset_token = :token";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bindParam(":password", password_hash($new_password, PASSWORD_DEFAULT), PDO::PARAM_STR);
            $stmt->bindParam(":token", $token_hash, PDO::PARAM_STR);
            
            if ($stmt->execute()) {
                // Password updated successfully. Destroy the session, and redirect to login page
                session_destroy();
                header("location: login.php?password_reset=success");
                exit();
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
        unset($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Pet Adoption System</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-md w-96">
            <h2 class="text-2xl font-bold mb-6 text-center text-gray-800">Set New Password</h2>
            
            <?php 
            if(!empty($token_err)){
                echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">';
                echo $token_err;
                echo '</div>';
            }
            ?>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?token=' . htmlspecialchars($token); ?>" method="post">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="new_password">New Password</label>
                    <input type="password" name="new_password" id="new_password" 
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline <?php echo (!empty($new_password_err)) ? 'border-red-500' : ''; ?>">
                    <?php if(!empty($new_password_err)): ?>
                        <p class="text-red-500 text-xs italic"><?php echo $new_password_err; ?></p>
                    <?php endif; ?>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="confirm_password">Confirm Password</label>
                    <input type="password" name="confirm_password" id="confirm_password" 
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline <?php echo (!empty($confirm_password_err)) ? 'border-red-500' : ''; ?>">
                    <?php if(!empty($confirm_password_err)): ?>
                        <p class="text-red-500 text-xs italic"><?php echo $confirm_password_err; ?></p>
                    <?php endif; ?>
                </div>

                <div class="flex items-center justify-between">
                    <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" 
                            type="submit">Update Password</button>
                    <a class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800" 
                       href="login.php">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html> 