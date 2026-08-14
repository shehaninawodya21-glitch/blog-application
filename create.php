<?php

session_start();

require_once "config/database.php";

// Only registered users can create posts
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $title = trim($_POST["title"]);
    $content = trim($_POST["content"]);
    $imageUrl = trim($_POST["image_url"] ?? "");

    if (empty($title) || empty($content)) {

        $message = "Please fill in all fields.";

    } else {

        $stmt = $pdo->prepare(
            "INSERT INTO blogPost (user_id, title, content, image_url)
             VALUES (?, ?, ?, ?)"
        );

        $stmt->execute([
            $_SESSION["user_id"],
            $title,
            $content,
            $imageUrl !== "" ? $imageUrl : null
        ]);

        header("Location: index.php");
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Create Blog - My Blog</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">

</head>

<body class="form-page">

<div class="form-container">

    <h1>Create New Blog</h1>

    <?php if (!empty($message)): ?>

        <div class="message">
            <?php echo htmlspecialchars($message); ?>
        </div>

    <?php endif; ?>


    <form method="POST">

        <label>Blog Title</label>

        <input
            type="text"
            name="title"
            placeholder="Enter your blog title"
            required
        >

        <label>Blog Cover Image URL (optional)</label>

        <input
            type="url"
            name="image_url"
            placeholder="https://example.com/image.jpg"
        >

        <label>Blog Content</label>

        <textarea
            name="content"
            placeholder="Write your blog here..."
            required
        ></textarea>

        <div class="actions-row">
            <button type="submit">
                Publish Blog ✎
            </button>

            <a class="btn btn-outline" href="index.php">
                ← Back to Home
            </a>
        </div>

    </form>

</div>

</body>

</html>