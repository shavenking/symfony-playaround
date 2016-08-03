<?php

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

require_once 'vendor/autoload.php';

$path      = [__DIR__ . '/src'];
$isDevMode = true;
$config = Setup::createAnnotationMetadataConfiguration($path, $isDevMode);

$conn = [
    'driver'   => 'pdo_mysql',
    'user'     => 'root',
    'password' => 'root',
    'dbname'   => 'message_board'
];

$entityManager = EntityManager::create($conn, $config);
