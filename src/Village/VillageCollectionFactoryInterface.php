<?php

namespace Javame\UlovDomov\Village;

use Doctrine\DBAL\Statement;

interface VillageCollectionFactoryInterface
{
    /**
     * @param Statement $villages
     * @return VillageCollection
     */
    public function build(Statement $villages);
}
