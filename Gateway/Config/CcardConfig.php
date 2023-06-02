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


class CcardConfig extends GatewayConfig
{
    public const METHOD_CODE = ConfigVars::CCARD_CODE;
    private const CONFIG_PATH = ConfigVars::MODULE . '/' . ConfigVars::CCARD_CODE . '/';

    private $scopeConfigResolver;
    private $configWriter;
    private $scopeConfig;
    private $dbConn;
    private $storeId;
    private $scope;
    private $scopeCode;


    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ScopeConfigResolver $scopeConfigResolver,
        WriterInterface $configWriter,
        ResourceConnection $resourceConnection,
        string $methodCode = self::METHOD_CODE,
    ) {
        parent::__construct($scopeConfig, $methodCode);
        $this->scopeConfigResolver = $scopeConfigResolver;
        $this->configWriter = $configWriter;
        $this->scopeConfig = $scopeConfig;
        $this->dbConn = $resourceConnection->getConnection();

        $this->scope = $this->scopeConfigResolver->scope;
        $this->scopeCode = $this->scopeConfigResolver->scopeCode;
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
        $value = $this->getConfigValue(ConfigVars::CCARD_SEND_INVOICE_EMAIL);

        return $value === '1' ? true : false;
    }

    public function getKey(): string
    {
        $value = $this->getConfigValue(ConfigVars::CCARD_KEY);

        return $value ?? '';
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


    public function getIsRefundEnabled(): bool
    {
        $value = $this->getConfigValue(ConfigVars::SHOW_REFUND);

        return $value === '1' ? true : false;
    }


    public function deleteAllPaymentMethodConfig()
    {
        $this->deleteConfigValue(ConfigVars::CCARD_KEY);
        $this->deleteConfigValue(ConfigVars::CCARD_SEND_INVOICE_EMAIL);
        $this->deleteConfigValue(ConfigVars::SHOW_REFUND);
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
        $tableName = $this->dbConn->getTableName('core_config_data');
        $query = "SELECT value FROM {$tableName} WHERE path = :path";
        $binds = [
            ':path' => ConfigVars::DB_CONFIG_PREFIX_CCARD . ConfigVars::CCARD_KEY

        ];
        $keyRecords = $this->dbConn->fetchAll($query, $binds);

        $keyArr = [];
        foreach ($keyRecords as $key => $keyRecord) {
            if ($thisKey !== $keyRecord['value']) {
                array_push($keyArr, $keyRecord['value']);
            }
        }
        return $keyArr;
    }

}
