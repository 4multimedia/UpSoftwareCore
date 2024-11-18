<?php

namespace Upsoftware\Core\Models;

use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Upsoftware\Auth\Models\User;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    public function users()
    {
        return $this->belongsToMany(User::class, 'role_user', 'tenant_id', 'user_id')
            ->withPivot('role_id')
            ->withTimestamps();
    }
}
