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

use Ifthenpay\Payment\Lib\Payments\Gateway;
use Ifthenpay\Payment\Model\PaymentModelBase;
use Ifthenpay\Payment\Lib\Contracts\Payments\PaymentConfigInterface;

class MbwayPayment extends PaymentModelBase implements PaymentConfigInterface
{
    protected $_code = Gateway::MBWAY;

    public function checkPaymentConfig(): bool
    {
        if (empty($this->configData)) {
            return false;
        }

        if (!$this->configData['mbwayKey'] || $this->configData['mbwayKey'] === 'Choose Account') {
            $this->ifthenpayLogger->debug('Mbway key is not set', ['configData' => $this->configData]);
            return false;
        }
        return true;
    }
}
