<?php

session_start();

require_once "config/database.php";

// Only registered users can create posts
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $title = trim($_POST["title"]);
    $content = trim($_POST["content"]);
    $imageUrl = trim($_POST["image_url"] ?? "");

    if (empty($title) || empty($content)) {

        $message = "Please fill in all fields.";

    } else {

        $stmt = $pdo->prepare(
            "INSERT INTO blogPost (user_id, title, content, image_url)
             VALUES (?, ?, ?, ?)"
        );

        // If user entered a URL without scheme, prefix http:// for convenience
        if ($imageUrl !== "" && !preg_match('#^https?://#i', $imageUrl)) {
            $imageUrl = 'http://' . $imageUrl;
        }

        $stmt->execute([
            $_SESSION["user_id"],
            $title,
            $content,
            $imageUrl !== "" ? $imageUrl : null
        ]);

        // Redirect to the newly created blog so the user can immediately see the image
        $newId = $pdo->lastInsertId();
        header("Location: blog.php?id=" . $newId);
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Create Blog - BlogNest</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">

</head>

<body class="form-page">

<div class="create-shell">

    <div class="create-panel">

        <div class="create-side">
            <div class="form-container">
                <h1 class="create-heading">Ready to start your story?</h1>
                <p class="create-sub"><span class="create-sub-phrase">share your thoughts,ideas and experiences</span><span class="divider"></span><span class="create-sub-highlight">Write Something that Matters</span></p>

                <?php if (!empty($message)): ?>

                    <div class="message">
                        <?php echo htmlspecialchars($message); ?>
                    </div>

                <?php endif; ?>

                <form method="POST">

                    <label for="title">Full title</label>
                    <input id="title" type="text" name="title" placeholder="Enter your blog title" required>

                    <label for="image_url">Cover image URL (optional)</label>
                    <input id="image_url" type="url" name="image_url" placeholder="https://example.com/image.jpg">
                    <div class="field-help">Paste a direct image URL (jpg, png) to use as the post cover. Leave empty to use the site default.</div>
 
                    <label for="richEditor">Your story</label>

<div class="editor-wrapper">

    <div class="editor-toolbar">

        <button type="button" onclick="formatText('bold')" title="Bold">
            <b>B</b>
        </button>

        <button type="button" onclick="formatText('italic')" title="Italic">
            <i>I</i>
        </button>

        <button type="button" onclick="formatText('underline')" title="Underline">
            <u>U</u>
        </button>

        <span class="toolbar-divider"></span>

        <label class="color-tool" title="Text color">
            A
            <input type="color"
                   id="textColor"
                   value="#000000"
                   onchange="changeTextColor(this.value)">
        </label>

        <label class="color-tool" title="Highlight">
            🖍
            <input type="color"
                   id="highlightColor"
                   value="#fff59d"
                   onchange="changeHighlight(this.value)">
        </label>

        <span class="toolbar-divider"></span>

        <select onchange="changeFontSize(this.value)">
            <option value="">Size</option>
            <option value="1">Small</option>
            <option value="3">Normal</option>
            <option value="5">Large</option>
            <option value="7">Huge</option>
        </select>

        <button type="button" onclick="formatText('justifyLeft')">
            ≡
        </button>

        <button type="button" onclick="formatText('justifyCenter')">
            ≡
        </button>

        <button type="button" onclick="formatText('justifyRight')">
            ≡
        </button>

        <button type="button"
                onclick="formatText('insertUnorderedList')">
            • List
        </button>

        <button type="button"
                onclick="formatText('insertOrderedList')">
            1. List
        </button>

        <button type="button" onclick="addLink()">
            🔗
        </button>

    </div>


    <!-- THIS IS THE ACTUAL TYPING BOX -->

    <div
        id="richEditor"
        class="rich-editor"
        contenteditable="true"
        data-placeholder="Write your blog here..."
    ></div>


    <!-- Hidden field sent to PHP -->

    <input
        type="hidden"
        name="content"
        id="content"
    >

</div>

                    <div class="actions-row">
                        <button type="submit" class="publish-btn">Publish Blog ✎</button>
                        <a class="back-btn" href="index.php"> Back to Home</a>
                    </div>

                </form>
            </div>
        </div>

        <div class="create-visual" aria-hidden="true">
            <img
                src="assets/images/canva-create-1600w.jpg"
                alt="Girl writing a blog in a notebook"
                class="create-illustration"
            >
        </div>

    </div>

</div>

<script>

const editor = document.getElementById("richEditor");
const contentInput = document.getElementById("content");


function formatText(command) {

    editor.focus();

    document.execCommand(command, false, null);

}


function changeTextColor(color) {

    editor.focus();

    document.execCommand("foreColor", false, color);

}


function changeHighlight(color) {

    editor.focus();

    document.execCommand("hiliteColor", false, color);

}


function changeFontSize(size) {

    if (!size) {
        return;
    }

    editor.focus();

    document.execCommand("fontSize", false, size);

}


function addLink() {

    editor.focus();

    const url = prompt("Enter website URL:");

    if (url) {

        document.execCommand(
            "createLink",
            false,
            url
        );

    }

}


/* Send formatted HTML to PHP */

document.querySelector("form").addEventListener(
    "submit",
    function(event) {

        const text = editor.innerText.trim();

        if (text === "") {

            event.preventDefault();

            alert("Please write something in your blog.");

            editor.focus();

            return;
        }

        contentInput.value = editor.innerHTML;

    }
);

</script>

</body>

</html>