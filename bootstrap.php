<?php

// bootstrap.php
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Dotenv\Dotenv;

require_once "vendor/autoload.php";

$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/.env');

// Create a simple "default" Doctrine ORM configuration for Annotations
$isDevMode = true;
$config = Setup::createAnnotationMetadataConfiguration([__DIR__."/src"], $isDevMode, null, null, false);

// database configuration parameters
$conn = [
    'driver'   => 'pdo_mysql',
    'user'     => $_ENV['DBUSER'],
    'password' => $_ENV['DBPASSWORD'],
    'host'     => '127.0.0.1',
    'port '    => '3306',
    'dbname'   => $_ENV['DBNAME'],
];

/** @var EntityManager */
$em = EntityManager::create($conn, $config);
