<?php
require_once '../includes/header.php';
?>

<div class="bg-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="px-6 py-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-6">About Our Pet Adoption System</h1>
                
                <div class="prose max-w-none">
                    <p class="text-lg text-gray-700 mb-6">
                        Welcome to our Pet Adoption System, where we connect loving homes with pets in need. 
                        Our mission is to make the pet adoption process simple, transparent, and rewarding for both 
                        the adopters and our furry friends.
                    </p>

                    <h2 class="text-2xl font-semibold text-gray-900 mt-8 mb-4">Our Mission</h2>
                    <p class="text-gray-700 mb-6">
                        We believe every pet deserves a loving home. Our platform serves as a bridge between 
                        shelters, rescue organizations, and potential pet parents, making it easier than ever 
                        to find your perfect companion.
                    </p>

                    <h2 class="text-2xl font-semibold text-gray-900 mt-8 mb-4">How It Works</h2>
                    <div class="grid md:grid-cols-3 gap-6 mb-8">
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h3 class="text-xl font-semibold text-gray-900 mb-3">1. Browse</h3>
                            <p class="text-gray-700">
                                Explore our database of available pets. Filter by species, size, and other 
                                characteristics to find your perfect match.
                            </p>
                        </div>
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h3 class="text-xl font-semibold text-gray-900 mb-3">2. Apply</h3>
                            <p class="text-gray-700">
                                Found a pet you love? Submit an adoption application through our simple online form.
                            </p>
                        </div>
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h3 class="text-xl font-semibold text-gray-900 mb-3">3. Adopt</h3>
                            <p class="text-gray-700">
                                Once approved, complete the adoption process and welcome your new family member home!
                            </p>
                        </div>
                    </div>

                    <h2 class="text-2xl font-semibold text-gray-900 mt-8 mb-4">Why Choose Us?</h2>
                    <ul class="list-disc pl-6 text-gray-700 mb-6">
                        <li class="mb-2">Comprehensive pet profiles with detailed information and photos</li>
                        <li class="mb-2">Simple and transparent adoption process</li>
                        <li class="mb-2">Support throughout your adoption journey</li>
                        <li class="mb-2">Partnership with reputable shelters and rescue organizations</li>
                        <li>Post-adoption resources and guidance</li>
                    </ul>

                    <div class="mt-8 bg-blue-50 p-6 rounded-lg">
                        <h2 class="text-2xl font-semibold text-blue-900 mb-4">Ready to Find Your Perfect Pet?</h2>
                        <p class="text-blue-800 mb-4">
                            Start your journey today by browsing our available pets or registering for an account.
                        </p>
                        <div class="flex gap-4">
                            <a href="/pages/available-pets.php" class="inline-flex items-center px-4 py-2 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700">
                                Browse Pets
                            </a>
                            <a href="/pages/register.php" class="inline-flex items-center px-4 py-2 border border-blue-600 text-base font-medium rounded-md text-blue-600 bg-white hover:bg-blue-50">
                                Register Now
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once '../includes/footer.php';
?> 