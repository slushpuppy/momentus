<?php


namespace Module\Notification\Websocket;


use \Config\Websocket;
use \GripControl\GripControl;
use \GripControl\GripPubControl;
use GripControl\WebSocketMessageFormat;
use PubControl\Item;

class Publish
{
    private static $_i;

    /**
     * @var GripControl[]
     */
    protected $clients;

    private function __construct()
    {
        foreach (Websocket::PUSH_SERVERS as $server) {
            $this->clients[] = new GripPubControl(array(
                'control_uri' => $server
            ));
        }
    }
    public static function I() {
        if (self::$_i == NULL) {
            self::$_i = new Publish();
        }
        return self::$_i;
    }

    /**
     * @param string $channel
     * @param string $content
     */
    public function send(string $channel,string $content) {
        foreach ($this->clients as $client) {
            $item = new Item(
                new WebSocketMessageFormat($content));
            $client->publish($channel, $item);
        }
    }


}