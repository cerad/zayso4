<?php
declare(strict_types=1);

namespace App\Reg\Team;

class RegTeams extends \ArrayIterator
{
    public function __construct(RegTeam ...$items)
    {
        parent::__construct($items);
    }
    public function current() : RegTeam
    {
        return parent::current();
    }
    public function offsetGet($offset) : RegTeam
    {
        return parent::offsetGet($offset);
    }
}