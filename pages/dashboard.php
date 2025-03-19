<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: /pages/login.php");
    exit();
}

// Get user information
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Get user's adoption applications
$stmt = $conn->prepare("
    SELECT a.*, p.name as pet_name, p.species, p.image_url 
    FROM adoption_applications a 
    JOIN pets p ON a.pet_id = p.id 
    WHERE a.user_id = ? 
    ORDER BY a.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$applications = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once '../includes/header.php';
?>

<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Welcome, <?php echo htmlspecialchars($user['username']); ?>!</h1>
        
        <!-- User Information -->
        <div class="bg-gray-50 rounded-lg p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Your Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-gray-600">Username:</p>
                    <p class="font-medium"><?php echo htmlspecialchars($user['username']); ?></p>
                </div>
                <div>
                    <p class="text-gray-600">Email:</p>
                    <p class="font-medium"><?php echo htmlspecialchars($user['email']); ?></p>
                </div>
                <div>
                    <p class="text-gray-600">Member Since:</p>
                    <p class="font-medium"><?php echo date('F j, Y', strtotime($user['created_at'])); ?></p>
                </div>
            </div>
        </div>

        <!-- Adoption Applications -->
        <div class="bg-gray-50 rounded-lg p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Your Adoption Applications</h2>
            
            <?php if (empty($applications)): ?>
                <p class="text-gray-600">You haven't submitted any adoption applications yet.</p>
                <a href="/pages/available-pets.php" class="inline-block mt-4 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Browse Available Pets
                </a>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($applications as $application): ?>
                        <div class="bg-white rounded-lg shadow-md overflow-hidden">
                            <img src="<?php echo htmlspecialchars($application['image_url']); ?>" 
                                 alt="<?php echo htmlspecialchars($application['pet_name']); ?>" 
                                 class="w-full h-48 object-cover">
                            <div class="p-4">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                    <?php echo htmlspecialchars($application['pet_name']); ?>
                                </h3>
                                <p class="text-gray-600 mb-2">
                                    Species: <?php echo htmlspecialchars($application['species']); ?>
                                </p>
                                <p class="text-gray-600 mb-2">
                                    Status: 
                                    <span class="inline-block px-2 py-1 rounded-full text-sm 
                                        <?php echo $application['status'] === 'Pending' ? 'bg-yellow-100 text-yellow-800' : 
                                            ($application['status'] === 'Approved' ? 'bg-green-100 text-green-800' : 
                                            'bg-red-100 text-red-800'); ?>">
                                        <?php echo htmlspecialchars($application['status']); ?>
                                    </span>
                                </p>
                                <p class="text-gray-600 text-sm">
                                    Applied on: <?php echo date('F j, Y', strtotime($application['created_at'])); ?>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?> 