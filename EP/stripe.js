// stripe.js
// Set your Stripe public key
const stripe = Stripe('your-publishable-key-here'); // Use your actual Stripe public key
const elements = stripe.elements();

// Create an instance of the card Element.
const card = elements.create('card');

// Add the card Element into the `card-element` div.
card.mount('#card-element');

// Handle form submission and token creation
const form = document.getElementById('payment-form');
form.addEventListener('submit', function(event) {
  event.preventDefault(); // Prevent form submission

  // Create a token with the card details entered
  stripe.createToken(card).then(function(result) {
    if (result.error) { 
      // If there's an error with the card, show it to the user
      const errorElement = document.getElementById('card-errors');
      errorElement.textContent = result.error.message;
    } else {
      // Send the token to your server for further processing
      stripeTokenHandler(result.token);
    }
  });
});

// Function to send the token to your server for processing
function stripeTokenHandler(token) {
  const form = document.getElementById('payment-form');
  const hiddenInput = document.createElement('input');
  hiddenInput.setAttribute('type', 'hidden');
  hiddenInput.setAttribute('name', 'stripeToken');
  hiddenInput.setAttribute('value', token.id);
  form.appendChild(hiddenInput);

  // Submit the form to your server
  form.submit();
}
