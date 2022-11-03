<?php
// composer require fanout/gripcontrol
require(__DIR__.'/../../autoload.php');

use \app\websocket\Controller;
use \app\websocket\Model\Payload;
use GripControl\GripControl;
use GripControl\GripPubControl;
use \GripControl\WebSocketEvent;

define('SOURCE_MODULE','websocket/index');


$content = file_get_contents("php://input");
$in_events = GripControl::decode_websocket_events(
    $content);
$out_events = array();



$activeUserId = 0;
if ($_SERVER['HTTP_META_USER'])
$activeUserId = $_SERVER['HTTP_META_USER'];


foreach($in_events as $in_event)
{
    switch ($in_event->type) {
        case 'OPEN':
            Controller::I()->addOutputEvent('OPEN');
            $payload = \Module\OAuth\JWT\Payload::loadPayloadWithJWT(substr($_SERVER['HTTP_AUTHORIZATION'],7));
            if ($payload) {
                header("Set-Meta-User: ".$payload->user_id);
                $activeUserId = $payload->user_id;
                $channels = \Module\Notification\Websocket\Channel::loadChannelsFromUserId($activeUserId);
                foreach($channels as $channel)
                {
                    Controller::I()->subscribe($channel->channel_name);
                }
            } else {
                Controller::I()->setHttpCode(401);
            }


            break;

        case 'TEXT':
            if (strpos($in_event->content,'{"') === 0) {
                $decoded_content = json_decode($in_event->content);
                if ($decoded_content) {
                    //error_log(print_r($decoded_content,TRUE),0);

                    $payload = new Payload();
                    \Lib\Core\Common::copyObjectProperties($decoded_content,$payload);

                    //error_log(print_r($payload,TRUE),0);
                    if ($payload->event == 'WHOAMI') {
                        Controller::I()->addOutputMessageEvent(new Payload('','WHOAMI',$activeUserId));
                    }
                    //error_log(print_r($decoded_content,TRUE),0);
                    //$out_events[] = new WebSocketEvent('TEXT', "m:OK");
                }
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
http_response_code(Controller::I()->getHttpCode());

error_log(print_r(Controller::I()->getOutputEvents(),TRUE),0);

echo GripControl::encode_websocket_events(Controller::I()->getOutputEvents());
?>
