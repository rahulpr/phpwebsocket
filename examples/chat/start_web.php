<?php

use Workerman\Worker;
use Workerman\WebServer;
use Workerman\Autoloader;
use PHPSocketIO\SocketIO;

// composer autoload
//require_once  __DIR__ . '/../../../../../vendor/autoload.php';
require_once __DIR__ . '/../../vendor/autoload.php';

//$web = new WebServer('http://0.0.0.0:2022');
//$web->addRoot('localhost', __DIR__ . '/public');
//$web = new WebServer('http://127.0.0.1:2022');
//$host = '192.168.1.247';
$host = '127.0.0.1';
$web = new WebServer('http://' . $host . ':2022');
$web->addRoot('http://' . $host, __DIR__ . '/public');

if (!defined('GLOBAL_START')) {
    Worker::runAll();
}
