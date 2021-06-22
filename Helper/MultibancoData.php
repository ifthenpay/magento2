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

class MultibancoData extends Data implements IfthenpayDataInterface
{
    const USER_ENTIDADE = 'payment/ifthenpay/multibanco/entidade';
    const USER_SUBENTIDADE = 'payment/ifthenpay/multibanco/subEntidade';

    protected $paymentMethod = 'multibanco';

    public function getConfig(): array
    {
        $dataEntidade = $this->scopeConfig->getValue(self::USER_ENTIDADE, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $this->getStoreCode());
        $dataSubEntidade = $this->scopeConfig->getValue(self::USER_SUBENTIDADE, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $this->getStoreCode());

        if ($dataEntidade && $dataSubEntidade) {

            return array_merge(parent::getConfig(), [
                'entidade' => $dataEntidade,
                'subEntidade' => $dataSubEntidade,
            ]);
        } else {
            return [];
        }

    }

    public function deleteConfig(): void
    {
        $this->configWriter->delete(self::USER_ENTIDADE, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);
        $this->configWriter->delete(self::USER_SUBENTIDADE, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);
        parent::deleteConfig();
    }
}