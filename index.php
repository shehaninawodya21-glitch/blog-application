<?php

session_start();

require_once "config/database.php";

// Get all blog posts with author information
$stmt = $pdo->query(
    "SELECT blogpost.*, user.username
     FROM blogpost
     INNER JOIN user ON blogpost.user_id = user.id
     ORDER BY blogpost.created_at DESC"
);

$blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>BlogNest</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">

</head>

<body class="blog-list-page">

<header class="topbar">
    <div class="topbar-inner">
        <div class="brand" aria-label="BlogNest brand">
            <span class="brand-mark">✦</span>
            <span>BlogNest</span>
        </div>

        <nav class="topnav" aria-label="Main navigation">
            <a href="#fresh-stories">Home</a>
            <a href="#posts">All Posts</a>
        </nav>

        <div class="auth-actions">
            <?php if (isset($_SESSION["user_id"])): ?>
                <span class="welcome-badge">Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?></span>
                <a class="btn btn-primary btn-small" href="create.php">✎ Create Post</a>
                <a class="btn btn-outline btn-small" href="logout.php">Logout</a>
            <?php else: ?>
                <a class="btn btn-outline btn-small" href="login.php">Login</a>
                <a class="btn btn-primary btn-small" href="register.php">Register</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<main class="blog-list-shell">
    <section class="hero-panel" id="fresh-stories">
        <div class="hero-copy">
            <span class="eyebrow eyebrow-soft">Fresh stories</span>
            <h1>Thoughtful writing for curious minds.</h1>
            <p>Discover ideas, stories, and practical inspiration from writers who are building better conversations online.</p>
            <div class="hero-actions">
            <?php if (isset($_SESSION["user_id"])): ?>
        <a class="btn btn-primary" href="create.php">✎ Create Post</a>
    <?php else: ?>
        <a class="btn btn-primary" href="login.php">✎ Login to Write</a>
    <?php endif; ?>

    <a class="btn btn-outline" href="#posts">Explore Posts</a>
            </div>
        </div>

        <div class="featured-panel">
            <?php if (!empty($blogs)): ?>
                <?php $featured = $blogs[0]; ?>
                <?php $featuredImage = !empty($featured["image_url"]) ? $featured["image_url"] : "https://images.unsplash.com/photo-1499951360447-b19be8fe80f5?auto=format&fit=crop&w=1200&q=80"; ?>
                <div class="featured-image" style="background-image: url('<?php echo htmlspecialchars($featuredImage); ?>');"></div>
                <div class="featured-content">
                    <span class="featured-label">Featured post</span>
                    <h2><?php echo htmlspecialchars($featured["title"]); ?></h2>
                    <p>By <?php echo htmlspecialchars($featured["username"]); ?> · <?php echo date("M d, Y", strtotime($featured["created_at"])); ?></p>
                    <a href="blog.php?id=<?php echo $featured["id"]; ?>" class="btn btn-primary btn-small">Read story</a>
                </div>
            <?php else: ?>
                <div class="featured-empty">No posts yet. Be the first to publish.</div>
            <?php endif; ?>
        </div>
    </section>

    <div class="list-topbar" id="posts">
        <div>
            <span class="section-kicker">Latest</span>
            <h2>All stories</h2>
        </div>
        <?php if (isset($_SESSION["user_id"])): ?>
    <a class="view-all" href="create.php">✎ Create Post</a>
<?php else: ?>
    <a class="view-all" href="login.php">✎ Login to Write</a>
<?php endif; ?>
    </div>

    <div class="blog-grid">
        <?php if (!empty($blogs)): ?>
            <?php foreach ($blogs as $blog): ?>
                <?php $coverImage = !empty($blog["image_url"]) ? $blog["image_url"] : "https://images.unsplash.com/photo-1455390582262-044cdead277a?auto=format&fit=crop&w=1200&q=80"; ?>
                <article class="post-card">
                    <div class="post-image">
                        <img src="<?php echo htmlspecialchars($coverImage); ?>" alt="<?php echo htmlspecialchars($blog["title"]); ?> cover" loading="lazy">
                    </div>

                    <div class="post-body">
                        <span class="post-tag">Writing</span>
                        <h3>
                            <a href="blog.php?id=<?php echo $blog["id"]; ?>"><?php echo htmlspecialchars($blog["title"]); ?></a>
                        </h3>

                        <div class="author-row">
                            <span class="author-badge"><?php echo strtoupper(substr(htmlspecialchars($blog["username"]), 0, 1)); ?></span>
                            <span class="author-name"><?php echo htmlspecialchars($blog["username"]); ?></span>
                            <span class="date-text"><?php echo date("M d, Y", strtotime($blog["created_at"])); ?></span>
                        </div>

                        <a class="btn btn-primary btn-small read-more-btn" href="blog.php?id=<?php echo $blog["id"]; ?>">Read More →</a>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">No blogs yet. Be the first to write one.</div>
        <?php endif; ?>
    </div>
</main>

<footer class="site-footer" role="contentinfo">
    <div class="footer-bar">© 2026 BlogNest — All rights reserved.</div>
</footer>

</body>

</html>