<?php
use Workerman\Worker;
use Workerman\Connection\AsyncTcpConnection;
use Workerman\Connection\TcpConnection;

require __DIR__ . '/vendor/autoload.php';

$ws_worker = new Worker("websocket://0.0.0.0:9191");
$ws_worker->count = 1;

$rooms = [];  // Each room will have its own players and answers


/*
 ------- CONNECTION ------- 
$connection->uuid = uniqid();
$connection->roomKey = $decoded['roomKey']; //store roomKey in connection
$connection->admin = true;
$connection->initialised = true;
$connection->answer = $answer; //store answer in connection


 -------------------------- 
  ------- ROOMS ------- 
$rooms[$roomKey] = [
    'connections' => [],
    'answers' => []
];



// Add the connection to the rooms array
  $rooms[$roomKey]['connections'][$uuid] = $connection;

  $rooms[$roomKey][$connection->uuid]->roomKey,

  $rooms[$roomKey]['answers'] // get all 
  "answer": {
        "toto je moja odpovedddd": 1,
        "toto je moja odpoved": 2
    },



// Check if the answer already exists
    if (isset($rooms[$roomKey]['answers'][$answer])) {
        // If the answer already exists, increment its count
        $rooms[$roomKey]['answers'][$answer]['count']++;
    } else {
        // If the answer doesn't exist, add it to the array and set its count to 1
        $rooms[$roomKey]['answers'][$answer] = ['text' => $answer, 'count' => 1];
    }


 -------------------------- 
*/




// CONNECTION
$ws_worker->onConnect = function (TcpConnection $connection) use ($ws_worker) {
  // $connection->send(json_encode(["status" => "working"]));

  // $ws_worker->connections[$connection->player_id] = $connection;

  $connection->uuid = uniqid();
  
  // Send initialization data back to the client
  $dataToSend = [
    'type' => 'initBE',
    'uuid' => $connection->uuid,
    'message' => 'You are connected to the server!'
  ];
  $connection->send(json_encode($dataToSend));

};


