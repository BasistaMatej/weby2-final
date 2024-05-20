<?php
use Workerman\Worker;
use Workerman\Connection\TcpConnection;

require __DIR__ . '/vendor/autoload.php';

class Room
{
    public $roomKey;
    public $players = [];
    public $answers = [];

    public function __construct($roomKey)
    {
        $this->roomKey = $roomKey;
    }

    public function addPlayer(TcpConnection $connection, $answer)
    {
        $connection->roomKey = $this->roomKey;
        $connection->answer = $answer;
        $this->players[$connection->uuid] = $connection;

        if (isset($this->answers[$answer])) {
            $this->answers[$answer]++;
        } else {
            $this->answers[$answer] = 1;
        }
    }

    public function getPlayers() {
        return $this->players;
    }

    public function getAnswers() {
        return $this->answers;
    }

    public function removePlayer(TcpConnection $connection)
    {
        unset($this->players[$connection->uuid]);
    }
}

$ws_worker = new Worker("websocket://0.0.0.0:9999");
$ws_worker->count = 1;

$rooms = []; // Each room will have its own Room object

// CONNECTION
$ws_worker->onConnect = function (TcpConnection $connection) use ($ws_worker) {
    $connection->uuid = uniqid();

    $dataToSend = [
        'type' => 'initBE',
        'uuid' => $connection->uuid,
        'message' => 'You are connected to the server!'
    ];
    $connection->send(json_encode($dataToSend));
};

// MESSAGE
$ws_worker->onMessage = function (TcpConnection $connection, $data) use ($ws_worker, &$rooms) {
    $decoded = json_decode($data, true);

    if (!$decoded) {
        $connection->send(json_encode(['error' => 'Invalid message format']));
        return;
    }

    if (!isset($decoded['type'])) {
        $connection->send(json_encode(['error' => 'Invalid message format, message must contain "type"']));
        return;
    }

    switch ($decoded['type']) {
        case "initRoom":
            if (!isset($decoded['roomKey'])) {
                $connection->send(json_encode(['error' => 'Invalid message format, message must contain "roomKey"']));
                return;
            }

            $roomKey = $decoded['roomKey'];

            if (isset($rooms[$roomKey])) {
                $connection->send(json_encode(['error' => 'Room already exists']));
                return;
            }

            $rooms[$roomKey] = new Room($roomKey);
            $connection->admin = true;
            $rooms[$roomKey]->players[$connection->uuid] = $connection;

            $dataToSend = [
                'type' => 'RESPONSE: initRoom',
                'roomKey' => $roomKey,
                'message' => 'Room created successfully'
            ];
            $connection->send(json_encode($dataToSend));
            break;

        case 'initPlayer':
            if (!isset($decoded['roomKey'])) {
                $connection->send(json_encode(['error' => 'Invalid message format, message must contain "roomKey"']));
                return;
            }

            if (!isset($decoded['answer'])) {
                $connection->send(json_encode(['error' => 'Invalid message format, message must contain "answer"']));
                return;
            }

            $roomKey = $decoded['roomKey'];
            $answer = $decoded['answer'];

            if (!isset($rooms[$roomKey])) {
                $connection->send(json_encode(['error' => 'Room not found']));
                return;
            }

            $rooms[$roomKey]->addPlayer($connection, $answer);

            $dataToSend = [
                'type' => 'RESPONSE: initPlayer',
                'my_answer' => $answer,
                'all_answers' => $rooms[$roomKey]->answers,
                'message' => 'Your answer was added successfully'
            ];
            $connection->send(json_encode($dataToSend));

              // Broadcast the updated answer count to all players in the room
            $dataToSend = [
              'type' => 'updateAnswers',
              'answers' => $connection->answer,
            ];

            foreach ($rooms[$roomKey]->getPlayers() as $connectionPlayer) {
              if($connectionPlayer->uuid != $connection->uuid)
                $connectionPlayer->send(json_encode($dataToSend));
            }
            //broadcast($ws_worker, $connection, json_encode($dataToSend), $roomKey);
            break;
          case 'closeRoom':
              //if player is not admin
              /*if (!$connection->admin) {
                $connection->send(json_encode(['error' => 'Only admin can close the room']));
                return;
              }*/

              if(!isset($decoded['roomKey'])) {
                $connection->send(json_encode(['error' => 'Invalid message format']));
                return;
              }
        
              $roomKey = $decoded['roomKey'];
        
              //check if roomkey is valid
              if (!isset($rooms[$roomKey])) {
                $connection->send(json_encode(['error' => 'Room not found']));
                return;
              }
        
              $dataToSend = [
                'type' => 'RESPONSE: closeRoom',
                'roomKey' => $roomKey,
                'all_answers' => $rooms[$roomKey]->getAnswers(),
                'message' => 'Room closed successfully'
              ];
              $connection->send(json_encode($dataToSend));
        
              // Broadcast the room closure to all players in the room
              $dataToSend = [
                'type' => 'closeRoom',
                'roomKey' => $roomKey,
                'message' => 'Room closed'
              ];
              foreach ($rooms[$roomKey]->getPlayers() as $connectionPlayer) {
                if($connectionPlayer->uuid != $connection->uuid)
                  $connectionPlayer->send(json_encode($dataToSend));
              }
        
              unset($rooms[$roomKey]);
              break;
            default:
                $connection->send(json_encode(['error' => 'Unknown message type']));
                break;
          }
};

// DISCONNECT
$ws_worker->onClose = function (TcpConnection $connection) use (&$rooms) {
    if (isset($connection->roomKey)) {
        $roomKey = $connection->roomKey;
        if (isset($rooms[$roomKey])) {
            $rooms[$roomKey]->removePlayer($connection);
        }
    }
};

function broadcast($ws_worker, $connection, $data, $roomKey)
{
    foreach ($ws_worker->connections as $conn) {
        if ($connection === null || $conn !== $connection) {
            if (isset($conn->initialised) && $conn->initialised == true && isset($conn->roomKey) && $conn->roomKey === $roomKey) {
                $conn->send($data); // Odeslat data pouze pokud je připojení inicializováno a má stejný roomKey
            }
        }
    }
}

Worker::runAll();

?>