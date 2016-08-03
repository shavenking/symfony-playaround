<?php

require __DIR__ . '/../bootstrap.php';

$message = $entityManager->find(\App\Entity\Message::class, $_POST['id']);
$message->setDisplayName($_POST['display_name']);
$message->setMsg($_POST['msg']);
$entityManager->flush();

// Redirect back to index
header('Location: /');
