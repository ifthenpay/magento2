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

namespace Ifthenpay\Payment\Lib\Factory\Config;

use Ifthenpay\Payment\Lib\Factory\Factory;
use Ifthenpay\Payment\Lib\Forms\ConfigForm;
use Ifthenpay\Payment\Lib\Payments\Gateway;
use Ifthenpay\Payment\Lib\Request\WebService;
use Magento\Store\Model\StoreManagerInterface;
use Ifthenpay\Payment\Lib\Forms\CCardConfigForm;
use Ifthenpay\Payment\Lib\Forms\MbwayConfigForm;
use Ifthenpay\Payment\Helper\Factory\DataFactory;
use Ifthenpay\Payment\Lib\Forms\PayshopConfigForm;
use Ifthenpay\Payment\Lib\Callback\CallbackFactory;
use Ifthenpay\Payment\Lib\Forms\MultibancoConfigForm;
use Ifthenpay\Payment\Lib\Builders\GatewayDataBuilder;

class IfthenpayConfigFormFactory extends Factory
{
    private $gatewayDataBuilder;
    private $gateway;
    private $dataFactory;
    private $webService;
    private $storeManager;
    private $magentoCallbackFactory;

	public function __construct(
        GatewayDataBuilder $gatewayDataBuilder,
        Gateway $gateway,
        DataFactory $dataFactory,
        WebService $webService,
        StoreManagerInterface $storeManager,
        CallbackFactory $callbackFactory
    )
	{
        $this->gatewayDataBuilder = $gatewayDataBuilder;
        $this->gateway = $gateway;
        $this->dataFactory = $dataFactory;
        $this->webService = $webService;
        $this->storeManager = $storeManager;
        $this->magentoCallbackFactory = $callbackFactory;
	}

    public function build(
    ): ConfigForm {
        switch ($this->type) {
            case Gateway::MULTIBANCO:
                return new MultibancoConfigForm(
                    $this->gatewayDataBuilder,
                    $this->gateway,
                    $this->dataFactory,
                    $this->webService,
                    $this->storeManager,
                    $this->magentoCallbackFactory
                );
            case Gateway::MBWAY:
                return new MbwayConfigForm(
                    $this->gatewayDataBuilder,
                    $this->gateway,
                    $this->dataFactory,
                    $this->webService,
                    $this->storeManager,
                    $this->magentoCallbackFactory
                );
            case Gateway::PAYSHOP:
                return new PayshopConfigForm(
                    $this->gatewayDataBuilder,
                    $this->gateway,
                    $this->dataFactory,
                    $this->webService,
                    $this->storeManager,
                    $this->magentoCallbackFactory
                );
            case Gateway::CCARD:
                return new CCardConfigForm(
                    $this->gatewayDataBuilder,
                    $this->gateway,
                    $this->dataFactory,
                    $this->webService,
                    $this->storeManager,
                    $this->magentoCallbackFactory
                );
            default:
                throw new \Exception('Unknown Admin Config Form');
        }
    }
}
