<?php

session_start();

require_once "config/database.php";

// Check whether blog ID exists
if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: index.php");
    exit();
}

$blog_id = (int) $_GET["id"];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["comment_text"])) {
    if (!isset($_SESSION["user_id"])) {
        header("Location: login.php");
        exit();
    }

    $commentText = trim($_POST["comment_text"]);

    if ($commentText !== "") {
        $commentStmt = $pdo->prepare(
            "INSERT INTO comments (blog_id, user_id, comment)
             VALUES (?, ?, ?)"
        );

        $commentStmt->execute([
            $blog_id,
            $_SESSION["user_id"],
            $commentText
        ]);
    }

    header("Location: blog.php?id=" . $blog_id);
    exit();
}

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

$commentsStmt = $pdo->prepare(
    "SELECT comments.*, user.username
     FROM comments
     INNER JOIN user ON comments.user_id = user.id
     WHERE comments.blog_id = ?
     ORDER BY comments.created_at ASC"
);

$commentsStmt->execute([$blog_id]);
$comments = $commentsStmt->fetchAll(PDO::FETCH_ASSOC);

// find previous post (by created_at)
$prevStmt = $pdo->prepare("SELECT id FROM blogPost WHERE created_at < ? ORDER BY created_at DESC LIMIT 1");
$prevStmt->execute([$blog['created_at']]);
$prev = $prevStmt->fetch(PDO::FETCH_ASSOC);
$prevId = $prev ? $prev['id'] : null;

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

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">

</head>

<body class="blog-page">


<nav>

    <a href="index.php">
    ← Home
    </a>

</nav>


<div class="blog-container">

   <article class="blog-post">

       <h1>
           <?php echo htmlspecialchars($blog["title"]); ?>
       </h1>

       <?php $coverImage = !empty($blog["image_url"]) ? $blog["image_url"] : "assets/images/fixed-blog-image.jpg"; ?>
       <div class="featured-image" style="background-image: url('<?php echo htmlspecialchars($coverImage); ?>');"></div>

       <div class="blog-meta">

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


       <div class="blog-content">

           <?php
           echo htmlspecialchars($blog["content"]);
           ?>

       </div>

       <section class="comment-section">
           <h2>Comments</h2>

           <?php if (!empty($comments)): ?>
               <div class="comment-list">
                   <?php foreach ($comments as $comment): ?>
                       <div class="comment-item">
                           <div class="comment-header">
                               <strong><?php echo htmlspecialchars($comment["username"]); ?></strong>
                               <span><?php echo date("M d, Y", strtotime($comment["created_at"])); ?></span>
                           </div>
                           <p><?php echo nl2br(htmlspecialchars($comment["comment"])); ?></p>
                       </div>
                   <?php endforeach; ?>
               </div>
           <?php else: ?>
               <p class="no-comments">No comments yet. Be the first to share your thoughts.</p>
           <?php endif; ?>

           <?php if (isset($_SESSION["user_id"])): ?>
               <form method="POST" class="comment-form">
                   <textarea name="comment_text" placeholder="Write your thoughts..." required></textarea>
                   <button type="submit" class="btn btn-primary">Post Comment</button>
               </form>
           <?php else: ?>
               <p class="comment-login-message">
                   <a href="login.php">Login</a> to comment on this blog.
               </p>
           <?php endif; ?>
       </section>

<?php $isOwner = isset($_SESSION["user_id"]) && $_SESSION["user_id"] == $blog["user_id"]; ?>

<div class="actions">
    <?php if ($prevId): ?>
        <a class="btn btn-secondary" href="blog.php?id=<?php echo $prevId; ?>"> Previous</a>
    <?php endif; ?>

    <?php if ($isOwner): ?>
        <a class="edit-link btn btn-secondary" href="edit.php?id=<?php echo $blog["id"]; ?>">Edit Blog</a>
        <form method="POST" class="delete-form" action="delete.php?id=<?php echo $blog["id"]; ?>" style="display:inline;">
            <button type="submit" class="delete-button btn btn-danger">Delete Blog</button>
        </form>
    <?php endif; ?>

    <a class="back-link btn btn-secondary" href="index.php"> Back to Blogs</a>
</div>

   </article>

</div>

<footer class="site-footer" role="contentinfo">
    <div class="footer-bar">© 2026 BlogNest — All rights reserved.</div>
</footer>

<script src="js/script.js"></script>
</body>

</html>