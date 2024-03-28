<?php

namespace App\Input;
use Symfony\Component\Validator\Constraints as Assert;
class AuthInput
{
    public function __construct(
        #[Assert\NotBlank(message: 'The username field cannot be empty')]
        #[Type('string', message: 'The username field must be of type string')]
        #[Assert\Length(min: 5, max: 50, minMessage: 'The length of the username field must be equal to 5', maxMessage: 'The length of the username field must be equal to 50')]
        public readonly string $username,

        #[Assert\NotBlank(message: 'The password field cannot be empty')]
        #[Type('string', message: 'The password field must be of type string')]
        #[Assert\Length(min: 5, max: 50, minMessage: 'The length of the password field must be equal to 5', maxMessage: 'The length of the password field must be equal to 50')]
        public readonly string $password,
    ){
    }

}

