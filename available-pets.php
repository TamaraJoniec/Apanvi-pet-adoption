<?php
session_start();
require_once 'config/database.php';

// Handle filters
$species = isset($_GET['species']) ? $_GET['species'] : '';
$gender = isset($_GET['gender']) ? $_GET['gender'] : '';
$age_range = isset($_GET['age_range']) ? $_GET['age_range'] : '';

// Build query
$query = "SELECT * FROM pets WHERE status = 'Available'";
$params = [];

if ($species) {
    $query .= " AND species = ?";
    $params[] = $species;
}

if ($gender) {
    $query .= " AND gender = ?";
    $params[] = $gender;
}

if ($age_range) {
    switch ($age_range) {
        case 'young':
            $query .= " AND age < 2";
            break;
        case 'adult':
            $query .= " AND age >= 2 AND age < 8";
            break;
        case 'senior':
            $query .= " AND age >= 8";
            break;
    }
}

$query .= " ORDER BY created_at DESC";

$stmt = $conn->prepare($query);
$stmt->execute($params);
$pets = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'includes/header.php';
?>

<div class="container mx-auto px-4 py-8">
    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-2xl font-bold mb-4">Filter Pets</h2>
        <form method="GET" action="" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="species">
                    Species
                </label>
                <select class="shadow border rounded w-full py-2 px-3 text-gray-700" id="species" name="species">
                    <option value="">All Species</option>
                    <option value="Dog" <?php echo $species === 'Dog' ? 'selected' : ''; ?>>Dogs</option>
                    <option value="Cat" <?php echo $species === 'Cat' ? 'selected' : ''; ?>>Cats</option>
                    <option value="Bird" <?php echo $species === 'Bird' ? 'selected' : ''; ?>>Birds</option>
                    <option value="Other" <?php echo $species === 'Other' ? 'selected' : ''; ?>>Other</option>
                </select>
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="gender">
                    Gender
                </label>
                <select class="shadow border rounded w-full py-2 px-3 text-gray-700" id="gender" name="gender">
                    <option value="">All Genders</option>
                    <option value="Male" <?php echo $gender === 'Male' ? 'selected' : ''; ?>>Male</option>
                    <option value="Female" <?php echo $gender === 'Female' ? 'selected' : ''; ?>>Female</option>
                </select>
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="age_range">
                    Age Range
                </label>
                <select class="shadow border rounded w-full py-2 px-3 text-gray-700" id="age_range" name="age_range">
                    <option value="">All Ages</option>
                    <option value="young" <?php echo $age_range === 'young' ? 'selected' : ''; ?>>Young (< 2 years)</option>
                    <option value="adult" <?php echo $age_range === 'adult' ? 'selected' : ''; ?>>Adult (2-8 years)</option>
                    <option value="senior" <?php echo $age_range === 'senior' ? 'selected' : ''; ?>>Senior (8+ years)</option>
                </select>
            </div>

            <div class="md:col-span-3 flex justify-end">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Apply Filters
                </button>
                <a href="available-pets.php" class="ml-4 text-blue-600 hover:text-blue-800 py-2">
                    Clear Filters
                </a>
            </div>
        </form>
    </div>

    <!-- Pet Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach($pets as $pet): ?>
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <img src="<?php echo htmlspecialchars($pet['image_url']); ?>" 
                 alt="<?php echo htmlspecialchars($pet['name']); ?>" 
                 class="w-full h-64 object-cover">
            <div class="p-6">
                <h3 class="text-2xl font-bold text-gray-800 mb-2">
                    <?php echo htmlspecialchars($pet['name']); ?>
                </h3>
                <div class="flex items-center mb-4">
                    <span class="bg-blue-100 text-blue-800 text-sm font-semibold px-3 py-1 rounded-full mr-2">
                        <?php echo htmlspecialchars($pet['species']); ?>
                    </span>
                    <span class="bg-gray-100 text-gray-800 text-sm font-semibold px-3 py-1 rounded-full">
                        <?php echo htmlspecialchars($pet['gender']); ?>
                    </span>
                </div>
                <p class="text-gray-600 mb-4">
                    <?php echo htmlspecialchars(substr($pet['description'], 0, 150)) . '...'; ?>
                </p>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Age: <?php echo htmlspecialchars($pet['age']); ?> years</span>
                    <a href="pet-details.php?id=<?php echo $pet['id']; ?>" 
                       class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        View Details
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <?php if (empty($pets)): ?>
    <div class="text-center py-8">
        <h3 class="text-xl text-gray-600">No pets found matching your criteria.</h3>
        <p class="mt-2">Try adjusting your filters or check back later for new additions.</p>
    </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?> 