<?php

namespace App\Chart;

use App\Exception\SvgConverterException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class InkscapeConverter implements SvgConverterInterface
{
    const SUPPORTED_FORMATS = ['png'];

    private Filesystem $filesystem;

    /**
     * InkscapeConverter constructor.
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * @param string $filename
     * @param mixed $svg
     * @param string $format
     * @return string
     * @throws SvgConverterException
     */
    public function convert(string $filename, $svg, string $format): string
    {
        if (!$this->isFormatSupported($format)) {
            throw new SvgConverterException("Unsupported format $format format.");
        }

        $this->filesystem->appendToFile("$filename.svg", $svg);
        $process = new Process(['inkscape', '-z', "$filename.svg", '-e', "$filename.$format"]);
        $process->run();
        $this->filesystem->remove("$filename.svg");

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return "$filename.$format";
    }

    /**
     * @param string $format
     * @return bool
     */
    public function isFormatSupported(string $format): bool
    {
        return in_array($format, self::SUPPORTED_FORMATS);
    }
}
