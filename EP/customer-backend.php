/*
* File: api/customers.php
*/
<?php
// Database Connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "saree_boutique";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle API Requests
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // Fetch all customers
    $sql = "SELECT * FROM Customers";
    $result = $conn->query($sql);
    $customers = [];
    while ($row = $result->fetch_assoc()) {
        $customers[] = $row;
    }
    echo json_encode($customers);
} elseif ($method === 'POST') {
    // Add or update customer details
    $data = json_decode(file_get_contents('php://input'), true);
    $name = $data['name'];
    $email = $data['email'];
    $message = $data['message'];

    $sql = "INSERT INTO Customers (name, email, message) VALUES ('$name', '$email', '$message')";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(["message" => "Customer added successfully!", "id" => $conn->insert_id]);
    } else {
        echo json_encode(["error" => "Failed to add customer: " . $conn->error]);
    }
}

$conn->close();
?>
