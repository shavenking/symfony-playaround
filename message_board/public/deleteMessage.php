<?php

require __DIR__ . '/../bootstrap.php';

$message = $entityManager->find(\App\Entity\Message::class, $_GET['id']);
$entityManager->remove($message);
$entityManager->flush();

// Redirect back to index
header('Location: /');
