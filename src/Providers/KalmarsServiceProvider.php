<?php

namespace KALMARS\Providers;
use Plenty\Plugin\ServiceProvider;
use Plenty\Plugin\Events\Dispatcher;
use Plenty\Plugin\Templates\Twig;
use IO\Helper\TemplateContainer;
use IO\Helper\ResourceContainer;
use IO\Extensions\Functions\Partial;
use IO\Helper\ComponentContainer;
use Plenty\Plugin\ConfigRepository;
use Plenty\Modules\ShopBuilder\Contracts\ContentWidgetRepositoryContract;

use KALMARS\Widgets\WidgetCollection;

use KALMARS\Contracts\KeyValueRepositoryContract;
use KALMARS\Repositories\KeyValueRepository;

use KALMARS\Extensions\TwigServiceProvider;
use KALMARS\Contexts\LegendSingleItemContext;
use KALMARS\Services\WebhookService;

class KALMARSServiceProvider extends ServiceProvider
{
    const PRIORITY = 999;


    public function boot(Twig $twig, Dispatcher $dispatcher, ConfigRepository $config)
    {

//        $dispatcher->listen('IO.Resources.Import', function (ResourceContainer $container) {
//            $container->addStyleTemplate('KALMARS::Stylesheet');
//            $container->addScriptTemplate('KALMARS::Script');
//        }, self::PRIORITY);

        $dispatcher->listen('IO.init.templates', function (Partial $partial)
        {
            $partial->set('head', 'KALMARS::PageDesign.Partials.Head');
            $partial->set('page-design', 'KALMARS::PageDesign.PageDesign');
        }, self::PRIORITY);
    }
}

