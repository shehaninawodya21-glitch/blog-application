<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once "config/database.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if (empty($username) || empty($email) || empty($password)) {

        $message = "Please fill all fields.";

    } else {

        // Check whether email already exists
        $check = $pdo->prepare("SELECT id FROM `user` WHERE email = ?");
        $check->execute([$email]);

        if ($check->fetch()) {

            $message = "Email already exists.";

        } else {

            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insert user
            $stmt = $pdo->prepare(
                "INSERT INTO `user` (username, email, password, role)
                 VALUES (?, ?, ?, ?)"
            );

            $stmt->execute([
                $username,
                $email,
                $hashedPassword,
                "user"
            ]);

            $userId = $pdo->lastInsertId();

            $_SESSION["user_id"] = $userId;
            $_SESSION["username"] = $username;
            $_SESSION["role"] = "user";

            header("Location: index.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Register - BlogNest</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">

</head>

<body class="auth-page">

<div class="auth-shell">

    <div class="auth-visual">
        <div class="auth-visual-content">
            <span class="eyebrow">Join now</span>
            <h2>Start publishing your ideas today.</h2>
            <p>Create your account and build a community around your stories.</p>
        </div>
    </div>

    <div class="register-box auth-box">

        <h2>Create Account</h2>

        <?php if (!empty($message)): ?>
            <div class="auth-message">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <label>Username</label>
            <input type="text" name="username" required>

            <label>Email</label>
            <input type="email" name="email" required>

            <label>Password</label>
            <input type="password" name="password" required>

            <button type="submit">Register</button>

        </form>

        <div class="auth-link">
            Already have an account?
            <a href="login.php">Login</a>
        </div>

    </div>

</div>

</body>

</html>