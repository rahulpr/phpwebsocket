<?php
use Workerman\Worker;
use Workerman\WebServer;
use Workerman\Autoloader;
use PHPSocketIO\SocketIO;

// composer autoload
//require_once  __DIR__ . '/../../../../../vendor/autoload.php';
require_once  __DIR__ . '/../../vendor/autoload.php';

//$web = new WebServer('http://0.0.0.0:2022');
//$web->addRoot('localhost', __DIR__ . '/public');

//$web = new WebServer('http://127.0.0.1:2022');
$web = new WebServer('http://192.168.1.247:2022');
$web->addRoot('http://192.168.1.247', __DIR__ . '/public');

if (!defined('GLOBAL_START')) {
    Worker::runAll();
}
