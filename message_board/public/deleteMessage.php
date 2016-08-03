<?php

require 'Database.php';

$db = new Database;

$db->delete('messages', $_GET['id']);

// Redirect back to index
header('Location: /');
