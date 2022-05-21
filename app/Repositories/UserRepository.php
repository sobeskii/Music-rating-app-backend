<?php

namespace App\Repositories;

use App\Interfaces\UserRepositoryInterface;
use App\Models\User;

class UserRepository implements UserRepositoryInterface
{
    /**
     * @param string $term
     * @param int $perPage
     * @return mixed
     */
    public function getUsers(string $term, int $perPage)
    {
        return (strlen($term) > 0) ? User::where('name', 'like', '%' . $term . '%')->with(['role'])->paginate($perPage) :
            User::with(['role'])->paginate($perPage);
    }

}
