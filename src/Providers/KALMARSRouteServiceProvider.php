<?php

namespace KALMARS\Providers;

use Plenty\Plugin\RouteServiceProvider;
use Plenty\Plugin\Routing\Router;
use Plenty\Plugin\Routing\ApiRouter;

class KALMARSRouteServiceProvider extends RouteServiceProvider
{
    public function map(Router $router, ApiRouter $api)
    {
        $api->version(['v1'], ['namespace' => 'KALMARS\Api\Resources'], function (ApiRouter $api)
        {
            $api->resource('KALMARS/customer/contact/mail', 'ContactMailResource');
            $api->get('KALMARS/webhook/handle', 'WebhookResource@handle');
        });
    }
}
