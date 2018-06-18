<?php

namespace Javame\UlovDomov;

use Doctrine\DBAL\Connection;
use Javame\UlovDomov\Exception\FailedToExecuteQueryException;
use Javame\UlovDomov\Infrastructure\Database\User\UserAdminRightsRepository;
use Javame\UlovDomov\Infrastructure\Database\Village\VillageCollectionFactory;
use Javame\UlovDomov\Infrastructure\Database\Village\VillageRepository;
use Javame\UlovDomov\User\Right\Right;
use Javame\UlovDomov\User\User;

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
        /**
         * Just for demo purposes
         */
        $this->displayVillages();

        $this->displayUserSetting(1, Right::ADDRESS_BOOK_RIGHT_ID, 'User with right to 1 city');
        $this->displayUserSetting(4, Right::SEARCH_RIGHT_ID, 'User without rights');
        $this->displayUserSetting(5, Right::SEARCH_RIGHT_ID, 'User with admin, all cities');

        //@TODO Would be better to work here with some Collection/DTO or other object
        $this->setRightsToUser(
            [
                Right::ADDRESS_BOOK_RIGHT_ID => [
                    1 => true,
                    2 => false,
                ],
                Right::SEARCH_RIGHT_ID => [
                    1 => false,
                    2 => false,
                ],
            ]
        );
    }

    /**
     * @throws FailedToExecuteQueryException
     */
    private function displayVillages(): void
    {
        echo '======================' . PHP_EOL;
        echo '==== Village list ====' . PHP_EOL;
        $villageRepository = $this->getVillageRepository();
        var_export($villageRepository->findAll());
        echo PHP_EOL;
    }

    /**
     * @return VillageRepository
     */
    private function getVillageRepository(): VillageRepository
    {
        $villageRepository = new VillageRepository(
            $this->connection,
            new VillageCollectionFactory()
        );

        return $villageRepository;
    }

    /**
     * @param int $userId
     * @param int $rightId
     * @param int $message
     * @throws FailedToExecuteQueryException
     */
    private function displayUserSetting($userId, $rightId, $message): void
    {
        echo '##### ' . $message . ' #####' . PHP_EOL;
        $userRightRepository = new UserAdminRightsRepository(
            $this->connection,
            $this->getVillageRepository()
        );
        var_export($userRightRepository->get($this->getUser($userId), $rightId));
        echo PHP_EOL . PHP_EOL;
    }

    /**
     * @param int $userId
     * @return User
     */
    private function getUser($userId): User
    {
        $user = new User();
        $user->setId($userId);

        return $user;
    }


    /**
     * @param $userVillageRights
     * @throws FailedToExecuteQueryException
     */
    private function setRightsToUser($userVillageRights): void
    {
        echo '##### Set rights to user #####' . PHP_EOL;
        $userRightRepository = new UserAdminRightsRepository(
            $this->connection,
            $this->getVillageRepository()
        );
        $userRightRepository->set($this->getUser(5), $userVillageRights);
        echo '== User rights set ==' . PHP_EOL;
        echo PHP_EOL;
    }

}
