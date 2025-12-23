<?php
session_start();
include 'db.php';

// STEP 1: SECURITY CHECK
if (!isset($_SESSION['user_id']) || $_SESSION['username'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$id = $_GET['id'];
$error = "";
$success = "";

// STEP 2: FETCH CURRENT GAME DATA
// We need this to fill the boxes with the old info
$sql = "SELECT * FROM games WHERE id = $id";
$result = $conn->query($sql);
$game = $result->fetch_assoc();

// STEP 3: HANDLE UPDATE
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $conn->real_escape_string($_POST['title']);
    $genre = $conn->real_escape_string($_POST['genre']);
    $price = $_POST['price'];
    $description = $conn->real_escape_string($_POST['description']);
    
    // Check if a NEW image was uploaded
    if (!empty($_FILES['image']['name'])) {
        $image_name = $_FILES['image']['name'];
        $target_dir = "images/";
        $target_file = $target_dir . basename($image_name);
        
        move_uploaded_file($_FILES['image']['tmp_name'], $target_file);
        
        // Update SQL includes the new image path
        $update_sql = "UPDATE games SET title='$title', genre='$genre', price='$price', description='$description', image_path='$target_file' WHERE id=$id";
    } else {
        // Update SQL keeps the OLD image (we don't change image_path)
        $update_sql = "UPDATE games SET title='$title', genre='$genre', price='$price', description='$description' WHERE id=$id";
    }

    if ($conn->query($update_sql) === TRUE) {
        $success = "Game updated! Redirecting...";
        header("refresh:1;url=admin.php");
    } else {
        $error = "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Game</title>
    <link rel="stylesheet" href="stylesheet.css">
</head>
<body>
    <header>
        <div class="navbar">
            <div class="logo">Admin Panel</div>
            <ul class="nav-links">
                <li><a href="admin.php">Dashboard</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </header>

    <main class="container">
        <div class="form-container">
            <h2 class="form-title">Edit Game</h2>
            
            <?php if($success): ?> <p class="success-msg"><?= $success ?></p> <?php endif; ?>
            <?php if($error): ?> <p class="error-msg"><?= $error ?></p> <?php endif; ?>

            <form action="edit_game.php?id=<?= $id ?>" method="POST" enctype="multipart/form-data">
                
                <div class="form-group">
                    <label>Title</label>
                    <input type="text" name="title" value="<?= $game['title'] ?>" required>
                </div>

                <div class="form-group">
                    <label>Genre</label>
                    <input type="text" name="genre" value="<?= $game['genre'] ?>" required>
                </div>

                <div class="form-group">
                    <label>Price</label>
                    <input type="number" step="0.01" name="price" value="<?= $game['price'] ?>" required>
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="4" class="desc-box"><?= $game['description'] ?></textarea>
                </div>

                <div class="form-group">
                    <label>New Image (Optional)</label>
                    <input type="file" name="image">
                    
                    <p class="current-img-label">
                        Current: <img src="<?= $game['image_path'] ?>" class="preview-thumb">
                    </p>
                </div>

                <button type="submit" class="form-btn">Update Game</button>
            </form>
        </div>
    </main>
</body>
</html>