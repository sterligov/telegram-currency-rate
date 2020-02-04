<?php
/**
 * Created by PhpStorm.
 * User: denis
 * Date: 10/23/19
 * Time: 7:52 PM
 */

return [
    'token'      => $_ENV['TG_TOKEN'],
    'bot_username' => $_ENV['TG_BOT_NAME'],
    'webhook'      => [
        'url' => $_ENV['TG_WEBHOOK'],
    ],
    'commands' => [
        'paths'   => [
            __DIR__ . '/../src/BotCommand',
        ],
    ],
    'logging'  => [
        'debug'  => __DIR__ . "/../log/{$_ENV['TG_BOT_NAME']}_debug.log",
        'error'  => __DIR__ . "/../log/{$_ENV['TG_BOT_NAME']}_error.log",
        'update' => __DIR__ . "/../log/{$_ENV['TG_BOT_NAME']}_update.log",
    ],
];
