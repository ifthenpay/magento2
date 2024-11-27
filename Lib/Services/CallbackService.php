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

    public function setUrlCallback(string $urlCallback){
        $this->urlCallback = $urlCallback;
    }

    public function getUrlCallback(): string{
        return $this->urlCallback;
    }


    /**
     * used in update script to reactivate callbacks, since ifthenpaygateway introduced changes to the callback URL
     */
    public function reactivateCallback(string $scope, string $scopeId, string $paymentMethod, bool $useHttps) {

        $paymentMethodCode = ConfigVars::IFTHENPAY_CODE . '_' . $paymentMethod;

        $paymentMethodConfig = $this->configFactory->createConfig($paymentMethodCode);
        $paymentMethodConfig->setScopeAndScopeCode($scope, $scopeId);
        $this->configData->setScopeAndScopeCode($scope, $scopeId);
        $this->prepareParamsForRequest($paymentMethodConfig);

        if ($useHttps && !strpos($this->getUrlCallback(), 'https:')) {
            $safeCallbackUrl = str_replace('http:', 'https:', $this->getUrlCallback());
            $this->setUrlCallback($safeCallbackUrl);
        }

        $this->requestCallbackActivation();
        $paymentMethodConfig->saveCallbackUrl($this->urlCallback, $this->antiPhishingKey);


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
                ($isCallbackActivated !== true || $paymentMethod === ConfigVars::IFTHENPAYGATEWAY)
            ) {

                if ($paymentMethod === ConfigVars::IFTHENPAYGATEWAY) {
                    $forceActivation = !$isCallbackActivated;
                    $this->bulkActivateIfthenpaygatewayCallbacks($forceActivation);
                } else {
                    $this->prepareParamsForRequest($paymentMethodConfig);
                    $this->requestCallbackActivation();
                    $paymentMethodConfig->saveCallbackUrl($this->urlCallback, $this->antiPhishingKey);
                }
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

    private function bulkActivateIfthenpaygatewayCallbacks(bool $forceActivation)
    {

        // get methods to activate from config
        $ifthenpaygatewayConfig = $this->configFactory->createConfig(ConfigVars::IFTHENPAYGATEWAY_CODE);
        $paymentMethods = $ifthenpaygatewayConfig->getPaymentMethods();
        $paymentMethods = $paymentMethods != '' ? json_decode($paymentMethods, true) : [];

        if ($forceActivation) {
            $previousActivatedMethods = [];
        } else {
            $previousActivatedMethods = $ifthenpaygatewayConfig->getPreviousActivatedCallbacks();
            $previousActivatedMethods = $previousActivatedMethods != '' ? json_decode($previousActivatedMethods, true) : [];
        }

        // loop through them
        $paymentMethodsToActivate = [];

        if (
            empty($previousActivatedMethods)
        ) {
            $paymentMethodsToActivate = array_filter($paymentMethods, fn($item) => $item['is_active'] === '1');
        } else {
            foreach ($paymentMethods as $key => $paymentMethod) {

                if (
                    (isset($previousActivatedMethods[$key]) && $previousActivatedMethods[$key]['is_active'] === '0' && $paymentMethod['is_active'] === '1') ||
                    (!isset($previousActivatedMethods[$key]) && $paymentMethod['is_active'] === '1') ||
                    ((isset($previousActivatedMethods[$key]) && $previousActivatedMethods[$key]['is_active'] === '1' && $paymentMethod['is_active'] === '1') &&
                        $previousActivatedMethods[$key]['account'] !== $paymentMethod['account'])
                ) {
                    $paymentMethodsToActivate[$key] = $paymentMethod;
                }
            }
        }


        if (!empty($paymentMethodsToActivate)) {

            $antiPhishingKey = $ifthenpaygatewayConfig->getAntiPhishingKey();
            $antiPhishingKey = $antiPhishingKey != '' ? $antiPhishingKey : md5((string) rand());

            foreach ($paymentMethodsToActivate as $key => $values) {

                $paymentMethodEntitySubentity = explode('|', $values['account']);
                $paymentMethodEntity = trim($paymentMethodEntitySubentity[0]);
                $paymentMethodSubEntity = trim($paymentMethodEntitySubentity[1]);


                $this->antiPhishingKey = $antiPhishingKey;
                $this->urlCallback = $this->configData->getWebsiteBaseUrl() . $ifthenpaygatewayConfig->getCallbackUrlPartialStringWithScopeAndScopeCode();
                $this->entity = $paymentMethodEntity;
                $this->subEntity = $paymentMethodSubEntity;
                $this->backofficeKey = $this->configData->getBackofficeKey();

                $this->requestCallbackActivation();
            }
            $ifthenpaygatewayConfig->saveCallbackUrl($this->urlCallback, $this->antiPhishingKey);
        }
        // saveActivatedCallbacks to later check which were already activated
        $ifthenpaygatewayConfig->saveActivatedCallbacks(json_encode($paymentMethods));
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
