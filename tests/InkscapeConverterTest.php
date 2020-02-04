<?php


namespace App\Tests;


use App\Chart\InkscapeConverter;
use App\Exception\SvgConverterException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Exception\ProcessFailedException;

class InkscapeConverterTest extends TestCase
{
    private $converter;

    protected function setUp(): void
    {
        $this->converter = new InkscapeConverter(new Filesystem());
    }

    public function testUnsupportedException()
    {
        $this->expectException(SvgConverterException::class);
        $this->expectExceptionMessage('Unsupported format bmp format.');

        $this->converter->convert('', '', 'bmp');
    }

    public function testBadSvgFile()
    {
        $this->expectException(ProcessFailedException::class);

        $this->converter->convert('', '', 'png');
    }
}