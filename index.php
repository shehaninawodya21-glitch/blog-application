<?php

session_start();

require_once "config/database.php";

// Get all blog posts with author information
$stmt = $pdo->query(
    "SELECT blogPost.*, user.username
     FROM blogPost
     INNER JOIN user ON blogPost.user_id = user.id
     ORDER BY blogPost.created_at DESC"
);

$blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>My Blog</title>

    <link rel="stylesheet" href="css/style.css">

</head>

<body>


<!-- Navigation -->

<nav>

    <h2>My Blog</h2>

    <div>

        <?php if (isset($_SESSION["user_id"])): ?>

            <span>
                Welcome,
                <?php echo htmlspecialchars($_SESSION["username"]); ?>
            </span>

            <a href="create.php">Create Blog</a>

            <a href="logout.php">Logout</a>

        <?php else: ?>

            <a href="login.php">Login</a>

            <a href="register.php">Register</a>

        <?php endif; ?>

    </div>

</nav>


<!-- Main Content -->

<div class="container">


    <?php if (isset($_SESSION["user_id"])): ?>

        <div class="welcome">

            <h2>
                Welcome,
                <?php echo htmlspecialchars($_SESSION["username"]); ?>!
            </h2>

            <p>
                Share your thoughts by creating a new blog post.
            </p>

            <a class="create-button" href="create.php">
                + Create New Blog
            </a>

        </div>

    <?php endif; ?>


    <h1>Latest Blog Posts</h1>


    <?php if (count($blogs) > 0): ?>


        <?php foreach ($blogs as $blog): ?>

            <div class="blog-card">

                <h2>
                    <?php echo htmlspecialchars($blog["title"]); ?>
                </h2>


                <div class="blog-info">

                    By
                    <?php echo htmlspecialchars($blog["username"]); ?>

                    |

                    <?php
                    echo date(
                        "F j, Y",
                        strtotime($blog["created_at"])
                    );
                    ?>

                </div>


                <p>

                    <?php

                    $preview = substr(
                        strip_tags($blog["content"]),
                        0,
                        200
                    );

                    echo htmlspecialchars($preview);

                    if (strlen($blog["content"]) > 200) {
                        echo "...";
                    }

                    ?>

                </p>


                <a
                    class="read-more"
                    href="blog.php?id=<?php echo $blog["id"]; ?>"
                >
                    Read More
                </a>

            </div>

        <?php endforeach; ?>


    <?php else: ?>


        <div class="no-blogs">

            <h2>No blog posts yet.</h2>

            <p>Be the first person to create a blog!</p>

        </div>


    <?php endif; ?>


</div>

</body>

</html>