<?php


namespace App\Chart;


use \SVG\SVG;
use \SVG\Nodes\Shapes\SVGPolyline;

class DynamicChart
{
    const BORDER_OFFSET = 75;

    const Y_NUM_MARKUP = 10;

    /**
     * @var SVG
     */
    private SVG $svg;

    /**
     * @var CoordinatePlaneBuilder
     */
    private CoordinatePlaneBuilder $coordinatePlaneBuilder;

    /**
     * DynamicChart constructor.
     * @param CoordinatePlaneBuilder $coordinatePlaneBuilder
     * @param SVG $svg
     */
    public function __construct(CoordinatePlaneBuilder $coordinatePlaneBuilder, SVG $svg)
    {
        $this->svg = $svg;
        $this->coordinatePlaneBuilder = $coordinatePlaneBuilder;
    }

    /**
     * @param array $dates
     * @param array $values
     * @return array|SVG
     * @throws \App\Exception\CoordinatePlaneException
     */
    public function draw(array $dates, array $values)
    {
        $min = min($values);
        $pixelStep = (max($values) - $min) / (self::Y_NUM_MARKUP - 1);
        $axisYMark = [];

        for ($i = 0; $i < self::Y_NUM_MARKUP; $i++) {
            $axisYMark[] = round($min + $i * $pixelStep, 2);
        }

        $axisYMark = array_values(array_unique($axisYMark));

        $plane = $this->coordinatePlaneBuilder
            ->setSVG($this->svg->getDocument())
            ->setOrigin(self::BORDER_OFFSET, $this->originY())
            ->setAxisLength($this->axisXLength(), $this->axisYLength())
            ->setAxisMarkup($dates, $axisYMark)
            ->build();

        $plane->draw();

        $this->polyline($values);

        return $this->svg;
    }

    /**
     * @param array $values
     */
    private function polyline(array $values)
    {
        $nPoint = count($values);
        $pixelStepY = $this->axisYLength() / $nPoint;
        $minValue = min($values);
        $maxValue = max($values);

        $maxPixel = $this->originY();
        $minPixel = $this->originY() - $nPoint * $pixelStepY;
        $pixelStepX = $this->axisXLength() / ($nPoint - 1);
        $points = [];

        for ($i = 0; $i < $nPoint; $i++) { // convert values to pixel
            $y = $maxPixel + ($minPixel - $maxPixel) * ($values[$i] - $minValue) / ($maxValue - $minValue);
            $points[] = [self::BORDER_OFFSET + $i * $pixelStepX, $y];
        }

        $polyline = new SVGPolyline($points);
        $polyline->setStyle('stroke-width', '2px')
            ->setStyle('fill', 'none')
            ->setStyle('stroke', '#ff0000');

        $this->svg->getDocument()->addChild($polyline);
    }

    /**
     *
     * Return axis X pixel length
     *
     * @return float|int|string
     */
    private function axisXLength()
    {
        return $this->svg->getDocument()->getWidth() - 2 * self::BORDER_OFFSET;
    }

    /**
     *
     * Return axis Y pixel length
     *
     * @return float|int|string
     */
    private function axisYLength()
    {
        return $this->svg->getDocument()->getHeight() - 2 * self::BORDER_OFFSET;
    }

    /**
     *
     * Return pixel Y origin
     *
     * @return int|string
     */
    private function originY()
    {
        return $this->svg->getDocument()->getHeight() - self::BORDER_OFFSET;
    }

    /**
     *
     * Return pixel X Origin
     *
     * @return int|string
     */
    private function originX()
    {
        return $this->svg->getDocument()->getWidth() - self::BORDER_OFFSET;
    }
}