<?php

use App\TelegramRequest;
use App\TelegramRequestInterface;
use Longman\TelegramBot\Telegram;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use \Psr\Container\ContainerInterface;
use \Symfony\Component\Cache\Adapter\FilesystemAdapter;
use \Psr\Cache\CacheItemPoolInterface;
use \App\Chart\DynamicChart;
use \App\CallbackQueryEvent\CurrencyChartEvent;
use \App\Chart\SvgConverterInterface;
use \App\Chart\InkscapeConverter;
use App\Currency\PeriodCurrencyRateInterface;
use \App\Currency\RussianCentralBank;
use \App\Chart\CoordinatePlaneBuilder;
use \App\BotCommand\CurrencyCommand;

$botConfig = require __DIR__ . '/bot.php';

return [
    'debugErrorBotLogger' => function(ContainerInterface $c) use ($botConfig) {
        $debugHandler = new StreamHandler($botConfig['logging']['debug'], Logger::DEBUG);
        $debugHandler->setFormatter(new LineFormatter(null, null, true));

        $errorHandler = new StreamHandler($botConfig['logging']['error'], Logger::ERROR);
        $errorHandler->setFormatter(new LineFormatter(null, null, true));

        return new Logger('telegram_bot', [$debugHandler, $errorHandler]);
    },

    'updateBotLogger' => function(ContainerInterface $c) use ($botConfig) {
        $updateHandler = new StreamHandler($botConfig['logging']['update'], Logger::INFO);
        $updateHandler->setFormatter(new LineFormatter('%message%' . PHP_EOL));

        return new Logger('telegram_bot_updates', [$updateHandler]);
    },

    CacheItemPoolInterface::class => \Di\Create(FilesystemAdapter::class),

    TelegramRequestInterface::class => \Di\Create(TelegramRequest::class),

    'currencyChart' => \Di\autowire(CurrencyChartEvent::class),

    DynamicChart::class => Di\Create(DynamicChart::class)
        ->constructor(new CoordinatePlaneBuilder(), new \SVG\SVG(1200, 1000)),

    SvgConverterInterface::class => \Di\autowire(InkscapeConverter::class),

    PeriodCurrencyRateInterface::class => \Di\autowire(RussianCentralBank::class),

    CurrencyCommand::class => Di\Create(CurrencyCommand::class)
        ->constructor(
            \Di\get(TelegramRequestInterface::class),
            \Di\get(\App\Currency\PipelineConverter::class),
            \Di\get(CacheItemPoolInterface::class)
        ),

    Telegram::class => DI\create(Telegram::class)
        ->constructor($botConfig['token'], $botConfig['bot_username'])
        ->method('addCommandsPaths', $botConfig['commands']['paths']),
];

