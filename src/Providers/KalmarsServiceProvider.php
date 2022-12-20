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

use Kalmars\Widgets\WidgetCollection;

use Kalmars\Contracts\KeyValueRepositoryContract;
use Kalmars\Repositories\KeyValueRepository;

use Kalmars\Extensions\TwigServiceProvider;
use Kalmars\Contexts\KalmarsSingleItemContext;
use Kalmars\Services\WebhookService;


class KalmarsServiceProvider extends ServiceProvider
{
    const PRIORITY = 999;

    public function register()
    {
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
            $container->addStyleTemplate('Kalmars::Stylesheet');
            $container->addScriptTemplate('Kalmars::Script');
        }, self::PRIORITY);

        $dispatcher->listen('IO.init.templates', function (Partial $partial)
        {
            $partial->set('head', 'Ceres::PageDesign.Partials.Head');
            $partial->set('header', 'Ceres::PageDesign.Partials.Header.Header');
            $partial->set('page-design', 'Ceres::PageDesign.PageDesign');
            $partial->set('footer', 'Ceres::PageDesign.Partials.Footer');
            $partial->set('page-metadata', 'Ceres::PageDesign.Partials.PageMetadata');

            $partial->set('header', 'Kalmars::PageDesign.Partials.Header.Header');
            $partial->set('page-design', 'Kalmars::PageDesign.PageDesign');
            $partial->set('footer', 'Kalmars::PageDesign.Partials.Footer');
            $partial->set('page-metadata', 'Kalmars::PageDesign.Partials.PageMetadata');
        }, self::PRIORITY);

        $dispatcher->listen('IO.tpl.item', function (TemplateContainer $container)
        {
            $container->setTemplate('Kalmars::Item.SingleItemWrapper');
        }, self::PRIORITY);
        $dispatcher->listen('IO.ctx.item', function (TemplateContainer $container)
        {
            $container->setContext( KalmarsSingleItemContext::class );
            return false;
        }, self::PRIORITY);

        $dispatcher->listen('IO.tpl.category.item', function (TemplateContainer $container)
        {
            $container->setTemplate('Kalmars::Category.Item.CategoryItem');
        }, self::PRIORITY);
        $dispatcher->listen('IO.tpl.search', function (TemplateContainer $container)
        {
            $container->setTemplate('Kalmars::Category.Item.CategoryItem');
        }, self::PRIORITY);
        $dispatcher->listen('IO.tpl.tags', function (TemplateContainer $container)
        {
            $container->setTemplate('Kalmars::Category.Item.CategoryItem');
        }, self::PRIORITY);

        /* Heders remove only */
        $dispatcher->listen('IO.tpl.contact', function (TemplateContainer $container)
        {
            $container->setTemplate('Kalmars::Customer.Contact');
        }, self::PRIORITY);
        $dispatcher->listen('IO.tpl.wish-list', function (TemplateContainer $container)
        {
            $container->setTemplate('Kalmars::WishList.WishListView');
        }, self::PRIORITY);
        $dispatcher->listen('IO.tpl.basket', function (TemplateContainer $container)
        {
            $container->setTemplate('Kalmars::Basket.Basket');
        }, self::PRIORITY);
        $dispatcher->listen('IO.tpl.cancellation-form', function (TemplateContainer $container)
        {
            $container->setTemplate('Kalmars::StaticPages.CancellationForm');
        }, self::PRIORITY);
        $dispatcher->listen('IO.tpl.cancellation-rights', function (TemplateContainer $container)
        {
            $container->setTemplate('Kalmars::StaticPages.CancellationRights');
        }, self::PRIORITY);
        $dispatcher->listen('IO.tpl.legal-disclosure', function (TemplateContainer $container)
        {
            $container->setTemplate('Kalmars::StaticPages.LegalDisclosure');
        }, self::PRIORITY);
        $dispatcher->listen('IO.tpl.privacy-policy', function (TemplateContainer $container)
        {
            $container->setTemplate('Kalmars::StaticPages.PrivacyPolicy');
        }, self::PRIORITY);
        $dispatcher->listen('IO.tpl.terms-conditions', function (TemplateContainer $container)
        {
            $container->setTemplate('Kalmars::StaticPages.TermsAndConditions');
        }, self::PRIORITY);
    }
}

