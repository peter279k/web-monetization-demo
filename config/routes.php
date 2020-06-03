<?php

use Slim\App;
use App\Action\IndexAction;
use App\Action\IntroductionAction;
use App\Action\BlogAction;
use App\Action\DonateAction;
use App\Action\HistoryAction;
use App\Action\WebMonetizationAction;

return function (App $app) {
    $app->get('/', IndexAction::class);
    $app->get('/introduction', IntroductionAction::class);
    $app->get('/blog', BlogAction::class);
    $app->get('/donate', DonateAction::class);
    $app->get('/history', HistoryAction::class);

    $app->post('/blog', WebMonetizationAction::class);
    $app->post('/donate', WebMonetizationAction::class);
    $app->post('/history', WebMonetizationAction::class);
};
