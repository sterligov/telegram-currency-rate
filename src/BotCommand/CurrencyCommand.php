<?php


namespace App\BotCommand;


use App\Currency\ConverterInterface;
use App\Currency\Currency;
use App\Exception\CurrencyCommandException;
use App\TelegramRequestInterface;
use App\UI\ChartInlineKeyboard;
use Longman\TelegramBot\Entities\Message;
use Longman\TelegramBot\TelegramLog;
use \Longman\TelegramBot\Entities\ServerResponse;
use Psr\Cache\CacheItemPoolInterface;

class CurrencyCommand implements CommandInterface
{
    /**
     * @var ConverterInterface
     */
    private ConverterInterface $converter;

    /**
     * @var TelegramRequestInterface
     */
    private TelegramRequestInterface $request;

    /**
     * GenericMessage constructor.
     * @param TelegramRequestInterface $request
     * @param ConverterInterface $converter
     * @param CacheItemPoolInterface $cache
     */
    public function __construct(
        TelegramRequestInterface $request,
        ConverterInterface $converter
    ) {
        $this->request = $request;
        $this->converter = $converter;
    }

    /**
     * @param Message $message
     * @return ServerResponse
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function execute(Message $message): ServerResponse
    {
        try {
            [$fromCurrency, $toCurrency] = $this->parse($message->getText(true));

            $replyMarkup = [];
            $text = 'No data was found for the specified currency pair';
            $rate = $this->converter->convert($fromCurrency, $toCurrency);

            if ($rate) {
                $keyboard = new ChartInlineKeyboard($message->getChat()->getId(), $fromCurrency, $toCurrency);
                $replyMarkup = $keyboard->build();
                $text = round($rate, 2);
            }

            $data = [
                'chat_id' => $message->getChat()->getId(),
                'reply_markup' => $replyMarkup,
                'text' => $text,
            ];

            return $this->request->sendMessage($data);
        } catch (\Exception $e) {
            TelegramLog::error($e->getMessage());

            return $this->request->sendEmpty();
        }
    }

    /**
     * @param string $message
     * @return array
     * @throws CurrencyCommandException
     */
    private function parse(string $message): array
    {
        $message = trim($message);
        $jsonText = trim(json_encode($message), '"');

        if (mb_substr($jsonText, 0, 2) === '\u') { // flag
            $currency = explode(' ', $jsonText);

            if (count($currency) === 1) {
                $currency = [];
                $currency[] = mb_substr($jsonText, 0, mb_strlen($jsonText) / 2);
                $currency[] = mb_substr($jsonText,  mb_strlen($jsonText) / 2);
            }
        } else {
            $currency = explode(' ', $message, 2);
        }

        if (count($currency) < 2) {
            throw new CurrencyCommandException("Cannot get currency pair $message");
        }

        return [new Currency($currency[0]), new Currency($currency[1])];
    }
}