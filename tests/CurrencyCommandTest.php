<?php


namespace App\Tests;

use App\BotCommand\CurrencyCommand;
use App\Currency\ConverterInterface;
use App\TelegramRequestInterface;
use Longman\TelegramBot\Entities\Message;
use Longman\TelegramBot\Entities\ServerResponse;
use PHPUnit\Framework\TestCase;

class CurrencyCommandTest extends TestCase
{
    public function testExecute()
    {
        $from = 'USD';
        $to = 'RUB';
        $chatID = 1;

        $callbackData = [
            'currencyChart',
            $from,
            $to,
            (new \DateTime())->modify('-1 month')->format('Y-m-d'),
            (new \DateTime())->format('Y-m-d'),
            1
        ];

        $data[0] = implode(' ', $callbackData);
        $callbackData[3] = (new \DateTime())->modify('-6 month')->format('Y-m-d');
        $data[1] = implode(' ', $callbackData);
        $callbackData[3] = (new \DateTime())->modify('-1 year')->format('Y-m-d');
        $data[2] = implode(' ', $callbackData);

        $replyMarkup = [
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

        $rate = 2.0;

        $expected = [
            'chat_id' => $chatID,
            'reply_markup' => $replyMarkup,
            'text' => $rate,
        ];

        $command = new CurrencyCommand(
            $this->requestMock(),
            $this->converterMock($rate)
        );

        $message = $this->messageMock($from, $to, $chatID);

        $this->assertEquals($expected, $command->execute($message)->data);
    }

    public function testErrorWithZeroRate()
    {
        $from = 'EUR';
        $to = 'USD';
        $chatID = 3;
        $rate = 0.0;

        $expected = [
            'chat_id' => $chatID,
            'reply_markup' => [],
            'text' => 'No data was found for the specified currency pair',
        ];

        $command = new CurrencyCommand(
            $this->requestMock(),
            $this->converterMock($rate)
        );

        $message = $this->messageMock($from, $to, $chatID);

        $this->assertEquals($expected, $command->execute($message)->data);
    }

    private function messageMock($from, $to, $chatID)
    {
        $message = $this->getMockBuilder(Message::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getText'])
            ->addMethods(['getId', 'getChat'])
            ->getMock();

        $message->method('getChat')
            ->willReturnSelf();

        $message->method('getId')
            ->willReturn($chatID);

        $message->method('getText')
            ->willReturn("$from $to");

        return $message;
    }

    private function converterMock($rate)
    {
        $converter = $this->createMock(ConverterInterface::class);
        $converter->method('convert')
            ->willReturn($rate);

        return $converter;
    }

    private function requestMock()
    {
        $request = $this->createMock(TelegramRequestInterface::class);
        $response = fn ($data = []) => new class($data, '') extends ServerResponse {
            public array $data;

            public function __construct(array $data, $botUsername)
            {
                $this->data = $data;
            }
        };
        $request->method('sendMessage')
            ->willReturnCallback($response);
        $request->method('sendEmpty')
            ->willReturnCallback($response);

        return $request;
    }
}
