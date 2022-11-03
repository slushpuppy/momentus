<?php
require_once __DIR__.'/../../../autoload.php';

use app\websocket\Model\Payload;
use Tests\Init;

Init::tearDown();
Init::setUp();

$loop = \React\EventLoop\Factory::create();
$reactConnector = new \React\Socket\Connector($loop, [
]);
$connector = new \Ratchet\Client\Connector($loop, $reactConnector);

$connector('ws://ws1.rust.bike/app/websocket/index.php',[],['Authorization' => 'Bearer '.Init::$testUser1JWT])
    ->then(function(\Ratchet\Client\WebSocket $conn) {
        $conn->on('message', function(\Ratchet\RFC6455\Messaging\MessageInterface $msg) use ($conn) {
            echo "Message Received:". $msg;
            if ($msg == 'Close') {
                $conn->close();
            }
        });

        $conn->on('close', function($code = null, $reason = null) {
            echo "Connection closed ({$code} - {$reason})\n";
        });
        $this->conn = $conn;
        $this->conn->send("OPEN\r\n");
        $this->sendChunk('TEXT',\json_encode(new Payload('','WHOAMI','')));
    }, function(\Exception $e) use ($loop) {
        $loop->stop();
    });

$loop->run();



