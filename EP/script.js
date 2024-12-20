// Cart Data
let cart = [];


function addToCart(productName, price) {
  const existingProduct = cart.find(item => item.name === productName);

  if (existingProduct) {
    existingProduct.quantity += 1;
  } else {
    cart.push({ name: productName, price: price, quantity: 1 });
  }

  saveCartToLocalStorage();
  const viewCart = confirm(`${productName} has been added to your cart! Do you want to view your cart?`);

  if (viewCart) {
    window.location.href = 'cart.html'; // Redirect to cart page
  }
}


// Save cart to local storage
function saveCartToLocalStorage() {
  localStorage.setItem('cart', JSON.stringify(cart));
}

// Load cart from local storage
function loadCartFromLocalStorage() {
  const savedCart = localStorage.getItem('cart');
  if (savedCart) {
    cart = JSON.parse(savedCart);
  }
}

// Display cart items on the cart page
function displayCartItems() {
  loadCartFromLocalStorage();
  const cartItemsContainer = document.getElementById('cart-items');
  const cartTotalElement = document.getElementById('cart-total');
  cartItemsContainer.innerHTML = '';
  
  let total = 0;

  cart.forEach(item => {
    const itemElement = document.createElement('div');
    itemElement.className = 'cart-item';
    itemElement.innerHTML = `
      <p>${item.name} - ₹${item.price} x ${item.quantity}</p>
      <button onclick="removeFromCart('${item.name}')">Remove</button>
    `;
    cartItemsContainer.appendChild(itemElement);
    total += item.price * item.quantity;
  });

  cartTotalElement.textContent = total;
}

// Function to remove an item from the cart
function removeFromCart(productName) {
  cart = cart.filter(item => item.name !== productName);
  saveCartToLocalStorage();
  displayCartItems();
}

// Initialize the cart page
if (document.getElementById('cart-items')) {
  displayCartItems();
}
// Display cart items on the checkout page
function displayCheckoutItems() {
  loadCartFromLocalStorage();
  const orderItemsContainer = document.getElementById('order-items');
  const orderTotalElement = document.getElementById('order-total');
  
  let total = 0;
  orderItemsContainer.innerHTML = '';

  cart.forEach(item => {
    const itemElement = document.createElement('div');
    itemElement.className = 'order-item';
    itemElement.innerHTML = `
      <p>${item.name} - ₹${item.price} x ${item.quantity}</p>
    `;
    orderItemsContainer.appendChild(itemElement);
    total += item.price * item.quantity;
  });

  orderTotalElement.textContent = total;
}
// Update cart item count in header
function updateCartCount() {
  loadCartFromLocalStorage();
  const itemCount = cart.reduce((acc, item) => acc + item.quantity, 0);
  document.getElementById('cart-item-count').textContent = itemCount;
}

// Run this function whenever the cart is updated, like after adding an item
updateCartCount();
// Handle user login
document.getElementById('login-form').addEventListener('submit', function(event) {
  event.preventDefault();  // Prevent default form submission

  const email = document.getElementById('email').value;
  const password = document.getElementById('password').value;

  // Check if user data is saved in localStorage (simulate database)
  const userData = JSON.parse(localStorage.getItem('userData')) || [];

  const user = userData.find(u => u.email === email && u.password === password);

  if (user) {
    alert('Login successful');
    // Redirect to home page after successful login
    window.location.href = 'index.html';
  } else {
    alert('Invalid email or password');
  }
});
// Handle user registration
document.getElementById('register-form').addEventListener('submit', function(event) {
  event.preventDefault();

  const name = document.getElementById('name').value;
  const email = document.getElementById('email').value;
  const password = document.getElementById('password').value;

  const newUser = { name, email, password };
  let userData = JSON.parse(localStorage.getItem('userData')) || [];
  userData.push(newUser);

  localStorage.setItem('userData', JSON.stringify(userData));

  alert('Registration successful! Please log in.');
  window.location.href = 'login.html';
});
// Admin login validation
document.getElementById('admin-login-form').addEventListener('submit', function(event) {
  event.preventDefault(); // Prevent form submission

  const email = document.getElementById('admin-email').value;
  const password = document.getElementById('admin-password').value;

  // Simulate admin login (hardcoded credentials for now)
  if (email === 'admin@sareeboutique.com' && password === 'admin123') {
    alert('Admin Login Successful');
    window.location.href = 'admin-dashboard.html'; // Redirect to admin dashboard
  } else {
    alert('Invalid admin credentials');
  }
});




