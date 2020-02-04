<?php

namespace App\Chart;


interface SvgConverterInterface
{
    /**
     * @param string $filename
     * @param mixed $svg
     * @param string $format
     * @return string
     */
    public function convert(string $filename, $svg, string $format): string;

    /**
     * @param string $format
     * @return bool
     */
    public function isFormatSupported(string $format): bool;
}