<?php

namespace App\Constant\User;

use App\Constant\ConstantInterface;

/**
 * Class UserRole
 */
class UserRole implements ConstantInterface
{
    const ROLE_USER = 'ROLE_USER';

    /**
     * {@inheritDoc}
     */
    public static function getChoices(): array
    {
        return [
            self::ROLE_USER => self::ROLE_USER,
        ];
    }
}
