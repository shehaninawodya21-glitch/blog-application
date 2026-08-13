<?php

session_start();

require_once "config/database.php";

// User must be logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $title = trim($_POST["title"]);
    $content = trim($_POST["content"]);

    if (empty($title) || empty($content)) {

        $message = "Please fill in all fields.";

    } else {

        $stmt = $pdo->prepare(
            "INSERT INTO blogPost (user_id, title, content)
             VALUES (?, ?, ?)"
        );

        $stmt->execute([
            $_SESSION["user_id"],
            $title,
            $content
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

    <style>

        body {
            font-family: Arial, sans-serif;
            background: #f2f2f2;
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

        button:hover {
            background: #555;
        }

        .message {
            color: red;
            margin-bottom: 15px;
        }

        .back {
            margin-top: 20px;
        }

    </style>

</head>

<body>

<div class="container">

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


        <label>Blog Content</label>

        <textarea
            name="content"
            placeholder="Write your blog here..."
            required
        ></textarea>


        <button type="submit">
            Publish Blog
        </button>

    </form>


    <div class="back">

        <a href="index.php">
            ← Back to Home
        </a>

    </div>

</div>

</body>

</html>