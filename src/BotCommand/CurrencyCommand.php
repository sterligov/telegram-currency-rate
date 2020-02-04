<?php


namespace App\BotCommand;


use App\Currency\CodeConverter;
use App\Currency\ConverterInterface;
use App\TelegramRequestInterface;
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
     * @var CacheItemPoolInterface
     */
    private CacheItemPoolInterface $cache;

    /**
     * GenericMessage constructor.
     * @param TelegramRequestInterface $request
     * @param ConverterInterface $converter
     * @param CacheItemPoolInterface $cache
     */
    public function __construct(
        TelegramRequestInterface $request,
        ConverterInterface $converter,
        CacheItemPoolInterface $cache
    ) {
        $this->request = $request;
        $this->converter = $converter;
        $this->cache = $cache;
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
            $rate = $this->getRate($fromCurrency, $toCurrency);

            if ($rate) {
                $replyMarkup = $this->getInlineKeyboard($message->getChat()->getId(), $fromCurrency, $toCurrency);
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
     * @param $from
     * @param $to
     * @return mixed
     * @throws \Psr\Cache\InvalidArgumentException
     */
    private function getRate($from, $to)
    {
        $key = 'currency.' . CodeConverter::toAlpha($from) . '.' . CodeConverter::toAlpha($to);
        $rateCache = $this->cache->getItem($key);

        if (!$rateCache->isHit()) {
            try {
                $rate = $this->converter->convert($from, $to);
                $expireDate = (new \DateTime())
                    ->modify('+1 day')
                    ->setTime(0, 0, 0);
                $rateCache->expiresAt($expireDate);
                $rateCache->set($rate);
                $this->cache->save($rateCache);
            } catch (\Exception $e) {
                TelegramLog::debug($e->getMessage());
            }
        }

        return $rateCache->get();
    }

    /**
     * @param string $message
     * @return array
     */
    private function parse(string $message): array
    {
        $message = trim($message);
        $jsonText = trim(json_encode($message), '"');

        if (mb_substr($jsonText, 0, 2) === '\u') {
            $currency = explode(' ', $jsonText);

            if (count($currency) === 1) {
                $currency = [];
                $currency[] = mb_substr($jsonText, 0, mb_strlen($jsonText) / 2);
                $currency[] = mb_substr($jsonText,  mb_strlen($jsonText) / 2);
            }
        } else {
            $currency = explode(' ', $message, 2);
        }

        return $currency;
    }

    /**
     * @param $chatID
     * @param $from
     * @param $to
     * @return array
     * @throws \Exception
     */
    private function getInlineKeyboard($chatID, $from, $to)
    {
        $from = CodeConverter::toAlpha($from);
        $to = CodeConverter::toAlpha($to);

        $callbackData = [
            'currencyChart',
            $from,
            $to,
            (new \DateTime())->modify('-1 month')->format('Y-m-d'),
            (new \DateTime())->format('Y-m-d'),
            $chatID
        ];

        $data[0] = implode(' ', $callbackData);
        $callbackData[3] = (new \DateTime())->modify('-6 month')->format('Y-m-d');
        $data[1] = implode(' ', $callbackData);
        $callbackData[3] = (new \DateTime())->modify('-1 year')->format('Y-m-d');
        $data[2] = implode(' ', $callbackData);

        return [
            'inline_keyboard' => [
                [
                    ['text' => '1 month chart', 'callback_data' => $data[0]],
                    ['text' => '6 month chart', 'callback_data' => $data[1]],
                    ['text' => '1 year chart', 'callback_data' => $data[2]],
                ],
            ],
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ];
    }
}