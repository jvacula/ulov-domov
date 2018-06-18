<?php

namespace Javame\UlovDomov\Infrastructure\Database\Village;

use Doctrine\DBAL\Statement;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

class VillageCollectionFactoryTest extends TestCase
{
    /**
     * @var Statement|ObjectProphecy
     */
    private $statement;

    protected function setUp()
    {
        $this->statement = $this->prophesize(Statement::class);
    }


    /**
     * @test
     **/
    public function build_emptyResultSet_returnsEmptyCollection()
    {
        $this->statement->getIterator()->willReturn(
            new \ArrayIterator()
        );

        $factory = new VillageCollectionFactory();

        $this->assertCount(0, $factory->build($this->statement->reveal()));
    }

    /**
     * @test
     **/
    public function build_villageResult_returnsCollection()
    {
        $this->statement->getIterator()->willReturn(
            new \ArrayIterator(

                [
                    [
                        'id' => 0,
                        'name' => 'fooBar',
                    ],
                ]
            )
        );

        $factory = new VillageCollectionFactory();

        $this->assertCount(1, $factory->build($this->statement->reveal()));
    }
}
