<?php


namespace App;


use Longman\TelegramBot\Entities\ServerResponse;

interface TelegramRequestInterface
{
    /**
     * @return ServerResponse
     */
    public function sendEmpty(): ServerResponse;

    /**
     * @param array $data
     * @return ServerResponse
     */
    public function sendMessage(array $data): ServerResponse;

    /**
     * @param array $data
     * @return ServerResponse
     */
    public function sendPhoto(array $data): ServerResponse;

    /**
     * @param string $file
     * @return mixed
     */
    public function encodeFile(string $file);
}