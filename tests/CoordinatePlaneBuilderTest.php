<?php


namespace App\Tests;

use App\Chart\CoordinatePlane;
use App\Exception\CoordinatePlaneException;
use PHPUnit\Framework\TestCase;
use \App\Chart\CoordinatePlaneBuilder;
use SVG\SVG;

class CoordinatePlaneBuilderTest extends TestCase
{
    private CoordinatePlaneBuilder $builder;

    protected function setUp(): void
    {
        $this->builder = new CoordinatePlaneBuilder();
        $svg = new SVG(1000, 1000);
        $this->builder
            ->setSVG($svg->getDocument())
            ->setAxisLength(100, 100)
            ->setStep(100, 100)
            ->setOrigin(100, 100)
            ->setMaxValues(100, 100)
            ->setAxisMarkup(['1'], ['1']);
    }

    public function testBuildObject()
    {
        $this->assertInstanceOf(CoordinatePlane::class, $this->builder->build());
    }

    public function testAxisXMarkupException()
    {
        $this->builder->setAxisMarkup([], ['1']);
        $this->builder->setMaxValues(0, 10);
        $this->expectException(CoordinatePlaneException::class);
        $this->expectExceptionMessage('stepX and maxX must be greater than 0, axisXMarkup cannot be empty');

        $this->builder->build();
    }

    public function testAxisYMarkupException()
    {
        $this->builder->setAxisMarkup(['2'], []);
        $this->builder->setMaxValues(10, 0);
        $this->expectException(CoordinatePlaneException::class);
        $this->expectExceptionMessage('stepY and maxY must be greater than 0, axisYMarkup cannot be empty');

        $this->builder->build();
    }

    public function testOriginException()
    {
        $this->builder->setOrigin(0, 0);
        $this->expectException(CoordinatePlaneException::class);
        $this->expectExceptionMessage('originX and originY must be greater than 0');

        $this->builder->build();
    }

    public function testAxisLengthException()
    {
        $this->builder->setAxisLength(0, 0);
        $this->expectException(CoordinatePlaneException::class);
        $this->expectExceptionMessage('axisXLength and axisXLength must be greater than 0');

        $this->builder->build();
    }

    public function testSvgException()
    {
        $builder = new CoordinatePlaneBuilder();
        $this->expectException(CoordinatePlaneException::class);
        $this->expectExceptionMessage('svg cannot be null');

        $builder->build();
    }
}