// MESSAGE
$ws_worker->onMessage = function (TcpConnection $connection, $data) use ($ws_worker, &$rooms) {

  $decoded = json_decode($data, true); //decode the incoming message

  if (!$decoded){
    $connection->send(json_encode(['error' => 'Invalid message format']));
    return;
  }
  if (!isset($decoded['type']) ) {
    $connection->send(json_encode(['error' => 'Invalid message format, nessage must contain "type"']));
    return;
  }

  switch ($decoded['type']) {

    case "initRoom":
      if (!isset($decoded['roomKey'])) {
        $connection->send(json_encode(['error' => 'Invalid message format, message must contain "roomKey"']));
        return;
      }

      if ($connection->initialised) {
        $connection->send(json_encode(['error' => 'Admin already initialised']));
        return;
      }

      $roomKey = $decoded['roomKey'];

      
      $connection->admin = true;
      $connection->roomKey = $decoded['roomKey']; //store roomKey in connection
      $connection->initialised = true;

      // initiate new room 
      if (!isset($rooms[$roomKey])) {
        $rooms[$roomKey] = [
          'connections' => [],
          'answers' => []
        ];
      }

      
      else {
        $connection->send(json_encode(['error' => 'Room already exists']));
        return;
      }

      $rooms[$roomKey]['connections'][$connection->uuid] = $connection;

      $dataToSend = [
        'type' => 'RESPONSE: initRoom',
        'roomKey' => $connection->roomKey,
        'message' => 'Room created successfully'
      ];
      $connection->send(json_encode($dataToSend));
    break;

    case 'initPlayer':
      if (!isset($decoded['roomKey'])) {
        $connection->send(json_encode(['error' => 'Invalid message format, nessage must contain roomKey']));
        return;
      }
      
      if (!isset($decoded['answer'])) {
        $connection->send(json_encode(['error' => 'Invalid message format, nessage must contain answer']));
        return;
      }

      if ($connection->initialised) {
        $connection->send(json_encode(['error' => 'Player already initialised']));
        return;
      }

      $roomKey = $decoded['roomKey'];
      $answer = $decoded['answer'];
      

      //check if roomkey is valid
      if (!isset($rooms[$roomKey])) {
        $connection->send(json_encode(['error' => 'Room not found']));
        return;
      }

      $connection->admin = false;
      $connection->roomKey = $roomKey; //store roomKey in connection
      $connection->answer = $answer; //store answer in connection
      $connection->initialised = true;
      // Store player information in the room
      $rooms[$roomKey][$connection->uuid] = $connection;

      //store answer in room
      if (isset($rooms[$roomKey]['answers'][$answer])) {
        $rooms[$roomKey]['answers'][$answer]++;
      } else {
        $rooms[$roomKey]['answers'][$answer] = 1;
      }

      $dataToSend = [
        'type' => 'RESPONSE: initPlayer',
        'my_answer' => $connection->answer,
        'all_answers' => $rooms[$roomKey]['answers'],
        'message' => 'Your answer was added successfully'
      ];
      $connection->send(json_encode($dataToSend));

      // Broadcast the updated answer count to all players in the room
      $dataToSend = [
        'type' => 'updateAnswers',
        'answers' => $connection->answer,
      ];
      broadcast($ws_worker, $connection, json_encode($dataToSend));
      

    break;
    case 'closeRoom':
      // if (!isset($decoded['roomKey'])) {
      //   $connection->send(json_encode(['error' => 'Invalid message format, nessage must contain roomKey']));
      //   return;
      // }

      //if player is not admin
      if (!$connection->admin) {
        $connection->send(json_encode(['error' => 'Only admin can close the room']));
        return;
      }

      $roomKey = $connection->roomKey;

      //check if roomkey is valid
      if (!isset($rooms[$roomKey])) {
        $connection->send(json_encode(['error' => 'Room not found']));
        return;
      }

      

      $dataToSend = [
        'type' => 'RESPONSE: closeRoom',
        'roomKey' => $roomKey,
        'all_answers' => $rooms[$roomKey]['answers'],
        'message' => 'Room closed successfully'
      ];
      $connection->send(json_encode($dataToSend));

      // Broadcast the room closure to all players in the room
      $dataToSend = [
        'type' => 'closeRoom',
        'roomKey' => $roomKey,
        'message' => 'Room closed by admin'
      ];
      broadcast($ws_worker, $connection, json_encode($dataToSend));

      // Remove the room from the rooms array
      foreach ($ws_worker->connections as $conn) {
       
        $conn->close();
        unset($rooms[$roomKey][$conn->uuid]);
      }

      unset($rooms[$roomKey]);
      

      // // Close all connections in the room
      // foreach ($rooms[$roomKey]['connections'] as $conn) {
      //   // $conn->close();


      //   $dataToSend = [
      //     'type' => 'piseeeeee',
      //     'roomKey' => $roomKey,
      //     'all_answers' => $rooms[$roomKey]['answers'],
      //     'message' => 'Room closed successfully'
      //   ];
      //   $connection->send(json_encode($dataToSend));



      //   $conn->send(json_encode($dataToSend));
      // }

    break;
    default:
        $connection->send(json_encode(['error' => 'Unknown message type']));
        break;
  }







};

$ws_worker->onClose = function ($connection) use ($ws_worker) {
  // On connection close
  // delele himself from room

  // if ($connection->admin) {
  //   $roomKey = $connection->roomKey;
  //   if (isset($rooms[$roomKey])) {
  //     $connection->send(json_encode(['error' => 'Admin left the room, room will be closed']));
  //     unset($rooms[$roomKey]);
  //   }
  // } else {
  //   $roomKey = $connection->roomKey;
  //   if (isset($rooms[$roomKey])) {
  //     unset($rooms[$roomKey][$connection->uuid]);
  //   }
  // }
};

$ws_worker->onWorkerStart = function() use ($ws_worker) {
  // On worker start
};












function broadcast($ws_worker, $connection, $data){
  foreach ($ws_worker->connections as $conn) {
      if ($connection === null || $conn !== $connection) {
        if (isset($conn->initialised) && $conn->initialised == true) {
          $conn->send($data); // Send the data only if the connection is initialised
      }
          // $conn->send($data); // Send the data to all other connections
          // sendDataOfOtherPositions($players, $decoded, $conn); 
      }
  }
}




















Worker::runAll();

?>