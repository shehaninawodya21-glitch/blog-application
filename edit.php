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
    die("You are not authorized to edit this blog.");
}

$message = "";

// Update blog
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $title = trim($_POST["title"]);
    $content = trim($_POST["content"]);

    if (empty($title) || empty($content)) {

        $message = "Please fill in all fields.";

    } else {

        $update = $pdo->prepare(
            "UPDATE blogPost
             SET title = ?, content = ?
             WHERE id = ? AND user_id = ?"
        );

        $update->execute([
            $title,
            $content,
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

    <title>Edit Blog</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            margin: 0;
        }

        .container {
            width: 90%;
            max-width: 800px;
            margin: 40px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
        }

        h1 {
            text-align: center;
        }

        label {
            display: block;
            margin-top: 15px;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input,
        textarea {
            width: 100%;
            padding: 12px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        textarea {
            height: 250px;
            resize: vertical;
        }

        button {
            margin-top: 20px;
            padding: 12px 25px;
            background: #333;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .message {
            color: red;
            margin-bottom: 15px;
        }

        .back {
            display: inline-block;
            margin-top: 20px;
        }

    </style>

</head>

<body>

<div class="container">

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
            required
        >


        <label>Blog Content</label>

        <textarea
            name="content"
            required
        ><?php echo htmlspecialchars($blog["content"]); ?></textarea>


        <button type="submit">
            Update Blog
        </button>

    </form>


    <a class="back" href="blog.php?id=<?php echo $blog_id; ?>">
        ← Cancel
    </a>

</div>

</body>

</html>