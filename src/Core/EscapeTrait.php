<?php

namespace App\Core;

trait EscapeTrait
{
    protected function escape($content)
    {
        return htmlspecialchars($content, ENT_COMPAT);
    }
}