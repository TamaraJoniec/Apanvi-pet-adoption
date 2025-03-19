<?php
session_start();
// Check if user is logged in as admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

require_once '../config/database.php';

$errors = [];
$name = '';
$breed = '';
$status = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $breed = trim($_POST['breed'] ?? '');
    $status = trim($_POST['status'] ?? '');

    // Validate inputs
    if (empty($name)) {
        $errors['name'] = 'Name is required';
    }
    if (empty($breed)) {
        $errors['breed'] = 'Breed is required';
    }
    if (empty($status)) {
        $errors['status'] = 'Status is required';
    }

    // If no validation errors, insert new pet
    if (empty($errors)) {
        try {
            $stmt = $conn->prepare("INSERT INTO pets (name, breed, status) VALUES (?, ?, ?)");
            $stmt->execute([$name, $breed, $status]);

            // Redirect to manage pets page
            header("Location: manage-pets.php");
            exit();
        } catch (PDOException $e) {
            $errors['database'] = 'Error adding pet. Please try again.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Pet - Pet Adoption System</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <div class="min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Add New Pet
            </h2>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
                <?php if (isset($errors['database'])): ?>
                    <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-red-700"><?php echo htmlspecialchars($errors['database']); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <form class="space-y-6" action="add-pet.php" method="POST">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">
                            Name
                        </label>
                        <div class="mt-1">
                            <input id="name" name="name" type="text" required
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                value="<?php echo htmlspecialchars($name); ?>">
                            <?php if (isset($errors['name'])): ?>
                                <p class="mt-2 text-sm text-red-600"><?php echo htmlspecialchars($errors['name']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div>
                        <label for="breed" class="block text-sm font-medium text-gray-700">
                            Breed
                        </label>
                        <div class="mt-1">
                            <input id="breed" name="breed" type="text" required
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                value="<?php echo htmlspecialchars($breed); ?>">
                            <?php if (isset($errors['breed'])): ?>
                                <p class="mt-2 text-sm text-red-600"><?php echo htmlspecialchars($errors['breed']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">
                            Status
                        </label>
                        <div class="mt-1">
                            <select id="status" name="status" required
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="Available" <?php echo $status === 'Available' ? 'selected' : ''; ?>>Available</option>
                                <option value="Adopted" <?php echo $status === 'Adopted' ? 'selected' : ''; ?>>Adopted</option>
                            </select>
                            <?php if (isset($errors['status'])): ?>
                                <p class="mt-2 text-sm text-red-600"><?php echo htmlspecialchars($errors['status']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div>
                        <button type="submit"
                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Add Pet
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>