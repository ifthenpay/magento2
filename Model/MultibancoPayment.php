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

class MultibancoPayment extends PaymentModelBase implements PaymentConfigInterface
{
    protected $_code = Gateway::MULTIBANCO;

    public function checkPaymentConfig(): bool
    {
        if (empty($this->configData)) {
            return false;
        }

        if (!$this->configData['entidade'] || $this->configData['entidade'] === 'Choose Account') {
            $this->ifthenpayLogger->debug('Multibanco entidade is not set', ['configData' => $this->configData]);
            return false;
        }
        if (!$this->configData['subEntidade'] || $this->configData['subEntidade'] === 'Choose Account') {
            $this->ifthenpayLogger->debug('Multibanco subEntidade is not set', ['configData' => $this->configData]);
            return false;
        }
        return true;
    }
}
