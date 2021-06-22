<?php
/**
* Ifthenpay_Payment module dependency
*
* @category    Gateway Payment
* @package     Ifthenpay_Payment
* @author      Ifthenpay
* @copyright   Ifthenpay (http://www.ifthenpay.com)
* @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*/

namespace Ifthenpay\Payment\Helper;

use Ifthenpay\Payment\Helper\Data;
use Ifthenpay\Payment\Helper\Contracts\IfthenpayDataInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class MbwayData extends Data implements IfthenpayDataInterface
{
    const USER_MBWAY_KEY = 'payment/ifthenpay/mbway/mbwayKey';
    const CALLBACK_URL = 'payment/ifthenpay/mbway/callbackUrl';
    const CHAVE_ANTI_PHISHING = 'payment/ifthenpay/mbway/chaveAntiPhishing';
    const CALLBACK_ACTIVATED = 'payment/ifthenpay/mbway/callbackActivated';
    const CANCEL_MBWAY_ORDER = 'payment/ifthenpay/mbway/cancelMbwayOrder';

    protected $paymentMethod = 'mbway';

    public function getConfig(): array
    {
        $dataMbwayKey = $this->scopeConfig->getValue(self::USER_MBWAY_KEY, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $this->getStoreCode());
        $cancelMbwayOrder = $this->scopeConfig->getValue(self::CANCEL_MBWAY_ORDER, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $this->getStoreCode());
        if ($dataMbwayKey) {
            return array_merge(parent::getConfig(), [
                'mbwayKey' => $dataMbwayKey,
                'cancelMbwayOrder' => $cancelMbwayOrder
            ]);
        }

    }

    public function deleteConfig(): void
    {
        $this->configWriter->delete(self::USER_MBWAY_KEY, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);
        parent::deleteConfig();
    }
}