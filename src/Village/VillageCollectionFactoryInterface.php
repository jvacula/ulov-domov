<?php

namespace Javame\UlovDomov\Village;

interface VillageCollectionFactoryInterface
{
    /**
     * @param array $villages
     * @return VillageCollection
     */
    public function build(array $villages);
}
