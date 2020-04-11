<?php
/**
 * Created by PhpStorm.
 * User: denis
 * Date: 10/23/19
 * Time: 7:52 PM
 */

return [
    'bot.commands' => [
        __DIR__ . '/../src/BotCommand'
    ],
    'log.debug' => __DIR__ . "/../log/{$_ENV['TG_BOT_NAME']}_debug.log",
    'log.error' => __DIR__ . "/../log/{$_ENV['TG_BOT_NAME']}_error.log",
    'log.update' => __DIR__ . "/../log/{$_ENV['TG_BOT_NAME']}_update.log",
];
