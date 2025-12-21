# stylesheet.css - Website Styling File

## What This File Does

`stylesheet.css` contains all the CSS (Cascading Style Sheets) styling for the GameStore website. It defines the visual appearance, layout, colors, fonts, and responsive design for all pages in the project.

## Role in the Project

- **Visual Design**: Defines colors, fonts, spacing, and overall look
- **Layout Management**: Controls page structure using Flexbox and CSS Grid
- **Responsive Design**: Makes website work on different screen sizes
- **Consistency**: Ensures all pages have unified styling
- **User Experience**: Creates professional, modern appearance

## Code Breakdown

### 1. General Reset & Base Styles
```css
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Arial, Helvetica, sans-serif;
}
```
- **Purpose**: Resets default browser styles for consistency
- **`*`**: Universal selector (applies to all elements)
- **`margin: 0; padding: 0`**: Removes default spacing
- **`box-sizing: border-box`**: Makes width calculations include padding/border
- **`font-family`**: Sets default font (Arial fallback)

### 2. Body & Layout Structure
```css
body {
    background-color: #302727;
    color: #e50707;
    line-height: 1.6;
    display: grid;
    grid-template-rows: auto 1fr auto;
    min-height: 100vh;
    margin: 0;
}
```
- **Purpose**: Sets overall page background and layout structure
- **`background-color: #302727`**: Dark gray background
- **`color: #e50707`**: Red text color (brand color)
- **`display: grid`**: Uses CSS Grid for page layout
- **`grid-template-rows: auto 1fr auto`**: Three rows (header, content, footer)
- **`min-height: 100vh`**: Ensures page fills viewport height
- **Layout**: Header at top, content in middle (grows), footer at bottom

### 3. Navigation Bar (Header)
```css
header {
    background: #e50707;
    color: #fff;
    padding: 1rem 0;
}

.navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    max-width: 1300px;
    margin: auto;
    padding: 10px;
}
```
- **Purpose**: Styles the top navigation bar
- **`background: #e50707`**: Red background (brand color)
- **`display: flex`**: Uses Flexbox for horizontal layout
- **`justify-content: space-between`**: Spreads logo and links apart
- **`max-width: 1300px`**: Limits width, centers content
- **`margin: auto`**: Centers navbar horizontally

### 4. Navigation Links
```css
.nav-links {
    list-style: none;
    display: flex;
}

.nav-links li {
    margin-left: 20px;
}

.nav-links a {
    color: #fff;
    text-decoration: none;
    font-weight: 500;
}
```
- **Purpose**: Styles navigation menu items
- **`list-style: none`**: Removes bullet points
- **`display: flex`**: Horizontal layout for links
- **`margin-left: 20px`**: Spacing between links
- **`color: #fff`**: White text on red background
- **`text-decoration: none`**: Removes underline from links

### 5. Main Container
```css
.container {
    max-width: 1300px;
    width: 100%;
    margin: auto;
    padding: 20px;
}
```
- **Purpose**: Wraps main content, centers and limits width
- **`max-width: 1300px`**: Maximum content width
- **`margin: auto`**: Centers container
- **`padding: 20px`**: Inner spacing

### 6. Game Grid (CSS Grid - Responsive)
```css
.game-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}
```
- **Purpose**: **Critical for responsive design** - creates flexible game card grid
- **`display: grid`**: Uses CSS Grid layout
- **`repeat(auto-fit, minmax(250px, 1fr))`**: 
  - **Auto-fit**: Automatically fits as many columns as possible
  - **minmax(250px, 1fr)**: Each column minimum 250px, can grow equally
  - **Result**: Responsive grid that adapts to screen size
- **`gap: 20px`**: Spacing between grid items
- **Why Important**: No media queries needed - automatically responsive

### 7. Game Card Styling
```css
.game-card {
    background: #ffffff;
    border-radius: 5px;
    overflow: hidden;
}

.game-card:hover {
    border: 1px solid #000; 
    background-color: #f9f9f9; 
    cursor: pointer; 
}

.game-card img {
    width: 100%;
    height: 200px;
    object-fit: cover;
}
```
- **Purpose**: Styles individual game cards
- **`background: #ffffff`**: White card background
- **`border-radius: 5px`**: Rounded corners
- **`overflow: hidden`**: Clips image to card boundaries
- **`:hover`**: Styles when mouse hovers over card
- **`object-fit: cover`**: Images fill space, maintain aspect ratio

### 8. Buttons
```css
.btn {
    display: inline-block;
    background: #e50707;
    color: #ffffff;
    padding: 8px 15px;
    text-decoration: none;
    border-radius: 3px;
    margin-top: 10px;
}

.btn:hover {
    background: #c0392b;
}
```
- **Purpose**: Styles all buttons and button-like links
- **`display: inline-block`**: Allows padding while staying inline
- **`background: #e50707`**: Red button color
- **`:hover`**: Darker red on hover (user feedback)

### 9. Search Bar
```css
.search-container {
    margin-bottom: 20px;
}

.search {
    padding: 10px;
    width: 100%;
    max-width: 300px;
    border-radius: 5px;
    border: 1px solid #ccc;
}
```
- **Purpose**: Styles search input on home page
- **`max-width: 300px`**: Limits search bar width
- **`border: 1px solid #ccc`**: Light gray border

### 10. Footer
```css
footer {
    text-align: center;
    padding: 20px;
    background: #e50707;
    color: #fff;
    margin-top: 40px;
}
```
- **Purpose**: Styles page footer
- **`background: #e50707`**: Matches header color
- **`text-align: center`**: Centers footer text

