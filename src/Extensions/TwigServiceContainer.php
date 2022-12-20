<?php

namespace KALMARS\Extensions;

use KALMARS\Services\ManufacturerService;

class TwigServiceContainer
{
    public function getManufacturer()
    {
        return pluginApp( ManufacturerService::class );
    }
}
