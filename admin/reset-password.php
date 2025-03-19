<?php
session_start();
require_once '../config/database.php';

$email = $email_err = $success_msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter your email address.";
    } else {
        $email = trim($_POST["email"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email_err = "Invalid email format.";
        }
    }
    
    if (empty($email_err)) {
        // Check if email exists in admin_users table
        $sql = "SELECT id FROM admin_users WHERE email = :email";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bindParam(":email", $email, PDO::PARAM_STR);
            
            if ($stmt->execute()) {
                if ($stmt->rowCount() == 1) {
                    // Generate reset token
                    $token = bin2hex(random_bytes(32));
                    $token_hash = hash('sha256', $token);
                    $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
                    
                    // Store token in database
                    $update_sql = "UPDATE admin_users SET reset_token = :token, reset_expiry = :expiry WHERE email = :email";
                    $update_stmt = $conn->prepare($update_sql);
                    $update_stmt->execute([
                        ':token' => $token_hash,
                        ':expiry' => $expiry,
                        ':email' => $email
                    ]);
                    
                    // Send reset email (you'll need to configure email sending)
                    $reset_link = "https://" . $_SERVER['HTTP_HOST'] . "/admin/new-password.php?token=" . $token;
                    
                    // For now, we'll just show the reset link
                    $success_msg = "Password reset instructions have been sent to your email.";
                    
                    // TODO: Implement actual email sending
                    // mail($email, "Password Reset Request", "Click this link to reset your password: " . $reset_link);
                } else {
                    $email_err = "No account found with that email address.";
                }
            }
            unset($stmt);
        }
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
            <h2 class="text-2xl font-bold mb-6 text-center text-gray-800">Reset Password</h2>
            
            <?php 
            if(!empty($success_msg)){
                echo '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">';
                echo $success_msg;
                echo '</div>';
            }
            ?>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="email">Email Address</label>
                    <input type="email" name="email" id="email" 
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline <?php echo (!empty($email_err)) ? 'border-red-500' : ''; ?>" 
                           value="<?php echo $email; ?>">
                    <?php if(!empty($email_err)): ?>
                        <p class="text-red-500 text-xs italic"><?php echo $email_err; ?></p>
                    <?php endif; ?>
                </div>

                <div class="flex items-center justify-between">
                    <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" 
                            type="submit">Reset Password</button>
                    <a class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800" 
                       href="login.php">Back to Login</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html> 