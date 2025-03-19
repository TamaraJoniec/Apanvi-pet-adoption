<?php
session_start();
require_once '../config/database.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch all pets
$stmt = $conn->prepare("SELECT * FROM pets ORDER BY created_at DESC");
$stmt->execute();
$pets = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch pending applications
$stmt = $conn->prepare("SELECT a.*, p.name as pet_name FROM adoption_applications a 
                       JOIN pets p ON a.pet_id = p.id 
                       WHERE a.status = 'Pending' 
                       ORDER BY a.application_date DESC");
$stmt->execute();
$pending_applications = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Pet Adoption System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <nav class="bg-blue-600 text-white shadow-lg">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="text-2xl font-bold">Admin Dashboard</div>
                <div class="space-x-4">
                    <a href="add-pet.php" class="hover:text-blue-200">Add New Pet</a>
                    <a href="logout.php" class="hover:text-blue-200">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-4 py-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Pets Management -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-2xl font-bold mb-4">Manage Pets</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="px-4 py-2">Name</th>
                                <th class="px-4 py-2">Species</th>
                                <th class="px-4 py-2">Status</th>
                                <th class="px-4 py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($pets as $pet): ?>
                            <tr class="border-b">
                                <td class="px-4 py-2"><?php echo htmlspecialchars($pet['name']); ?></td>
                                <td class="px-4 py-2"><?php echo htmlspecialchars($pet['species']); ?></td>
                                <td class="px-4 py-2">
                                    <span class="px-2 py-1 rounded text-sm 
                                        <?php echo $pet['status'] === 'Available' ? 'bg-green-100 text-green-800' : 
                                        ($pet['status'] === 'Pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800'); ?>">
                                        <?php echo htmlspecialchars($pet['status']); ?>
                                    </span>
                                </td>
                                <td class="px-4 py-2">
                                    <a href="edit-pet.php?id=<?php echo $pet['id']; ?>" class="text-blue-600 hover:text-blue-800 mr-2">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="delete-pet.php?id=<?php echo $pet['id']; ?>" class="text-red-600 hover:text-red-800" 
                                       onclick="return confirm('Are you sure you want to delete this pet?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pending Applications -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-2xl font-bold mb-4">Pending Applications</h2>
                <div class="space-y-4">
                    <?php foreach($pending_applications as $application): ?>
                    <div class="border rounded p-4">
                        <h3 class="font-semibold"><?php echo htmlspecialchars($application['applicant_name']); ?></h3>
                        <p class="text-gray-600">Pet: <?php echo htmlspecialchars($application['pet_name']); ?></p>
                        <p class="text-gray-600">Applied: <?php echo date('M d, Y', strtotime($application['application_date'])); ?></p>
                        <div class="mt-2">
                            <a href="review-application.php?id=<?php echo $application['id']; ?>" 
                               class="text-blue-600 hover:text-blue-800">Review Application</a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </main>
</body>
</html> 