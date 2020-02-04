<?php


namespace App\Tests;


use App\Chart\CoordinatePlane;
use PHPUnit\Framework\TestCase;
use SVG\SVG;

class CoordinatePlaneTest extends TestCase
{
    private CoordinatePlane $plane;

    protected function setUp(): void
    {
        $this->plane = new CoordinatePlane();
    }

    public function testDrawWithAxisMarkUp()
    {
        $svg = new SVG(1200, 1000);
        $this->plane->setSVG($svg->getDocument());
        $this->plane->setAxisXMarkup(range(0, 10, 5));
        $this->plane->setAxisYMarkup(range(0, 20, 10));
        $this->plane->setAxisXLength(500);
        $this->plane->setAxisYLength(500);
        $this->plane->setOriginY(10);
        $this->plane->setOriginX(10);

        $this->plane->draw();

        $validXML = '<?xml version="1.0" encoding="utf-8"?><svg width="1200" height="1000" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><line x1="10" y1="10" x2="510" y2="10" style="stroke-width: 1px; stroke: #000000" /><line x1="10" y1="10" x2="10" y2="-490" style="stroke-width: 1px; stroke: #000000" /><line x1="10" y1="10" x2="510" y2="10" style="stroke-width: 1px; stroke: #000000; opacity: 0.5" /><text x="-25" y="12.5" style="font-size: 10">0</text><line x1="10" y1="-240" x2="510" y2="-240" style="stroke-width: 1px; stroke: #000000; opacity: 0.5" /><text x="-25" y="-237.5" style="font-size: 10">10</text><line x1="10" y1="-490" x2="510" y2="-490" style="stroke-width: 1px; stroke: #000000; opacity: 0.5" /><text x="-25" y="-487.5" style="font-size: 10">20</text><line x1="10" y1="10" x2="10" y2="-490" style="stroke-width: 1px; stroke: #000000; opacity: 0.5" /><text x="10" y="20" style="font-size: 10; writing-mode: tb; glyph-orientation-vertical: 90">0</text><line x1="260" y1="10" x2="260" y2="-490" style="stroke-width: 1px; stroke: #000000; opacity: 0.5" /><text x="260" y="20" style="font-size: 10; writing-mode: tb; glyph-orientation-vertical: 90">5</text><line x1="510" y1="10" x2="510" y2="-490" style="stroke-width: 1px; stroke: #000000; opacity: 0.5" /><text x="510" y="20" style="font-size: 10; writing-mode: tb; glyph-orientation-vertical: 90">10</text></svg>';

        $this->assertEquals($validXML, $svg->toXMLString());
    }

    public function testDraw()
    {
        $svg = new SVG(1200, 1000);
        $this->plane->setSVG($svg->getDocument());
        $this->plane->setAxisXLength(500);
        $this->plane->setAxisYLength(500);
        $this->plane->setOriginY(10);
        $this->plane->setOriginX(10);
        $this->plane->setMaxY(20);
        $this->plane->setStepY(10);
        $this->plane->setMaxX(10);
        $this->plane->setStepX(5);

        $this->plane->draw();

        $validXML = '<?xml version="1.0" encoding="utf-8"?><svg width="1200" height="1000" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><line x1="10" y1="10" x2="510" y2="10" style="stroke-width: 1px; stroke: #000000" /><line x1="10" y1="10" x2="10" y2="-490" style="stroke-width: 1px; stroke: #000000" /><line x1="10" y1="10" x2="510" y2="10" style="stroke-width: 1px; stroke: #000000; opacity: 0.5" /><text x="-25" y="12.5" style="font-size: 10">0</text><line x1="10" y1="-240" x2="510" y2="-240" style="stroke-width: 1px; stroke: #000000; opacity: 0.5" /><text x="-25" y="-237.5" style="font-size: 10">10</text><line x1="10" y1="-490" x2="510" y2="-490" style="stroke-width: 1px; stroke: #000000; opacity: 0.5" /><text x="-25" y="-487.5" style="font-size: 10">20</text><line x1="10" y1="10" x2="10" y2="-490" style="stroke-width: 1px; stroke: #000000; opacity: 0.5" /><text x="10" y="20" style="font-size: 10; writing-mode: tb; glyph-orientation-vertical: 90">0</text><line x1="260" y1="10" x2="260" y2="-490" style="stroke-width: 1px; stroke: #000000; opacity: 0.5" /><text x="260" y="20" style="font-size: 10; writing-mode: tb; glyph-orientation-vertical: 90">5</text><line x1="510" y1="10" x2="510" y2="-490" style="stroke-width: 1px; stroke: #000000; opacity: 0.5" /><text x="510" y="20" style="font-size: 10; writing-mode: tb; glyph-orientation-vertical: 90">10</text></svg>';

        $this->assertEquals($validXML, $svg->toXMLString());
    }
}