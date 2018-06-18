<?php

namespace Javame\UlovDomov\User;

interface UserRightsRepositoryInterface
{
    /**
     * @param User $user
     * @param int $right
     * @return array
     */
    public function get(User $user, int $right): array;

    /**
     * @param User $user
     * @param array $rights
     * @return mixed
     */
    public function set(User $user, array $rights);
}
