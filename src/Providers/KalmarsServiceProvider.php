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




class KALMARSServiceProvider extends ServiceProvider
{
    const PRIORITY = 999;
    public function boot(Twig $twig, Dispatcher $dispatcher, ConfigRepository $config)
    {
        $dispatcher->listen('IO.init.templates', function (Partial $partial)
        {
            $partial->set('page-design', 'KALMARS::PageDesign.PageDesign');
        }, self::PRIORITY);
    }
}

