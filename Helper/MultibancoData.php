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
use Ifthenpay\Payment\Lib\Payments\Gateway;
use Ifthenpay\Payment\Lib\Payments\Payment;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Ifthenpay\Payment\Helper\Contracts\IfthenpayDataInterface;

class MultibancoData extends Data implements IfthenpayDataInterface
{
    const USER_ENTIDADE = 'payment/ifthenpay/multibanco/entidade';
    const USER_SUBENTIDADE = 'payment/ifthenpay/multibanco/subEntidade';

    protected $paymentMethod = Gateway::MULTIBANCO;

    public function getConfig(): array
    {

    // $websiteId = $this->storeManager->getStore()->getWebsiteId();;

    //     $dataEntidade = $this->scopeConfig->getValue(self::USER_ENTIDADE, 'website', $websiteId);
    //     $dataSubEntidade = $this->scopeConfig->getValue(self::USER_SUBENTIDADE, 'website', $websiteId);

        $dataEntidade = $this->scopeConfig->getValue(self::USER_ENTIDADE, $this->scopeType, $this->scopeId);
        $dataSubEntidade = $this->scopeConfig->getValue(self::USER_SUBENTIDADE, $this->scopeType, $this->scopeId);
        if ($dataEntidade && $dataSubEntidade) {

            return array_merge(parent::getConfig(), [
                'entidade' => $dataEntidade,
                'subEntidade' => $dataSubEntidade
            ]);
        } else {
            return [];
        }

    }

    public function deleteConfig(): void
    {
        $this->configWriter->delete(self::USER_ENTIDADE, $this->scopeType, $this->scopeId);
        $this->configWriter->delete(self::USER_SUBENTIDADE, $this->scopeType, $this->scopeId);
        parent::deleteConfig();
    }
}
