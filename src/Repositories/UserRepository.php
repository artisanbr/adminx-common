<?php

namespace Adminx\Common\Repositories;

use Adminx\Common\Models\User;
use Laravel\Passport\Client;
use Laravel\Passport\ClientRepository;

class UserRepository
{
    public function __construct(
        protected User|null $user = null,
        protected $clientRepository = new ClientRepository(),
    )
    {

    }

    public static function personalAcessClientName(): string
    {
        return config('app.name').' Personal Access Client';
    }

    public static function passwordGrantClientName(): string
    {
        return config('app.name').' Password Grant Client';
    }

    public function user(User|int $user): static
    {
        $this->user = is_int($user) ? User::find($user) : $user;

        return $this;
    }

    public function save($data){

    }

    public function prepareAuthentication(User|int $user = null): bool|Client
    {

        if($user){
            $this->user($user);
        }

        if($this->user){
           $this->clientRepository->createPasswordGrantClient($this->user->id, self::passwordGrantClientName(), config('app.url'), 'users');
           $this->clientRepository->createPersonalAccessClient($this->user->id, self::personalAcessClientName(), config('app.url'));
        }

        return false;
    }
}
