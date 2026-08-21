<?php

$host = "sql306.infinityfree.com";
$dbname = "if0_42707993_blogapp";
$username = "if0_42707993";
$password = "ruvlWcRlzBVP";

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $columns = $pdo->query("SHOW COLUMNS FROM blogpost")->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array("image_url", $columns, true)) {
        $pdo->exec("ALTER TABLE blogpost ADD COLUMN image_url VARCHAR(255) NULL AFTER content");
    }

    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS comments (
            id INT AUTO_INCREMENT PRIMARY KEY,
            blog_id INT NOT NULL,
            user_id INT NOT NULL,
            comment TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (blog_id) REFERENCES blogpost(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE
        )"
    );

} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>