<?php

namespace Javame\UlovDomov\Infrastructure\Database\Village;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Javame\UlovDomov\Exception\FailedToExecuteQueryException;
use Javame\UlovDomov\Village\VillageCollection;
use Javame\UlovDomov\Village\VillageRepositoryInterface;

class VillageRepository implements VillageRepositoryInterface
{
    /**
     * @var Connection
     */
    private $connection;
    /**
     * @var
     */
    private const TABLE_NAME = 'village';
    /**
     * @var VillageCollectionFactory
     */
    private $collectionFactory;

    /**
     * @param Connection $connection
     * @param VillageCollectionFactory $collectionFactory
     */
    public function __construct(
        Connection $connection,
        VillageCollectionFactory $collectionFactory
    ) {
        $this->connection = $connection;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @return VillageCollection
     */
    public function findAll()
    {
        try {
            $statement = $this->connection->prepare(
                "SELECT * FROM " . self::TABLE_NAME
            );
            $statement->execute();

            return $this->collectionFactory->build($statement);
        } catch (DBALException $e) {
            throw new FailedToExecuteQueryException();
        }
    }
}
