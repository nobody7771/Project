# script.js - Client-Side JavaScript Functions

## What This File Does

`script.js` contains all the client-side JavaScript functionality for the GameStore website. It provides interactive features like form validation, live search filtering, and real-time cart calculations without requiring page reloads.

## Role in the Project

- **Form Validation**: Validates password confirmation on registration page
- **Live Search**: Filters games on home page as user types (no page reload)
- **Cart Updates**: Recalculates cart totals instantly when quantity changes
- **User Experience**: Provides immediate feedback without server round-trips
- **Client-Side Logic**: Handles UI interactions that don't require database access

## Code Breakdown

### 1. Form Validation Function (Register Page)
```javascript
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
```

**Purpose**: Validates that password and confirm password fields match before form submission.

**How it works**:
- **`event.preventDefault()`**: Stops form from submitting if passwords don't match
- **`document.getElementById()`**: Gets form input values
- **Comparison**: Checks if `password` equals `confirm_password`
- **Error Display**: Shows error message if mismatch
- **Return Value**: `false` prevents submission, `true` allows it

**Used in**: `register.php` form (`onsubmit="validateForm(event)"`)

**Why Important**: 
- Provides immediate feedback (no server round-trip)
- Prevents invalid data from being sent to server
- Better user experience

### 2. Live Search Function (Home Page)
```javascript
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
```

**Purpose**: Filters game cards on home page as user types in search box.

**How it works**:
- **Gets Search Input**: Reads value from search input field
- **Converts to Lowercase**: Makes search case-insensitive (`toLowerCase()`)
- **Gets All Cards**: Finds all elements with class `game-card`
- **Loops Through Cards**: Checks each game card
- **Gets Title**: Finds `<h3>` tag inside each card (game title)
- **Compares**: Checks if title contains search text (`includes()`)
- **Shows/Hides**: Sets `display` style to `block` (show) or `none` (hide)

**Used in**: `Home.php` search input (`onkeyup="liveSearch()"`)

**Why Important**:
- **No Page Reload**: Filters instantly without server request
- **Better Performance**: Faster than server-side search
- **User Experience**: Immediate visual feedback

**Limitations**:
- Only searches game titles (not genre, description)
- Case-insensitive but simple (no fuzzy matching)
- All games must be loaded on page (not suitable for huge catalogs)

### 3. Cart Update Function (Cart Page)
```javascript
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
```

**Purpose**: Recalculates cart totals instantly when user changes quantity.

**How it works**:
- **Gets All Quantity Inputs**: Finds all inputs with class `cart-qty-input`
- **Loops Through Each**: Processes each cart item
- **Gets Price**: Reads `data-price` attribute (set by PHP in `cart.php`)
- **Gets Quantity**: Reads current value from input field
- **Calculates Row Total**: `price × quantity` for each item
- **Updates Row Total Display**: Finds `.row-total` span in same table row and updates it
- **Sums Grand Total**: Adds all row totals together
- **Updates Grand Total**: Updates the grand total display at bottom

**Used in**: `cart.php` quantity inputs (`onchange="updateCart()"` and `onkeyup="updateCart()"`)

**Key JavaScript Concepts**:
- **`getAttribute("data-price")`**: Reads HTML5 data attribute
- **`parseFloat()`**: Converts string to decimal number
- **`parseInt()`**: Converts string to integer
- **`closest("tr")`**: Finds nearest parent `<tr>` element
- **`querySelector()`**: Finds element using CSS selector
- **`toFixed(2)`**: Formats number to 2 decimal places

**Why Important**:
- **Real-Time Updates**: User sees totals change immediately
- **No Page Reload**: Instant feedback
- **Better UX**: User can experiment with quantities easily

**Note**: This only updates the display. To persist changes, user would need to submit form or use AJAX.

## Important Functions Summary

### `validateForm(event)`
- **Input**: Form submission event
- **Output**: `true` (allow submission) or `false` (prevent submission)
- **Side Effect**: Shows error message if passwords don't match
- **Used By**: Registration form

### `liveSearch()`
- **Input**: None (reads from DOM)
- **Output**: None (modifies DOM)
- **Side Effect**: Shows/hides game cards based on search input
- **Used By**: Home page search input

### `updateCart()`
- **Input**: None (reads from DOM)
- **Output**: None (modifies DOM)
- **Side Effect**: Updates row totals and grand total display
- **Used By**: Cart page quantity inputs

## Connections to Other Files

- **Used by**: 
  - `register.php` (password validation)
  - `Home.php` (live search)
  - `cart.php` (cart calculations)
- **Dependencies**: 
  - HTML elements with specific IDs/classes
  - DOM structure must match expected format

## Dependencies

- **Browser JavaScript**: Requires JavaScript enabled in browser
- **DOM Elements**: Requires specific HTML structure:
  - `id="password"` and `id="confirm_password"` (register form)
  - `id="js-error"` (error message element)
  - `id="search-input"` (search box)
  - `class="game-card"` (game cards)
  - `class="cart-qty-input"` (quantity inputs)
  - `data-price` attribute (price data)
  - `class="row-total"` (row total spans)
  - `id="grand-total-text"` (grand total element)

## Browser Compatibility

- **Modern Browsers**: Works in Chrome, Firefox, Safari, Edge
- **ES5 Syntax**: Uses older JavaScript syntax (compatible with older browsers)
- **No External Libraries**: Pure JavaScript (no jQuery, React, etc.)

## Common Issues

1. **Function not working**:
   - JavaScript file not loaded (`<script src="script.js"></script>` missing)
   - JavaScript errors in browser console
   - File path incorrect

2. **Search not filtering**:
   - `liveSearch()` function not called (`onkeyup` attribute missing)
   - Game cards don't have class `game-card`
   - Title not in `<h3>` tag

3. **Cart totals not updating**:
   - `updateCart()` function not called
   - Missing `data-price` attribute on inputs
   - Missing `class="row-total"` spans
   - Missing `id="grand-total-text"` element

4. **Password validation not working**:
   - Form doesn't have `onsubmit="validateForm(event)"`
   - Missing `id="password"` or `id="confirm_password"`
   - Missing `id="js-error"` element

5. **JavaScript errors in console**:
   - Elements not found (wrong IDs/classes)
   - Null reference errors
   - Check browser console for specific errors

## Best Practices Used

1. **Event Prevention**: Uses `event.preventDefault()` to stop form submission
2. **Case Insensitive**: Converts to lowercase for better search experience
3. **Number Formatting**: Uses `toFixed(2)` for currency display
4. **DOM Traversal**: Uses `closest()` and `querySelector()` for reliable element finding
5. **Error Handling**: Shows user-friendly error messages

## Potential Improvements

1. **Debouncing**: Add delay to search to reduce function calls
2. **AJAX**: Update cart quantities in database without page reload
3. **Better Validation**: More comprehensive form validation
4. **Error Handling**: Try-catch blocks for robustness
5. **Modern Syntax**: Could use ES6+ features (arrow functions, const/let)

