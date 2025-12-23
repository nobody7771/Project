/* =========================================
   1. REGISTER PAGE: Check Passwords
   ========================================= */
function validateForm(event) {
    // 1. Get the values from the HTML inputs
    const password = document.getElementById("password").value;
    const confirmPass = document.getElementById("confirm_password").value;
    const errorBox = document.getElementById("js-error");

    // 2. Check if they match
    if (password !== confirmPass) {
        // STOP the form from sending to the server
        event.preventDefault(); 
        
        // Show the error message box
        errorBox.style.display = "block";
        errorBox.innerText = "Error: Passwords do not match!";
        
        return false;
    }
    
    // If they match, let the form continue
    return true;
}


/* =========================================
   2. HOME PAGE: Search Bar
   ========================================= */
function liveSearch() {
    // 1. Get what the user is typing (make it lowercase to match easily)
    const userSearch = document.getElementById("search-input").value.toLowerCase();
    
    // 2. Get the list of all game cards
    const cards = document.getElementsByClassName("game-card");

    // 3. Loop through every card to check its title
    for (let i = 0; i < cards.length; i++) {
        
        // Get the current card
        const card = cards[i];

        // Find the <h3> tag inside this card (which holds the title)
        const h3 = card.getElementsByTagName("h3")[0];
        const titleText = h3.innerText.toLowerCase();

        // 4. Check if the title contains the user's search text
        if (titleText.includes(userSearch)) {
            card.style.display = "block"; // Show it
        } else {
            card.style.display = "none";  // Hide it
        }
    }
}


/* =========================================
   3. CART PAGE: Calculate Totals
   ========================================= */
function updateCart() {
    let grandTotal = 0;
    
    // 1. Get all the quantity boxes on the page
    const allQtyInputs = document.getElementsByClassName("cart-qty-input");

    // 2. Loop through each box to do the math
    for (let i = 0; i < allQtyInputs.length; i++) {
        
        const input = allQtyInputs[i];
        
        // Get the price (We hid this in the 'data-price' attribute in HTML)
        const price = parseFloat(input.getAttribute("data-price"));
        
        // Get the quantity number the user typed
        const quantity = parseInt(input.value);
        
        // Math: Price x Quantity
        const rowTotal = price * quantity;
        
        // 3. Display the new total for this specific row
        // 'closest("tr")' means: Go up to the table row that holds this input
        const row = input.closest("tr"); 
        const totalSpan = row.querySelector(".row-total");
        totalSpan.innerText = "$" + rowTotal.toFixed(2);

        // 4. Add this row's money to the Grand Total
        grandTotal = grandTotal + rowTotal;
    }

    // 5. Update the big Grand Total at the bottom
    document.getElementById("grand-total-text").innerText = "$" + grandTotal.toFixed(2);
}