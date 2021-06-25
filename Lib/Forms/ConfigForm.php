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

use Ifthenpay\Payment\Lib\Payments\Gateway;
use Ifthenpay\Payment\Lib\Request\WebService;
use Magento\Store\Model\StoreManagerInterface;
use Ifthenpay\Payment\Helper\Factory\DataFactory;
use Ifthenpay\Payment\Lib\Callback\CallbackFactory;
use Ifthenpay\Payment\Lib\Builders\GatewayDataBuilder;

abstract class ConfigForm
{
    protected $paymentMethod;
    protected $gatewayDataBuilder;
    private $gateway;
    protected $formFactory;
    protected $options;
    protected $magentoCallbackFactory;
    protected $dataFactory;
    protected $webservice;
    protected $storeManager;
    protected $configData;
    protected $helperData;

    public function __construct(
        GatewayDataBuilder $gatewayDataBuilder,
        Gateway $gateway,
        DataFactory $dataFactory,
        WebService $webservice,
        StoreManagerInterface $storeManager,
        CallbackFactory $callbackFactory
    )
    {
        $this->gatewayDataBuilder = $gatewayDataBuilder;
        $this->gateway = $gateway;
        $this->dataFactory = $dataFactory;
        $this->webservice = $webservice;
        $this->storeManager = $storeManager;
        $this->magentoCallbackFactory = $callbackFactory;
        $this->helperData = $this->dataFactory->setType($this->paymentMethod)->build();
        $this->configData = $this->helperData->getConfig();
        $this->options = [
            'Choose Account' => 'Choose Account'
        ];
    }

    protected function setGatewayBuilderData(): void
    {
        $this->gatewayDataBuilder->setBackofficeKey($this->helperData->getBackofficeKey());
    }

    protected function getCallbackControllerUrl(): string
    {
        return $this->storeManager->getStore()->getBaseUrl() . 'ifthenpay/Frontend/Callback';
    }

    public function getOptions($useEntidade = true): array
    {
        $this->checkConfigValues($useEntidade);
        if ($this->helperData->getBackofficeKey() && !isset($this->options['Choose Account']) && $useEntidade) {
            $this->gateway->setAccount($this->helperData->getUserAccount());
            foreach ($this->gateway->getEntidadeSubEntidade($this->paymentMethod) as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $key2 => $value2) {
                        if (strlen($value2) > 3) {
                            $this->options[$value2] = $value2;
                        }
                    }
                } else {
                    $this->options[$value] = $value;
                }
            }
        }
        return $this->options;
    }

    public function createCallback(): array
    {
        try {
            if (!empty($this->configData)) {

                $this->setGatewayBuilderData();

                $activateCallback = !$this->helperData->getSandboxMode() && $this->configData['activateCallback'] ? true : false;

                $ifthenpayCallback = $this->magentoCallbackFactory->create([
                    'data' => $this->gatewayDataBuilder,
                    'webservice' => $this->webservice
                ]);

                if (!$this->configData['callbackActivated']) {
                    $ifthenpayCallback->make($this->paymentMethod, $this->getCallbackControllerUrl(), $activateCallback);
                }

                if (!$this->configData['callbackUrl'] && !$this->configData['chaveAntiPhishing']) {
                    $this->helperData->saveCallback(
                        $ifthenpayCallback->getUrlCallback(),
                        $ifthenpayCallback->getChaveAntiPhishing(),
                        $ifthenpayCallback->getActivatedFor()
                    );
                    $this->configData['callbackUrl'] = $ifthenpayCallback->getUrlCallback();
                    $this->configData['chaveAntiPhishing'] = $ifthenpayCallback->getChaveAntiPhishing();
                    $this->configData['callbackActivated'] = $ifthenpayCallback->getActivatedFor();
                } else if ($this->configData['callbackUrl'] && $this->configData['chaveAntiPhishing'] && !$ifthenpayCallback->getActivatedFor() && $this->configData['callbackActivated']) {
                    $this->configData['callbackUrl'] = $this->configData['callbackUrl'];
                    $this->configData['chaveAntiPhishing'] = $this->configData['chaveAntiPhishing'];
                    $this->configData['callbackActivated'] = $this->configData['callbackActivated'];
                } else if ($this->configData['callbackUrl'] && $this->configData['chaveAntiPhishing'] && !$this->configData['callbackActivated'] && $ifthenpayCallback->getActivatedFor()) {
                    $this->helperData->saveCallback(
                        $ifthenpayCallback->getUrlCallback(),
                        $ifthenpayCallback->getChaveAntiPhishing(),
                        $ifthenpayCallback->getActivatedFor()
                    );
                    $this->configData['callbackUrl'] = $ifthenpayCallback->getUrlCallback();
                    $this->configData['chaveAntiPhishing'] = $ifthenpayCallback->getChaveAntiPhishing();
                    $this->configData['callbackActivated'] = $ifthenpayCallback->getActivatedFor();
                }

            }
            return $this->configData;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    abstract protected function checkConfigValues($useEntidade = true): void;
    abstract public function displayCallbackInfo(): bool;
}
