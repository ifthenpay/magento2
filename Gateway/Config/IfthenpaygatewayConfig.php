<?php

/**
 * @category    Gateway Payment
 * @package     Ifthenpay_Payment
 * @author      Ifthenpay
 * @copyright   Ifthenpay (https://www.ifthenpay.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Ifthenpay\Payment\Gateway\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Payment\Gateway\Config\Config as GatewayConfig;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\ResourceConnection;
use Ifthenpay\Payment\Config\ConfigVars;
use Ifthenpay\Payment\Lib\Enums\ShowLogoOptionsEnum;
use Ifthenpay\Payment\Lib\Utility\ArrayTools;
use Ifthenpay\Payment\Model\ScopeConfigResolver;
use Magento\Store\Model\ScopeInterface;
use Ifthenpay\Payment\Lib\Utility\Version;


class IfthenpaygatewayConfig extends GatewayConfig
{
    public const METHOD_CODE = ConfigVars::IFTHENPAYGATEWAY_CODE;
    private const CONFIG_PATH = ConfigVars::MODULE . '/' . ConfigVars::IFTHENPAYGATEWAY_CODE . '/';
    private $scopeConfigResolver;
    private $configWriter;
    private $scopeConfig;
    private $scope;
    private $scopeCode;
    private $resourceConnection;
    private $version;


    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ScopeConfigResolver $scopeConfigResolver,
        WriterInterface $configWriter,
        ResourceConnection $resourceConnection,
        Version $version,
        string $methodCode = self::METHOD_CODE
    ) {
        parent::__construct($scopeConfig, $methodCode);
        $this->configWriter = $configWriter;
        $this->scopeConfig = $scopeConfig;
        $this->scopeConfigResolver = $scopeConfigResolver;
        $this->resourceConnection = $resourceConnection;
        $this->scope = $this->scopeConfigResolver->scope;
        $this->scopeCode = $this->scopeConfigResolver->scopeCode;
        $this->version = $version;
    }


    public function setScopeAndScopeCode($scope, $scopeCode)
    {
        $this->scope = $scope;
        $this->scopeCode = $scopeCode;
    }

    public function getScope()
    {
        return $this->scope;
    }
    public function getScopeCode()
    {
        return $this->scopeCode;
    }


    public function getConfigValue($field, $isFullPath = false): ?string
    {
        $path = self::CONFIG_PATH . $field;
        if ($isFullPath) {
            $path = $field;
        }

        $result = $this->scopeConfig->getValue(
            $path,
            $this->scope,
            $this->scopeCode
        );
        return $result;
    }

    public function saveConfigValue($field, $value)
    {
        if ($this->scope === ScopeInterface::SCOPE_WEBSITE) {
            $this->scope = ScopeInterface::SCOPE_WEBSITES;
        }

        $this->configWriter->save(
            self::CONFIG_PATH . $field,
            $value,
            $this->scope,
            $this->scopeCode
        );
    }


    /**
     * shorthand for using configwriter delete with scope and scopecode obtained from constructor
     * it is important to use the "$this->scopeConfig->clean();" after a block or single call
     */
    public function deleteConfigValue($field)
    {
        if ($this->scope === ScopeInterface::SCOPE_WEBSITE) {
            $this->scope = ScopeInterface::SCOPE_WEBSITES;
        }


        $this->configWriter->delete(
            self::CONFIG_PATH . $field,
            $this->scope,
            $this->scopeCode
        );
    }



    public function getCanNotifyInvoice(): bool
    {
        $value = $this->getConfigValue(ConfigVars::IFTHENPAYGATEWAY_SEND_INVOICE_EMAIL);

        return $value === '1' ? true : false;
    }

    public function getKey(): string
    {
        $value = $this->getConfigValue(ConfigVars::IFTHENPAYGATEWAY_KEY);

        return $value ?? '';
    }

    public function getEntity(): string
    {
        return ConfigVars::IFTHENPAYGATEWAY;
    }

    public function getSubEntity(): string
    {
        return $this->getKey();
    }



    public function getIsActive(): bool
    {
        $value = $this->getConfigValue(ConfigVars::IS_PAYMENT_METHOD_ACTIVE);

        return $value === '1' ? true : false;
    }
    public function getShowPaymentIcon(): string
    {
        // defaults to ifthenpay logo
        $value = $this->getConfigValue(ConfigVars::SHOW_PAYMENT_ICON);

        return $value ?? ShowLogoOptionsEnum::DEFAULT->value;
    }

    public function getTitle(): string
    {
        $value = $this->getConfigValue(ConfigVars::TITLE);

        return $value ?? '';
    }


    public function getPaymentMethods(): string
    {
        $value = $this->getConfigValue(ConfigVars::IFTHENPAYGATEWAY_PAYMENT_METHODS);

        return $value ?? '';
    }


    /**
     * return a value array of the logo image URLs from the saved ifthenpaay payment methods that are active
     */
    public function getSelectedPaymentMethodsUrls(): array
    {
        $paymentMethods = ArrayTools::jsonToArray($this->getPaymentMethods());

        $imgUrlArray = [];
        foreach ($paymentMethods as $paymentMethod) {
            if ($paymentMethod['is_active'] === '1') {
                $imgUrlArray[] = $paymentMethod['image_url'];
            }
        }

        return $imgUrlArray;
    }



    public function getDeadline(): string
    {
        $value = $this->getConfigValue(ConfigVars::IFTHENPAYGATEWAY_DEADLINE);

        return $value ?? '';
    }


    public function getPreviousActivatedCallbacks(): string
    {
        $value = $this->getConfigValue(ConfigVars::IFTHENPAYGATEWAY_PREVIOUS_ACTIVATED_CALLBACKS);

        return $value ?? '';
    }

    /**
     * this refers to the config of previous activated callback
     * since we require a history of registered activations
     */
    public function saveActivatedCallbacks(string $activatedCallbacks): void
    {
        $this->saveConfigValue(ConfigVars::IFTHENPAYGATEWAY_PREVIOUS_ACTIVATED_CALLBACKS, $activatedCallbacks);
        $this->scopeConfig->clean();
    }



    public function getDefaultPaymentMethod(): string
    {
        $value = $this->getConfigValue(ConfigVars::IFTHENPAYGATEWAY_DEFAULT_PAYMENT_METHOD);

        return $value ?? '';
    }


    public function getActivateCallback(): bool
    {
        $value = $this->getConfigValue(ConfigVars::IFTHENPAYGATEWAY_ACTIVATE_CALLBACK);

        return $value === '1' ? true : false;
    }

    public function getIsCallbackActivated(): bool
    {
        $value = $this->getConfigValue(ConfigVars::IFTHENPAYGATEWAY_PREVIOUS_ACTIVATED_CALLBACKS);
        $ActivatedCallbackArray = $value != '' ? json_decode($value, true) : [];

        if (! $this->getActivateCallback()) {
            return false;
        }

        foreach ($ActivatedCallbackArray as $callback) {
            if ($callback['is_active'] == '1') {
                return true;
            }
        }

        return false;
    }

    public function getCallbackUrlPartialStringWithScopeAndScopeCode(): string
    {
        return $this->version->replaceVersionVariables(ConfigVars::IFTHENPAYGATEWAY_CALLBACK_STRING) . '&scp=' . $this->scope . '&scpcd=' . $this->scopeCode;
    }

    public function saveCallbackUrl(string $callbackUrl, string $antiPhishingKey): void
    {
        $this->saveConfigValue(ConfigVars::IFTHENPAYGATEWAY_CALLBACK_URL, $callbackUrl);
        $this->saveConfigValue(ConfigVars::IFTHENPAYGATEWAY_ANTI_PHISHING_KEY, $antiPhishingKey);
        $this->scopeConfig->clean();
    }


    public function getCallbackUrl(): string
    {
        $value = $this->getConfigValue(ConfigVars::IFTHENPAYGATEWAY_CALLBACK_URL);

        return $value ?? '';
    }

    public function getAntiPhishingKey(): string
    {
        $value = $this->getConfigValue(ConfigVars::IFTHENPAYGATEWAY_ANTI_PHISHING_KEY);

        return $value ?? '';
    }


    public function deactivateCallback(): void
    {
        $this->saveConfigValue(ConfigVars::IFTHENPAYGATEWAY_PREVIOUS_ACTIVATED_CALLBACKS, '');
        $this->scopeConfig->clean();
    }

    public function deleteAllPaymentMethodConfig()
    {
        $this->deleteConfigValue(ConfigVars::IFTHENPAYGATEWAY_KEY);
        $this->deleteConfigValue(ConfigVars::IFTHENPAYGATEWAY_CALLBACK_URL);
        $this->deleteConfigValue(ConfigVars::IFTHENPAYGATEWAY_ACTIVATE_CALLBACK);
        $this->deleteConfigValue(ConfigVars::IFTHENPAYGATEWAY_PREVIOUS_ACTIVATED_CALLBACKS);
        $this->deleteConfigValue(ConfigVars::IFTHENPAYGATEWAY_ANTI_PHISHING_KEY);
        $this->deleteConfigValue(ConfigVars::IFTHENPAYGATEWAY_SEND_INVOICE_EMAIL);
        $this->deleteConfigValue(ConfigVars::IS_PAYMENT_METHOD_ACTIVE);
        $this->deleteConfigValue(ConfigVars::TITLE);
        $this->deleteConfigValue(ConfigVars::SHOW_PAYMENT_ICON);
        $this->deleteConfigValue(ConfigVars::MIN_VALUE);
        $this->deleteConfigValue(ConfigVars::MAX_VALUE);
        $this->deleteConfigValue(ConfigVars::ALLOWSPECIFIC);
        $this->deleteConfigValue(ConfigVars::SPECIFICCOUNTRY);
        $this->deleteConfigValue(ConfigVars::SORT_ORDER);
        $this->deleteConfigValue(ConfigVars::IFTHENPAYGATEWAY_CLOSE_BUTTON_LABEL);
        $this->deleteConfigValue(ConfigVars::IFTHENPAYGATEWAY_DEADLINE);
        $this->deleteConfigValue(ConfigVars::IFTHENPAYGATEWAY_DEFAULT_PAYMENT_METHOD);
        $this->deleteConfigValue(ConfigVars::IFTHENPAYGATEWAY_PAYMENT_METHODS);
        $this->scopeConfig->clean();
    }

    public function getOtherKeysInUse($thisKey)
    {
        $coreConfigTableName = $this->resourceConnection->getTableName('core_config_data');
        $dbConn = $this->resourceConnection->getConnection();

        $query = "SELECT value FROM {$coreConfigTableName} WHERE path = :path";
        $binds = [
            ':path' => ConfigVars::DB_CONFIG_PREFIX_IFTHENPAYGATEWAY . ConfigVars::IFTHENPAYGATEWAY_KEY
        ];
        $keyRecords = $dbConn->fetchAll($query, $binds);

        $keyArr = [];
        foreach ($keyRecords as $key => $keyRecord) {
            if ($thisKey !== $keyRecord['value']) {
                array_push($keyArr, $keyRecord['value']);
            }
        }
        return $keyArr;
    }
}
