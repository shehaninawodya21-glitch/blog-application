<?php

session_start();

require_once "config/database.php";

// Check whether blog ID exists
if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: index.php");
    exit();
}

$blog_id = $_GET["id"];

// Get blog and author
$stmt = $pdo->prepare(
    "SELECT blogPost.*, user.username
     FROM blogPost
     INNER JOIN user ON blogPost.user_id = user.id
     WHERE blogPost.id = ?"
);

$stmt->execute([$blog_id]);

$blog = $stmt->fetch(PDO::FETCH_ASSOC);

// Blog not found
if (!$blog) {
    die("Blog post not found.");
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        <?php echo htmlspecialchars($blog["title"]); ?>
    </title>

    <style>

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f4f4;
        }

        nav {
            background: #222;
            padding: 15px 5%;
        }

        nav a {
            color: white;
            text-decoration: none;
        }

        .container {
            width: 90%;
            max-width: 800px;
            margin: 40px auto;
        }

        .blog {
            background: white;
            padding: 35px;
            border-radius: 10px;
        }

        h1 {
            margin-top: 0;
        }

        .info {
            color: #777;
            margin-bottom: 30px;
        }

        .content {
            line-height: 1.8;
            white-space: pre-wrap;
        }

        .back {
            display: inline-block;
            margin-top: 25px;
            background: #333;
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 5px;
        }

    </style>

</head>

<body>


<nav>

    <a href="index.php">
        ← My Blog
    </a>

</nav>


<div class="container">

    <article class="blog">

        <h1>
            <?php echo htmlspecialchars($blog["title"]); ?>
        </h1>


        <div class="info">

            By
            <strong>
                <?php echo htmlspecialchars($blog["username"]); ?>
            </strong>

            |

            <?php
            echo date(
                "F j, Y",
                strtotime($blog["created_at"])
            );
            ?>

        </div>


        <div class="content">

            <?php
            echo htmlspecialchars($blog["content"]);
            ?>

        </div>

<?php if (isset($_SESSION["user_id"]) && $_SESSION["user_id"] == $blog["user_id"]): ?>

    <a
        href="edit.php?id=<?php echo $blog["id"]; ?>"
        style="
            display: inline-block;
            margin-top: 25px;
            background: #333;
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 5px;
        "
    >
        Edit Blog
    </a>
    <form
    method="POST"
    class="delete-form"
    action="delete.php?id=<?php echo $blog["id"]; ?>"
    style="display: inline;"
>

    <button
        type="submit"
        style="
            margin-top: 25px;
            background: #b00020;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        "
    >
        Delete Blog
    </button>

</form>

<?php endif; ?>
        <a class="back" href="index.php">
            ← Back to Blogs
        </a>

    </article>

</div>
<script src="js/script.js"></script>
</body>

</html>