<?php
namespace app\websocket;

use app\websocket\Model\Payload;
use GripControl\GripControl;
use GripControl\WebSocketEvent;

class Controller {

    private static $_i;

    protected $outputEvents;
    protected $http_code;

    private function __construct()
    {
        $this->outputEvents = [];
        $this->http_code = 200;
    }
    public static function I() {
        if (self::$_i == NULL) {
            self::$_i = new Controller();
        }
        return self::$_i;
    }

    /**
     * @param string $type
     * @param $output
     */
    public function addOutputEvent(string $type,$output = '') {
        $this->outputEvents[] = new WebSocketEvent($type,$output);
    }

    /**
     * @param Payload $payload
     */
    public function addOutputMessageEvent(Payload $payload) {
        $this->outputEvents[] = new WebSocketEvent('TEXT', 'm:'.(string) $payload);
    }

    public function subscribe(string $channel) {
        $this->addOutputControlEvent('subscribe',['channel'=>$channel]);
    }

    /**
     * @param string $type
     * @param array $args
     */
    public function addOutputControlEvent(string $type,array $args = []) {
        $this->outputEvents[] = new WebSocketEvent('TEXT', 'c:' .
            GripControl::websocket_control_message($type,
                $args));
    }

    /**
     * @param int $http_code
     * @return Controller
     */
    public function setHttpCode(int $http_code)
    {
        $this->http_code = $http_code;
    }

    /**
     * @return array
     */
    public function getOutputEvents(): array
    {
        return $this->outputEvents;
    }

    /**
     * @return int
     */
    public function getHttpCode(): int
    {
        return $this->http_code;
    }

}