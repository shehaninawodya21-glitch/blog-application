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

// Delete only if the blog belongs to the logged-in user
$stmt = $pdo->prepare(
    "DELETE FROM blogPost
     WHERE id = ? AND user_id = ?"
);

$stmt->execute([
    $blog_id,
    $_SESSION["user_id"]
]);

header("Location: index.php");

exit();

?>