<?php
/**
 * Created by PhpStorm.
 * User: denis
 * Date: 10/23/19
 * Time: 3:39 PM
 */


namespace Longman\TelegramBot\Commands\SystemCommands;

use App\Currency\Currency;
use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Request;

class StartCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'start';
    /**
     * @var string
     */
    protected $description = 'Start command';
    /**
     * @var string
     */
    protected $usage = '/start';
    /**
     * @var string
     */
    protected $version = '1.1.0';
    /**
     * @var bool
     */
    protected $private_only = true;

    /**
     * Command execute method
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute()
    {
        $message = $this->getMessage();
        $text    = <<<EOD
Hi, there!
It's currency exchange rate bot.
You can use it in different formats.
For example to convert US Dollar to EURO send message with one of these content:
1. 840 978 (base on currency code)
2. USD EUR (base on currency letter code)
3. USA European Union (base on country name)
4. \xF0\x9F\x87\xBA\xF0\x9F\x87\xB8	\xF0\x9F\x87\xAA\xF0\x9F\x87\xBA (base on flags)
EOD;

        $pairs = [
            [Currency::CURRENCY['USD'], Currency::CURRENCY['RUB']],
            [Currency::CURRENCY['EUR'], Currency::CURRENCY['RUB']],
            [Currency::CURRENCY['USD'], Currency::CURRENCY['UZS']],
            [Currency::CURRENCY['RUB'], Currency::CURRENCY['UZS']],
            [Currency::CURRENCY['EUR'], Currency::CURRENCY['USD']],
            [Currency::CURRENCY['USD'], Currency::CURRENCY['UAH']],
            [Currency::CURRENCY['UAH'], Currency::CURRENCY['RUB']],
            [Currency::CURRENCY['USD'], Currency::CURRENCY['KZT']],
            [Currency::CURRENCY['RUB'], Currency::CURRENCY['KZT']],
            [Currency::CURRENCY['USD'], Currency::CURRENCY['BYN']],
            [Currency::CURRENCY['BYN'], Currency::CURRENCY['RUB']],
        ];
        $keyboard = [];

        foreach ($pairs as $pair) {
            $keyboard[] = json_decode("\"{$pair[0]['flag']}{$pair[1]['flag']}\"");
        }

        $replyMarkup = [
            'keyboard' => [$keyboard],
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ];

        $data = [
            'chat_id' => $message->getChat()->getId(),
            'reply_markup' => $replyMarkup,
            'text'    => $text,
        ];

        return Request::sendMessage($data);
    }
}
