/*
* File: admin/dashboard.php
*/
<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: login.php');
    exit();
}

// Logout logic
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
    <h1>Welcome to the Admin Dashboard</h1>
    <a href="?logout=true">Logout</a>

    <section>
        <h2>Manage Sarees</h2>
        <form id="addSareeForm">
            <input type="text" name="name" placeholder="Saree Name" required>
            <textarea name="description" placeholder="Description" required></textarea>
            <input type="number" name="price" placeholder="Price" required>
            <input type="text" name="image_url" placeholder="Image URL" required>
            <button type="submit">Add Saree</button>
        </form>
        <div id="sareeList"></div>
    </section>

    <section>
        <h2>Customer Details</h2>
        <div id="customerList"></div>
    </section>

    <script>
        async function fetchSarees() {
            const response = await fetch('../api/sarees.php');
            const sarees = await response.json();
            document.getElementById('sareeList').innerHTML = sarees.map(saree =>
                `<p>${saree.name} - ₹${saree.price} <button onclick="deleteSaree(${saree.id})">Delete</button></p>`
            ).join('');
        }

        async function deleteSaree(id) {
            await fetch(`../api/sarees.php?id=${id}`, { method: 'DELETE' });
            fetchSarees();
        }

        document.getElementById('addSareeForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData.entries());
            await fetch('../api/sarees.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            e.target.reset();
            fetchSarees();
        });

        fetchSarees();

        async function fetchCustomers() {
            const response = await fetch('../api/customers.php');
            const customers = await response.json();
            document.getElementById('customerList').innerHTML = customers.map(customer =>
                `<p>${customer.name} - ${customer.email}: ${customer.message}</p>`
            ).join('');
        }

        fetchCustomers();
    </script>
</body>
</html>
