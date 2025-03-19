<?php
session_start();
require_once '../config/database.php';

// Initialize variables
$username = $password = "";
$username_err = $password_err = $login_err = "";

// Check if already logged in
if(isset($_SESSION["admin_loggedin"]) && $_SESSION["admin_loggedin"] === true) {
    header("location: dashboard.php");
    exit;
}

// Process form data when submitted
if($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate username
    if(empty(trim($_POST["username"]))) {
        $username_err = "Please enter username.";
    } else {
        $username = trim($_POST["username"]);
    }
    
    // Validate password
    if(empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }
    
    // Validate credentials
    if(empty($username_err) && empty($password_err)) {
        $sql = "SELECT id, username, password FROM admin_users WHERE username = :username";
        
        if($stmt = $conn->prepare($sql)) {
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $username;
            
            if($stmt->execute()) {
                if($stmt->rowCount() == 1) {
                    if($row = $stmt->fetch()) {
                        $id = $row["id"];
                        $username = $row["username"];
                        $hashed_password = $row["password"];
                        if(password_verify($password, $hashed_password)) {
                            // Password is correct, start a new session
                            session_regenerate_id(true); // Prevent session fixation attacks
                            
                            // Store data in session variables
                            $_SESSION["admin_loggedin"] = true;
                            $_SESSION["admin_id"] = $id;
                            $_SESSION["admin_username"] = $username;
                            $_SESSION["last_activity"] = time(); // For session timeout
                            
                            // Log successful login
                            $log_sql = "INSERT INTO admin_login_logs (admin_id, action, ip_address) VALUES (:admin_id, 'login', :ip)";
                            $log_stmt = $conn->prepare($log_sql);
                            $log_stmt->execute([
                                ':admin_id' => $id,
                                ':ip' => $_SERVER['REMOTE_ADDR']
                            ]);
                            
                            header("location: dashboard.php");
                            exit;
                        } else {
                            $login_err = "Invalid username or password.";
                            // Log failed attempt
                            $log_sql = "INSERT INTO admin_login_logs (username, action, ip_address, status) VALUES (:username, 'failed_login', :ip, 'failed')";
                            $log_stmt = $conn->prepare($log_sql);
                            $log_stmt->execute([
                                ':username' => $username,
                                ':ip' => $_SERVER['REMOTE_ADDR']
                            ]);
                        }
                    }
                } else {
                    $login_err = "Invalid username or password.";
                }
            } else {
                $login_err = "Oops! Something went wrong. Please try again later.";
            }
            unset($stmt);
        }
    }
    unset($conn);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Pet Adoption System</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-md w-96">
            <h2 class="text-2xl font-bold mb-6 text-center text-gray-800">Admin Login</h2>
            
            <?php 
            if(!empty($login_err)){
                echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">';
                echo $login_err;
                echo '</div>';
            }        
            ?>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="username">Username</label>
                    <input type="text" name="username" id="username" 
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline <?php echo (!empty($username_err)) ? 'border-red-500' : ''; ?>" 
                           value="<?php echo $username; ?>">
                    <?php if(!empty($username_err)): ?>
                        <p class="text-red-500 text-xs italic"><?php echo $username_err; ?></p>
                    <?php endif; ?>
                </div>    

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="password">Password</label>
                    <input type="password" name="password" id="password" 
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline <?php echo (!empty($password_err)) ? 'border-red-500' : ''; ?>">
                    <?php if(!empty($password_err)): ?>
                        <p class="text-red-500 text-xs italic"><?php echo $password_err; ?></p>
                    <?php endif; ?>
                </div>

                <div class="flex items-center justify-between">
                    <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" 
                            type="submit">Sign In</button>
                    <a class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800" 
                       href="reset-password.php">Forgot Password?</a>
                </div>
            </form>
            
            <div class="mt-6 text-center">
                <a href="../index.php" class="text-sm text-gray-600 hover:text-gray-900">Back to Homepage</a>
            </div>
        </div>
    </div>
</body>
</html> 