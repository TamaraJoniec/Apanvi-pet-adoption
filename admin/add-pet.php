<?php
session_start();

// Check if user is logged in
if(!isset($_SESSION["admin_loggedin"]) || $_SESSION["admin_loggedin"] !== true){
    header("location: login.php");
    exit;
}

require_once '../config/database.php';
require_once '../config/upload_config.php';

$name = $species = $breed = $age = $gender = $size = $description = "";
$name_err = $species_err = $gender_err = $size_err = $image_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validate name
    if(empty(trim($_POST["name"]))){
        $name_err = "Please enter the pet's name.";
    } else {
        $name = trim($_POST["name"]);
    }
    
    // Validate species
    if(empty(trim($_POST["species"]))){
        $species_err = "Please enter the species.";
    } else {
        $species = trim($_POST["species"]);
    }
    
    // Validate gender
    if(empty($_POST["gender"])){
        $gender_err = "Please select a gender.";
    } else {
        $gender = $_POST["gender"];
    }
    
    // Validate size
    if(empty($_POST["size"])){
        $size_err = "Please select a size.";
    } else {
        $size = $_POST["size"];
    }
    
    // Optional fields
    $breed = !empty($_POST["breed"]) ? trim($_POST["breed"]) : null;
    $age = !empty($_POST["age"]) ? (int)$_POST["age"] : null;
    $description = !empty($_POST["description"]) ? trim($_POST["description"]) : null;
    
    // Handle image upload
    $image_filename = null;
    if(isset($_FILES["image"]) && $_FILES["image"]["error"] !== UPLOAD_ERR_NO_FILE) {
        $upload_result = handleFileUpload($_FILES["image"]);
        if($upload_result["success"]) {
            $image_filename = $upload_result["filename"];
        } else {
            $image_err = $upload_result["message"];
        }
    }
    
    // Check input errors before inserting in database
    if(empty($name_err) && empty($species_err) && empty($gender_err) && empty($size_err) && empty($image_err)){
        $sql = "INSERT INTO pets (name, species, breed, age, gender, size, description, image_filename) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        if($stmt = $conn->prepare($sql)){
            $stmt->execute([$name, $species, $breed, $age, $gender, $size, $description, $image_filename]);
            $_SESSION['success_msg'] = "Pet added successfully.";
            header("location: dashboard.php");
            exit();
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
                            <a href="add-pet.php" class="inline-flex items-center px-1 pt-1 border-b-2 border-blue-500 text-gray-900">Add Pet</a>
                            <a href="manage-users.php" class="inline-flex items-center px-1 pt-1 text-gray-600 hover:text-gray-900">Manage Users</a>
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
            <?php if(isset($_SESSION['error_msg'])): ?>
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                    <?php 
                    echo $_SESSION['error_msg'];
                    unset($_SESSION['error_msg']);
                    ?>
                </div>
            <?php endif; ?>

            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <div class="md:grid md:grid-cols-3 md:gap-6">
                        <div class="md:col-span-1">
                            <h3 class="text-lg font-medium leading-6 text-gray-900">Add New Pet</h3>
                            <p class="mt-1 text-sm text-gray-600">
                                Enter the details of the new pet available for adoption.
                            </p>
                        </div>
                        <div class="mt-5 md:mt-0 md:col-span-2">
                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
                                <div class="grid grid-cols-6 gap-6">
                                    <div class="col-span-6 sm:col-span-4">
                                        <label for="name" class="block text-sm font-medium text-gray-700">Pet Name</label>
                                        <input type="text" name="name" id="name" 
                                               class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md <?php echo (!empty($name_err)) ? 'border-red-500' : ''; ?>" 
                                               value="<?php echo $name; ?>">
                                        <?php if(!empty($name_err)): ?>
                                            <p class="mt-2 text-sm text-red-600"><?php echo $name_err; ?></p>
                                        <?php endif; ?>
                                    </div>

                                    <div class="col-span-6 sm:col-span-3">
                                        <label for="species" class="block text-sm font-medium text-gray-700">Species</label>
                                        <input type="text" name="species" id="species" 
                                               class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md <?php echo (!empty($species_err)) ? 'border-red-500' : ''; ?>" 
                                               value="<?php echo $species; ?>">
                                        <?php if(!empty($species_err)): ?>
                                            <p class="mt-2 text-sm text-red-600"><?php echo $species_err; ?></p>
                                        <?php endif; ?>
                                    </div>

                                    <div class="col-span-6 sm:col-span-3">
                                        <label for="breed" class="block text-sm font-medium text-gray-700">Breed (optional)</label>
                                        <input type="text" name="breed" id="breed" 
                                               class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" 
                                               value="<?php echo $breed; ?>">
                                    </div>

                                    <div class="col-span-6 sm:col-span-2">
                                        <label for="age" class="block text-sm font-medium text-gray-700">Age (optional)</label>
                                        <input type="number" name="age" id="age" min="0" 
                                               class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" 
                                               value="<?php echo $age; ?>">
                                    </div>

                                    <div class="col-span-6 sm:col-span-2">
                                        <label for="gender" class="block text-sm font-medium text-gray-700">Gender</label>
                                        <select name="gender" id="gender" 
                                                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm <?php echo (!empty($gender_err)) ? 'border-red-500' : ''; ?>">
                                            <option value="">Select gender</option>
                                            <option value="Male" <?php echo ($gender === "Male") ? "selected" : ""; ?>>Male</option>
                                            <option value="Female" <?php echo ($gender === "Female") ? "selected" : ""; ?>>Female</option>
                                        </select>
                                        <?php if(!empty($gender_err)): ?>
                                            <p class="mt-2 text-sm text-red-600"><?php echo $gender_err; ?></p>
                                        <?php endif; ?>
                                    </div>

                                    <div class="col-span-6 sm:col-span-2">
                                        <label for="size" class="block text-sm font-medium text-gray-700">Size</label>
                                        <select name="size" id="size" 
                                                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm <?php echo (!empty($size_err)) ? 'border-red-500' : ''; ?>">
                                            <option value="">Select size</option>
                                            <option value="Small" <?php echo ($size === "Small") ? "selected" : ""; ?>>Small</option>
                                            <option value="Medium" <?php echo ($size === "Medium") ? "selected" : ""; ?>>Medium</option>
                                            <option value="Large" <?php echo ($size === "Large") ? "selected" : ""; ?>>Large</option>
                                        </select>
                                        <?php if(!empty($size_err)): ?>
                                            <p class="mt-2 text-sm text-red-600"><?php echo $size_err; ?></p>
                                        <?php endif; ?>
                                    </div>

                                    <div class="col-span-6">
                                        <label for="description" class="block text-sm font-medium text-gray-700">Description (optional)</label>
                                        <textarea name="description" id="description" rows="3" 
                                                  class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"><?php echo $description; ?></textarea>
                                    </div>

                                    <div class="col-span-6">
                                        <label class="block text-sm font-medium text-gray-700">Pet Photo</label>
                                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                            <div class="space-y-1 text-center">
                                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                                <div class="flex text-sm text-gray-600">
                                                    <label for="image" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                                        <span>Upload a file</span>
                                                        <input id="image" name="image" type="file" class="sr-only" accept="image/jpeg,image/png,image/webp">
                                                    </label>
                                                    <p class="pl-1">or drag and drop</p>
                                                </div>
                                                <p class="text-xs text-gray-500">PNG, JPG, WEBP up to 5MB</p>
                                            </div>
                                        </div>
                                        <?php if(!empty($image_err)): ?>
                                            <p class="mt-2 text-sm text-red-600"><?php echo $image_err; ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="mt-6 flex items-center justify-end space-x-4">
                                    <a href="dashboard.php" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        Cancel
                                    </a>
                                    <button type="submit" class="bg-blue-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        Add Pet
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Preview image before upload
    document.getElementById('image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            if (file.size > <?php echo MAX_FILE_SIZE; ?>) {
                alert('File is too large. Maximum size is 5MB.');
                this.value = '';
                return;
            }
            
            const allowedTypes = <?php echo json_encode(ALLOWED_MIME_TYPES); ?>;
            if (!allowedTypes.includes(file.type)) {
                alert('Invalid file type. Allowed types: JPG, PNG, WEBP');
                this.value = '';
                return;
            }
        }
    });
    </script>
</body>
</html> 