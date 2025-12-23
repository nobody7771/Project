# script.js - JavaScript Functions

## Overview
This file contains all client-side JavaScript functionality. Includes form validation, live search, and cart calculations.

## Function 1: validateForm() - Password Validation

### Code Block
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

### Explanation
- **Purpose**: Validates that password and confirm password match before form submission
- **Used On**: Register page (`register.php`)
- **How It Works**:
  1. Gets password values from form fields
  2. Compares passwords
  3. If don't match → Prevents form submission → Shows error
  4. If match → Allows form to submit

### Detailed Breakdown

#### Line 1: Function Declaration
```javascript
function validateForm(event) {
```
- `function`: Keyword to create a function
- `validateForm`: Function name (can be called from HTML)
- `(event)`: Parameter that receives the form submission event
  - `event` object contains information about what happened
  - Allows us to prevent default behavior (form submission)

#### Line 2: Get Password Value
```javascript
const password = document.getElementById("password").value;
```
- **Breaking this down step by step**:
  1. `document` → Represents the HTML page
  2. `.getElementById("password")` → Finds element with id="password"
  3. `.value` → Gets the text user typed in that input field
  4. `const password` → Stores that value in variable
- **Example**: If user typed "mypass123", `password` = "mypass123"
- `const`: Variable that cannot be changed (constant)

#### Line 3: Get Confirm Password Value
```javascript
const confirm = document.getElementById("confirm_password").value;
```
- Same process as line 2, but for confirm password field
- **Example**: If user typed "mypass456", `confirm` = "mypass456"

#### Line 4: Get Error Message Element
```javascript
const errorMsg = document.getElementById("js-error");
```
- Finds the error message element (hidden `<p>` tag)
- Doesn't get `.value` because it's not an input - it's a paragraph element
- We'll modify this element to show error message

#### Line 6: Compare Passwords
```javascript
if (password !== confirm) {
```
- `!==` → "Not equal to" operator (strict comparison)
- Checks if password and confirm are different
- If different → Execute code inside `{}`
- If same → Skip to `return true`

#### Line 7: Prevent Form Submission
```javascript
event.preventDefault();
```
- **Breaking this down**:
  1. `event` → The form submission event
  2. `.preventDefault()` → Method that stops default behavior
  3. Default behavior = form submits to server
  4. After this → Form does NOT submit
- **Why needed**: If passwords don't match, we don't want form to submit

#### Line 8: Show Error Element
```javascript
errorMsg.style.display = "block";
```
- **Breaking this down**:
  1. `errorMsg` → The error message element we found earlier
  2. `.style` → Accesses CSS styles of element
  3. `.display` → CSS property that controls visibility
  4. `= "block"` → Makes element visible (was "none" = hidden)
- **Result**: Error message becomes visible on page

#### Line 9: Set Error Text
```javascript
errorMsg.innerText = "Error: Passwords do not match!";
```
- **Breaking this down**:
  1. `errorMsg` → The error message element
  2. `.innerText` → Property that contains text inside element
  3. `= "Error: Passwords do not match!"` → Sets the text
- **Result**: Error message now shows this text

#### Line 10: Return False
```javascript
return false;
```
- `return` → Exits function immediately
- `false` → Indicates validation failed
- Form will NOT submit

#### Line 12: Return True
```javascript
return true;
```
- Only reached if passwords match (skipped the `if` block)
- `true` → Indicates validation passed
- Form WILL submit normally

### Key Parts Explained
- `event.preventDefault()`: 
  - Stops form from submitting
  - Like saying "stop, don't do that!"
- `getElementById()`: 
  - Finds HTML element by its ID attribute
  - Like looking up a person by their ID number
- `style.display = "block"`: 
  - Changes CSS to make element visible
  - `"none"` = hidden, `"block"` = visible
- `innerText`: 
  - Gets or sets text content of element
  - Different from `innerHTML` (which allows HTML tags)

---

## Function 2: liveSearch() - Game Search Filter

### Code Block
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

### Explanation
- **Purpose**: Filters games as user types in search box
- **Used On**: Home page (`Home.php`)
- **How It Works**:
  1. Gets search input value (converts to lowercase)
  2. Gets all game card elements
  3. Loops through each card
  4. Gets game title from card
  5. Checks if title contains search text
  6. Shows/hides card based on match

