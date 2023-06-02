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
use Ifthenpay\Payment\Gateway\Config\CcardConfig;



class CcardKeyOptions implements OptionSourceInterface
{
    private $configData;
    private $ccardConfig;

    public function __construct(
        IfthenpayConfig $configData,
        CcardConfig $ccardConfig
    ) {
        $this->configData = $configData;
        $this->ccardConfig = $ccardConfig;
    }


    /**
     * Return array of order canceling options as value-label pairs
     * @return array
     */
    public function toOptionArray()
    {
        $cardKeys = $this->configData->getUserPaymentMethodAccounts(ConfigVars::CCARD);

        // remove keys in use by other stores (in case of multistore)
        $thisKey = $this->ccardConfig->getKey();
        $otherKeysInUse = $this->ccardConfig->getOtherKeysInUse($thisKey);

        // remove thiskey from list of excludes
        foreach ($otherKeysInUse as $key => $inUse) {
            if ($thisKey === $inUse) {
                unset($otherKeysInUse[$key]);
            }
        }

        $optionArray = [];
        foreach ($cardKeys as $key => $value) {

            if (is_array($value) && isset($value['Entidade']) && isset($value['SubEntidade'])) {

                foreach ($value['SubEntidade'] as $ccardKey) {

                    // only add option for keys not in the otherKeysInUse
                    if (!in_array($ccardKey, $otherKeysInUse)) {
                        $optionArray[] = [
                            'value' => $ccardKey,
                            'label' => $ccardKey
                        ];
                    }
                }
            }
        }

        if (!empty($optionArray)) {
            array_unshift($optionArray, ['value' => '', 'label' => __('Select a Credit Card key')]);
        } else {
            // fallback for when there are no ccard keys and the the request ccard account button is not showing
            array_unshift($optionArray, ['value' => 'nokey', 'label' => __('No Credit Card keys found')]);
        }

        return $optionArray;
    }
}
