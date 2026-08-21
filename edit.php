<?php

session_start();

require_once "config/database.php";

// User must be logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Check blog ID
if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: index.php");
    exit();
}

$blog_id = $_GET["id"];

// Get the blog
$stmt = $pdo->prepare(
    "SELECT * FROM blogpost WHERE id = ?"
);

$stmt->execute([$blog_id]);

$blog = $stmt->fetch(PDO::FETCH_ASSOC);

// Blog not found
if (!$blog) {
    die("Blog post not found.");
}

// IMPORTANT: Check ownership
if ($blog["user_id"] != $_SESSION["user_id"]) {
    header("Location: blog.php?id=" . $blog_id);
    exit();
}

$message = "";

// Update blog
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $title = trim($_POST["title"]);
    $content = trim($_POST["content"]);
    $imageUrl = trim($_POST["image_url"] ?? "");

    if (empty($title) || empty($content)) {

        $message = "Please fill in all fields.";

    } else {

        $update = $pdo->prepare(
            "UPDATE blogpost
             SET title = ?, content = ?, image_url = ?
             WHERE id = ? AND user_id = ?"
        );

        $update->execute([
            $title,
            $content,
            $imageUrl !== "" ? $imageUrl : null,
            $blog_id,
            $_SESSION["user_id"]
        ]);

        header("Location: blog.php?id=" . $blog_id);
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

    <title>Edit Blog | MyBlog</title>

    <link rel="stylesheet" href="css/style.css">

</head>

<body class="form-page">

<div class="edit-shell">

    <div class="edit-card">

        <div class="edit-content">

            <h1 class="edit-heading">
                Edit your story.
            </h1>

            <p class="edit-subtitle">
                <span class="edit-sub-phrase">
                    Refine it.
                </span>

                <span class="edit-sub-desc">
                    Update your title, image and content,
                    then save your changes.
                </span>
            </p>


            <?php if (!empty($message)): ?>

                <div class="message">
                    <?php echo htmlspecialchars($message); ?>
                </div>

            <?php endif; ?>


            <form method="POST">


                <!-- BLOG TITLE -->

                <div class="form-group">

                    <label for="title">
                        Blog Title
                    </label>

                    <input
                        type="text"
                        id="title"
                        name="title"
                        value="<?php echo htmlspecialchars($blog["title"]); ?>"
                        placeholder="Enter your blog title"
                        required
                    >

                </div>


                <!-- IMAGE URL -->

                <div class="form-group">

                    <label for="image_url">
                        Blog Cover Image URL
                    </label>

                    <input
                        type="url"
                        id="image_url"
                        name="image_url"
                        value="<?php echo htmlspecialchars($blog["image_url"] ?? ""); ?>"
                        placeholder="https://example.com/image.jpg"
                    >

                    <div class="field-help">
                        Add an image URL to make your blog more attractive.
                    </div>

                </div>


                <!-- BLOG CONTENT -->

                <div class="form-group">

                    <label for="content">
                        Blog Content
                    </label>

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
            <input
                type="color"
                id="textColor"
                value="#000000"
                onchange="changeTextColor(this.value)"
            >
        </label>

        <label class="color-tool highlight-tool" title="Highlight">
            🖍
            <input
                type="color"
                id="highlightColor"
                value="#fff59d"
                onchange="changeHighlight(this.value)"
            >
        </label>

        <span class="toolbar-divider"></span>

        <select onchange="changeFontSize(this.value)" title="Font size">
            <option value="">Size</option>
            <option value="1">Small</option>
            <option value="3">Normal</option>
            <option value="5">Large</option>
            <option value="7">Huge</option>
        </select>

        <span class="toolbar-divider"></span>

        <button type="button" onclick="formatText('justifyLeft')" title="Align left">
            ≡
        </button>

        <button type="button" onclick="formatText('justifyCenter')" title="Center">
            ≡
        </button>

        <button type="button" onclick="formatText('justifyRight')" title="Align right">
            ≡
        </button>

        <span class="toolbar-divider"></span>

        <button
            type="button"
            onclick="formatText('insertUnorderedList')"
            title="Bullet list"
        >
            • List
        </button>

        <button
            type="button"
            onclick="formatText('insertOrderedList')"
            title="Numbered list"
        >
            1. List
        </button>

        <button
            type="button"
            onclick="addLink()"
            title="Add link"
        >
            🔗
        </button>

    </div>


    <div
        id="richEditor"
        class="rich-editor"
        contenteditable="true"
    ><?php echo $blog["content"]; ?></div>

</div>


<!-- Hidden field that sends formatted HTML to PHP -->

<input
    type="hidden"
    name="content"
    id="content"
>

                </div>


                <!-- BUTTONS -->

                <div class="actions-row">

                    <button
                        type="submit"
                        class="publish-btn"
                    >
                        ✓ Update Blog
                    </button>


                    <a
                        href="blog.php?id=<?php echo $blog_id; ?>"
                        class="back-btn"
                    >
                        ← Cancel
                    </a>

                </div>


            </form>

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

    document.execCommand(
        "foreColor",
        false,
        color
    );

}


function changeHighlight(color) {

    editor.focus();

    document.execCommand(
        "hiliteColor",
        false,
        color
    );

}


function changeFontSize(size) {

    if (!size) {
        return;
    }

    editor.focus();

    document.execCommand(
        "fontSize",
        false,
        size
    );

}


function addLink() {

    editor.focus();

    const url = prompt(
        "Enter the website URL:"
    );

    if (url) {

        document.execCommand(
            "createLink",
            false,
            url
        );

    }

}


/*
   Copy rich text HTML into hidden input
   before submitting the form.
*/

document.querySelector("form").addEventListener(
    "submit",
    function (event) {

        const text = editor.innerText.trim();

        if (text === "") {

            event.preventDefault();

            alert("Please write something in your blog.");

            editor.focus();

            return;
        }

        contentInput.value = editor.innerHTML.trim();

    }
);

</script>

</body>

</html>