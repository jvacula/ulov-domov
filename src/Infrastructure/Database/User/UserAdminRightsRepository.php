<?php

namespace Javame\UlovDomov\Infrastructure\Database\User;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Exception\ConstraintViolationException;
use Doctrine\DBAL\ParameterType;
use Javame\UlovDomov\Exception\FailedToExecuteQueryException;
use Javame\UlovDomov\User\User;
use Javame\UlovDomov\User\UserRightsRepositoryInterface;
use Javame\UlovDomov\Village\VillageRepositoryInterface;

class UserAdminRightsRepository implements UserRightsRepositoryInterface
{
    /**
     * @var Connection
     */
    private $connection;
    /**
     * @var VillageRepositoryInterface
     */
    private $villageRepository;

    /**
     * @param Connection $connection
     * @param VillageRepositoryInterface $villageRepository
     */
    public function __construct(
        Connection $connection,
        VillageRepositoryInterface $villageRepository
    ) {
        $this->connection = $connection;
        $this->villageRepository = $villageRepository;
    }

    /**
     * @param User $user
     * @param int $right
     * @return array
     * @throws FailedToExecuteQueryException
     */
    public function get(User $user, int $right): array
    {
        try {
            $result = $this->connection->fetchAll(
                '
                SELECT village_id FROM user
                JOIN user_admin a ON user.id = a.user_id
                LEFT JOIN user_village_right u ON a.id = u.user_admin_id
                LEFT JOIN village v ON u.village_id = v.id
                WHERE user_id = ?
                AND (right_id = ? OR right_id IS NULL)
            ',
                [$user->getId(), $right],
                [ParameterType::INTEGER, ParameterType::INTEGER]
            );

            return $this->processResult($result);
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

    /**
     * @param User $user
     * @param array $rights
     * @throws FailedToExecuteQueryException
     */
    public function set(User $user, array $rights): void
    {
        //@TODO Separate the setting of rights to user to separate class
        try {
            $userAdminId = $this->prepareUserAdmin($user);

            foreach ($rights as $rightId => $villages) {
                $this->processVillages($villages, $userAdminId, $rightId);
            }
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

    /**
     * @param array $foundVillages
     * @return array
     */
    private function processResult(array $foundVillages): array
    {
        $villages = [];
        foreach ($foundVillages as $village) {
            if (is_null($village['village_id'])) {
                return $this->villageRepository->findAll()->getKeys();
            }
            $villages[] = $village['village_id'];
        }

        return $villages;
    }

    /**
     * @param User $user
     * @return int
     * @throws DBALException
     */
    private function prepareUserAdmin(User $user): int
    {
        try {
            return $this->connection->insert(
                'user_admin',
                ['user_id' => $user->getId()],
                ['user_id' => ParameterType::INTEGER]
            );
        } catch (ConstraintViolationException $e) {
            return
                $this->connection->fetchArray(
                    'SELECT user_id FROM `user_admin` WHERE `user_id` = ?',
                    [$user->getId()],
                    [ParameterType::INTEGER]
                )[0];
        }
    }

    /**
     * @param $villages
     * @param $userAdminId
     * @param $rightId
     * @throws DBALException
     */
    private function processVillages($villages, $userAdminId, $rightId)
    {
        foreach ($villages as $villageId => $rightEnabled) {
            try {
                $this->insertRight($rightEnabled, $userAdminId, $villageId, $rightId);
            } catch (ConstraintViolationException $e) {
                continue;
            }
        }
    }

    /**
     * @param $rightEnabled
     * @param $userAdminId
     * @param $villageId
     * @param $rightId
     * @throws DBALException
     */
    private function insertRight($rightEnabled, $userAdminId, $villageId, $rightId): void
    {
        if ($rightEnabled) {
            $this->connection->insert(
                'user_village_right',
                ['user_admin_id' => $userAdminId, 'village_id' => $villageId, 'right_id' => $rightId],
                [ParameterType::INTEGER, ParameterType::INTEGER, ParameterType::INTEGER]
            );
        }
    }
}
