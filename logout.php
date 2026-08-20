<?php

session_start();

session_unset();
session_destroy();

// Delete remember email cookie
setcookie(
    "remember_email",
    "",
    time() - 3600,
    "/"
);

header("Location: login.php");
exit();

?>