<?php

namespace KALMARS\Widgets\Common;

use Ceres\Widgets\Helper\BaseWidget;
use Ceres\Widgets\Helper\Factories\Settings\ValueListFactory;
use Ceres\Widgets\Helper\Factories\WidgetSettingsFactory;
use Ceres\Widgets\Helper\WidgetCategories;
use Ceres\Widgets\Helper\Factories\WidgetDataFactory;
use Ceres\Widgets\Helper\WidgetTypes;

class ImageBoxWidget extends BaseWidget
{
    protected $template = 'KALMARS::Widgets.Common.ImageBoxWidget';
    const IMAGE_EXTENSIONS = [
        'jpg',
        'jpeg',
        'png',
        'gif',
        'svg',
        'apng'
    ];
    const MODERN_IMAGE_EXTENSIONS = [
        'webp'
    ];

    public function getData()
    {
        return WidgetDataFactory::make('KALMARS::ImageBoxWidget')
            ->withLabel('Widget.imageBoxLabel')
            ->withPreviewImageUrl('/images/widgets/image-box.svg')
            ->withType(WidgetTypes::STATIC)
            ->withCategory("KALMARS")
            ->withPosition(600)
            ->toArray();
    }

    public function getSettings()
    {
        /** @var WidgetSettingsFactory $settings */
        $settings = pluginApp(WidgetSettingsFactory::class);

        $settings->createCustomClass();

        $settings->createSelect('style')
            ->withDefaultValue('block-caption')
            ->withName('Widget.imageBoxStyleLabel')
            ->withTooltip('Widget.imageBoxStyleTooltip')
            ->withListBoxValues(
                ValueListFactory::make()
                    ->addEntry('block-caption', 'Widget.imageBoxStyleBlockCaption')
                    ->addEntry('inline-caption', 'Widget.imageBoxStyleInlineCaption')
                    ->addEntry('fullwidth', 'Widget.imageBoxStyleFullwidth')
                    ->addEntry('no-caption', 'Widget.imageBoxStyleNoCaption')
                    ->toArray()
            );

        $settings->createSelect('imageSize')
            ->withCondition("fullHeight !== true")
            ->withDefaultValue('cover')
            ->withName('Widget.imageBoxImageSizeLabel')
            ->withTooltip('Widget.imageBoxImageSizeTooltip')
            ->withListBoxValues(
                ValueListFactory::make()
                    ->addEntry('cover', 'Widget.imageBoxImageSizeCover')
                    ->addEntry('contain', 'Widget.imageBoxImageSizeContain')
                    ->toArray()
            );

        $settings->createUrl('url')
            ->withName('Widget.imageBoxUrlLabel');

        $settings->createCheckbox('customCaption')
            ->withCondition("style !== 'no-caption'")
            ->withName('Widget.imageBoxCustomCaption');

        $settings->createFile('customImagePath')
            ->withDefaultValue('')
            ->withName('Widget.imageBoxCustomImagePathLabel')
            ->withTooltip('Widget.imageBoxCustomImagePathTooltip')
            ->withAllowedExtensions(array_merge(self::IMAGE_EXTENSIONS, self::MODERN_IMAGE_EXTENSIONS));

        $settings->createFile('fallbackImagePath')
            ->withDefaultValue('')
            ->withName('Widget.imageBoxFallbackImagePathLabel')
            ->withTooltip('Widget.imageBoxFallbackImagePathTooltip')
            ->withCondition('!!customImagePath && /.?(\.webp)(?:$|\?)/.test(customImagePath)')
            ->withAllowedExtensions(self::IMAGE_EXTENSIONS);

        $settings->createCheckbox('fullHeight')
            ->withDefaultValue(false)
            ->withName('Widget.imageBoxFullHeightLabel')
            ->withTooltip('Widget.imageBoxFullHeightTooltip');

        $settings->createCheckbox('lazyLoading')
            ->withCondition("!preloadImage")
            ->withName('Widget.imageBoxLazyLoadingName')
            ->withTooltip('Widget.imageBoxLazyLoadingTooltip')
            ->withDefaultValue(true);

        $settings->createCheckbox('preloadImage')
            ->withName('Widget.preloadImageLabel')
            ->withTooltip('Widget.preloadImageTooltip')
            ->withCondition("!lazyLoading");

        $settings->createCheckbox('zoomImage')
            ->withName('Widget.zoomImageLabel')
            ->withTooltip('Widget.zoomImageTooltip')
            ->withDefaultValue(false);

        $settings->createSpacing(false, true);

        return $settings->toArray();
    }

    protected function getTemplateData($widgetSettings, $isPreview)
    {
        $urlType = '';
        $urlValue = '';

        if (array_key_exists('url', $widgetSettings) && $widgetSettings['url']['value']['mobile']) {
            $urlType = $widgetSettings['url']['type']['mobile'];
            $urlValue = $widgetSettings['url']['value']['mobile'];
        } else {
            if ($widgetSettings['categoryId']['mobile']) {
                $urlType = 'category';
                $urlValue = $widgetSettings['categoryId']['mobile'];
            } else {
                $urlType = 'item';
                $urlValue = $widgetSettings['variationId']['mobile'];
            }
        }

        return [
            'urlType' => $urlType,
            'urlValue' => $urlValue
        ];
    }
}
