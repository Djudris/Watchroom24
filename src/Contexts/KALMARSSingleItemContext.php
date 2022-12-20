<?php
namespace KALMARS\Contexts;

use Ceres\Contexts\SingleItemContext;
use IO\Helper\ContextInterface;
use Plenty\Modules\Webshop\ItemSearch\SearchPresets\CrossSellingItems;
use Plenty\Modules\Webshop\ItemSearch\Services\ItemSearchService;
use Plenty\Plugin\ConfigRepository;

class KALMARSSingleItemContext extends SingleItemContext implements ContextInterface
{
    public $accessories = [];

    public function init($params)
    {
        parent::init($params);

        $configRepository = pluginApp(ConfigRepository::class);
        $searchService = pluginApp(ItemSearchService::class);

        $relations = explode(", ", $configRepository->get("KALMARS.singleItemLists.relations"));
        $itemsToShow = $configRepository->get("KALMARS.singleItemLists.pages");
        $itemId = $this->item['documents'][0]['data']['item']['id'];
        //$relations = ["Similar", "Accessory", "ReplacementPart", "Bundle"];
        if( !empty($relations) && !empty($itemId) ){
            foreach ($relations as $relation) {
                $searchfactory = CrossSellingItems::getSearchFactory([
                    "itemId" => $itemId,
                    "relation" => $relation,
                ]);
                if ($itemsToShow > 0) {
                    $searchfactory->setPage(1, $itemsToShow);
                }
                $result = $searchService->getResult($searchfactory);
                $this->accessories[] = [
                    'relation' => $relation,
                    'items' => $result['documents'],
                ];
            }
        }
    }
}
