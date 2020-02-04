<?php
/**
 * Created by PhpStorm.
 * User: denis
 * Date: 10/22/19
 * Time: 11:17 PM
 */

require __DIR__ . '/../vendor/autoload.php';

use App\Container;
use Longman\TelegramBot\TelegramLog;
use Symfony\Component\Dotenv\Dotenv;

$dotenv = new Dotenv();
$dotenv->load(__DIR__ . '/../.env');

$containerBuilder = new \DI\ContainerBuilder();
$containerBuilder->addDefinitions(__DIR__ . '/../config/dependencies.php');
Container::register($containerBuilder->build());

$botConfig = require __DIR__ . '/../config/bot.php';

try {
    TelegramLog::initialize(Container::get('debugErrorBotLogger'), Container::get('updateBotLogger'));

    $bot = Container::get(\App\Bot::class);

    if (!empty($_GET['set_webhook'])) {
        $bot->setWebhook($botConfig['webhook']['url']);
    } else {
        $bot->start();
    }
} catch (Throwable $e) {
    TelegramLog::error($e->getMessage());
}

