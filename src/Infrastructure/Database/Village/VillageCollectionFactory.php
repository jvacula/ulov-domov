<?php

namespace Javame\UlovDomov\Infrastructure\Database\Village;

use Doctrine\DBAL\Statement;
use Javame\UlovDomov\Village\Village;
use Javame\UlovDomov\Village\VillageCollection;
use Javame\UlovDomov\Village\VillageCollectionFactoryInterface;

class VillageCollectionFactory implements VillageCollectionFactoryInterface
{

    /**
     * @param Statement $villages
     * @return VillageCollection
     */
    public function build(Statement $villages)
    {
        $collection = new VillageCollection();
        foreach ($villages as $village) {
            $collection->add($this->buildVillage($village));
        }

        return $collection;
    }

    /**
     * @param Statement $village
     * @return Village
     */
    private function buildVillage($village): Village
    {
        $villageDTO = new Village();
        $villageDTO->setId($village['id']);
        $villageDTO->setName($village['name']);

        return $villageDTO;
    }
}
