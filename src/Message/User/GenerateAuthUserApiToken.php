<?php
namespace App\Message\User;

use App\Security\AuthUser;

class GenerateAuthUserApiToken
{
    private AuthUser $user;

    public function __construct(AuthUser $user)
    {
        $this->user = $user;
    }

    public function getUser(): AuthUser
    {
        return $this->user;
    }
}
