<?php //strict

namespace KALMARS\Api\Resources;

use Plenty\Plugin\Http\Request;
use Plenty\Plugin\Http\Response;
use IO\Api\ApiResource;
use IO\Api\ApiResponse;
use IO\Api\ResponseCode;
use Plenty\Plugin\Application;
use Plenty\Modules\Plugin\Contracts\PluginRepositoryContract;
use Plenty\Modules\Plugin\PluginSet\Contracts\PluginSetRepositoryContract;
use KALMARS\Services\WebhookService;

class WebhookResource extends ApiResource
{
    public function __construct(
        Request $request,
        ApiResponse $response
    )
    {
        parent::__construct($request, $response);
    }


    public function handle():Response
    {
        $webhookService = pluginApp(WebhookService::class);
        $result = $webhookService->handle();

        return $this->response->create($result, ResponseCode::OK);
    }
}
