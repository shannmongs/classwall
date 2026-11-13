<?php
// index.php - Class Wall message board

// Session must start before any output.
session_start();
require 'db.php';

// ---- Session: visit counter (resets when the browser is closed) ----
if (!isset($_SESSION['visits'])) {
    $_SESSION['visits'] = 0;
}
$_SESSION['visits']++;

// ---- Handle form submission (POST) ----
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $author   = trim($_POST['author'] ?? '');
    $body     = trim($_POST['body'] ?? '');
    $remember = isset($_POST['remember']);

    if ($author === '' || $body === '') {
        $error = 'Please enter both a name and a message.';
    } elseif (mb_strlen($author) > 100) {
        $error = 'Name must be 100 characters or fewer.';
    } else {
        // Prepared statement: user input is never placed directly in the SQL.
        $stmt = $pdo->prepare('INSERT INTO messages (author, body) VALUES (:author, :body)');
        $stmt->execute([':author' => $author, ':body' => $body]);

        // ---- Cookie: remember the name only if the checkbox is ticked ----
        if ($remember) {
            setcookie('author_name', $author, time() + 60 * 60 * 24 * 30, '/');
        } else {
            // Unticked: remove any previously saved name.
            setcookie('author_name', '', time() - 3600, '/');
        }

        // Redirect so refreshing the page does not resubmit the form.
        header('Location: index.php');
        exit;
    }
}

// ---- Pre-fill the name from the cookie ----
$savedName = $_COOKIE['author_name'] ?? '';
$postedName = $_SERVER['REQUEST_METHOD'] === 'POST' ? trim($_POST['author'] ?? '') : '';
$nameValue = $postedName !== '' ? $postedName : $savedName;
$rememberChecked = ($savedName !== '' || isset($_POST['remember']));

// ---- Search (GET) and display, newest first ----
$search = trim($_GET['search'] ?? '');

if ($search !== '') {
    $stmt = $pdo->prepare(
        'SELECT id, author, body, created_at FROM messages
         WHERE body ILIKE :search
         ORDER BY created_at DESC, id DESC'
    );
    // Escape % and _ so they are treated as plain characters.
    $stmt->execute([':search' => '%' . addcslashes($search, '%_\\') . '%']);
} else {
    $stmt = $pdo->prepare(
        'SELECT id, author, body, created_at FROM messages
         ORDER BY created_at DESC, id DESC'
    );
    $stmt->execute();
}
$messages = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Class Wall</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 640px; margin: 2rem auto; padding: 0 1rem; color: #222; }
        h1 { margin-bottom: 0.25rem; }
        .visits { color: #666; margin-top: 0; }
        form { margin-bottom: 1.5rem; }
        input[type=text], textarea { width: 100%; padding: 0.5rem; margin: 0.25rem 0 0.75rem; box-sizing: border-box; }
        button { padding: 0.5rem 1rem; cursor: pointer; }
        .error { color: #b00020; }
        .message { border: 1px solid #ddd; border-radius: 6px; padding: 0.75rem; margin-bottom: 0.75rem; }
        .meta { color: #666; font-size: 0.85rem; }
    </style>
</head>
<body>
    <h1>Class Wall</h1>
    <p class="visits">You've visited <?= (int) $_SESSION['visits'] ?> times</p>

    <?php if ($error !== ''): ?>
        <p class="error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <!-- Post a message (POST) -->
    <form method="POST" action="index.php">
        <label for="author">Name</label>
        <input type="text" id="author" name="author" maxlength="100"
               value="<?= htmlspecialchars($nameValue, ENT_QUOTES, 'UTF-8') ?>" required>

        <label for="body">Message</label>
        <textarea id="body" name="body" rows="3" required></textarea>

        <label>
            <input type="checkbox" name="remember" <?= $rememberChecked ? 'checked' : '' ?>>
            Remember my name
        </label>
        <br><br>
        <button type="submit">Post</button>
    </form>

    <!-- Search messages (GET) -->
    <form method="GET" action="index.php">
        <input type="text" name="search" placeholder="Search messages..."
               value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>">
        <button type="submit">Search</button>
        <?php if ($search !== ''): ?>
            <a href="index.php">Clear</a>
        <?php endif; ?>
    </form>

    <h2>Messages<?= $search !== '' ? ' matching "' . htmlspecialchars($search, ENT_QUOTES, 'UTF-8') . '"' : '' ?></h2>

    <?php if (count($messages) === 0): ?>
        <p>No messages found.</p>
    <?php endif; ?>

    <?php foreach ($messages as $m): ?>
        <div class="message">
            <strong><?= htmlspecialchars($m['author'], ENT_QUOTES, 'UTF-8') ?></strong>
            <span class="meta"> &middot; <?= htmlspecialchars($m['created_at'], ENT_QUOTES, 'UTF-8') ?></span>
            <p><?= nl2br(htmlspecialchars($m['body'], ENT_QUOTES, 'UTF-8')) ?></p>
        </div>
    <?php endforeach; ?>
</body>
</html>
