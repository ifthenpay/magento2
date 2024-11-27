<?php
/**
 * @category    Gateway Payment
 * @package     Ifthenpay_Payment
 * @author      Ifthenpay
 * @copyright   Ifthenpay (https://www.ifthenpay.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Ifthenpay\Payment\Gateway\Config;

use Ifthenpay\Payment\Config\ConfigVars;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Payment\Gateway\Config\Config as GatewayConfig;
use Ifthenpay\Payment\Model\ScopeConfigResolver;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Store\Model\ScopeInterface;
use Ifthenpay\Payment\Lib\Utility\Version;


class PixConfig extends GatewayConfig
{
    public const METHOD_CODE = ConfigVars::PIX_CODE;
    private const CONFIG_PATH = ConfigVars::MODULE . '/' . ConfigVars::PIX_CODE . '/';

    private $scopeConfigResolver;
    private $configWriter;
    private $scopeConfig;
    private $storeId;
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
        $this->scopeConfigResolver = $scopeConfigResolver;
        $this->configWriter = $configWriter;
        $this->scopeConfig = $scopeConfig;
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
        $value = $this->getConfigValue(ConfigVars::PIX_SEND_INVOICE_EMAIL);

        return $value === '1' ? true : false;
    }

    public function getKey(): string
    {
        $value = $this->getConfigValue(ConfigVars::PIX_KEY);

        return $value ?? '';
    }

    public function getEntity(): string
    {
        // The entity for pix is pix
        return ConfigVars::PIX;
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
    public function getShowPaymentIcon(): bool
    {
        $value = $this->getConfigValue(ConfigVars::SHOW_PAYMENT_ICON);

        return $value === '1' ? true : false;
    }

    public function getTitle(): string
    {
        $value = $this->getConfigValue(ConfigVars::TITLE);

        return $value ?? '';
    }


    public function getActivateCallback(): bool
    {
        $value = $this->getConfigValue(ConfigVars::PIX_ACTIVATE_CALLBACK);

        return $value === '1' ? true : false;
    }

    public function getIsCallbackActivated(): bool
    {
        $value = $this->getConfigValue(ConfigVars::PIX_IS_CALLBACK_ACTIVATED);

        return $value === '1' ? true : false;
    }

    public function getCallbackUrlPartialStringWithScopeAndScopeCode(): string
    {
        return $this->version->replaceVersionVariables(ConfigVars::PIX_CALLBACK_STRING) . '&scp=' . $this->scope . '&scpcd=' . $this->scopeCode;
    }


    public function saveCallbackUrl(string $callbackUrl, string $antiPhishingKey): void
    {
        $this->saveConfigValue(ConfigVars::PIX_CALLBACK_URL, $callbackUrl);
        $this->saveConfigValue(ConfigVars::PIX_ANTI_PHISHING_KEY, $antiPhishingKey);
        $this->saveConfigValue(ConfigVars::PIX_IS_CALLBACK_ACTIVATED, '1');
        $this->scopeConfig->clean();
    }


    public function getCallbackUrl(): string
    {
        $value = $this->getConfigValue(ConfigVars::PIX_CALLBACK_URL);

        return $value ?? '';
    }

    public function getAntiPhishingKey(): string
    {
        $value = $this->getConfigValue(ConfigVars::PIX_ANTI_PHISHING_KEY);

        return $value ?? '';
    }

    public function deactivateCallback(): void
    {
        $this->saveConfigValue(ConfigVars::PIX_IS_CALLBACK_ACTIVATED, '0');
        $this->scopeConfig->clean();
    }


    public function deleteAllPaymentMethodConfig()
    {
        $this->deleteConfigValue(ConfigVars::PIX_KEY);
        $this->deleteConfigValue(ConfigVars::PIX_CALLBACK_URL);
        $this->deleteConfigValue(ConfigVars::PIX_ACTIVATE_CALLBACK);
        $this->deleteConfigValue(ConfigVars::PIX_IS_CALLBACK_ACTIVATED);
        $this->deleteConfigValue(ConfigVars::PIX_ANTI_PHISHING_KEY);
        $this->deleteConfigValue(ConfigVars::PIX_SEND_INVOICE_EMAIL);
        $this->deleteConfigValue(ConfigVars::IS_PAYMENT_METHOD_ACTIVE);
        $this->deleteConfigValue(ConfigVars::TITLE);
        $this->deleteConfigValue(ConfigVars::SHOW_PAYMENT_ICON);
        $this->deleteConfigValue(ConfigVars::MIN_VALUE);
        $this->deleteConfigValue(ConfigVars::MAX_VALUE);
        $this->deleteConfigValue(ConfigVars::ALLOWSPECIFIC);
        $this->deleteConfigValue(ConfigVars::SPECIFICCOUNTRY);
        $this->deleteConfigValue(ConfigVars::SORT_ORDER);
        $this->scopeConfig->clean();
    }


    public function getOtherKeysInUse($thisKey)
    {
        $coreConfigTableName = $this->resourceConnection->getTableName('core_config_data');
        $dbConn = $this->resourceConnection->getConnection();

        $query = "SELECT value FROM {$coreConfigTableName} WHERE path = :path";
        $binds = [
            ':path' => ConfigVars::DB_CONFIG_PREFIX_PIX . ConfigVars::PIX_KEY
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
