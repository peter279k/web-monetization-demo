<?php

namespace App\Action;

use Dotenv\Dotenv;
use Carbon\Carbon;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;

final class BlogAction
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
        $posDateTime = Carbon::now();

        $webMonetizationPointer = '';

        $webMonetizationState = $_SESSION['web_monetization_state'] ?? false;

        if ($webMonetizationState === false || $webMonetizationState === 'disabled') {
            $webMonetizationPointer = '';
        } else {
           $webMonetizationPointer = getenv('WEB_MONETIZATION_POINTER');
        }

        $viewData = [
            'meta_author' => 'Peter',
            'meta_description' => 'This is the Web Monetization Demonstration',
            'web_monetization_pointer' => $webMonetizationPointer,
            'web_monetization_state' => $webMonetizationState,
            'posted_date_time' => $posDateTime->format('M m, Y-m-d H:i:s'),
            'author_link' => 'https://github.com/peter279k',
            'author_name' => 'Peter',
            'section_one' => 'This is the normal section and everyone can look at this.',
        ];

        return $this->twig->render($response, 'blog.twig', $viewData);
    }
}
