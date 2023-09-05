<?php

namespace NovaResourceDynamicExport\Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Orchestra\Testbench\Factories\UserFactory;

class User extends \Illuminate\Foundation\Auth\User
{
    use Notifiable;
    use HasFactory;

    protected static function newFactory(): Factory
    {
        return UserFactory::new();
    }
}
