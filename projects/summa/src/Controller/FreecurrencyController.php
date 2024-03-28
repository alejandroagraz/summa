<?php

namespace App\Controller;

use App\Dto\AuthDto;
use App\Dto\CurrenciesDto;
use App\Dto\FreeCurrencyDto;
use App\Input\AuthInput;
use App\Input\FreeCurrencyInput;
use http\Exception\BadMessageException;
use http\Exception\UnexpectedValueException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

#[Route('/api', name: 'api_')]
class FreecurrencyController extends AbstractController
{
    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * Convert currency
     *
     * Convert currency according to origin currency, final currency and quantity.
     */
    #[Route('/freecurrency', name: 'app_freecurrency', methods: 'post')]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(ref: new Model(type: FreeCurrencyInput::class))
    )]
    #[OA\Response(
        response: 200,
        description: 'Returns convert currency',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: FreeCurrencyDto::class))
        )
    )]
    #[OA\Tag(name: 'Freecurrency')]
    #[Security(name: 'Bearer')]
    public function freecurrency(
        Request $request,
        ValidatorInterface $validator
    )
    {
        $decoded = json_decode($request->getContent());
        if (!isset($decoded->source))
            return new JsonResponse(['error' => 'you must send the source parameter']);
        if (!isset($decoded->target))
            return new JsonResponse(['error' => 'you must send the target parameter']);
        if (!isset($decoded->quantity))
            return new JsonResponse(['error' => 'you must send the quantity parameter']);

        $freeCurrencyInput = new FreeCurrencyInput(
            $decoded->source,
            $decoded->target,
            $decoded->quantity
        );
        $errors = $validator->validate($freeCurrencyInput);
        if (count($errors) > 0) {
            $errorsString = (string) $errors;
            return new JsonResponse(['error' => $errorsString]);
        }

        try {
            $source = strtoupper($decoded->source);
            $target = strtoupper($decoded->target);


            $response = $this->client->request(
                'GET',
                "https://api.cambio.today/v1/quotes/{$source}/{$target}/json?quantity={$decoded->quantity}&key=".$this->getParameter('free.currency')
            );

            return new JsonResponse(['response' => $response->toArray()],200);
        } catch (\Exception $e) {
            return new JsonResponse(["error" =>
                "Please verify the parameters sent, they must include the valid currency type code, consult the api/currencies endpoint to obtain the available currency types and their codes."],
                500);
        }

    }

    /**
     * Get All Currency
     *
     * Returns all available currencies.
     */
    #[Route('/currencies', name: 'app_currecies', methods: 'get')]
    #[OA\Response(
        response: 200,
        description: 'Returns convert currency',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: CurrenciesDto::class))
        )
    )]
    #[OA\Tag(name: 'Freecurrency')]
    #[Security(name: 'Bearer')]
    public function getCurrencies()
    {
        $response = $this->client->request(
            'GET',
            "https://api.cambio.today/v1/full/EUR/json?key=".$this->getParameter('free.currency')
        );

        return new JsonResponse(['response' => $response->toArray()], 200);
    }
}
