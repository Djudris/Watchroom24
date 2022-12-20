<?php
namespace KALMARS\Services;

use Plenty\Plugin\Application;
use Plenty\Modules\Plugin\Contracts\PluginRepositoryContract;
use Plenty\Modules\Plugin\PluginSet\Contracts\PluginSetRepositoryContract;
use KALMARS\Repositories\KeyValueRepository;

class WebhookService
{
    public function cronHandle()
    {
        $keyValueRepository = pluginApp(KeyValueRepository::class);
        $cronLock = $keyValueRepository->getItem('cronLock');

        $shouldRunCron = false;
        if (empty($cronLock['lastRun']) || intval($cronLock['lastRun']) <= (time() - 7*86400)) { // 7 days
            $shouldRunCron = true;

            $keyValueRepository->setItem('cronLock', [
                'lastRun' => time(),
            ]);
        }

        if ($shouldRunCron) {
            $this->handle();
        }
    }

    public function handle()
    {
        $app = pluginApp(Application::class);
        $pluginSetRepo = pluginApp(PluginSetRepositoryContract::class);
        $pluginSetId = $app->getPluginSetId();
        $pluginSet = $pluginSetRepo->get($pluginSetId);

        $requests = [];
        if (count($pluginSet->webstores) == 0) {
            return $requests;
        }

        $request = [
            'template' => 'KALMARS',
            'templateVersion' => $this->getPluginVersion('KALMARS', $pluginSetId),
            'plentyId' => $app->getPlentyId(),
            'pluginSetId' => $pluginSet->id,
            'pluginSetName' => $pluginSet->name,
            'storeIdentifier' => '',
            'storeDomain' => '',
            'ceresVersion' => $this->getPluginVersion('Ceres', $pluginSetId),
            'ioVersion' => $this->getPluginVersion('IO', $pluginSetId)
        ];

        foreach ($pluginSet->webstores as $webstore) {
            $webstoreId = $webstore->storeIdentifier;
            $webstoreDomainSsl = $webstore->configuration->domainSsl;

            $request['storeIdentifier'] = $webstoreId;
            $request['storeDomain'] = $webstoreDomainSsl;
            $requests[] = $request;
            $json_request = json_encode($request, JSON_UNESCAPED_UNICODE);

            $ch = curl_init('https://hook.integromat.com/pyfyu1m8d53w4n7ppxwr79bvgxz7jq79');
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Expect:'));
            curl_setopt($ch, CURLOPT_USERAGENT, 'KALMARS');
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json_request);
            $result = curl_exec($ch);
            curl_close($ch);
        }

        return $requests;
    }

    private function getPluginVersion($pluginName, $pluginSetId)
    {
        $pluginRepo = pluginApp(PluginRepositoryContract::class);
        $plugin = $pluginRepo->getPluginByName($pluginName);
        $plugin = $pluginRepo->decoratePlugin($plugin, $pluginSetId);

        return $plugin->versionProductive;
    }
}
