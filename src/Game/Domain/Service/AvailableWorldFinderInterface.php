<?php

namespace App\Game\Domain\Service;

use App\Game\Domain\Exception\NoWorldAvailableException;
use App\Game\Domain\Model\Entity\World;

interface AvailableWorldFinderInterface
{
    /**
     * @throws NoWorldAvailableException
     */
    public function find(): World;
}
