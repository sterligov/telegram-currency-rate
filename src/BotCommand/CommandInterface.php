<?php


namespace App\BotCommand;

use Longman\TelegramBot\Entities\Message;
use \Longman\TelegramBot\Entities\ServerResponse;

interface CommandInterface
{
    /**
     * @param Message $message
     * @return ServerResponse
     */
    public function execute(Message $message): ServerResponse;
}
