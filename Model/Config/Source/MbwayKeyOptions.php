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
use Ifthenpay\Payment\Gateway\Config\MbwayConfig;



class MbwayKeyOptions implements OptionSourceInterface
{
    private $configData;
    private $mbwayConfig;

    public function __construct(
        IfthenpayConfig $configData,
        MbwayConfig $mbwayConfig
    ) {
        $this->configData = $configData;
        $this->mbwayConfig = $mbwayConfig;
    }


    /**
     * Return array of order canceling options as value-label pairs
     * @return array
     */
    public function toOptionArray()
    {
        $mbwayKeys = $this->configData->getUserPaymentMethodAccounts(ConfigVars::MBWAY);


        // remove keys in use by other stores (in case of multistore)
        $thisKey = $this->mbwayConfig->getKey();
        $otherKeysInUse = $this->mbwayConfig->getOtherKeysInUse($thisKey);

        // remove thiskey from list of excludes
        foreach ($otherKeysInUse as $key => $inUse) {
            if ($thisKey === $inUse) {
                unset($otherKeysInUse[$key]);
            }
        }

        $optionArray = [];
        foreach ($mbwayKeys as $key => $value) {

            if (is_array($value) && isset($value['Entidade']) && isset($value['SubEntidade'])) {

                foreach ($value['SubEntidade'] as $mbwayKey) {

                    // only add option for keys not in the otherKeysInUse
                    if (!in_array($mbwayKey, $otherKeysInUse)) {
                        $optionArray[] = [
                            'value' => $mbwayKey,
                            'label' => $mbwayKey
                        ];
                    }
                }
            }
        }

        if (!empty($optionArray)) {
            array_unshift($optionArray, ['value' => '', 'label' => __('Select a MB WAY key')]);
        } else {
            // fallback for when there are no mbway keys and the the request mbway account button is not showing
            array_unshift($optionArray, ['value' => 'nokey', 'label' => __('No MB WAY keys found')]);
        }

        return $optionArray;
    }
}
