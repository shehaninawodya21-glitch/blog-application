<?php

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

            $message = "Registration successful!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Register - My Blog</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f2f2f2;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .register-box {
            background: white;
            padding: 30px;
            width: 350px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
        }

        input {
            width: 100%;
            padding: 10px;
            margin: 8px 0 15px 0;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            padding: 10px;
            background: #333;
            color: white;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background: #555;
        }

        .message {
            text-align: center;
            margin-bottom: 15px;
            color: green;
        }

        .login-link {
            text-align: center;
            margin-top: 15px;
        }
    </style>

</head>

<body>

<div class="register-box">

    <h2>Create Account</h2>

    <?php if (!empty($message)): ?>
        <div class="message">
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

    <div class="login-link">
        Already have an account?
        <a href="login.php">Login</a>
    </div>

</div>

</body>

</html>