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

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch pet data
    try {
        $stmt = $conn->prepare("SELECT * FROM pets WHERE id = ?");
        $stmt->execute([$id]);
        $pet = $stmt->fetch();

        if ($pet) {
            $name = $pet['name'];
            $breed = $pet['breed'];
            $status = $pet['status'];
        } else {
            $errors['database'] = 'Pet not found.';
        }
    } catch (PDOException $e) {
        $errors['database'] = 'Error fetching pet data.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
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

    // If no validation errors, update pet
    if (empty($errors)) {
        try {
            $stmt = $conn->prepare("UPDATE pets SET name = ?, breed = ?, status = ? WHERE id = ?");
            $stmt->execute([$name, $breed, $status, $id]);

            // Redirect to manage pets page
            header("Location: manage-pets.php");
            exit();
        } catch (PDOException $e) {
            $errors['database'] = 'Error updating pet. Please try again.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pet - Pet Adoption System</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <div class="min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Edit Pet
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

                <form class="space-y-6" action="edit-pet.php" method="POST">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">
                    <div>
                        <label for="name" class="block text-sm font// filepath: /Users/tamarajoniec/Sites/pet-adoption-system/admin/edit-pet.php
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

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch pet data
    try {
        $stmt = $conn->prepare("SELECT * FROM pets WHERE id = ?");
        $stmt->execute([$id]);
        $pet = $stmt->fetch();

        if ($pet) {
            $name = $pet['name'];
            $breed = $pet['breed'];
            $status = $pet['status'];
        } else {
            $errors['database'] = 'Pet not found.';
        }
    } catch (PDOException $e) {
        $errors['database'] = 'Error fetching pet data.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
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

    // If no validation errors, update pet
    if (empty($errors)) {
        try {
            $stmt = $conn->prepare("UPDATE pets SET name = ?, breed = ?, status = ? WHERE id = ?");
            $stmt->execute([$name, $breed, $status, $id]);

            // Redirect to manage pets page
            header("Location: manage-pets.php");
            exit();
        } catch (PDOException $e) {
            $errors['database'] = 'Error updating pet. Please try again.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang=" en">

                            <head>
                                <meta charset="UTF-8">
                                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                                <title>Edit Pet - Pet Adoption System</title>
                                <script src="https://cdn.tailwindcss.com"></script>
                            </head>

                            <body class="bg-gray-100">
                                <div class="min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8">
                                    <div class="sm:mx-auto sm:w-full sm:max-w-md">
                                        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                                            Edit Pet
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

                                            <form class="space-y-6" action="edit-pet.php" method="POST">
                                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">
                                                <div>
                                                    <label for="name" class="block text-sm font