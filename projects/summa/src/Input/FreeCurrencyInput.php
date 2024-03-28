<?php

namespace App\Input;
use Symfony\Component\Validator\Constraints as Assert;
class FreeCurrencyInput
{    public function __construct(
        #[Assert\NotBlank(message: 'The source field cannot be empty')]
        #[Type('string', message: 'The source field must be of type string')]
        #[Assert\Length(min: 3, max: 3, minMessage: 'The length of the source field must be equal to 3', maxMessage: 'The length of the source field must be equal to 3')]
        public readonly string $source,

        #[Assert\NotBlank(message: 'The target field cannot be empty')]
        #[Type('string', message: 'The target field must be of type string')]
        #[Assert\Length(min: 3, max: 3, minMessage: 'The length of the target field must be equal to 3', maxMessage: 'The length of the target field must be equal to 3')]
        public readonly string $target,

        #[Assert\NotBlank(message: 'The quantity field cannot be empty')]
        #[Type('float', message: 'The target field must be of type float')]
        #[Positive()]
        #[Assert\Length(min: 1, max: 9, minMessage: 'The length of the quantity field must be equal to 1', maxMessage: 'The length of the quantity field must be equal to 9')]
        public readonly float $quantity,
    ){
    }
}