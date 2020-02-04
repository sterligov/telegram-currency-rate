<?php

namespace App\CallbackQueryEvent;

interface EventInterface
{
    /**
     * @param mixed $data
     * @return mixed
     */
    public function handle($data);
}