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

namespace Ifthenpay\Payment\Lib\Strategy\Form;

use Ifthenpay\Payment\Lib\Factory\Config\IfthenpayConfigFormFactory;

class IfthenpayConfigForms
{
    private $paymentMethod;
    private $ifthenpayController;
    private $configData;

	public function __construct(IfthenpayConfigFormFactory $ifthenpayConfigFormFactory)
	{
        $this->ifthenpayConfigFormFactory = $ifthenpayConfigFormFactory;
	}

    public function buildForm(): array
    {
        return $this->ifthenpayConfigFormFactory->setType($this->paymentMethod)
            ->build()
            ->setIfthenpayController($this->ifthenpayController)
            ->setConfigData($this->configData)
            ->getForm();
    }

    public function processForm(): void
    {
        $this->ifthenpayConfigFormFactory->setType($this->paymentMethod)
            ->build()
            ->setIfthenpayController($this->ifthenpayController)
            ->setConfigData($this->configData)
            ->processForm();
    }

    public function deleteConfigFormValues(): void
    {
        $this->ifthenpayConfigFormFactory->setType($this->paymentMethod)
            ->build()
            ->setIfthenpayController($this->ifthenpayController)
            ->deleteConfigValues();
    }

    public function setIfthenpayController($ifthenpayController)
    {
        $this->ifthenpayController = $ifthenpayController;

        return $this;
    }

    public function setConfigData($configData)
    {
        $this->configData = $configData;

        return $this;
    }

    public function setPaymentMethod($paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }
}
