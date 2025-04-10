<?php

namespace App\Providers;


use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use App\Models\DynamoUser;
use Illuminate\Support\Facades\Hash;

class DynamoUserProvider implements UserProvider
{
    public function retrieveById($identifier)
    {
        return DynamoUser::findByEmail($identifier);
    }

    public function retrieveByToken($identifier, $token)
    {
        return null; 
    }

    public function updateRememberToken(Authenticatable $user, $token)
    {
        return null; 
    }

    public function retrieveByCredentials(array $credentials)
    {
        if (!isset($credentials['email'])) {
            return null;
        }

        return DynamoUser::findByEmail($credentials['email']);
    }

    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        return $user->getAuthPassword() == $credentials['password'];
    }

    public function rehashPasswordIfRequired(Authenticatable $user, array $credentials, bool $force = false)
    {

    }
}
