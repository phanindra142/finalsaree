<?php
$host = 'localhost'; // Change this to your database host if not localhost
$dbname = 'saree_boutique'; // Database name
$username = 'root'; // Database username (default for local servers)
$password = ''; // Database password (empty for local servers like XAMPP)

try {
    // Create a new PDO instance
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Set error mode to exceptions
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>