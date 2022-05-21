<?php

namespace App\Interfaces;

interface UserRepositoryInterface
{
    public function getUsers(string $term,int $perPage);
}
