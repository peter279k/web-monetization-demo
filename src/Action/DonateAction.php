<?php

namespace App\Action;

use Dotenv\Dotenv;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;

final class DonateAction
{
    private $twig;

    private $dotenv;

    public function __construct(Twig $twig, Dotenv $dotenv)
    {
        $this->twig = $twig;
        $this->dotenv = $dotenv;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response
    ): ResponseInterface {
        $this->dotenv->load();

        $webMonetizationPointer = '';

        $webMonetizationState = $_SESSION['web_monetization_state_donate'] ?? false;

        if ($webMonetizationState === false || $webMonetizationState === 'disabled') {
            $webMonetizationPointer = '';
        } else {
           $webMonetizationPointer = getenv('WEB_MONETIZATION_POINTER');
        }

        $viewData = [
            'meta_author' => 'Peter',
            'meta_description' => 'This is the Web Monetization Demonstration with Donation Button Demo',
            'web_monetization_pointer' => $webMonetizationPointer,
            'web_monetization_state' => $webMonetizationState,
            'author_link' => 'https://github.com/peter279k',
            'author_name' => 'Peter',
        ];

        return $this->twig->render($response, 'donate.twig', $viewData);
    }
}
