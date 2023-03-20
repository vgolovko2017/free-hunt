<?php

require 'vendor/autoload.php';

use Dotenv\Dotenv;
use Src\Models\Database;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$dbConnection = (new Database())?->getConnection();