<?php
session_start();
require_once '../config/database.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $species = $_POST['species'];
    $breed = $_POST['breed'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $description = $_POST['description'];
    $health_status = $_POST['health_status'];

    // Handle file upload
    $target_dir = "../assets/images/pets/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $image_url = '';
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
        $file_extension = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
        $new_filename = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_url = "assets/images/pets/" . $new_filename;
        }
    }

    try {
        $stmt = $conn->prepare("INSERT INTO pets (name, species, breed, age, gender, description, health_status, image_url, status) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Available')");
        $stmt->execute([$name, $species, $breed, $age, $gender, $description, $health_status, $image_url]);
        
        $_SESSION['message'] = "Pet added successfully!";
        header('Location: dashboard.php');
        exit();
    } catch(PDOException $e) {
        $error = "Error adding pet: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Pet - Pet Adoption System</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <nav class="bg-blue-600 text-white shadow-lg mb-8">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="text-2xl font-bold">Add New Pet</div>
                <a href="dashboard.php" class="hover:text-blue-200">Back to Dashboard</a>
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-4">
        <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md p-8">
            <?php if (isset($error)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="" enctype="multipart/form-data" class="space-y-6">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                        Pet Name
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        id="name" type="text" name="name" required>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="species">
                            Species
                        </label>
                        <select class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            id="species" name="species" required>
                            <option value="">Select Species</option>
                            <option value="Dog">Dog</option>
                            <option value="Cat">Cat</option>
                            <option value="Bird">Bird</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="breed">
                            Breed
                        </label>
                        <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            id="breed" type="text" name="breed">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="age">
                            Age (years)
                        </label>
                        <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            id="age" type="number" name="age" min="0" step="0.1" required>
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="gender">
                            Gender
                        </label>
                        <select class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            id="gender" name="gender" required>
                            <option value="">Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Unknown">Unknown</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="description">
                        Description
                    </label>
                    <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        id="description" name="description" rows="4" required></textarea>
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="health_status">
                        Health Status
                    </label>
                    <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        id="health_status" name="health_status" rows="3" required></textarea>
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="image">
                        Pet Image
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        id="image" type="file" name="image" accept="image/*" required>
                </div>

                <div class="flex items-center justify-between">
                    <button class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                        type="submit">
                        Add Pet
                    </button>
                    <a href="dashboard.php" class="text-blue-600 hover:text-blue-800">Cancel</a>
                </div>
            </form>
        </div>
    </main>
</body>
</html> 