<?php
session_start();
// Check if user is logged in as admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

require_once '../config/database.php';

// Fetch all pets
try {
    $stmt = $conn->query("SELECT * FROM pets");
    $pets = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Error fetching pets data.";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Pets - Pet Adoption System</title>
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
                            <a href="manage-pets.php" class="inline-flex items-center px-1 pt-1 text-gray-600 hover:text-gray-900">Manage Pets</a>
                            <a href="applications.php" class="inline-flex items-center px-1 pt-1 text-gray-600 hover:text-gray-900">Applications</a>
                            <a href="users.php" class="inline-flex items-center px-1 pt-1 text-gray-600 hover:text-gray-900">Users</a>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <span class="text-gray-600 mr-4">Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
                        <a href="logout.php" class="text-gray-600 hover:text-gray-900">Logout</a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Manage Pets</h2>
            <?php if (isset($error)): ?>
                <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700"><?php echo htmlspecialchars($error); ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="flex justify-end mb-4">
                <a href="add-pet.php" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    Add New Pet
                </a>
            </div>

            <div class="bg-white shadow overflow-hidden sm:rounded-md">
                <ul class="divide-y divide-gray-200">
                    <?php foreach ($pets as $pet): ?>
                        <li>
                            <div class="px-4 py-4 sm:px-6">
                                <div class="flex items-center justify-between">
                                    <div class="text-sm font-medium text-blue-600 truncate">
                                        <?php echo htmlspecialchars($pet['name']); ?>
                                    </div>
                                    <div class="ml-2 flex-shrink-0 flex">
                                        <a href="edit-pet.php?id=<?php echo $pet['id']; ?>" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                        <form action="delete-pet.php" method="POST" class="ml-4">
                                            <input type="hidden" name="id" value="<?php echo $pet['id']; ?>">
                                            <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                        </form>
                                    </div>
                                </div>
                                <div class="mt-2 sm:flex sm:justify-between">
                                    <div class="sm:flex">
                                        <p class="flex items-center text-sm text-gray-500">
                                            <?php echo htmlspecialchars($pet['breed']); ?>
                                        </p>
                                    </div>
                                    <div class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0">
                                        <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 00-1-1H6zm0 2h8V3H6v1zM4 7h12v9H4V7z" clip-rule="evenodd" />
                                        </svg>
                                        <p>
                                            <?php echo htmlspecialchars($pet['status']); ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</body>

</html>