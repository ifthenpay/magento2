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

declare(strict_types=1);

namespace Ifthenpay\Payment\Lib\Forms;

use Ifthenpay\Payment\Lib\Forms\ConfigForm;
use Ifthenpay\Payment\Lib\Payments\Gateway;
use Ifthenpay\Payment\Lib\Base\Payments\MbwayBase;

class MbwayConfigForm extends ConfigForm
{
    protected $paymentMethod = Gateway::MBWAY;


    protected function checkConfigValues($useEntidade = true): void
    {
        if (!empty($this->configData)) {
            $this->options[$this->configData['mbwayKey']] = $this->configData['mbwayKey'];
        }
    }

    protected function checkIfConfigValueIsSet(): bool
    {
        if ($this->configData['mbwayKey'] !== 'Choose Account') {
            return true;
        } else {
            return false;
        }
    }

    public function displayCallbackInfo(): bool
    {
        return isset($this->configData['mbwayKey']);
    }


    public function setGatewayBuilderData(): void
    {
        parent::setGatewayBuilderData();
        $this->gatewayDataBuilder->setEntidade(strtoupper($this->paymentMethod));
        $this->gatewayDataBuilder->setSubEntidade($this->configData['mbwayKey']);
    }
}
