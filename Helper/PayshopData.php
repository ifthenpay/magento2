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

class PayshopData extends Data implements IfthenpayDataInterface
{
    const USER_PAYSHOP_KEY = 'payment/ifthenpay/payshop/payshopKey';
    const USER_PAYSHOP_VALIDADE = 'payment/ifthenpay/payshop/validade';

    protected $paymentMethod = 'payshop';

    public function getConfig(): array
    {
        $dataPayshopKey = $this->scopeConfig->getValue(self::USER_PAYSHOP_KEY, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $this->getStoreCode());

        if ($dataPayshopKey) {
            return array_merge(parent::getConfig(), [
                'payshopKey' => $dataPayshopKey,
                'validade' => $this->scopeConfig->getValue(self::USER_PAYSHOP_VALIDADE, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $this->getStoreCode())
            ]);
        } else {
            return [];
        }

    }

    public function deleteConfig(): void
    {
        $this->configWriter->delete(self::USER_PAYSHOP_KEY, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);
        $this->scopeConfig->clean();
        parent::deleteConfig();
    }
}