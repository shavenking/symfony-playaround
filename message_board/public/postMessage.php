<?php

require __DIR__ . '/../bootstrap.php';

$message = new \App\Entity\Message;
$message->setDisplayName($_POST['display_name']);
$message->setMsg($_POST['msg']);

$entityManager->persist($message);
$entityManager->flush();

// Redirect back to index
header('Location: /');
