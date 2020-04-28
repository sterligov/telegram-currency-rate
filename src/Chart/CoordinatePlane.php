<?php


namespace App\Chart;

use SVG\Nodes\Shapes\SVGLine;
use SVG\Nodes\Structures\SVGDocumentFragment;
use SVG\Nodes\Texts\SVGText;

/**
 *
 * Draw coordinate plane on a given svg
 *
 * Class CoordinatePlane
 * @package App\Chart
 */
class CoordinatePlane
{
    /**
     * @var float
     */
    private float $maxX = 0;

    /**
     * @var float
     */
    private float $maxY = 0;

    /**
     * @var float
     */
    private float $stepX = 0;

    /**
     * @var float
     */
    private float $stepY = 0;

    /**
     * @var SVGDocumentFragment|null
     */
    private ?SVGDocumentFragment $svg;

    /**
     * @var float
     */
    private float $originX = 0;

    /**
     * @var float
     */
    private float $originY = 0;

    /**
     * @var float
     */
    private float $axisXLength = 0;

    /**
     * @var float
     */
    private float $axisYLength = 0;

    /**
     * @var array
     */
    private array $axisXMarkup = [];

    /**
     * @var array
     */
    private array $axisYMarkup = [];

    /**
     * @param float $axisLength
     */
    public function setAxisXLength(float $axisLength)
    {
        $this->axisXLength = $axisLength;
    }

    /**
     * @param float $axisLength
     */
    public function setAxisYLength(float $axisLength)
    {
        $this->axisYLength = $axisLength;
    }

    /**
     * @param float $originX
     */
    public function setOriginX(float $originX)
    {
        $this->originX = $originX;
    }

    /**
     * @param float $originY
     */
    public function setOriginY(float $originY)
    {
        $this->originY = $originY;
    }

    /**
     * @param float $stepX
     */
    public function setStepX(float $stepX)
    {
        $this->stepX = $stepX;
    }

    /**
     * @param float $stepY
     */
    public function setStepY(float $stepY)
    {
        $this->stepY = $stepY;
    }

    /**
     * @param float $maxX
     */
    public function setMaxX(float $maxX)
    {
        $this->maxX = $maxX;
    }

    /**
     * @param float $maxY
     */
    public function setMaxY(float $maxY)
    {
        $this->maxY = $maxY;
    }

    /**
     * @param SVGDocumentFragment $svg
     */
    public function setSVG(SVGDocumentFragment $svg)
    {
        $this->svg = $svg;
    }

    /**
     * @param array $axisX
     */
    public function setAxisXMarkup(array $axisX)
    {
        $this->axisXMarkup = $axisX;
    }

    /**
     * @param array $axisY
     */
    public function setAxisYMarkup(array $axisY)
    {
        $this->axisYMarkup = $axisY;
    }

    public function draw()
    {
        $this->axis();

        if (!$this->axisXMarkup) {
            $nPoint = ceil($this->maxX / $this->stepX);
            for ($i = 0; $i <= $nPoint; $i++) {
                $this->axisXMarkup[] = $i * $this->stepX;
            }
        }

        if (!$this->axisYMarkup) {
            $nPoint = ceil($this->maxY / $this->stepY);
            for ($i = 0; $i <= $nPoint; $i++) {
                $this->axisYMarkup[] = $i * $this->stepY;
            }
        }

        $this->markup();
    }

    private function markup()
    {
        $nPoint = count($this->axisYMarkup);
        $pixelStep = $this->axisYLength / ($nPoint - 1);

        for ($i = 0; $i < $nPoint; $i++) {
            $y = $this->originY - $i * $pixelStep;
            $this->line($this->originX, $y, $this->originX + $this->axisXLength, $y, ['opacity' => '0.5']);
            $this->text($this->axisYMarkup[$i], $this->originX - 35, $y + 2.5);
        }

        $nPoint = count($this->axisXMarkup);
        $pixelStep = $this->axisXLength / ($nPoint - 1);

        for ($i = 0; $i < $nPoint; $i++) {
            $x = $this->originX + $i * $pixelStep;
            $this->line($x, $this->originY, $x, $this->originY - $this->axisYLength, ['opacity' => '0.5']);
            $this->text($this->axisXMarkup[$i], $x, $this->originY + 10, [
                'writing-mode' => 'tb',
                'glyph-orientation-vertical' => '90'
            ]);
        }
    }

    private function axis()
    {
        // Ox
        $this->line(
            $this->originX,
            $this->originY,
            $this->originX + $this->axisXLength,
            $this->originY
        );

        // Oy
        $this->line(
            $this->originX,
            $this->originY,
            $this->originX,
            $this->originY - $this->axisYLength
        );
    }

    /**
     * @param float $x1
     * @param float $y1
     * @param float $x2
     * @param float $y2
     * @param array $styles
     */
    private function line(float $x1, float $y1, float $x2, float $y2, array $styles = [])
    {
        $line = new SVGLine($x1, $y1, $x2, $y2);
        $line->setStyle('stroke-width', '1px')
            ->setStyle('stroke', '#000000');

        foreach ($styles as $name => $val) {
            $line->setStyle($name, $val);
        }

        $this->svg->addChild($line);
    }

    /**
     * @param string $text
     * @param float $x
     * @param float $y
     * @param array $styles
     */
    private function text(string $text, float $x, float $y, array $styles = [])
    {
        $text = (new SVGText($text, $x, $y))->setSize(10);

        foreach ($styles as $name => $val) {
            $text->setStyle($name, $val);
        }

        $this->svg->addChild($text);
    }
}
