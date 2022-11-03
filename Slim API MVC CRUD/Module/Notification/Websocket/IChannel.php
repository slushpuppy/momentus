<?php


namespace Module\Notification\Websocket;


interface IChannel
{
    /**
     * @return string
     */
    public function getChannelName();

}