### Detailed Breakdown

#### Line 2: Get Search Input
```javascript
const input = document.getElementById("search-input").value.toLowerCase();
```
- **Breaking this down step by step**:
  1. `document.getElementById("search-input")` → Finds search input field
  2. `.value` → Gets text user typed (e.g., "Elden")
  3. `.toLowerCase()` → Converts to lowercase (e.g., "elden")
  4. `const input` → Stores lowercase version
- **Why lowercase**: Makes search case-insensitive
  - "Elden" and "elden" both match
  - User can type in any case

#### Line 5: Get All Game Cards
```javascript
const cards = document.getElementsByClassName("game-card");
```
- **Breaking this down**:
  1. `document` → The HTML page
  2. `.getElementsByClassName("game-card")` → Finds ALL elements with class "game-card"
  3. Returns HTMLCollection (like an array of elements)
  4. `const cards` → Stores collection
- **Example**: If page has 10 games, `cards` contains 10 elements
- **Note**: `getElementsByClassName` (plural) gets multiple, `getElementById` (singular) gets one

#### Line 8: Loop Through Cards
```javascript
for (let i = 0; i < cards.length; i++) {
```
- **Breaking this down**:
  1. `for` → Loop that repeats code
  2. `let i = 0` → Start counter at 0
  3. `i < cards.length` → Continue while i is less than number of cards
  4. `i++` → Increase i by 1 each loop
  5. `cards.length` → Number of game cards (e.g., 10)
- **How it works**:
  - First loop: `i = 0` (first card)
  - Second loop: `i = 1` (second card)
  - Continues until `i = 9` (tenth card)
  - Then stops

