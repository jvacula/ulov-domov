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
     * @throws FailedToExecuteQueryException
     */
    public function findAll()
    {
        try {
            return $this->collectionFactory->build(
                $this->connection->fetchAll(
                "SELECT * FROM " . self::TABLE_NAME
            )
            );
        } catch (DBALException $e) {
            throw new FailedToExecuteQueryException(
                sprintf(
                    'Failed to execute query. Class: %s, error: %s (%s)',
                    __CLASS__,
                    $e->getMessage(),
                    $e->getCode()
                )
            );
        }
    }
}
