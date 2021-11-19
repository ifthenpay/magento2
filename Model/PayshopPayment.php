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

namespace Ifthenpay\Payment\Model;

use Ifthenpay\Payment\Lib\Contracts\Payments\PaymentConfigInterface;
use Ifthenpay\Payment\Lib\Payments\Gateway;
use Ifthenpay\Payment\Model\PaymentModelBase;

class PayshopPayment extends PaymentModelBase implements PaymentConfigInterface
{
    protected $_code = Gateway::PAYSHOP;

    public function checkPaymentConfig(): bool
    {
        if (empty($this->configData)) {
            return false;
        } else if (!$this->configData['payshopKey'] || $this->configData['payshopKey'] === 'Choose Account') {
            $this->ifthenpayLogger->debug('Payshop key is not set', ['configData' => $this->configData]);
            return false;
        }
        return true;
    }
}
