<?php
session_start();
require_once 'config/database.php';

// Fetch featured pets
$stmt = $conn->prepare("SELECT * FROM pets WHERE status = 'Available' ORDER BY created_at DESC LIMIT 6");
$stmt->execute();
$featured_pets = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'includes/header.php';
?>

<div class="text-center mb-12">
    <h1 class="text-4xl font-bold text-gray-800 mb-4">Find Your Perfect Companion</h1>
    <p class="text-xl text-gray-600">Give a loving home to our adorable pets</p>
</div>

<!-- Hero Section -->
<div class="bg-blue-600 text-white rounded-lg shadow-xl p-8 mb-12">
    <div class="max-w-3xl mx-auto text-center">
        <h2 class="text-3xl font-bold mb-4">Make a Difference</h2>
        <p class="text-xl mb-6">Every pet deserves a loving home. Browse our available pets and find your perfect match.</p>
        <a href="available-pets.php" class="inline-block bg-white text-blue-600 px-6 py-3 rounded-lg font-semibold hover:bg-blue-100 transition duration-300">
            View Available Pets
        </a>
    </div>
</div>

<!-- Featured Pets -->
<h2 class="text-3xl font-bold text-gray-800 mb-6">Featured Pets</h2>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php foreach($featured_pets as $pet): ?>
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <img src="<?php echo htmlspecialchars($pet['image_url']); ?>" alt="<?php echo htmlspecialchars($pet['name']); ?>" class="w-full h-48 object-cover">
        <div class="p-4">
            <h3 class="text-xl font-semibold text-gray-800 mb-2"><?php echo htmlspecialchars($pet['name']); ?></h3>
            <p class="text-gray-600 mb-2"><?php echo htmlspecialchars($pet['breed']); ?></p>
            <p class="text-gray-600 mb-4"><?php echo htmlspecialchars($pet['age']); ?> years old</p>
            <a href="pet-details.php?id=<?php echo $pet['id']; ?>" class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition duration-300">
                Learn More
            </a>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Call to Action -->
<div class="bg-gray-100 rounded-lg p-8 mt-12 text-center">
    <h2 class="text-3xl font-bold text-gray-800 mb-4">Ready to Adopt?</h2>
    <p class="text-xl text-gray-600 mb-6">Start your journey to pet parenthood today!</p>
    <a href="register.php" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition duration-300">
        Get Started
    </a>
</div>

<?php include 'includes/footer.php'; ?> 