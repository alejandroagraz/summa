<?php

namespace App\Controller;

use App\Dto\AuthDto;
use App\Input\AuthInput;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api', name: 'auth_')]
class AuthController extends AbstractController
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher, JWTEncoderInterface $jwtEncoder)
    {
        $this->hasher = $hasher;
    }

    #[Route('/login', name: 'login', methods: 'post')]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(ref: new Model(type: AuthInput::class))
    )]
    #[OA\Response(
        response: 200,
        description: 'Returns access_token for user',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: AuthDto::class))
        )
    )]
    #[OA\Tag(name: 'Auth')]
    #[Security(name: 'Bearer')]
    public function login(
        UserRepository $userRepository,
        Request $request,
        JWTTokenManagerInterface $JWTManager,
        ValidatorInterface $validator
    )
    {
        $decoded = json_decode($request->getContent());

        if (!isset($decoded->username))
            return new JsonResponse(['error' => 'you must send the username parameter']);
        if (!isset($decoded->password))
            return new JsonResponse(['error' => 'you must send the password parameter']);

        $authInput = new AuthInput($decoded->username, $decoded->password);
        $errors = $validator->validate($authInput);
        if (count($errors) > 0) {
            $errorsString = (string) $errors;
            return new JsonResponse(['error' => $errorsString]);
        }

        $user = $userRepository->findOneBy(['username' => $decoded->username]);

        if (!$user) {
            return new JsonResponse(['Invalid Username or Password or Email' => 401]);
            return $this->respondValidationError("Invalid Username or Password or Email");
        }

        if (!$this->hasher->isPasswordValid($user, $decoded->password)) {
            return new JsonResponse(['error' => 'Invalid email or password'], 401);
        }

        return new JsonResponse(['access_token' => $JWTManager->create($user)],200);
    }
}
