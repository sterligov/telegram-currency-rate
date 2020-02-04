<?php


namespace App;


use \Longman\TelegramBot\Request;
use Longman\TelegramBot\Entities\ServerResponse;

class TelegramRequest implements TelegramRequestInterface
{
    /**
     * @param array $data
     * @return ServerResponse
     */
    public function sendPhoto(array $data): ServerResponse
    {
        return Request::sendPhoto($data);
    }

    /**
     * @param array $data
     * @return ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function sendMessage(array $data): ServerResponse
    {
        return Request::sendMessage($data);
    }

    /**
     * @return ServerResponse
     */
    public function sendEmpty(): ServerResponse
    {
        return Request::emptyResponse();
    }

    /**
     * @param string $file
     * @return mixed|resource
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function encodeFile(string $file)
    {
        return Request::encodeFile($file);
    }
}