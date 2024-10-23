<?php

/**
 * @category    Gateway Payment
 * @package     Ifthenpay_Payment
 * @author      Ifthenpay
 * @copyright   Ifthenpay (https://www.ifthenpay.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Ifthenpay\Payment\Model\Config\Source;

use Ifthenpay\Payment\Config\ConfigVars;
use Magento\Framework\Data\OptionSourceInterface;
use Ifthenpay\Payment\Gateway\Config\IfthenpayConfig;
use Ifthenpay\Payment\Gateway\Config\IfthenpaygatewayConfig;
use Ifthenpay\Payment\Logger\Logger;




class IfthenpaygatewayKeyOptions implements OptionSourceInterface
{
    private $configData;
    private $ifthenpaygatewayConfig;
    private $logger;

    public function __construct(
        IfthenpayConfig $configData,
        IfthenpaygatewayConfig $ifthenpaygatewayConfig,
        Logger $logger
    ) {
        $this->configData = $configData;
        $this->ifthenpaygatewayConfig = $ifthenpaygatewayConfig;
        $this->logger = $logger;
    }


    /**
     * Return array of order canceling options as value-label pairs
     * @return array
     */
    public function toOptionArray()
    {
        $ifthenpaygatewayKeys = $this->configData->getUserPaymentMethodAccounts(ConfigVars::IFTHENPAYGATEWAY);

        // remove keys in use by other stores (in case of multistore)
        $thisKey = $this->ifthenpaygatewayConfig->getKey();
        $otherKeysInUse = $this->ifthenpaygatewayConfig->getOtherKeysInUse($thisKey);

        // remove thiskey from list of excludes
        foreach ($otherKeysInUse as $key => $inUse) {
            if ($thisKey === $inUse) {
                unset($otherKeysInUse[$key]);
            }
        }

        try {
            $optionArray = [];

            foreach ($ifthenpaygatewayKeys as $ifthenpaygatewayKey) {

                if (!in_array($ifthenpaygatewayKey['GatewayKey'], $otherKeysInUse)) {
                    $optionArray[] = [
                        'value' => $ifthenpaygatewayKey['GatewayKey'],
                        'label' => $ifthenpaygatewayKey['Alias'],
                        'type' => $ifthenpaygatewayKey['Tipo']
                    ];
                }
            }
        } catch (\Throwable $th) {
            $this->logger->error('admin/ifthenpaygatewayKeyOptions', [
                'error' => $th->getMessage(),
            ]);
            return [];
        }


        if (!empty($optionArray)) {
            array_unshift($optionArray, ['value' => '', 'label' => __('Select a Ifthenpay Gateway key')]);
        } else {
            // fallback for when there are no ifthenpaygateway keys and the the request ifthenpaygateway account button is not showing
            array_unshift($optionArray, ['value' => 'nokey', 'label' => __('No Ifthenpay Gateway keys found')]);
        }

        return $optionArray;
    }
}
