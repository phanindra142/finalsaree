<?php
// Database Connection
$servername = "localhost";
$username = "root"; // default username for XAMPP/WAMP
$password = ""; // default password for XAMPP/WAMP
$dbname = "saree_boutique";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create tables if they do not exist (for first-time setup)
$sql_create_tables = "
CREATE TABLE IF NOT EXISTS products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    image_url VARCHAR(255),
    stock_quantity INT NOT NULL
);

CREATE TABLE IF NOT EXISTS cart (
    cart_id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    total_price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (product_id) REFERENCES products(product_id)
);

-- Insert some sample data into products table if empty
INSERT INTO products (name, description, price, image_url, stock_quantity)
SELECT 'Red Saree', 'A beautiful red saree with golden embroidery.', 1500.00, 'images/red_saree.jpg', 20
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Red Saree');

INSERT INTO products (name, description, price, image_url, stock_quantity)
SELECT 'Blue Saree', 'A luxurious blue saree with silver work.', 2000.00, 'images/blue_saree.jpg', 15
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Blue Saree');
";

// Execute the table creation and sample data insertion
$conn->multi_query($sql_create_tables);

// Function to Add Item to Cart
function add_to_cart($product_id, $quantity) {
    global $conn;
    
    // Get product details
    $sql = "SELECT * FROM products WHERE product_id = $product_id";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        $total_price = $product['price'] * $quantity;

        // Check if product is already in the cart
        $sql_check = "SELECT * FROM cart WHERE product_id = $product_id";
        $result_check = $conn->query($sql_check);
        
        if ($result_check->num_rows > 0) {
            // Update quantity and total price if product is already in the cart
            $sql_update = "UPDATE cart SET quantity = quantity + $quantity, total_price = total_price + $total_price WHERE product_id = $product_id";
            $conn->query($sql_update);
        } else {
            // Add new product to the cart
            $sql_insert = "INSERT INTO cart (product_id, quantity, total_price) VALUES ($product_id, $quantity, $total_price)";
            $conn->query($sql_insert);
        }
    }
}

// Function to Get Cart Items
function get_cart_items() {
    global $conn;
    $sql = "SELECT c.cart_id, p.name, c.quantity, c.total_price
            FROM cart c
            JOIN products p ON c.product_id = p.product_id";
    $result = $conn->query($sql);
    return $result;
}

// Function to Get Cart Total
function get_cart_total() {
    global $conn;
    $sql = "SELECT SUM(total_price) AS cart_total FROM cart";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['cart_total'];
}

// Add to Cart if "add" action is triggered
if (isset($_GET['action']) && $_GET['action'] == 'add') {
    $product_id = $_GET['id'];
    $quantity = $_GET['quantity'];
    add_to_cart($product_id, $quantity);
}

// Get Cart Items
$cart_items = get_cart_items();
$cart_total = get_cart_total();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saree Boutique</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }

        header {
            background-color: #2c3e50;
            color: white;
            padding: 10px 0;
            text-align: center;
        }

        h2 {
            font-size: 28px;
            color: #2c3e50;
        }

        .product-list, .cart {
            padding: 20px;
            max-width: 1000px;
            margin: 20px auto;
        }

        .product {
            border: 1px solid #ccc;
            padding: 15px;
            margin: 15px 0;
            text-align: center;
            background-color: #fff;
        }

        .cart-item {
            padding: 10px;
            background-color: #fff;
            margin: 10px 0;
        }

        .cart-total {
            font-size: 18px;
            font-weight: bold;
        }

        .cart-actions a {
            background-color: #2c3e50;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-right: 10px;
        }

        .cart-actions a:hover {
            background-color: #34495e;
        }
    </style>
</head>
<body>

<header>
    <div class="logo">Saree Boutique</div>
</header>

<?php if (!isset($_GET['action'])): ?>
    <!-- Display Product List -->
    <section class="product-list">
        <h2>Our Collection</h2>
        <?php
        $sql = "SELECT * FROM products";
        $result = $conn->query($sql);
        
        while($row = $result->fetch_assoc()) {
            echo "<div class='product'>
                    <img src='".$row['image_url']."' alt='".$row['name']."' width='200px'>
                    <h3>".$row['name']."</h3>
                    <p>".$row['description']."</p>
                    <p>₹".$row['price']."</p>
                    <a href='?action=add&id=".$row['product_id']."&quantity=1'>Add to Cart</a>
                  </div>";
        }
        ?>
    </section>
<?php else: ?>
    <!-- Display Cart -->
    <section class="cart">
        <h2>Your Cart</h2>
        <?php if ($cart_items->num_rows > 0): ?>
            <?php while($row = $cart_items->fetch_assoc()): ?>
                <div class="cart-item">
                    <p><?php echo $row['name']; ?> - Quantity: <?php echo $row['quantity']; ?> - ₹<?php echo $row['total_price']; ?></p>
                </div>
            <?php endwhile; ?>
            <div class="cart-total">
                <h3>Total: ₹<?php echo $cart_total; ?></h3>
            </div>
            <div class="cart-actions">
                <a href="checkout.html" class="checkout-btn">Proceed to Checkout</a>
                <a href="index.php" class="continue-shopping-btn">Continue Shopping</a>
            </div>
        <?php else: ?>
            <p>Your cart is empty.</p>
            <a href="index.php" class="continue-shopping-btn">Go to Products</a>
        <?php endif; ?>
    </section>
<?php endif; ?>

</body>
</html>

<?php
// Close connection
$conn->close();
?>
