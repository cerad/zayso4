<?php
/**
 * Created by PhpStorm.
 * User: ahundiak
 * Date: 11/15/18
 * Time: 8:21 AM
 */

namespace App\User;


use Symfony\Component\EventDispatcher\Event;

/**
 * @property-read string $hash
 */
class UserEncoderEvent extends Event
{
    public $hash;

    public function __construct(string $hash)
    {
        $this->hash = $hash;
    }
}