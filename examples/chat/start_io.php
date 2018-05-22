<?php

use Workerman\Worker;
use Workerman\WebServer;
use Workerman\Autoloader;
use PHPSocketIO\SocketIO;

// composer autoload
//require_once __DIR__ . '/../../../../../vendor/autoload.php';
require_once __DIR__ . '/../../vendor/autoload.php';

$io = new SocketIO(2020);
$io->on('connection', function ($socket) {
    $socket->addedUser = false;

    // when the admin emits 'new message', this listens and executes
    $socket->on('new adminmessage', function ($data) use ($socket) {
        // we tell the client to execute 'new message'
        $socket->broadcast->emit('new adminmessage', array(
            'username' => $socket->username,
            'message' => $data
        ));
    });

    // when the admin emits 'new privatemessage', this listens and executes
    $socket->on('new adminmessage_private', function ($data) use ($socket) {
        // we tell the client to execute 'new message'
        $socket->in($data['to'])->emit('new adminmessage', array(
            'username' => $socket->username,
            'message' => $data['message'],
            'to' => $data['to']
        ));
    });

    // when the client emits 'new message', this listens and executes
    // sending to admin usig broadcast
    /*$socket->on('new clientmessage', function ($data) use ($socket) {
         // we tell the client to execute 'new message'
         $socket->broadcast->emit('new clientmessage', array(
             'username' => $socket->username,
             'message' => $data
         ));
     });*/

    $socket->on('new clientmessage', function ($data) use ($socket) {
        // we tell the client to execute 'new message'
        $socket->in('Admin')->emit('new clientmessage', array(
            'username' => $socket->username,
            'message' => $data
        ));
    });


    // when the client emits 'add user', this listens and executes
    $socket->on('add user', function ($username) use ($socket) {
        global $usernames, $numUsers;
        // we store the username in the socket session for this client
        $socket->username = $username;
        // add the client's username to the global list
        $usernames[$username] = $username;
        ++$numUsers;
        $socket->addedUser = true;
        $socket->emit('login', array(
            'numUsers' => $numUsers
        ));
        // echo globally (all clients) that a person has connected
        $socket->broadcast->emit('user joined', array(
            'username' => $socket->username,
            'numUsers' => count($usernames),
            'usernames' => $usernames
        ));

        // new addition
        $socket->join($socket->username);
    });

    // when the client emits 'typing', we broadcast it to others
    $socket->on('typing', function () use ($socket) {
        $socket->broadcast->emit('typing', array(
            'username' => $socket->username
        ));
    });

    // when the client emits 'stop typing', we broadcast it to others
    $socket->on('stop typing', function () use ($socket) {
        $socket->broadcast->emit('stop typing', array(
            'username' => $socket->username
        ));
    });

    // when the user disconnects.. perform this
    $socket->on('disconnect', function () use ($socket) {
        global $usernames, $numUsers;
        // remove the username from global usernames list
        if ($socket->addedUser) {
            unset($usernames[$socket->username]);
            --$numUsers;

            // echo globally that this client has left
            $socket->broadcast->emit('user left', array(
                'username' => $socket->username,
                'numUsers' => count($usernames),
                'usernames' => $usernames
            ));
        }
    });

    // when the user disconnects.. perform this
    $socket->on('force disconnect', function ($username) use ($socket) {
        global $usernames, $numUsers;
        // remove the username from global usernames list
        if ($socket->addedUser) {
            echo "Inside User = " . $username . PHP_EOL;
            unset($usernames[$username]);
            --$numUsers;
            // echo globally that this client has left
            $socket->broadcast->emit('user left', array(
                'username' => $username,
                'numUsers' => count($usernames),
                'usernames' => $usernames
            ));
        }
    });

    // when the admin asks for active clients
    $socket->on('get active', function () use ($socket) {

        echo 'GET ACTIVE' . PHP_EOL;

        global $usernames;
        // remove the username from global usernames list
        if ($socket->addedUser) {
            // echo globally that this client has left
            $socket->broadcast->emit('active clients', array(
                'numUsers' => count($usernames),
                'usernames' => $usernames
            ));
        } else {
            $socket->broadcast->emit('active clients', array(
                'numUsers' => 0,
                'usernames' => []
            ));
        }
    });


});

if (!defined('GLOBAL_START')) {
    Worker::runAll();
}
