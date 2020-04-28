<?php


namespace App\UI;

use App\Currency\Currency;

class ChartInlineKeyboard implements InlineKeyboardInterface
{
    /**
     * @var Currency
     */
    private Currency $fromCurrency;

    /**
     * @var Currency
     */
    private Currency $toCurrency;

    /**
     * @var mixed
     */
    private $chatID;

    public function __construct($chatID, Currency $from, Currency $to)
    {
        $this->chatID = $chatID;
        $this->fromCurrency = $from;
        $this->toCurrency = $to;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function build(): array
    {
        $fromCode = $this->fromCurrency->convertCode(Currency::ALPHA);
        $toCode = $this->toCurrency->convertCode(Currency::ALPHA);

        $callbackData = [
            'currencyChart',
            $fromCode,
            $toCode,
            (new \DateTime())->modify('-1 month')->format('Y-m-d'),
            (new \DateTime())->format('Y-m-d'),
            $this->chatID
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
