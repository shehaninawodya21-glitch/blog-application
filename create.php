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
                <p class="create-sub"><span class="create-sub-phrase">share your thoughts,ideas and experiences</span><span class="create-sub-highlight">Write Something That Matters</span></p>

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
                    <div class="field-help">Paste a direct image URL (jpg, png) to use as the post cover.<br>Leave empty to use the site default.</div>
 
                    <label for="richEditor">Your story</label>

<div class="editor-wrapper">

    <div class="editor-toolbar" role="toolbar" aria-label="Text formatting">

        <div class="toolbar-group">

            <button type="button" class="tool-btn" onclick="formatText('bold')" title="Bold" aria-label="Bold">
                <b>B</b>
            </button>

            <button type="button" class="tool-btn" onclick="formatText('italic')" title="Italic" aria-label="Italic">
                <i>I</i>
            </button>

            <button type="button" class="tool-btn" onclick="formatText('underline')" title="Underline" aria-label="Underline">
                <u>U</u>
            </button>

        </div>

        <div class="toolbar-group">

            <label class="tool-btn color-tool" title="Text color">
                <span class="color-letter">A</span>
                <svg class="caret" viewBox="0 0 10 6" aria-hidden="true"><path d="M1 1l4 4 4-4" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <input type="color"
                       id="textColor"
                       value="#000000"
                       onchange="changeTextColor(this.value)">
            </label>

            <label class="tool-btn color-tool" title="Highlight">
                <svg viewBox="0 0 20 20" aria-hidden="true"><path d="M4.5 12.8l6.4-6.4 2.7 2.7-6.4 6.4H4.5v-2.7z" fill="currentColor"/><path d="M12.3 4.9l1.6-1.6a1.3 1.3 0 011.8 0l1.1 1.1a1.3 1.3 0 010 1.8l-1.6 1.6-2.9-2.9z" fill="currentColor"/><rect x="3" y="17" width="14" height="1.6" rx="0.8" fill="currentColor"/></svg>
                <input type="color"
                       id="highlightColor"
                       value="#fff59d"
                       onchange="changeHighlight(this.value)">
            </label>

        </div>

        <div class="toolbar-group">

            <select class="tool-select" onchange="changeFontSize(this.value)" title="Font size" aria-label="Font size">
                <option value="">Size</option>
                <option value="1">Small</option>
                <option value="3">Normal</option>
                <option value="5">Large</option>
                <option value="7">Huge</option>
            </select>

        </div>

        <div class="toolbar-group">

            <button type="button" class="tool-btn" onclick="formatText('justifyLeft')" title="Align left" aria-label="Align left">
                <svg viewBox="0 0 20 20" aria-hidden="true"><path d="M3 5h14M3 9h9M3 13h14M3 17h9" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
            </button>

            <button type="button" class="tool-btn" onclick="formatText('justifyCenter')" title="Align center" aria-label="Align center">
                <svg viewBox="0 0 20 20" aria-hidden="true"><path d="M3 5h14M5.5 9h9M3 13h14M5.5 17h9" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
            </button>

            <button type="button" class="tool-btn" onclick="formatText('justifyRight')" title="Align right" aria-label="Align right">
                <svg viewBox="0 0 20 20" aria-hidden="true"><path d="M3 5h14M8 9h9M3 13h14M8 17h9" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
            </button>

        </div>

        <div class="toolbar-group">

            <button type="button" class="tool-btn" onclick="formatText('insertUnorderedList')" title="Bullet list" aria-label="Bullet list">
                <svg viewBox="0 0 20 20" aria-hidden="true"><circle cx="4" cy="6" r="1.4" fill="currentColor"/><circle cx="4" cy="10" r="1.4" fill="currentColor"/><circle cx="4" cy="14" r="1.4" fill="currentColor"/><path d="M8 6h9M8 10h9M8 14h9" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
            </button>

            <button type="button" class="tool-btn" onclick="formatText('insertOrderedList')" title="Numbered list" aria-label="Numbered list">
                <svg viewBox="0 0 20 20" aria-hidden="true"><text x="1.5" y="7.6" font-size="6" font-family="Inter, Arial, sans-serif" fill="currentColor">1</text><text x="1.5" y="12.4" font-size="6" font-family="Inter, Arial, sans-serif" fill="currentColor">2</text><text x="1.5" y="17.2" font-size="6" font-family="Inter, Arial, sans-serif" fill="currentColor">3</text><path d="M8 6h9M8 10.5h9M8 15h9" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
            </button>

            <button type="button" class="tool-btn" onclick="insertQuote()" title="Quote block" aria-label="Quote block">
                <svg viewBox="0 0 20 20" aria-hidden="true"><path d="M3 5h14M3 10h14M3 15h9" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
            </button>

        </div>

        <div class="toolbar-group">

            <button type="button" class="tool-btn" onclick="addLink()" title="Insert link" aria-label="Insert link">
                <svg viewBox="0 0 20 20" aria-hidden="true"><path d="M8.4 11.6a3.4 3.4 0 010-4.8l2.1-2.1a3.4 3.4 0 014.8 4.8l-1.2 1.2" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/><path d="M11.6 8.4a3.4 3.4 0 010 4.8l-2.1 2.1a3.4 3.4 0 01-4.8-4.8l1.2-1.2" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
            </button>

        </div>

    </div>


    <!-- THIS IS THE ACTUAL TYPING BOX -->

    <div
        id="richEditor"
        class="rich-editor"
        contenteditable="true"
        data-placeholder="Type your blog here..."
    ></div>


    <!-- Hidden field sent to PHP -->

    <input
        type="hidden"
        name="content"
        id="content"
    >

</div>

                    <div class="actions-row">
                        <button type="submit" class="publish-btn">
                            <svg class="btn-icon" viewBox="0 0 20 20" aria-hidden="true"><path d="M18 2L2 8.6l6.2 2.3L16 5l-5 7.1v5.2l2.6-3.4 3.1 1.2L18 2z" fill="currentColor"/></svg>
                            Publish Blog
                        </button>
                        <a class="back-btn" href="index.php">
                            <svg class="btn-icon" viewBox="0 0 20 20" aria-hidden="true"><path d="M3 9l7-6 7 6" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/><path d="M5 8.6V16a1 1 0 001 1h8a1 1 0 001-1V8.6" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            Back to Home
                        </a>
                    </div>

                </form>
            </div>
        </div>

        <div class="create-visual" aria-hidden="true" style="background-image: url('assets/images/canva-create-1600w.jpg');">
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


function insertQuote() {

    editor.focus();

    document.execCommand("formatBlock", false, "blockquote");

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