<?php


namespace app\websocket\Model;


class Payload
{
    public $handler;
    public $event;
    public $data;

    /**
     * Payload constructor.
     * @param $handler
     * @param $type
     * @param $data
     */
    public function __construct(string $handler = '', string $event = '', $data = '')
    {
        $this->handler = $handler;
        $this->event = $event;
        $this->data = $data;
    }


    public function __toString()
    {
        return "m:".\json_encode($this);
    }
}