<?php
namespace KALMARS\Services;

use Plenty\Modules\Item\Manufacturer\Contracts\ManufacturerRepositoryContract;

class ManufacturerService
{

    private $manufacturerRepositoryContract;

    public function __construct( ManufacturerRepositoryContract $manufacturerRepositoryContract )
    {
        $this->manufacturerRepositoryContract = $manufacturerRepositoryContract;
    }

    public function getAll()
    {
        $contactClassRepo = pluginApp(ManufacturerRepositoryContract::class);

        return $contactClassRepo->all();
    }

    public function findByIds($ids)
    {
        if( empty($ids) ){
            return [];
        }

        $ids_array = explode( ',', $ids );
        $manufacturers = [];

        if ( $ids_array ){
            $contactClassRepo = pluginApp(ManufacturerRepositoryContract::class);

            foreach( $ids_array as $id ){
                $id = trim( $id );
                if( empty($id) ){
                    continue;
                }

                try
                {
                    $manufacturer = $contactClassRepo->findById( $id );
                    $manufacturers[] = $manufacturer;
                }
                catch(\Exception $e)
                {
                    // $manufacturers[] = null;
                }
            }
        }

        return $manufacturers;
    }
}
