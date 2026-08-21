<?php

session_start();

require_once "config/database.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit();
}

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: index.php");
    exit();
}

$blog_id = (int) $_GET["id"];

$check = $pdo->prepare(
    "SELECT user_id FROM blogpost WHERE id = ?"
);
$check->execute([$blog_id]);
$blog = $check->fetch(PDO::FETCH_ASSOC);

if (!$blog) {
    header("Location: index.php");
    exit();
}

if ((int) $blog["user_id"] !== (int) $_SESSION["user_id"]) {
    header("Location: blog.php?id=" . $blog_id);
    exit();
}

$stmt = $pdo->prepare(
    "DELETE FROM blogpost
     WHERE id = ? AND user_id = ?"
);

$stmt->execute([
    $blog_id,
    $_SESSION["user_id"]
]);

header("Location: index.php");
exit();
?>