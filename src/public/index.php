<?php
namespace cds;

require '../../vendor/autoload.php';

// Relevant for multi-port / multi-domain setup only
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: authorization,content-type');

$app = new CaptainDomoService();

$app->run();