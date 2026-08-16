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

    <title>Edit Blog | MyBlog</title>

    <link rel="stylesheet" href="css/style.css">

</head>

<body class="form-page">

<div class="edit-shell">

    <div class="edit-card">

        <div class="edit-content">

            <h1 class="edit-heading">
                Edit your story.
            </h1>

            <p class="edit-subtitle">
                <span class="edit-sub-phrase">
                    Refine it.
                </span>

                <span class="edit-sub-desc">
                    Update your title, image and content,
                    then save your changes.
                </span>
            </p>


            <?php if (!empty($message)): ?>

                <div class="message">
                    <?php echo htmlspecialchars($message); ?>
                </div>

            <?php endif; ?>


            <form method="POST">


                <!-- BLOG TITLE -->

                <div class="form-group">

                    <label for="title">
                        Blog Title
                    </label>

                    <input
                        type="text"
                        id="title"
                        name="title"
                        value="<?php echo htmlspecialchars($blog["title"]); ?>"
                        placeholder="Enter your blog title"
                        required
                    >

                </div>


                <!-- IMAGE URL -->

                <div class="form-group">

                    <label for="image_url">
                        Blog Cover Image URL
                    </label>

                    <input
                        type="url"
                        id="image_url"
                        name="image_url"
                        value="<?php echo htmlspecialchars($blog["image_url"] ?? ""); ?>"
                        placeholder="https://example.com/image.jpg"
                    >

                    <div class="field-help">
                        Add an image URL to make your blog more attractive.
                    </div>

                </div>


                <!-- BLOG CONTENT -->

                <div class="form-group">

                    <label for="content">
                        Blog Content
                    </label>

                    <textarea
                        id="content"
                        name="content"
                        placeholder="Write your story here..."
                        required
                    ><?php echo htmlspecialchars($blog["content"]); ?></textarea>

                </div>


                <!-- BUTTONS -->

                <div class="actions-row">

                    <button
                        type="submit"
                        class="publish-btn"
                    >
                        ✓ Update Blog
                    </button>


                    <a
                        href="blog.php?id=<?php echo $blog_id; ?>"
                        class="back-btn"
                    >
                        ← Cancel
                    </a>

                </div>


            </form>

        </div>

    </div>

</div>

</body>

</html>