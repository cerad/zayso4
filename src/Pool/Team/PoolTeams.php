<?php
declare(strict_types=1);

namespace App\Pool\Team;

class PoolTeams extends \ArrayIterator
{
    public function __construct(PoolTeam ...$items)
    {
        parent::__construct($items);
    }
    public function current() : PoolTeam
    {
        return parent::current();
    }
    public function offsetGet($offset) : PoolTeam
    {
        return parent::offsetGet($offset);
    }
}