### 11. Details Page Styles
```css
.details-wrapper {
    background: #ffffff;
    padding: 30px;
    border-radius: 5px;
    display: flex;
    flex-wrap: wrap;
    gap: 40px;
    color: #333;
}

.details-image-col {
    flex: 1;
    min-width: 300px;
}

.details-info-col {
    flex: 1.5;
    min-width: 300px;
}
```
- **Purpose**: Two-column layout for game details page
- **`display: flex`**: Flexbox layout
- **`flex-wrap: wrap`**: Allows wrapping on small screens
- **`flex: 1` and `flex: 1.5`**: Image takes 1 part, info takes 1.5 parts
- **`min-width: 300px`**: Prevents columns from getting too narrow
- **Responsive**: Wraps to single column on mobile

### 12. Form Styles (Login/Register)
```css
.form-container {
    background: #ffffff;
    max-width: 400px;
    margin: 50px auto;
    padding: 30px;
    border-radius: 5px;
    box-shadow: 0 0 10px rgba(0,0,0,0.5);
    color: #333;
}

.form-group {
    margin-bottom: 15px;
}

.form-group input {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 3px;
}
```
- **Purpose**: Styles login and registration forms
- **`max-width: 400px`**: Limits form width
- **`margin: 50px auto`**: Centers form vertically and horizontally
- **`box-shadow`**: Adds depth/shadow effect
- **`width: 100%`**: Inputs fill container width

### 13. Cart Table Styles
```css
.cart-table {
    width: 100%;
    border-collapse: collapse;
    background: #ffffff;
    border-radius: 5px;
    overflow: hidden;
    margin-bottom: 20px;
    color: #333;
}

.cart-table th, .cart-table td {
    padding: 15px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

.cart-table th {
    background-color: #f4f4f4;
    font-weight: bold;
}
```
- **Purpose**: Styles shopping cart table
- **`border-collapse: collapse`**: Removes gaps between borders
- **`overflow: hidden`**: Clips content to rounded corners
- **Table Structure**: Header row with gray background, data rows with borders

### 14. Checkout Page Layout
```css
.checkout-wrapper {
    display: flex;
    flex-wrap: wrap;
    gap: 30px;
    justify-content: center;
}

.checkout-form {
    flex: 1.5;
    background: #fff;
    padding: 30px;
    border-radius: 5px;
    min-width: 300px;
}

.checkout-summary {
    flex: 1;
    background: #f4f4f4;
    padding: 30px;
    border-radius: 5px;
    height: fit-content;
}
```
- **Purpose**: Two-column layout for checkout page
- **`display: flex`**: Side-by-side layout
- **`flex-wrap: wrap`**: Wraps on small screens
- **Form**: Left side (larger, `flex: 1.5`)
- **Summary**: Right side (smaller, `flex: 1`)

## Important CSS Concepts Used

### CSS Grid
- **`.game-grid`**: Responsive grid layout
- **`grid-template-columns: repeat(auto-fit, minmax(250px, 1fr))`**: Auto-responsive columns
- **Why Important**: No media queries needed for basic responsiveness

### Flexbox
- **`.navbar`**: Horizontal navigation layout
- **`.details-wrapper`**: Two-column layout
- **`.checkout-wrapper`**: Side-by-side checkout layout
- **Why Important**: Flexible, easy-to-control layouts

### Color Scheme
- **Primary Red**: `#e50707` (brand color)
- **Dark Background**: `#302727` (page background)
- **White**: `#ffffff` (cards, forms)
- **Gray**: `#f4f4f4` (subtle backgrounds)

### Responsive Design
- **CSS Grid auto-fit**: Automatically adjusts columns
- **Flexbox wrap**: Columns stack on small screens
- **Min-width**: Prevents elements from getting too small
- **Max-width**: Limits content width on large screens

## Connections to Other Files

- **Used by**: All PHP files (`Home.php`, `login.php`, `register.php`, `cart.php`, `checkout.php`, `details.php`)
- **Linked via**: `<link rel="stylesheet" href="stylesheet.css?v=5">`
- **Version Parameter**: `?v=5` prevents browser caching (increment number to force reload)

## Dependencies

- **HTML Structure**: Requires specific HTML elements and classes:
  - `.navbar`, `.nav-links`, `.container`, `.game-grid`, `.game-card`
  - `.form-container`, `.cart-table`, `.details-wrapper`, etc.
- **Browser Support**: Works in modern browsers (Chrome, Firefox, Safari, Edge)

## Common Issues

1. **Styles not applying**:
   - CSS file not linked (`<link>` tag missing)
   - Wrong file path
   - Browser cache (try `?v=6` to force reload)

2. **Layout broken**:
   - Missing HTML elements/classes
   - CSS Grid/Flexbox not supported (very old browser)
   - Conflicting styles

3. **Not responsive**:
   - CSS Grid not working
   - Missing `flex-wrap: wrap`
   - Fixed widths preventing responsiveness

4. **Colors wrong**:
   - CSS file not loaded
   - Inline styles overriding CSS
   - Browser default styles

5. **Spacing issues**:
   - Missing padding/margin
   - Box-sizing not set to `border-box`
   - Conflicting margins

## Design Principles

1. **Consistency**: Same colors, fonts, spacing throughout
2. **Responsiveness**: Works on mobile, tablet, desktop
3. **Readability**: Good contrast, clear typography
4. **User Feedback**: Hover effects, visual states
5. **Modern**: Uses CSS Grid and Flexbox (modern layout methods)

## Key Features

- **Responsive Grid**: Auto-adjusting game grid
- **Flexbox Layouts**: Flexible navigation and forms
- **Consistent Branding**: Red color scheme throughout
- **Clean Design**: White cards on dark background
- **Professional Look**: Shadows, rounded corners, proper spacing

