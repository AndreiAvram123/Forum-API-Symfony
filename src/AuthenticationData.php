<?php


namespace App;


use App\Entity\User;

class AuthenticationData implements \JsonSerializable
{
   private User  $user;
   private string $token;

    /**
     * AuthenticationData constructor.
     * @param User $user
     * @param string $token
     */
    public function __construct(User $user, string $token)
    {
        $this->user = $user;
        $this->token = $token;
    }


    public function jsonSerialize()
    {
        return [
            'user'=> $this->user,
            'token'=>$this->token
        ];
    }
}