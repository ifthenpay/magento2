<?php
/**
 * @category    Gateway Payment
 * @package     Ifthenpay_Payment
 * @author      Ifthenpay
 * @copyright   Ifthenpay (https://www.ifthenpay.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
declare(strict_types=1);

namespace Ifthenpay\Payment\Lib\Services;

use Ifthenpay\Payment\Config\ConfigVars;
use Ifthenpay\Payment\Lib\HttpClient;
use Ifthenpay\Payment\Gateway\Config\IfthenpayConfig;
use Ifthenpay\Payment\Lib\Factory\ConfigFactory;


class CallbackService
{
    private $configFactory;
    private $configData;
    private $httpClient;
    private $entity;
    private $subEntity;
    private $backofficeKey;
    private $urlCallback;
    private $antiPhishingKey;

    public function __construct(
        HttpClient $httpClient,
        IfthenpayConfig $configData,
        ConfigFactory $configFactory
    ) {
        $this->httpClient = $httpClient;
        $this->configData = $configData;
        $this->configFactory = $configFactory;
    }

    public function toggleCallback()
    {



        $paymentMethods = $this->configData->getUserPaymentMethods();

        // activate or deactivate callback for each payment method
        foreach ($paymentMethods as $paymentMethod) {

            // doesn't run for ccard
            if ($paymentMethod === ConfigVars::CCARD) {
                continue;
            }

            $paymentMethodCode = ConfigVars::IFTHENPAY_CODE . '_' . $paymentMethod;

            $paymentMethodConfig = $this->configFactory->createConfig($paymentMethodCode);

            $isPaymentMethodActive = $paymentMethodConfig->getIsActive();

            $isActivatingCallback = $paymentMethodConfig->getActivateCallback();
            $isCallbackActivated = $paymentMethodConfig->getIsCallbackActivated();



            if (
                $isPaymentMethodActive === true &&
                $isActivatingCallback === true &&
                $isCallbackActivated !== true
            ) {
                $this->prepareParamsForRequest($paymentMethodConfig);
                $this->requestCallbackActivation();
                $paymentMethodConfig->saveCallbackUrl($this->urlCallback, $this->antiPhishingKey);
            }

            // if payment method callbackActive is set to
            if (
                $isActivatingCallback !== true &&
                $isCallbackActivated === true
            ) {
                $paymentMethodConfig->deactivateCallback();
            }
        }
    }

    private function prepareParamsForRequest($paymentMethodConfig): void
    {
        $this->antiPhishingKey = md5((string) rand());
        $this->urlCallback = $this->configData->getWebsiteBaseUrl() . $paymentMethodConfig->getCallbackUrlPartialStringWithScopeAndScopeCode();
        $this->entity = $paymentMethodConfig->getEntity();
        $this->subEntity = $paymentMethodConfig->getSubEntity();
        $this->backofficeKey = $this->configData->getBackofficeKey();
    }

    public function requestCallbackActivation(): void
    {
        $this->httpClient->doPost(
            ConfigVars::API_URL_ACTIVATE_CALLBACK,
            [
                'chave' => $this->backofficeKey,
                'entidade' => $this->entity,
                'subentidade' => $this->subEntity,
                'apKey' => $this->antiPhishingKey,
                'urlCb' => $this->urlCallback,
            ]
        );

        if (!$this->httpClient->getStatus() === 200) {
            throw new \Exception("Error Activating Callback");
        }
    }







}
