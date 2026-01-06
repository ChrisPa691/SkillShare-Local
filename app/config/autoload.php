<?php
/**
 * Autoloader for Models and Classes
 * 
 * Automatically loads class files when they are referenced.
 * Eliminates the need for manual require_once statements.
 * 
 * @package SkillShareLocal
 * @version 1.0.0
 */

/**
 * Autoload function for models and classes
 * 
 * @param string $className Name of the class to load
 * @return void
 */
spl_autoload_register(function ($className) {
    // Define base paths for different types of classes
    $basePaths = [
        __DIR__ . '/../models/',      // Model classes
        __DIR__ . '/../controllers/', // Controller classes
        __DIR__ . '/../includes/',    // Utility classes (Validator, Utils, etc.)
    ];
    
    // Try to load from each base path
    foreach ($basePaths as $basePath) {
        $file = $basePath . $className . '.php';
        
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
    
    // Log if class not found (for debugging)
    error_log("Autoloader: Could not find class file for '{$className}'");
});

/**
 * Alternative: Class map for faster loading (optional)
 * Uncomment and populate if you want explicit class mapping
 */
/*
$classMap = [
    'User' => __DIR__ . '/../models/User.php',
    'Session' => __DIR__ . '/../models/Session.php',
    'Booking' => __DIR__ . '/../models/Booking.php',
    'Rating' => __DIR__ . '/../models/Rating.php',
    'Impact' => __DIR__ . '/../models/Impact.php',
    'ImpactFactor' => __DIR__ . '/../models/ImpactFactor.php',
    'Settings' => __DIR__ . '/../models/Settings.php',
    'Validator' => __DIR__ . '/../includes/Validator.php',
    'Utils' => __DIR__ . '/../includes/Utils.php',
    'AuthController' => __DIR__ . '/../controllers/AuthController.php',
    'SessionController' => __DIR__ . '/../controllers/SessionController.php',
    'BookingController' => __DIR__ . '/../controllers/BookingController.php',
    'RatingController' => __DIR__ . '/../controllers/RatingController.php',
];

spl_autoload_register(function ($className) use ($classMap) {
    if (isset($classMap[$className])) {
        require_once $classMap[$className];
    }
});
*/
