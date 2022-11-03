<?php
// composer require fanout/gripcontrol
require(__DIR__.'/../../autoload.php');

use \app\websocket\Controller;
use \app\websocket\Model\Payload;
use GripControl\GripControl;
use GripControl\GripPubControl;
use \GripControl\WebSocketEvent;

define('SOURCE_MODULE','websocket/guest');


$in_events = GripControl::decode_websocket_events(
    file_get_contents("php://input"));
$out_events = array();
foreach($in_events as $in_event)
{
    switch ($in_event->type) {
        case 'OPEN':
            Controller::I()->addOutputEvent('OPEN');


            break;

        case 'TEXT':
            if (strpos($in_event->content,'SUB ') === 0) {
                Controller::I()->subscribe(substr($in_event->content,4));
            }


            break;
        case 'BINARY':
            break;
        case 'PING':
            break;
        case 'CLOSE':
            break;
        case 'DISCONNECT':
            break;
    }
}

header('Content-Type: application/websocket-events');
header('Sec-WebSocket-Extensions: grip');
http_response_code(200);

echo GripControl::encode_websocket_events(Controller::I()->getOutputEvents());

?>

