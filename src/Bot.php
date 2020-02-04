<?php
/**
 * Created by PhpStorm.
 * User: denis
 * Date: 10/22/19
 * Time: 11:27 PM
 */

namespace App;

use Longman\TelegramBot\Commands\SystemCommands\CallbackqueryCommand;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Telegram;
use Longman\TelegramBot\TelegramLog;

class Bot
{
    private Telegram $bot;

    public function __construct(Telegram $bot)
    {
        $this->bot = $bot;
    }

    /**
     * @throws TelegramException
     */
    public function start()
    {
        $this->initCallbackQuery();
        $this->bot->handle();
    }

    /**
     * @param string $webHook
     * @throws TelegramException
     */
    public function setWebhook(string $webHook)
    {
        $this->bot->deleteWebhook();
        $result = $this->bot->setWebhook($webHook);

        if (!$result->isOk()) {
            throw new TelegramException('Cannot set webhook');
        }
    }

    private function initCallbackQuery()
    {
        CallbackqueryCommand::addCallbackHandler(function ($query) {
            $data = explode(' ', $query->data);

            if (!Container::has($data[0])) {
                TelegramLog::error("Event {$data[0]} not found");
                return;
            }

            $event = Container::get($data[0]);
            $event->handle(array_slice($data, 1));
        });
    }
}