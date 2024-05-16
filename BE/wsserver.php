<?php
require __DIR__ . '/vendor/autoload.php';

use Workerman\Worker;
use Workerman\Connection\AsyncTcpConnection;

$ws_worker = new Worker("websocket://0.0.0.0:9191");

$ws_worker->count = 1;

$ws_worker->onConnect = function ($connection) use ($ws_worker) {
  $connection->send(json_encode(["status" => "working"]));

  $ws_worker->connections[$connection->player_id] = $connection;
};


$ws_worker->onMessage = function ($connection, $data) use ($ws_worker) {
  // On get message from client
};

$ws_worker->onClose = function ($connection) use ($ws_worker) {
  // On connection close
};

$ws_worker->onWorkerStart = function() use ($ws_worker) {
  // On worker start
};

Worker::runAll();

?>