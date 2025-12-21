/* --- 1. Form Validation (Register Page) --- */
function validateForm(event) {
    const password = document.getElementById("password").value;
    const confirm = document.getElementById("confirm_password").value;
    const errorMsg = document.getElementById("js-error");

    if (password !== confirm) {
        event.preventDefault(); // Stop form
        errorMsg.style.display = "block";
        errorMsg.innerText = "Error: Passwords do not match!";
        return false;
    }
    return true;
}

/* --- 2. Live Search (Home Page) --- */
function liveSearch() {
    // Get what the user typed (convert to lowercase)
    const input = document.getElementById("search-input").value.toLowerCase();
    
    // Get all game cards
    const cards = document.getElementsByClassName("game-card");

    // Loop through all cards
    for (let i = 0; i < cards.length; i++) {
        // Find the title inside the card (h3 tag)
        const title = cards[i].getElementsByTagName("h3")[0].innerText.toLowerCase();

        // If title contains the input, show it. Otherwise, hide it.
        if (title.includes(input)) {
            cards[i].style.display = "block"; // Show
        } else {
            cards[i].style.display = "none";  // Hide
        }
    }
}

/* --- 3. Instant Cart Math (Cart Page) --- */
function updateCart() {
    let grandTotal = 0;
    
    // Get all quantity inputs
    const qtyInputs = document.getElementsByClassName("cart-qty-input");

    // Loop through them to calculate totals
    for (let i = 0; i < qtyInputs.length; i++) {
        const input = qtyInputs[i];
        
        // Get price from the data-price attribute (we added this in PHP)
        const price = parseFloat(input.getAttribute("data-price"));
        const qty = parseInt(input.value);
        
        // Calculate Row Total
        const rowTotal = price * qty;
        
        // Update the "Total" text for this specific row
        // We find the 'row-total' span in the same row
        const rowTotalSpan = input.closest("tr").querySelector(".row-total");
        rowTotalSpan.innerText = "$" + rowTotal.toFixed(2);

        // Add to Grand Total
        grandTotal += rowTotal;
    }

    // Update the Grand Total at the bottom
    document.getElementById("grand-total-text").innerText = "$" + grandTotal.toFixed(2);
}