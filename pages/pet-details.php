<?php
session_start();
require_once 'config/database.php';

if (!isset($_GET['id'])) {
    header('Location: available-pets.php');
    exit();
}

$pet_id = $_GET['id'];

// Fetch pet details
$stmt = $conn->prepare("SELECT * FROM pets WHERE id = ?");
$stmt->execute([$pet_id]);
$pet = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$pet) {
    header('Location: available-pets.php');
    exit();
}

// Handle adoption application submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $applicant_name = $_POST['applicant_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

    try {
        $stmt = $conn->prepare("INSERT INTO adoption_applications (pet_id, user_id, applicant_name, email, phone, address) 
                               VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$pet_id, $user_id, $applicant_name, $email, $phone, $address]);

        // Update pet status to pending
        $stmt = $conn->prepare("UPDATE pets SET status = 'Pending' WHERE id = ?");
        $stmt->execute([$pet_id]);

        $_SESSION['message'] = "Your adoption application has been submitted successfully!";
        header('Location: available-pets.php');
        exit();
    } catch(PDOException $e) {
        $error = "Error submitting application: " . $e->getMessage();
    }
}

include 'includes/header.php';
?>

<div class="container mx-auto px-4 py-8">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Pet Details -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <img src="<?php echo htmlspecialchars($pet['image_url']); ?>" 
                 alt="<?php echo htmlspecialchars($pet['name']); ?>" 
                 class="w-full h-96 object-cover">
            <div class="p-6">
                <h1 class="text-3xl font-bold text-gray-800 mb-4">
                    <?php echo htmlspecialchars($pet['name']); ?>
                </h1>
                <div class="flex items-center mb-4 space-x-4">
                    <span class="bg-blue-100 text-blue-800 text-sm font-semibold px-3 py-1 rounded-full">
                        <?php echo htmlspecialchars($pet['species']); ?>
                    </span>
                    <span class="bg-gray-100 text-gray-800 text-sm font-semibold px-3 py-1 rounded-full">
                        <?php echo htmlspecialchars($pet['gender']); ?>
                    </span>
                    <span class="bg-green-100 text-green-800 text-sm font-semibold px-3 py-1 rounded-full">
                        <?php echo htmlspecialchars($pet['status']); ?>
                    </span>
                </div>
                <div class="space-y-4">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800 mb-2">About</h2>
                        <p class="text-gray-600"><?php echo nl2br(htmlspecialchars($pet['description'])); ?></p>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800 mb-2">Details</h2>
                        <ul class="space-y-2 text-gray-600">
                            <li><strong>Breed:</strong> <?php echo htmlspecialchars($pet['breed']); ?></li>
                            <li><strong>Age:</strong> <?php echo htmlspecialchars($pet['age']); ?> years</li>
                            <li><strong>Health Status:</strong> <?php echo nl2br(htmlspecialchars($pet['health_status'])); ?></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Adoption Application Form -->
        <?php if ($pet['status'] === 'Available'): ?>
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Adoption Application</h2>
            
            <?php if (isset($error)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="" class="space-y-6">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="applicant_name">
                        Full Name
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        id="applicant_name" type="text" name="applicant_name" required>
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                        Email Address
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        id="email" type="email" name="email" required>
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="phone">
                        Phone Number
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        id="phone" type="tel" name="phone" required>
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="address">
                        Home Address
                    </label>
                    <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        id="address" name="address" rows="3" required></textarea>
                </div>

                <div class="flex items-center justify-between">
                    <button class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                        type="submit">
                        Submit Application
                    </button>
                </div>
            </form>
        </div>
        <?php else: ?>
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
            <h2 class="text-xl font-semibold text-yellow-800 mb-2">Pet Not Available</h2>
            <p class="text-yellow-700">We're sorry, but this pet is currently not available for adoption. Please check our other available pets.</p>
            <a href="available-pets.php" class="inline-block mt-4 text-blue-600 hover:text-blue-800">
                View Available Pets
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 