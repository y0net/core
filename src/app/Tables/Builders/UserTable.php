<?php

namespace LaravelEnso\Core\app\Tables\Builders;

use LaravelEnso\Core\app\Models\User;
use LaravelEnso\Tables\app\Services\Table;

class UserTable extends Table
{
    protected $templatePath = __DIR__.'/../Templates/users.json';

    public function query()
    {
        return User::selectRaw('
            users.id, avatars.id as avatarId, user_groups.name as userGroup,
            people.name, people.appellative, people.phone, users.email, roles.name as role,
            users.is_active, users.created_at
        ')->join('people', 'users.person_id', '=', 'people.id')
        ->join('user_groups', 'users.group_id', '=', 'user_groups.id')
        ->join('roles', 'users.role_id', '=', 'roles.id')
        ->leftJoin('avatars', 'users.id', '=', 'avatars.user_id');
    }
}
