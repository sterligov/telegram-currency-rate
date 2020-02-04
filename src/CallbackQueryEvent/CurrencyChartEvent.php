<?php


namespace App\CallbackQueryEvent;


use App\Chart\PeriodCurrencyChart;
use App\Chart\SvgConverterInterface;
use App\TelegramRequestInterface;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\TelegramLog;
use Symfony\Component\Filesystem\Filesystem;

class CurrencyChartEvent implements EventInterface
{
    /**
     * @var PeriodCurrencyChart
     */
    private PeriodCurrencyChart $chart;

    /**
     * @var SvgConverterInterface
     */
    private SvgConverterInterface $svgConverter;

    /**
     * @var Filesystem
     */
    private Filesystem $filesystem;

    /**
     * @var TelegramRequestInterface
     */
    private TelegramRequestInterface $request;

    /**
     * CurrencyChartEvent constructor.
     * @param PeriodCurrencyChart $chart
     * @param SvgConverterInterface $svgConverter
     * @param Filesystem $filesystem
     * @param TelegramRequestInterface $request
     */
    public function __construct(
        PeriodCurrencyChart $chart,
        SvgConverterInterface $svgConverter,
        Filesystem $filesystem,
        TelegramRequestInterface $request
    ) {
        $this->chart = $chart;
        $this->svgConverter = $svgConverter;
        $this->filesystem = $filesystem;
        $this->request = $request;
    }

    /**
     *
     * $data[0] - fromCurrency
     * $data[1] - toCurrency
     * $data[2] - startDate
     * $data[3] - toDate
     * $data[4] - chatID
     *
     * @param mixed $data
     * @return mixed|void
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function handle($data)
    {
        try {
            $dates = $this->separateDates($data[2], $data[3]);
            $image = $this->getImageName($data[0], $data[1], $data[2], $data[3]);

            if (!$this->filesystem->exists("$image.png")) {
                $svg = $this->chart->draw($data[0], $data[1], $dates);
                $this->svgConverter->convert($image, $svg->toXMLString(), 'png');
            }
        } catch (\Exception $e) {
            TelegramLog::error($e->getMessage());
            $this->request->sendMessage([
                'chat_id' => $data[4],
                'text' => 'Error! Generate chart error'
            ]);
            return;
        }

        $this->request->sendPhoto([
            'chat_id' => $data[4],
            'photo' => $this->request->encodeFile("$image.png")
        ]);
    }

    /**
     * @param $startDate
     * @param $endDate
     * @return array
     * @throws \Exception
     */
    private function separateDates($startDate, $endDate)
    {
        $end = new \DateTime($endDate);
        $start = new \DateTime($startDate);
        $dayDiff = $start->diff($end)->days;

        $separatedDates = [];
        $separatedDates[] = $end->format('d/m/Y');
        $period = $dayDiff <= 90 ? '-5 days' : '-1 month';

        do {
            $end->modify($period);
            $separatedDates[] = $end->format('d/m/Y');
        } while ($start->diff($end)->invert == 0);

        return array_reverse($separatedDates);
    }

    /**
     * @param $fromCurrency
     * @param $toCurrency
     * @param $startDate
     * @param $endDate
     * @return string
     */
    private function getImageName($fromCurrency, $toCurrency, $startDate, $endDate)
    {
        $startDate = str_replace('/', '-', $startDate);
        $endDate = str_replace('/', '-', $endDate);

        return __DIR__ . "/../../files/{$fromCurrency}_{$toCurrency}_{$startDate}_{$endDate}";
    }
}