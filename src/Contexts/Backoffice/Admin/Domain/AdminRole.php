<?php

declare(strict_types=1);

namespace App\Contexts\Backoffice\Admin\Domain;

enum AdminRole: string
{
    case ADMIN = 'admin';
    case SUPERADMIN = 'superadmin';

    public function isSuperAdmin(): bool
    {
        return $this === self::SUPERADMIN;
    }

    public function isAdmin(): bool
    {
        return $this === self::ADMIN;
    }

    public function canManageAdmins(): bool
    {
        return $this->isSuperAdmin();
    }
}
