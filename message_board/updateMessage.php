<?php

require 'Database.php';

$db = new Database;

$db->update('messages', $_POST['id'], [
    'display_name' => $_POST['display_name'],
    'msg' => $_POST['msg']
]);

// Redirect back to index
header('Location: /');
