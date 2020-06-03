<?php

namespace App\Action;

use Dotenv\Dotenv;
use Carbon\Carbon;
use App\Domain\Monetization\Service\MonetizationCreator;
use App\Domain\Monetization\Service\MonetizationFetcher;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class WebMonetizationAction
{
    private $monetizationCreator;

    private $monetizationFetcher;

    private $dotEnv;

    public function __construct(MonetizationCreator $monetizationCreator, MonetizationFetcher $monetizationFetcher, Dotenv $dotEnv)
    {
        $this->monetizationCreator = $monetizationCreator;
        $this->monetizationFetcher = $monetizationFetcher;
        $this->dotEnv = $dotEnv;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response
    ): ResponseInterface {
        $requestBody = (array)$request->getParsedBody();

        $responseJson = [];
        $statusCode = 200;

        $webMonetizationEvent = $requestBody['web_monetization_event'] ?? false;
        $eventPage = $requestBody['event_page'] ?? false;

        if ($webMonetizationEvent === false) {
            $responseJson['message'] = 'the request web_monetization_event property is undefined';
        }

        if ($webMonetizationEvent === 'started') {
            $responseJson['message']['unlocked_section_one'] = 'This is the unlocked section one, thanks for using Coil to unlock this exclusive contents :)!';
            $responseJson['message']['unlocked_section_two'] = 'This is the unlocked section two, thanks for using Coil to unlock this exclusive contents :)!';
        }

        if ($webMonetizationEvent === 'enabled') {
            if ($eventPage === false) {
                $_SESSION['web_monetization_state'] = $webMonetizationEvent;
            } else {
                $_SESSION['web_monetization_state_donate'] = $webMonetizationEvent;
            }
            $responseJson['message'] = $webMonetizationEvent;
        }

        if ($webMonetizationEvent === 'disabled') {
            if (isset($_SESSION['web_monetization_state'])) {
                unset($_SESSION['web_monetization_state']);
            }
            if (isset($_SESSION['web_monetization_state_donate'])) {
                unset($_SESSION['web_monetization_state_donate']);
            }
            $responseJson['message'] = $webMonetizationEvent;
        }

        if ($webMonetizationEvent === 'pending') {
            $requestBody['created_date_time'] = (Carbon::now())->format('Y-m-d H:i:s');
            $monetizationId = $this->monetizationCreator->createMonetizationAmount($requestBody);

            $responseJson['monetization_id'] = $monetizationId;
            $statusCode = 201;
        }

        if ($webMonetizationEvent === 'transaction_history') {
            $this->dotEnv->load();
            $webMonetizationPointer = getenv('WEB_MONETIZATION_POINTER');

            $requestBody['created_date_time'] = (Carbon::now())->format('Y-m-d H:i:s');
            $transactionHistoryAmounts = $this->monetizationFetcher->fetchMonetizationAmount($webMonetizationPointer);

            $statusCode = 200;
        }

        $response->getBody()->write(json_encode($transactionHistoryAmounts));

        return $response->withHeader('Content-Type', 'application/json')->withStatus($statusCode);
    }
}
