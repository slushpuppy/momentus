<?php

namespace Tests\app\websocket;

use app\websocket\Model\Payload;
use Module\Notification\Websocket\Publish;
use PHPUnit\Framework\TestCase;
use Tests\Init;


class TestIndex extends TestCase
{
    public $conn;
    public function testConnect() {
        ;
        $loop = \React\EventLoop\Factory::create();
        $reactConnector = new \React\Socket\Connector($loop, [
        ]);
        $connector = new \Ratchet\Client\Connector($loop, $reactConnector);

        $connector('ws://ws1.rust.bike/app/websocket/index.php')//,[],['Authorization' => 'Bearer '.Init::$testUser1JWT])
            ->then(function(\Ratchet\Client\WebSocket $conn) {
                $conn->on('message', function(\Ratchet\RFC6455\Messaging\MessageInterface $msg) use ($conn) {
                });

                $conn->on('close', function($code = null, $reason = null) {
                    echo "Connection closed ({$code} - {$reason})\n";
                });
                $this->conn = $conn;
                $this->conn->send("OPEN\r\n");
            }, function(\Exception $e) use ($loop) {
                $this->assertStringContainsString("HTTP/1.1 401 Unauthorized",$e->getMessage());
                $loop->stop();
            });

        $loop->run();

    }

    public function testLogin() {
        $msg_state = 0;
        $loop = \React\EventLoop\Factory::create();
        $reactConnector = new \React\Socket\Connector($loop, [
        ]);
        $connector = new \Ratchet\Client\Connector($loop, $reactConnector);

        $connector('ws://ws1.rust.bike/app/websocket/index.php',[],['Authorization' => 'Bearer '.Init::$testUser1JWT])
        ->then(function(\Ratchet\Client\WebSocket $conn) use (&$msg_state) {

            $conn->on('message', function(\Ratchet\RFC6455\Messaging\MessageInterface $msg) use ($conn,&$msg_state) {
                echo $msg;
                if ($msg_state == 1) {
                    $this->assertStringContainsString("\"WHOAMI\",\"data\":\"1\"",$msg);
                    Publish::I()->send('social_group-1',"test");
                    //var_dump($msg_state);
                }
                if ($msg_state == 2) {
                    $this->assertStringContainsString("test",$msg);
                    Publish::I()->send('user-1',"test11");
                    //var_dump($msg_state);
                }
                if ($msg_state == 3) {
                    $this->assertStringContainsString("test11",$msg);
                    //var_dump($msg_state);
                    $conn->close();
                }
                $msg_state++;

            });

            $conn->on('close', function($code = null, $reason = null) {
                echo "Connection closed ({$code} - {$reason})\n";
            });
            $this->conn = $conn;
            $this->conn->send("OPEN\r\n");
            $this->sendChunk('TEXT',\json_encode(new Payload('','WHOAMI','')));
            $msg_state++;
        }, function(\Exception $e) use ($loop) {
            $loop->stop();
        });

        $loop->run();
        $this->assertEquals(4,$msg_state);
    }


    public function sendChunk(string $event,string $content) {
        $this->conn->send($event . " ".dechex(\mb_strlen($content))."\r\n");
        $this->conn->send($content."\r\n");
    }

}
