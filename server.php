<?php

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use Ratchet\Http\HttpServer;

require __DIR__ . '/vendor/autoload.php';

class PokerServer implements MessageComponentInterface {
    protected $clients;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn) {
        // Store the new connection
        $this->clients->attach($conn);
        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        echo "Message received: $msg\n";

        $data = json_decode($msg, true);
        if ($data && isset($data['action'])) {
            // Example: Handle poker moves
            if ($data['action'] === 'move') {
                $response = json_encode([
                    'type' => 'move_update',
                    'player' => $data['player'],
                    'move' => $data['move']
                ]);

                // Broadcast the move to all players
                foreach ($this->clients as $client) {
                    $client->send($response);
                }
            }
        }
    }

    public function onClose(ConnectionInterface $conn) {
        // Remove connection on close
        $this->clients->detach($conn);
        echo "Connection {$conn->resourceId} closed\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "Error: {$e->getMessage()}\n";
        $conn->close();
    }
}

// Start the WebSocket server on port 8080
$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new PokerServer()
        )
    ),
    8080
);

echo "WebSocket server started on ws://localhost:8080\n";
$server->run();
