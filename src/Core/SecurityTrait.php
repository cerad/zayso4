<?php

declare(strict_types = 1);

namespace App\Core;

trait SecurityTrait
{
    use AuthorizationTrait;
    use AuthenticationTrait;
}
