<?php
session_start();

// Check if user is logged in
if(!isset($_SESSION["admin_loggedin"]) || $_SESSION["admin_loggedin"] !== true){
    header("location: login.php");
    exit;
}

require_once '../config/database.php';

// Check if id parameter exists
if(!isset($_GET["id"])){
    header("location: manage-users.php");
    exit;
}

$id = $_GET["id"];
$username = $email = "";
$username_err = $email_err = $password_err = "";

// Fetch user data
$sql = "SELECT username, email FROM admin_users WHERE id = ?";
if($stmt = $conn->prepare($sql)){
    $stmt->execute([$id]);
    if($stmt->rowCount() == 1){
        $row = $stmt->fetch();
        $username = $row["username"];
        $email = $row["email"];
    } else {
        header("location: manage-users.php");
        exit();
    }
}

if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validate username
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter a username.";
    } else {
        $sql = "SELECT id FROM admin_users WHERE username = ? AND id != ?";
        if($stmt = $conn->prepare($sql)){
            if($stmt->execute([trim($_POST["username"]), $id])){
                if($stmt->rowCount() > 0){
                    $username_err = "This username is already taken.";
                } else {
                    $username = trim($_POST["username"]);
                }
            }
        }
    }

    // Validate email
    if(empty(trim($_POST["email"]))){
        $email_err = "Please enter an email.";
    } else {
        $sql = "SELECT id FROM admin_users WHERE email = ? AND id != ?";
        if($stmt = $conn->prepare($sql)){
            if($stmt->execute([trim($_POST["email"]), $id])){
                if($stmt->rowCount() > 0){
                    $email_err = "This email is already registered.";
                } else {
                    $email = trim($_POST["email"]);
                }
            }
        }
    }

    // Validate password (optional)
    $password = trim($_POST["password"]);
    if(!empty($password) && strlen($password) < 6){
        $password_err = "Password must have at least 6 characters.";
    }

    // Check input errors before updating the database
    if(empty($username_err) && empty($email_err) && empty($password_err)){
        // Prepare an update statement
        $sql = empty($password) 
            ? "UPDATE admin_users SET username = ?, email = ? WHERE id = ?"
            : "UPDATE admin_users SET username = ?, email = ?, password = ? WHERE id = ?";
        
        if($stmt = $conn->prepare($sql)){
            $params = [$username, $email];
            
            if(!empty($password)){
                $params[] = password_hash($password, PASSWORD_DEFAULT);
            }
            $params[] = $id;
            
            if($stmt->execute($params)){
                $_SESSION['success_msg'] = "User updated successfully.";
                header("location: manage-users.php");
                exit();
            } else {
                $_SESSION['error_msg'] = "Something went wrong. Please try again later.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - Pet Adoption System</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-white shadow-lg">
            <div class="max-w-7xl mx-auto px-4">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="flex-shrink-0 flex items-center">
                            <a href="dashboard.php" class="text-xl font-bold text-gray-800">Admin Dashboard</a>
                        </div>
                        <div class="hidden md:ml-6 md:flex md:space-x-8">
                            <a href="add-pet.php" class="inline-flex items-center px-1 pt-1 text-gray-600 hover:text-gray-900">Add Pet</a>
                            <a href="manage-users.php" class="inline-flex items-center px-1 pt-1 border-b-2 border-blue-500 text-gray-900">Manage Users</a>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <span class="text-gray-600 mr-4">Welcome, <?php echo htmlspecialchars($_SESSION["admin_username"]); ?></span>
                        <a href="logout.php" class="text-gray-600 hover:text-gray-900">Logout</a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <div class="md:grid md:grid-cols-3 md:gap-6">
                        <div class="md:col-span-1">
                            <h3 class="text-lg font-medium leading-6 text-gray-900">Edit Admin User</h3>
                            <p class="mt-1 text-sm text-gray-600">
                                Update the administrator account details.
                            </p>
                        </div>
                        <div class="mt-5 md:mt-0 md:col-span-2">
                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?id=" . $id; ?>" method="post">
                                <div class="grid grid-cols-6 gap-6">
                                    <div class="col-span-6 sm:col-span-4">
                                        <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                                        <input type="text" name="username" id="username" 
                                               class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md <?php echo (!empty($username_err)) ? 'border-red-500' : ''; ?>" 
                                               value="<?php echo $username; ?>">
                                        <?php if(!empty($username_err)): ?>
                                            <p class="mt-2 text-sm text-red-600"><?php echo $username_err; ?></p>
                                        <?php endif; ?>
                                    </div>

                                    <div class="col-span-6 sm:col-span-4">
                                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                        <input type="email" name="email" id="email" 
                                               class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md <?php echo (!empty($email_err)) ? 'border-red-500' : ''; ?>" 
                                               value="<?php echo $email; ?>">
                                        <?php if(!empty($email_err)): ?>
                                            <p class="mt-2 text-sm text-red-600"><?php echo $email_err; ?></p>
                                        <?php endif; ?>
                                    </div>

                                    <div class="col-span-6 sm:col-span-4">
                                        <label for="password" class="block text-sm font-medium text-gray-700">New Password (leave blank to keep current)</label>
                                        <input type="password" name="password" id="password" 
                                               class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md <?php echo (!empty($password_err)) ? 'border-red-500' : ''; ?>">
                                        <?php if(!empty($password_err)): ?>
                                            <p class="mt-2 text-sm text-red-600"><?php echo $password_err; ?></p>
                                        <?php endif; ?>
                                        <p class="mt-2 text-sm text-gray-500">Minimum 6 characters</p>
                                    </div>
                                </div>

                                <div class="mt-6 flex items-center justify-end space-x-4">
                                    <a href="manage-users.php" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        Cancel
                                    </a>
                                    <button type="submit" class="bg-blue-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        Update User
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>