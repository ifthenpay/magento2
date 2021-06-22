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

class CCardData extends Data implements IfthenpayDataInterface
{
    const USER_CCARD_KEY = 'payment/ifthenpay/ccard/ccardKey';

    protected $paymentMethod = 'ccard';

    public function getConfig(): array
    {
        $ccardKey = $this->scopeConfig->getValue(self::USER_CCARD_KEY, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $this->getStoreCode());
        if ($ccardKey) {
            return array_merge(parent::getConfig(), [
                'ccardKey' => $ccardKey
            ]);
        } else {
            return [];
        }

    }

    public function deleteConfig(): void
    {
        $this->configWriter->delete(self::USER_CCARD_KEY, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);
        $this->scopeConfig->clean();
        parent::deleteConfig();
    }
}