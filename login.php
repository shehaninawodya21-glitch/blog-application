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

            if (isset($_POST["remember"])) {

    setcookie(
        "remember_email",
        $email,
        time() + (86400 * 30),
        "/"
    );

}

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

    <title>Login - BlogNest</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">

</head>

<body class="auth-page">

<div class="auth-shell">

    <div class="auth-visual">
        <div class="auth-visual-content">
            <span class="eyebrow">Welcome back</span>
            <h2>Share your story with the world.</h2>
            <p>Write, publish, and inspire readers every day.</p>
        </div>
    </div>

    <div class="login-box auth-box">

        <div class="login-inner">
            <h2>Welcome back to BlogNest</h2>
            <p class="login-sub">Please enter your details</p>

            <?php if (!empty($message)): ?>
                <div class="message">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <form method="POST">

                <label>Email address</label>
               <input
    type="email"
    name="email"
    value="<?php echo htmlspecialchars($_COOKIE["remember_email"] ?? ""); ?>"
    placeholder="Email"
    required
>

                <label>Password</label>
                <input type="password" name="password" required>

            <label class="remember-me">
    <input
        type="checkbox"
        name="remember"
        <?php echo isset($_COOKIE["remember_email"]) ? "checked" : ""; ?>
    >
    Remember my email
</label>

                <div style="height:12px"></div>

                <button type="submit" class="btn btn-primary btn-full">Sign in</button>

            </form>

            <div class="auth-link" style="margin-top:18px; text-align:center;">
                Don't have an account? <a href="register.php">Sign up</a>
            </div>
        </div>

    </div>

</div>

</body>

</html>