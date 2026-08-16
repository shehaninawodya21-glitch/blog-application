<?php

session_start();

require_once "config/database.php";

// User must be logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Check blog ID
if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: index.php");
    exit();
}

$blog_id = $_GET["id"];

// Get the blog
$stmt = $pdo->prepare(
    "SELECT * FROM blogPost WHERE id = ?"
);

$stmt->execute([$blog_id]);

$blog = $stmt->fetch(PDO::FETCH_ASSOC);

// Blog not found
if (!$blog) {
    die("Blog post not found.");
}

// IMPORTANT: Check ownership
if ($blog["user_id"] != $_SESSION["user_id"]) {
    header("Location: blog.php?id=" . $blog_id);
    exit();
}

$message = "";

// Update blog
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $title = trim($_POST["title"]);
    $content = trim($_POST["content"]);
    $imageUrl = trim($_POST["image_url"] ?? "");

    if (empty($title) || empty($content)) {

        $message = "Please fill in all fields.";

    } else {

        $update = $pdo->prepare(
            "UPDATE blogPost
             SET title = ?, content = ?, image_url = ?
             WHERE id = ? AND user_id = ?"
        );

        $update->execute([
            $title,
            $content,
            $imageUrl !== "" ? $imageUrl : null,
            $blog_id,
            $_SESSION["user_id"]
        ]);

        header("Location: blog.php?id=" . $blog_id);
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

    <title>Edit Blog - My Blog</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">

</head>

<body class="form-page">

<div class="form-container">

    <h1>Edit Blog</h1>

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
            value="<?php echo htmlspecialchars($blog["title"]); ?>"
            placeholder="Enter your blog title"
            required
        >

        <label>Blog Cover Image URL (optional)</label>

        <input
            type="url"
            name="image_url"
            value="<?php echo htmlspecialchars($blog["image_url"] ?? ""); ?>"
            placeholder="https://example.com/image.jpg"
        >

        <label>Blog Content</label>

        <textarea
            name="content"
            placeholder="Write your blog here..."
            required
        ><?php echo htmlspecialchars($blog["content"]); ?></textarea>

        <div class="actions-row">
            <button type="submit">
                Update Blog ✎
            </button>

            <a class="btn btn-outline" href="blog.php?id=<?php echo $blog_id; ?>">
                ← Cancel
            </a>
        </div>

    </form>

</div>

</body>

</html>