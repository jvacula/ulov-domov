<?php

namespace Javame\UlovDomov\Village;

interface VillageRepositoryInterface
{
    /**
     * @return VillageCollection
     */
    public function findAll();
}
