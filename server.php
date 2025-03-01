<?php
require 'vendor/autoload.php';

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class StallServer implements MessageComponentInterface {
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
        $data = json_decode($msg, true);
        if (isset($data['event_items'])) {
            $event_items = $data['event_items'];
            $response = $this->getStallData($event_items);
            $from->send(json_encode($response));
        }
    }

    public function onClose(ConnectionInterface $conn) {
        // The connection is closed, remove it
        $this->clients->detach($conn);
        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";
        $conn->close();
    }

    private function getStallData($event_items) {
        // Database connection parameters
        $host = 'localhost';
        $dbname = 'vendi_db';
        $username = 'root';
        $password = '';

        try {
            $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Prepare SQL query
            $sql = "SELECT event_profile, event_items, item_description, item_prices FROM stalls";
            if ($event_items) {
                $sql .= " WHERE event_items = :event_items";
            }

            $stmt = $pdo->prepare($sql);
            if ($event_items) {
                $stmt->bindParam(':event_items', $event_items, PDO::PARAM_INT);
            }
            $stmt->execute();
            $stalls = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return ["success" => true, "data" => $stalls];
        } catch (PDOException $e) {
            return ["success" => false, "message" => "Error retrieving data: " . $e->getMessage()];
        }
    }
}

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new StallServer()
        )
    ),
    8080
);

$server->run();
?>