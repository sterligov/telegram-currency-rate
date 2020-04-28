<?php


namespace App\UI;

interface InlineKeyboardInterface
{
    /**
     * @return array
     */
    public function build(): array;
}
