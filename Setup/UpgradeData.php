<?php

namespace Ifthenpay\Payment\Setup;

use Ifthenpay\Payment\Lib\Services\CallbackService;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Ifthenpay\Payment\Logger\Logger;


class UpgradeData implements UpgradeDataInterface
{


    private $logger;
    private $callbackService;


    public function __construct(
        Logger $logger,
        CallbackService $callbackService
    ) {
        $this->logger = $logger;
        $this->callbackService = $callbackService;
    }


    public function upgrade(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ): void {


        $this->logger->info('running upgrade script', []);

        $setup->startSetup();

        if (version_compare($context->getVersion(), '2.3.0', '<')) {
            $this->upgradeToVersion_2_3_0($setup);
        }

        $setup->endSetup();
    }

    private function upgradeToVersion_2_3_0(ModuleDataSetupInterface $setup): void
    {
        try {

            $methodArray = [
                'multibanco',
                'mbway',
                'payshop',
                'cofidis'
            ];

            $connection = $setup->getConnection();

            foreach ($methodArray as $method) {

                $selectIsActivated = "SELECT `core_config_data`.`scope`, `core_config_data`.`scope_id` FROM `core_config_data` WHERE (path = 'payment/ifthenpay_" . $method . "/is_callback_activated') GROUP BY `scope`, `scope_id`";

                $isActivatedArray = $connection->fetchAll($selectIsActivated);

                foreach ($isActivatedArray as $isActivated) {
                    $scope = $isActivated['scope'];
                    $scopeId = $isActivated['scope_id'];
                    $selectExistingCallbackUrl = "SELECT `core_config_data`.`value` FROM `core_config_data` WHERE path = 'payment/ifthenpay_" . $method . "/callback_url' AND scope = '" . $scope . "'
                    AND scope_id = '" . $scopeId . "'";

                    $existingCallbackUrl = $connection->fetchOne($selectExistingCallbackUrl);

                    if (!$existingCallbackUrl) {
                        continue;
                    }

                    $forceHttps = strpos($existingCallbackUrl, 'https:') === false ? false : true;

                    $this->callbackService->reactivateCallback($scope, $scopeId, $method, $forceHttps);
                }
            }
            $this->logger->info('upgrade script run successfully', []);
        } catch (\Throwable $th) {
            $this->logger->error('Error during ifthenpay update script', [
                'error' => $th,
            ]);
        }
    }
}
