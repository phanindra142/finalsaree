<?php
header("Content-Type: application/json"); // Ensure the response is in JSON format
include 'db.php'; // Include the database connection file

// Get the requested route
$route = $_GET['route'] ?? '';

// Utility function to send a JSON response
function sendResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}

// Routes
switch ($route) {
    case 'register':
        // User Registration
        $data = json_decode(file_get_contents('php://input'), true);
        $email = $data['email'];
        $password = password_hash($data['password'], PASSWORD_BCRYPT);

        $query = "INSERT INTO Users (email, password) VALUES (:email, :password)";
        $stmt = $conn->prepare($query);

        try {
            $stmt->execute(['email' => $email, 'password' => $password]);
            sendResponse(['message' => 'User registered successfully']);
        } catch (Exception $e) {
            sendResponse(['message' => 'Error registering user', 'error' => $e->getMessage()], 400);
        }
        break;

    case 'login':
        // User Login
        $data = json_decode(file_get_contents('php://input'), true);
        $email = $data['email'];
        $password = $data['password'];

        $query = "SELECT * FROM Users WHERE email = :email";
        $stmt = $conn->prepare($query);
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $token = base64_encode(json_encode(['email' => $user['email'], 'role' => 'user']));
            sendResponse(['message' => 'Login successful', 'token' => $token]);
        } else {
            sendResponse(['message' => 'Invalid credentials'], 401);
        }
        break;

    case 'admin_login':
        // Admin Login
        $data = json_decode(file_get_contents('php://input'), true);
        $email = $data['email'];
        $password = $data['password'];

        if ($email === 'admin@sareeboutique.com' && $password === 'admin123') {
            $token = base64_encode(json_encode(['email' => $email, 'role' => 'admin']));
            sendResponse(['message' => 'Admin login successful', 'token' => $token]);
        } else {
            sendResponse(['message' => 'Invalid credentials'], 401);
        }
        break;

    case 'sarees':
        // Fetch All Sarees
        $query = "SELECT * FROM Sarees";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $sarees = $stmt->fetchAll(PDO::FETCH_ASSOC);
        sendResponse($sarees);
        break;

    case 'search_sarees':
        // Search Sarees
        $searchQuery = $_GET['query'] ?? '';
        $query = "SELECT * FROM Sarees WHERE name LIKE :searchQuery";
        $stmt = $conn->prepare($query);
        $stmt->execute(['searchQuery' => '%' . $searchQuery . '%']);
        $sarees = $stmt->fetchAll(PDO::FETCH_ASSOC);
        sendResponse($sarees);
        break;

    case 'add_saree':
        // Add New Saree (Admin Only)
        $data = json_decode(file_get_contents('php://input'), true);
        $name = $data['name'];
        $description = $data['description'];
        $price = $data['price'];
        $image = $data['image'];

        $query = "INSERT INTO Sarees (name, description, price, image) VALUES (:name, :description, :price, :image)";
        $stmt = $conn->prepare($query);

        try {
            $stmt->execute(['name' => $name, 'description' => $description, 'price' => $price, 'image' => $image]);
            sendResponse(['message' => 'Saree added successfully']);
        } catch (Exception $e) {
            sendResponse(['message' => 'Error adding saree', 'error' => $e->getMessage()], 400);
        }
        break;

    default:
        sendResponse(['message' => 'Invalid route'], 404);
        break;
}
?>