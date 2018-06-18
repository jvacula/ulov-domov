<?php

namespace Javame\UlovDomov\Infrastructure\Database\User;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Mysqli\MysqliException;
use Doctrine\DBAL\Exception\ConstraintViolationException;
use Javame\UlovDomov\User\User;
use Javame\UlovDomov\Village\VillageCollection;
use Javame\UlovDomov\Village\VillageRepositoryInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

class UserRightsRepositoryTest extends TestCase
{
    /**
     * @var Connection|ObjectProphecy
     */
    private $connection;
    /**
     * @var User|ObjectProphecy
     */
    private $user;
    /**
     * @var VillageRepositoryInterface|ObjectProphecy
     */
    private $villageRepository;
    /**
     * @var int
     */
    private $rightId = 1;
    /**
     * @var int
     */
    private $userId = 2;
    /**
     * @var int
     */
    private $villageId = 3;
    /**
     * @var array
     */
    private $allVillages = [1, 2, 3];
    /**
     * @var int
     */
    private $userAdminId = 10;
    /**
     * @var int
     */
    private $secondRightId = 2;
    /**
     * @var int
     */
    private $secondVillageId = 2;

    protected function setUp()
    {
        $this->connection = $this->prophesize(Connection::class);
        $this->villageRepository = $this->prophesize(VillageRepositoryInterface::class);
        $this->user = $this->prophesize(User::class);
        $this->user->getId()->willReturn($this->userId);
    }

    /**
     * @test
     **/
    public function get_userNotAdmin_returnsEmptyCollection()
    {
        $this
            ->connection
            ->fetchAll(
                Argument::containingString('SELECT'),
                [$this->userId, $this->rightId],
                Argument::any()
            )
            ->willReturn([]);

        $userAdminRepository = new UserAdminRightsRepository(
            $this->connection->reveal(),
            $this->villageRepository->reveal()
        );
        $this->assertCount(0, $userAdminRepository->get($this->user->reveal(), $this->rightId));
    }

    /**
     * @test
     **/
    public function get_userAdminLimitedRightToVillage_returnsCollectionWithVillage()
    {
        $this
            ->connection
            ->fetchAll(
                Argument::containingString('SELECT'),
                [$this->userId, $this->rightId],
                Argument::any()
            )
            ->willReturn(
                [
                    ['village_id' => $this->villageId],
                ]
            );

        $userAdminRepository = new UserAdminRightsRepository(
            $this->connection->reveal(),
            $this->villageRepository->reveal()
        );
        $this->assertCount(1, $userAdminRepository->get($this->user->reveal(), $this->rightId));
    }

    /**
     * @test
     **/
    public function get_userAdminUnlimitedRights_returnsCollectionWithAllVillages()
    {
        $this
            ->connection
            ->fetchAll(
                Argument::containingString('SELECT'),
                [$this->userId, $this->rightId],
                Argument::any()
            )
            ->willReturn(
                [
                    ['village_id' => null],
                ]
            );

        /** @var ObjectProphecy|VillageCollection $villagesCollection */
        $villagesCollection = $this->prophesize(VillageCollection::class);
        $villagesCollection->getKeys()->willReturn($this->allVillages);
        $this->villageRepository->findAll()->willReturn($villagesCollection->reveal())->shouldBeCalled();

        $userAdminRepository = new UserAdminRightsRepository(
            $this->connection->reveal(),
            $this->villageRepository->reveal()
        );
        $this->assertCount(3, $userAdminRepository->get($this->user->reveal(), $this->rightId));
    }

    /**
     * @test
     **/
    public function set_insertNewAdminWithFullRights_returnsVoid()
    {
        $this
            ->connection
            ->insert('user_admin', ['user_id' => $this->userId], Argument::any())
            ->willReturn($this->userAdminId)
            ->shouldBeCalledTimes(1);

        $userAdminRepository = new UserAdminRightsRepository(
            $this->connection->reveal(),
            $this->villageRepository->reveal()
        );
        $userAdminRepository->set($this->getUser()->reveal(), []);
    }

    /**
     * @test
     **/
    public function set_insertNewAdminWithRightToOneCity_returnsVoid()
    {
        $this
            ->connection
            ->insert('user_admin', ['user_id' => $this->userId], Argument::any())
            ->willReturn($this->userAdminId)
            ->shouldBeCalledTimes(1);

        $this
            ->connection
            ->insert(
                'user_village_right',
                ['user_admin_id' => $this->userAdminId, 'village_id' => $this->villageId, 'right_id' => $this->rightId],
                Argument::any()
            )
            ->shouldBeCalledTimes(1);

        $userAdminRepository = new UserAdminRightsRepository(
            $this->connection->reveal(),
            $this->villageRepository->reveal()
        );
        $userAdminRepository->set(
            $this->getUser()->reveal(),
            [
                $this->rightId => [
                    $this->villageId => true,
                    2 => false,
                ],
                2 => [
                    1 => false,
                    2 => false,
                ],
            ]
        );
    }

    /**
     * @test
     **/
    public function set_insertNewAdminWithRightsToMoreCities_returnsVoid()
    {
        $this
            ->connection
            ->insert('user_admin', ['user_id' => $this->userId], Argument::any())
            ->willReturn($this->userAdminId)
            ->shouldBeCalledTimes(1);

        $this
            ->connection
            ->insert(
                Argument::containingString('user_village_right'),
                ['user_admin_id' => $this->userAdminId, 'village_id' => $this->villageId, 'right_id' => $this->rightId],
                Argument::any()
            )
            ->shouldBeCalledTimes(1);

        $this
            ->connection
            ->insert(
                Argument::containingString('user_village_right'),
                ['user_admin_id' => $this->userAdminId, 'village_id' => $this->secondVillageId, 'right_id' => $this->secondRightId],
                Argument::any()
            )
            ->shouldBeCalledTimes(1);

        $userAdminRepository = new UserAdminRightsRepository(
            $this->connection->reveal(),
            $this->villageRepository->reveal()
        );
        $userAdminRepository->set(
            $this->getUser()->reveal(),
            [
                $this->rightId => [
                    $this->villageId => true,
                    2 => false,
                ],
                $this->secondRightId => [
                    $this->villageId => false,
                    $this->secondVillageId => true,
                ],
            ]
        );
    }

    /**
     * @test
     **/
    public function set_adminAlreadyExistsSelectItsId_returnsVoid()
    {
        $this
            ->connection
            ->insert('user_admin', ['user_id' => $this->userId], Argument::any())
            ->willThrow(new ConstraintViolationException('message', new MysqliException('message')))
            ->shouldBeCalledTimes(1);

        $this
            ->connection
            ->fetchArray(Argument::containingString('SELECT'), [$this->userId], Argument::any())
            ->willReturn([$this->userAdminId]);

        $userAdminRepository = new UserAdminRightsRepository(
            $this->connection->reveal(),
            $this->villageRepository->reveal()
        );
        $userAdminRepository->set(
            $this->getUser()->reveal(),
            []
        );
    }


    /**
     * @return User|ObjectProphecy
     * @throws \LogicException
     */
    private function getUser()
    {
        /** @var User|ObjectProphecy $user */
        $user = $this->prophesize(User::class);
        $user->getId()->willReturn($this->userId);

        return $user;
    }
}
