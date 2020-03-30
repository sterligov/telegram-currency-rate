<?php


namespace Longman\TelegramBot\Commands\SystemCommands;


use App\BotCommand\CurrencyCommand;
use App\Container;
use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;

/**
 * Generic message command
 *
 * Gets executed when any type of message is sent.
 */
class GenericmessageCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'genericmessage';

    /**
     * @var string
     */
    protected $description = 'Handle generic message';

    /**
     * @var string
     */
    protected $version = '1.1.0';

    /**
     * @var bool
     */
    protected $need_mysql = false;

    /**
     * GenericmessageCommand constructor.
     * @param Telegram $telegram
     * @param Update|null $update
     */
    public function __construct(Telegram $telegram, Update $update = null)
    {
        parent::__construct($telegram, $update);
    }

    /**
     * Command execute method if MySQL is required but not available
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function executeNoDb()
    {
        return Request::emptyResponse();
    }

    /**
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function execute()
    {
        $command = Container::get(CurrencyCommand::class);
        return $command->execute($this->getMessage());
    }
}