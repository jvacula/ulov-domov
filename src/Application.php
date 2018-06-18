<?php

namespace Javame\UlovDomov;

use Doctrine\DBAL\Connection;
use Javame\UlovDomov\Infrastructure\Database\Village\VillageCollectionFactory;
use Javame\UlovDomov\Infrastructure\Database\Village\VillageRepository;

class Application
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @param Connection $databaseConnection
     */
    public function __construct(Connection $databaseConnection)
    {
        $this->connection = $databaseConnection;
    }

    public function run()
    {
        $villageRepository = new VillageRepository(
            $this->connection,
            new VillageCollectionFactory()
        );
        var_export($villageRepository->findAll());
    }
}
