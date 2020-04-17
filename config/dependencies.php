<?php

use App\TelegramRequest;
use App\TelegramRequestInterface;
use Longman\TelegramBot\Telegram;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use \Psr\Container\ContainerInterface;
use \Symfony\Component\Cache\Adapter\RedisAdapter;
use \Psr\Cache\CacheItemPoolInterface;
use \App\Chart\DynamicChart;
use \App\CallbackQueryEvent\CurrencyChartEvent;
use \App\Chart\SvgConverterInterface;
use \App\Chart\InkscapeConverter;
use App\Currency\PeriodCurrencyRateInterface;
use \App\Currency\RussianCentralBank;
use \App\Chart\CoordinatePlaneBuilder;
use \App\BotCommand\CurrencyCommand;
use \App\Currency\CachingConverter;
use \App\Currency\PipelineConverter;

return [
    'debugErrorBotLogger' => function(ContainerInterface $c) {

        $debugHandler = new StreamHandler($c->get('log.debug'), Logger::DEBUG);
        $debugHandler->setFormatter(new LineFormatter(null, null, true));

        $errorHandler = new StreamHandler($c->get('log.error'), Logger::ERROR);
        $errorHandler->setFormatter(new LineFormatter(null, null, true));

        return new Logger('telegram_bot', [$debugHandler, $errorHandler]);
    },

    'updateBotLogger' => function(ContainerInterface $c) {
        $updateHandler = new StreamHandler($c->get('log.update'), Logger::INFO);
        $updateHandler->setFormatter(new LineFormatter('%message%' . PHP_EOL));

        return new Logger('telegram_bot_updates', [$updateHandler]);
    },

    CacheItemPoolInterface::class => function(ContainerInterface $c) {
        $host = $_ENV['REDIS_HOST'];
        $port = $_ENV['REDIS_PORT'];
        $client = RedisAdapter::createConnection("redis://$host:$port");

        return new RedisAdapter($client);
    },

    TelegramRequestInterface::class => \Di\Create(TelegramRequest::class),

    'currencyChart' => \Di\autowire(CurrencyChartEvent::class),

    DynamicChart::class => Di\Create(DynamicChart::class)
        ->constructor(new CoordinatePlaneBuilder(), new \SVG\SVG(1200, 1000)),

    SvgConverterInterface::class => \Di\autowire(InkscapeConverter::class),

    PeriodCurrencyRateInterface::class => \Di\autowire(RussianCentralBank::class),

    CachingConverter::class => \Di\Create(CachingConverter::class)
        ->constructor(
            \Di\get(PipelineConverter::class),
            \Di\get(CacheItemPoolInterface::class),
        ),

    CurrencyCommand::class => Di\Create(CurrencyCommand::class)
        ->constructor(
            \Di\get(TelegramRequestInterface::class),
            \Di\get(CachingConverter::class),
            \Di\get(CacheItemPoolInterface::class)
        ),

    Telegram::class => DI\create(Telegram::class)
        ->constructor(\Di\env('TG_TOKEN'), $_ENV['TG_BOT_NAME'])
        ->method('addCommandsPaths', \Di\get('bot.commands')),
];

