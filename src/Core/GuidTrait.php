<?php

namespace App\Core;

// todo Maybe replace with something more official looking
trait GuidTrait
{
    protected function generateGuid() : string
    {
        return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X',
            mt_rand(0,     65535), mt_rand(0,     65535), mt_rand(0, 65535),
            mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535),
            mt_rand(0,     65535), mt_rand(0,     65535)
        );
    }
}