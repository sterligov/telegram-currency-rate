<?php


namespace App\Chart;


use App\Exception\CoordinatePlaneException;
use SVG\Nodes\Structures\SVGDocumentFragment;

class CoordinatePlaneBuilder
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
    private ?SVGDocumentFragment $svg = null;

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
     * @var CoordinatePlane
     */
    private CoordinatePlane $coordinatePlane;

    public function __construct()
    {
        $this->coordinatePlane = new CoordinatePlane();
    }

    /**
     * @return CoordinatePlane
     * @throws CoordinatePlaneException
     */
    public function build(): CoordinatePlane
    {
        if (!$this->svg) {
            throw new CoordinatePlaneException('svg cannot be null');
        }

        $this->coordinatePlane->setSVG($this->svg);

        if ($this->originY <= 0 || $this->originX <= 0) {
            throw new CoordinatePlaneException('originX and originY must be greater than 0');
        }

        $this->coordinatePlane->setOriginX($this->originX);
        $this->coordinatePlane->setOriginY($this->originY);

        if ($this->axisXLength <= 0 || $this->axisYLength <= 0) {
            throw new CoordinatePlaneException('axisXLength and axisXLength must be greater than 0');
        }

        $this->coordinatePlane->setAxisXLength($this->axisXLength);
        $this->coordinatePlane->setAxisYLength($this->axisYLength);

        if (!$this->axisXMarkup && ($this->stepX <= 0 || $this->maxX <= 0)) {
            throw new CoordinatePlaneException('stepX and maxX must be greater than 0, axisXMarkup cannot be empty');
        }

        if ($this->axisXMarkup) {
            $this->coordinatePlane->setAxisXMarkup($this->axisXMarkup);
        } else {
            $this->coordinatePlane->setStepX($this->stepX);
            $this->coordinatePlane->setMaxX($this->maxX);
        }

        if (!$this->axisYMarkup && ($this->stepY <= 0 || $this->maxY <= 0)) {
            throw new CoordinatePlaneException('stepY and maxY must be greater than 0, axisYMarkup cannot be empty');
        }

        if ($this->axisYMarkup) {
            $this->coordinatePlane->setAxisYMarkup($this->axisYMarkup);
        } else {
            $this->coordinatePlane->setStepY($this->stepY);
            $this->coordinatePlane->setMaxY($this->maxY);
        }

        return $this->coordinatePlane;
    }

    /**
     * @param float $maxX
     * @param float $maxY
     * @return $this
     */
    public function setMaxValues(float $maxX, float $maxY)
    {
        $this->maxX = $maxX;
        $this->maxY = $maxY;

        return $this;
    }

    /**
     * @param float $axisXLength
     * @param float $axisYLength
     * @return $this
     */
    public function setAxisLength(float $axisXLength, float $axisYLength)
    {
        $this->axisXLength = $axisXLength;
        $this->axisYLength = $axisYLength;

        return $this;
    }

    /**
     * @param SVGDocumentFragment $svg
     * @return $this
     */
    public function setSVG(SVGDocumentFragment $svg)
    {
        $this->svg = $svg;

        return $this;
    }

    /**
     * @param float $originX
     * @param float $originY
     * @return $this
     */
    public function setOrigin(float $originX, float $originY)
    {
        $this->originX = $originX;
        $this->originY = $originY;

        return $this;
    }

    /**
     * @param float $stepX
     * @param float $stepY
     * @return $this
     */
    public function setStep(float $stepX, float $stepY)
    {
        $this->stepX = $stepX;
        $this->stepY = $stepY;

        return $this;
    }

    /**
     * @param array $axisXMarkup
     * @param array $axisYMarkup
     * @return $this
     */
    public function setAxisMarkup(array $axisXMarkup, array $axisYMarkup)
    {
        $this->axisXMarkup = $axisXMarkup;
        $this->axisYMarkup = $axisYMarkup;

        return $this;
    }
}