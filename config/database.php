<?php

$host = "localhost";
$dbname = "blog_app";
$username = "root";
$password = "";

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $columns = $pdo->query("SHOW COLUMNS FROM blogPost")->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array("image_url", $columns, true)) {
        $pdo->exec("ALTER TABLE blogPost ADD COLUMN image_url VARCHAR(255) NULL AFTER content");
    }

    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS comments (
            id INT AUTO_INCREMENT PRIMARY KEY,
            blog_id INT NOT NULL,
            user_id INT NOT NULL,
            comment TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (blog_id) REFERENCES blogPost(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE
        )"
    );

} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>