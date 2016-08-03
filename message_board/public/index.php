<?php

require 'Database.php';

$db = new Database;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Message Board</title>
</head>
<body>
    <h1>New Message</h1>
    <form method="POST"
        <?php if (isset($_GET['id'])): ?>
            action="updateMessage.php"
        <?php else: ?>
            action="postMessage.php"
        <?php endif ?>
    >
        <?php if (isset($_GET['id'])): ?>
            <input type="hidden" name="id" value="<?= $_GET['id'] ?>">
        <?php endif ?>
        Name <input type="text" name="display_name" value="<?= isset($_GET['display_name']) ? $_GET['display_name'] : '' ?>">
        Message <input type="text" name="msg" value="<?= isset($_GET['msg']) ? $_GET['msg'] : '' ?>">
        <button type="submit">Add Message</button>
    </form>

    <h1>Messages</h1>
    <?php foreach ($db->selectAll('messages') as $msg): ?>
        <p>
            <?= $msg['display_name'] ?>: <?= $msg['msg'] ?>
            <a href="/?id=<?= $msg['id'] ?>&display_name=<?= $msg['display_name'] ?>&msg=<?= $msg['msg'] ?>">Edit</a>
            <a href="deleteMessage.php?id=<?= $msg['id'] ?>">Delete</a>
        </p>
    <?php endforeach ?>
</body>
</html>
