<?php

session_start();

require_once "config/database.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if (empty($email) || empty($password)) {

        $message = "Please enter email and password.";

    } else {

        $stmt = $pdo->prepare(
            "SELECT * FROM `user` WHERE email = ?"
        );

        $stmt->execute([$email]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user["password"])) {

            $_SESSION["user_id"] = $user["id"];
            $_SESSION["username"] = $user["username"];
            $_SESSION["role"] = $user["role"];

            header("Location: index.php");
            exit();

        } else {

            $message = "Invalid email or password.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Login - My Blog</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            background: #f2f2f2;

            display: flex;
            justify-content: center;
            align-items: center;

            min-height: 100vh;
        }

        .login-box {

            background: white;

            padding: 30px;

            width: 350px;

            border-radius: 10px;

            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
        }

        label {
            display: block;
            margin-top: 10px;
        }

        input {

            width: 100%;

            padding: 10px;

            margin-top: 5px;
            margin-bottom: 15px;

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

            color: red;

            margin-bottom: 15px;
        }

        .register-link {

            text-align: center;

            margin-top: 15px;
        }

    </style>

</head>

<body>

<div class="login-box">

    <h2>Login</h2>

    <?php if (!empty($message)): ?>

        <div class="message">
            <?php echo htmlspecialchars($message); ?>
        </div>

    <?php endif; ?>


    <form method="POST">

        <label>Email</label>

        <input
            type="email"
            name="email"
            required
        >


        <label>Password</label>

        <input
            type="password"
            name="password"
            required
        >


        <button type="submit">
            Login
        </button>

    </form>


    <div class="register-link">

        Don't have an account?

        <a href="register.php">
            Register
        </a>

    </div>

</div>

</body>

</html>