// Handle form submission
document.getElementById('checkout-form')?.addEventListener('submit', function(event) {
  event.preventDefault(); // Prevent default form submission

  const name = document.getElementById('name').value;
  const email = document.getElementById('email').value;
  const address = document.getElementById('address').value;
  const payment = document.getElementById('payment').value;

  if (cart.length === 0) {
    alert('Your cart is empty. Please add items to the cart before checkout.');
    return;
  }

  // Simulate order placement
  alert(`Thank you, ${name}! Your order has been placed.`);
  
  // Clear the cart
  cart = [];
  saveCartToLocalStorage();

  // Redirect to home or confirmation page
  window.location.href = 'index.html';
});

// Initialize checkout page
if (document.getElementById('order-items')) {
  displayCheckoutItems();
}
// Debounce function to limit search function execution
let debounceTimer;
function searchSarees() {
  const searchQuery = document.getElementById('search-bar').value.toLowerCase();
  clearTimeout(debounceTimer);
  debounceTimer = setTimeout(() => {
    const sareeCards = document.querySelectorAll('.card');
    sareeCards.forEach(card => {
      const sareeName = card.getAttribute('data-name').toLowerCase();
      if (sareeName.includes(searchQuery)) {
        card.style.display = 'block'; // Show matching card
      } else {
        card.style.display = 'none'; // Hide non-matching card
      }
    });
  }, 300); // Adjust 300ms delay as needed
}

function applyFilter() {
  const filter = document.getElementById('filter').value;
  const gallery = document.querySelector('.gallery');
  const cards = Array.from(document.querySelectorAll('.card'));

  // Sorting logic
  cards.sort((a, b) => {
    const costA = parseInt(a.querySelector('p').textContent.replace('₹', '').replace(',', ''));
    const costB = parseInt(b.querySelector('p').textContent.replace('₹', '').replace(',', ''));
    const nameA = a.querySelector('h3').textContent.toLowerCase();
    const nameB = b.querySelector('h3').textContent.toLowerCase();

    if (filter === 'high-to-low') return costB - costA; // High to Low
    if (filter === 'low-to-high') return costA - costB; // Low to High
    if (filter === 'a-z') return nameA.localeCompare(nameB); // Alphabetical A-Z
    if (filter === 'z-a') return nameB.localeCompare(nameA); // Alphabetical Z-A
    if (filter === 'best-selling') {
      // Add your logic for best-selling if data is available
      return 0; // Placeholder, no change
    }
    return 0; // Default (no sorting)
  });

  // Append sorted cards back to the gallery
  gallery.innerHTML = '';
  cards.forEach(card => gallery.appendChild(card));
}

app.post('/admin-login', async (req, res) => {
  const { email, password } = req.body;

  if (email === 'admin@sareeboutique.com' && password === 'admin123') {
    const token = jwt.sign({ role: 'admin' }, 'secretKey', { expiresIn: '1h' });
    res.json({ message: 'Admin login successful', token });
  } else {
    res.status(401).send('Invalid admin credentials');
  }
});

app.post('/admin-login', async (req, res) => {
  const { email, password } = req.body;

  if (email === 'admin@sareeboutique.com' && password === 'admin123') {
    const token = jwt.sign({ role: 'admin' }, 'secretKey', { expiresIn: '1h' });
    res.json({ message: 'Admin login successful', token });
  } else {
    res.status(401).send('Invalid admin credentials');
  }
});



