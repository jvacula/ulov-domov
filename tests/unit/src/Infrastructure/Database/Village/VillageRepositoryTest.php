<?php

namespace Javame\UlovDomov\Infrastructure\Database\Village;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Statement;
use Javame\UlovDomov\Exception\FailedToExecuteQueryException;
use Javame\UlovDomov\Village\VillageCollection;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

class VillageRepositoryTest extends TestCase
{
    /**
     * @var Connection|ObjectProphecy
     */
    private $connection;
    /**
     * @var VillageCollectionFactory|ObjectProphecy
     */
    private $factory;

    protected function setUp()
    {
        $this->connection = $this->prophesize(Connection::class);
        $this->factory = $this->prophesize(VillageCollectionFactory::class);
    }

    /**
     * @test
     */
    public function findAll_correctlyExecuted_returnsCollection()
    {
        /** @var Statement|ObjectProphecy $statement */
        $statement = $this->prophesize(Statement::class);
        $this->connection->prepare(Argument::any())->willReturn($statement);
        $statement->execute()->willReturn(true);

        $collection = new VillageCollection();
        $this->factory->build($statement->reveal())->willReturn($collection);

        $repository = new VillageRepository(
            $this->connection->reveal(),
            $this->factory->reveal()
        );
        $this->assertEquals($collection, $repository->findAll());
    }


    /**
     * @test
     */
    public function findAll_failToExecute_throwsException()
    {
        /** @var Statement|ObjectProphecy $statement */
        $statement = $this->prophesize(Statement::class);
        $this->connection->prepare(Argument::any())->willReturn($statement);
        $statement->execute()->willThrow(new DBALException());

        $this->factory->build($statement->reveal())->shouldNotBeCalled();

        $repository = new VillageRepository(
            $this->connection->reveal(),
            $this->factory->reveal()
        );

        $this->expectException(FailedToExecuteQueryException::class);
        $repository->findAll();
    }
}