#### Line 10: Get Title from Card
```javascript
const title = cards[i].getElementsByTagName("h3")[0].innerText.toLowerCase();
```
- **Breaking this down step by step**:
  1. `cards[i]` → Gets one specific card (current loop iteration)
     - If `i = 0` → First card
     - If `i = 1` → Second card
  2. `.getElementsByTagName("h3")` → Finds all `<h3>` tags inside this card
  3. `[0]` → Gets the first h3 (there's only one title per card)
     - `[0]` means first element (arrays start at 0)
  4. `.innerText` → Gets text inside h3 tag (e.g., "Elden Ring")
  5. `.toLowerCase()` → Converts to lowercase (e.g., "elden ring")
  6. `const title` → Stores lowercase title
- **Why [0]**: `getElementsByTagName` returns array, we want first (and only) h3

#### Line 13: Check if Title Contains Search
```javascript
if (title.includes(input)) {
```
- **Breaking this down**:
  1. `title` → Game title (e.g., "elden ring")
  2. `.includes(input)` → Checks if title contains search text
  3. `input` → What user searched (e.g., "elden")
  4. Returns `true` if found, `false` if not
- **Example**:
  - Title: "elden ring", Input: "elden" → `true` (contains it)
  - Title: "elden ring", Input: "zelda" → `false` (doesn't contain it)

#### Line 14: Show Card
```javascript
cards[i].style.display = "block";
```
- **Breaking this down**:
  1. `cards[i]` → The current card we're checking
  2. `.style` → Access CSS styles
  3. `.display` → CSS property for visibility
  4. `= "block"` → Makes element visible
- **Result**: Card is shown on page

#### Line 16: Hide Card
```javascript
cards[i].style.display = "none";
```
- Same as above, but `"none"` hides the element
- **Result**: Card disappears from page (but still in HTML)

### Key Parts Explained
- `toLowerCase()`: 
  - Converts string to all lowercase letters
  - "Elden Ring" → "elden ring"
  - Makes comparison case-insensitive
- `getElementsByClassName()`: 
  - Gets multiple elements (returns collection/array)
  - Different from `getElementById()` which gets one element
- `getElementsByTagName("h3")`: 
  - Finds elements by HTML tag name
  - Returns array of all h3 tags
  - `[0]` gets first one
- `includes()`: 
  - String method that checks if one string contains another
  - Returns true/false
  - Case-sensitive (that's why we use toLowerCase)
- `style.display`: 
  - CSS property that controls element visibility
  - `"block"` = visible, `"none"` = hidden
  - Can also use `"flex"`, `"grid"`, etc.

### Performance Note
- Filters already-loaded games (client-side)
- No server requests needed
- Instant results as user types

---

## Function 3: updateCart() - Cart Total Calculator

### Code Block
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

### Explanation
- **Purpose**: Updates cart totals when quantity changes
- **Used On**: Cart page (`cart.php`)
- **How It Works**:
  1. Gets all quantity input fields
  2. Loops through each input
  3. Gets price from `data-price` attribute
  4. Gets quantity from input value
  5. Calculates row total (price × quantity)
  6. Updates row total display
  7. Adds to grand total
  8. Updates grand total display

### Detailed Breakdown

#### Line 2: Initialize Total
```javascript
let grandTotal = 0;
```
- Starts with zero total
- Will add up all item totals
- `let` allows variable to change (unlike `const`)

#### Line 5: Get All Quantity Inputs
```javascript
const qtyInputs = document.getElementsByClassName("cart-qty-input");
```
- **Breaking this down**:
  1. Finds all input fields with class "cart-qty-input"
  2. Returns collection (like array) of input elements
  3. Each input represents one item's quantity
- **Example**: If cart has 3 items, `qtyInputs` contains 3 input elements

#### Line 8: Loop Through Inputs
```javascript
for (let i = 0; i < qtyInputs.length; i++) {
```
- Standard for loop
- `qtyInputs.length` → Number of items in cart
- Loops once for each cart item

#### Line 9: Get Current Input
```javascript
const input = qtyInputs[i];
```
- Gets one specific input field
- `qtyInputs[i]` → Input at position i
- Example: `qtyInputs[0]` = first input, `qtyInputs[1]` = second input

#### Line 12: Get Price from Attribute
```javascript
const price = parseFloat(input.getAttribute("data-price"));
```
- **Breaking this down step by step**:
  1. `input` → The quantity input field element
  2. `.getAttribute("data-price")` → Gets value of `data-price` attribute
     - HTML looks like: `<input data-price="59.99">`
     - Returns string: `"59.99"`
  3. `parseFloat("59.99")` → Converts string to decimal number
     - `"59.99"` (string) → `59.99` (number)
  4. `const price` → Stores as number
- **Why parseFloat**: 
  - HTML attributes are always strings
  - Need number to do math (multiplication)
  - `parseFloat` handles decimals (unlike `parseInt`)

#### Line 13: Get Quantity Value
```javascript
const qty = parseInt(input.value);
```
- **Breaking this down**:
  1. `input.value` → Gets text user typed in input field
     - Example: User typed "2" → `input.value` = `"2"` (string)
  2. `parseInt("2")` → Converts string to integer
     - `"2"` (string) → `2` (number)
  3. `const qty` → Stores as number
- **Why parseInt**: 
  - Quantity is whole number (1, 2, 3, not 1.5)
  - `parseInt` removes decimals if user types them

#### Line 16: Calculate Row Total
```javascript
const rowTotal = price * qty;
```
- **Breaking this down**:
  1. `price` → Price per item (e.g., 59.99)
  2. `*` → Multiplication operator
  3. `qty` → Quantity (e.g., 2)
  4. Result: 59.99 × 2 = 119.98
- **Example**: 
  - Price: $59.99, Quantity: 2 → Row Total: $119.98
  - Price: $29.99, Quantity: 1 → Row Total: $29.99

#### Line 20: Find Row Total Element
```javascript
const rowTotalSpan = input.closest("tr").querySelector(".row-total");
```
- **Breaking this down step by step**:
  1. `input` → The quantity input field
  2. `.closest("tr")` → Finds parent `<tr>` (table row) element
     - Goes up the HTML tree to find nearest `<tr>` tag
     - Input is inside table row, this finds that row
  3. `.querySelector(".row-total")` → Finds element with class "row-total" inside that row
     - Looks for `<span class="row-total">` in same row
  4. `const rowTotalSpan` → Stores that span element
- **Why this works**: 
  - Each table row contains: image, title, price, quantity input, total span
  - We find the row, then find the total span in that row
  - This updates the correct total for this specific item

#### Line 21: Update Row Total Display
```javascript
rowTotalSpan.innerText = "$" + rowTotal.toFixed(2);
```
- **Breaking this down**:
  1. `rowTotalSpan` → The span element that displays total
  2. `.innerText` → Property that contains text inside element
  3. `= "$"` → Sets text to dollar sign
  4. `+` → String concatenation (joins strings)
  5. `rowTotal.toFixed(2)` → Formats number to 2 decimal places
     - `119.98` → `"119.98"`
     - `100` → `"100.00"`
  6. Result: `"$119.98"`
- **Example**: 
  - `rowTotal = 119.98`
  - `rowTotal.toFixed(2)` = `"119.98"`
  - Final: `"$119.98"`

#### Line 24: Add to Grand Total
```javascript
grandTotal += rowTotal;
```
- **Breaking this down**:
  1. `+=` → Addition assignment operator
  2. Same as: `grandTotal = grandTotal + rowTotal`
  3. Adds this item's total to overall total
- **Example**:
  - Start: `grandTotal = 0`
  - Item 1: `grandTotal = 0 + 119.98 = 119.98`
  - Item 2: `grandTotal = 119.98 + 29.99 = 149.97`
  - Item 3: `grandTotal = 149.97 + 49.99 = 199.96`

#### Line 28: Update Grand Total Display
```javascript
document.getElementById("grand-total-text").innerText = "$" + grandTotal.toFixed(2);
```
- **Breaking this down**:
  1. `document.getElementById("grand-total-text")` → Finds grand total element
  2. `.innerText` → Gets/sets text content
  3. `= "$" + grandTotal.toFixed(2)` → Sets text to formatted total
- **Example**: 
  - `grandTotal = 199.96`
  - `grandTotal.toFixed(2)` = `"199.96"`
  - Final display: `"$199.96"`

### Key Parts Explained
- `getAttribute("data-price")`: 
  - Gets value from HTML data attribute
  - HTML: `<input data-price="59.99">`
  - Returns: `"59.99"` (string)
- `parseFloat()`: 
  - Converts string to decimal number
  - `"59.99"` → `59.99`
  - Handles decimals (unlike parseInt)
- `parseInt()`: 
  - Converts string to whole number
  - `"2"` → `2`
  - Removes decimals
- `closest("tr")`: 
  - Finds nearest parent element with tag "tr"
  - Goes up HTML tree until finds `<tr>`
  - Useful for finding parent container
- `querySelector(".row-total")`: 
  - Finds first element matching CSS selector
  - `.row-total` means class="row-total"
  - Searches within the element (not whole page)
- `toFixed(2)`: 
  - Formats number to 2 decimal places
  - Always shows 2 decimals (adds .00 if needed)
  - Returns string (not number)

### Data Flow
1. PHP sets `data-price` attribute on input field
2. User changes quantity → `onchange` or `onkeyup` triggers function
3. JavaScript reads price from attribute
4. Calculates new totals
5. Updates display instantly (no page reload)

## How Functions Are Called

### validateForm()
```html
<form onsubmit="validateForm(event)">
```
- Called when form is submitted
- `event` parameter allows preventing default submission

### liveSearch()
```html
<input onkeyup="liveSearch()">
```
- Called every time user types
- `onkeyup`: Triggers when key is released

### updateCart()
```html
<input onchange="updateCart()" onkeyup="updateCart()">
```
- Called when quantity changes
- `onchange`: When input loses focus
- `onkeyup`: While typing (for instant updates)

## JavaScript Concepts Used

1. **DOM Manipulation**: Accessing and modifying HTML elements
2. **Event Handling**: Responding to user actions
3. **Loops**: Iterating through elements
4. **Conditionals**: Making decisions (if/else)
5. **String Methods**: `toLowerCase()`, `includes()`
6. **Number Methods**: `parseFloat()`, `parseInt()`, `toFixed()`
7. **Element Selection**: `getElementById()`, `getElementsByClassName()`, `querySelector()`

## Browser Compatibility
- Works in all modern browsers
- Uses standard JavaScript (no libraries needed)
- No jQuery or external dependencies

