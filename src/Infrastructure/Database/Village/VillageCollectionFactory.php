<?php

namespace Javame\UlovDomov\Infrastructure\Database\Village;

use Doctrine\DBAL\Statement;
use Javame\UlovDomov\Village\Village;
use Javame\UlovDomov\Village\VillageCollection;
use Javame\UlovDomov\Village\VillageCollectionFactoryInterface;

class VillageCollectionFactory implements VillageCollectionFactoryInterface
{

    /**
     * @param array $villages
     * @return VillageCollection
     */
    public function build(array $villages)
    {
        $collection = new VillageCollection();
        foreach ($villages as $village) {
            $villageObject =  $this->buildVillage($village);
            $collection->set($villageObject->getId(), $villageObject);
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
