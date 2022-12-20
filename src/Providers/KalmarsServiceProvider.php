<?php

namespace Kalmars\Providers;

use Plenty\Plugin\ServiceProvider;
use Plenty\Plugin\Events\Dispatcher;
use Plenty\Plugin\Templates\Twig;
use IO\Helper\TemplateContainer;
use IO\Helper\ResourceContainer;
use IO\Extensions\Functions\Partial;
use IO\Helper\ComponentContainer;
use Plenty\Plugin\ConfigRepository;
use Plenty\Modules\ShopBuilder\Contracts\ContentWidgetRepositoryContract;

use Legend\Widgets\WidgetCollection;

use Legend\Contracts\KeyValueRepositoryContract;
use Legend\Repositories\KeyValueRepository;

use Legend\Extensions\TwigServiceProvider;
use Legend\Contexts\LegendSingleItemContext;
use Legend\Services\WebhookService;


class LegendServiceProvider extends ServiceProvider
{
    const PRIORITY = 99;

    public function register()
    {
        $this->getApplication()->register(LegendRouteServiceProvider::class);
        $this->getApplication()->bind(KeyValueRepositoryContract::class, KeyValueRepository::class);
    }

    public function boot(Twig $twig, Dispatcher $dispatcher, ConfigRepository $config)
    {
        $webhookService = pluginApp(WebhookService::class);
        $dispatcher->listen('IO.tpl.login', function (TemplateContainer $container) use ($webhookService)
        {
            $webhookService->cronHandle();
        }, self::PRIORITY);


        $twig->addExtension(TwigServiceProvider::class);

        $widgetRepository = pluginApp(ContentWidgetRepositoryContract::class);
        $widgetClasses = WidgetCollection::all();
        foreach ($widgetClasses as $widgetClass) {
            $widgetRepository->registerWidget($widgetClass);
        }

        $dispatcher->listen('IO.Resources.Import', function (ResourceContainer $container) {
            $container->addStyleTemplate('Legend::Stylesheet');
            $container->addScriptTemplate('Legend::Script');
        }, self::PRIORITY);

        $dispatcher->listen('IO.init.templates', function (Partial $partial)
        {
            $partial->set('head', 'Ceres::PageDesign.Partials.Head');
            $partial->set('header', 'Ceres::PageDesign.Partials.Header.Header');
            $partial->set('page-design', 'Ceres::PageDesign.PageDesign');
            $partial->set('footer', 'Ceres::PageDesign.Partials.Footer');
            $partial->set('page-metadata', 'Ceres::PageDesign.Partials.PageMetadata');

            $partial->set('header', 'Legend::PageDesign.Partials.Header.Header');
            $partial->set('page-design', 'Legend::PageDesign.PageDesign');
            $partial->set('footer', 'Legend::PageDesign.Partials.Footer');
            $partial->set('page-metadata', 'Legend::PageDesign.Partials.PageMetadata');
        }, self::PRIORITY);

        $dispatcher->listen('IO.tpl.item', function (TemplateContainer $container)
        {
            $container->setTemplate('Legend::Item.SingleItemWrapper');
        }, self::PRIORITY);
        $dispatcher->listen('IO.ctx.item', function (TemplateContainer $container)
        {
            $container->setContext( LegendSingleItemContext::class );
            return false;
        }, self::PRIORITY);

        $dispatcher->listen('IO.tpl.category.item', function (TemplateContainer $container)
        {
            $container->setTemplate('Legend::Category.Item.CategoryItem');
        }, self::PRIORITY);
        $dispatcher->listen('IO.tpl.search', function (TemplateContainer $container)
        {
            $container->setTemplate('Legend::Category.Item.CategoryItem');
        }, self::PRIORITY);
        $dispatcher->listen('IO.tpl.tags', function (TemplateContainer $container)
        {
            $container->setTemplate('Legend::Category.Item.CategoryItem');
        }, self::PRIORITY);

        /* Heders remove only */
        $dispatcher->listen('IO.tpl.contact', function (TemplateContainer $container)
        {
            $container->setTemplate('Legend::Customer.Contact');
        }, self::PRIORITY);
        $dispatcher->listen('IO.tpl.wish-list', function (TemplateContainer $container)
        {
            $container->setTemplate('Legend::WishList.WishListView');
        }, self::PRIORITY);
        $dispatcher->listen('IO.tpl.basket', function (TemplateContainer $container)
        {
            $container->setTemplate('Legend::Basket.Basket');
        }, self::PRIORITY);
        $dispatcher->listen('IO.tpl.cancellation-form', function (TemplateContainer $container)
        {
            $container->setTemplate('Legend::StaticPages.CancellationForm');
        }, self::PRIORITY);
        $dispatcher->listen('IO.tpl.cancellation-rights', function (TemplateContainer $container)
        {
            $container->setTemplate('Legend::StaticPages.CancellationRights');
        }, self::PRIORITY);
        $dispatcher->listen('IO.tpl.legal-disclosure', function (TemplateContainer $container)
        {
            $container->setTemplate('Legend::StaticPages.LegalDisclosure');
        }, self::PRIORITY);
        $dispatcher->listen('IO.tpl.privacy-policy', function (TemplateContainer $container)
        {
            $container->setTemplate('Legend::StaticPages.PrivacyPolicy');
        }, self::PRIORITY);
        $dispatcher->listen('IO.tpl.terms-conditions', function (TemplateContainer $container)
        {
            $container->setTemplate('Legend::StaticPages.TermsAndConditions');
        }, self::PRIORITY);
    }
}

