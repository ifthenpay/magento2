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
use Magento\Store\Model\ScopeInterface;
use Ifthenpay\Payment\Config\ConfigVars;
use Ifthenpay\Payment\Model\ScopeConfigResolver;
use Ifthenpay\Payment\Lib\Utility\Version;




class MultibancoConfig extends GatewayConfig
{
    public const METHOD_CODE = ConfigVars::MULTIBANCO_CODE;
    private const CONFIG_PATH = ConfigVars::MODULE . '/' . ConfigVars::MULTIBANCO_CODE . '/';
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
        WriterInterface $configWriter,
        ScopeConfigResolver $scopeConfigResolver,
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
        $value = $this->getConfigValue(ConfigVars::MULTIBANCO_SEND_INVOICE_EMAIL);

        return $value === '1' ? true : false;
    }
    public function getEntity(): string
    {
        $value = $this->getConfigValue(ConfigVars::MULTIBANCO_ENTITY);

        return $value ?? '';
    }

    public function getSubEntity(): string
    {
        $value = $this->getConfigValue(ConfigVars::MULTIBANCO_SUB_ENTITY);

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


    public function getActivateCallback(): bool
    {
        $value = $this->getConfigValue(ConfigVars::MULTIBANCO_ACTIVATE_CALLBACK);

        return $value === '1' ? true : false;
    }

    public function getIsCallbackActivated(): bool
    {
        $value = $this->getConfigValue(ConfigVars::MULTIBANCO_IS_CALLBACK_ACTIVATED);

        return $value === '1' ? true : false;
    }

    public function getCallbackUrlPartialStringWithScopeAndScopeCode(): string
    {
        return $this->version->replaceVersionVariables(ConfigVars::MULTIBANCO_CALLBACK_STRING) . '&scp=' . $this->scope . '&scpcd=' . $this->scopeCode;
    }


    public function saveCallbackUrl(string $callbackUrl, string $antiPhishingKey): void
    {
        $this->saveConfigValue(ConfigVars::MULTIBANCO_CALLBACK_URL, $callbackUrl);
        $this->saveConfigValue(ConfigVars::MULTIBANCO_ANTI_PHISHING_KEY, $antiPhishingKey);
        $this->saveConfigValue(ConfigVars::MULTIBANCO_IS_CALLBACK_ACTIVATED, '1');
        $this->scopeConfig->clean();
    }

    public function getCallbackUrl(): string
    {
        $value = $this->getConfigValue(ConfigVars::MULTIBANCO_CALLBACK_URL);

        return $value ?? '';
    }

    public function getAntiPhishingKey(): string
    {
        $value = $this->getConfigValue(ConfigVars::MULTIBANCO_ANTI_PHISHING_KEY);

        return $value ?? '';
    }


    public function deactivateCallback(): void
    {
        $this->saveConfigValue(ConfigVars::MULTIBANCO_IS_CALLBACK_ACTIVATED, '0');
        // set flag to 1 to indicate that the callback can be activated again
        $this->scopeConfig->clean();
    }

    public function deleteAllPaymentMethodConfig()
    {
        $this->deleteConfigValue(ConfigVars::MULTIBANCO_ENTITY);
        $this->deleteConfigValue(ConfigVars::MULTIBANCO_SUB_ENTITY);
        $this->deleteConfigValue(ConfigVars::MULTIBANCO_DEADLINE);
        $this->deleteConfigValue(ConfigVars::MULTIBANCO_CALLBACK_URL);
        $this->deleteConfigValue(ConfigVars::MULTIBANCO_ACTIVATE_CALLBACK);
        $this->deleteConfigValue(ConfigVars::MULTIBANCO_IS_CALLBACK_ACTIVATED);
        $this->deleteConfigValue(ConfigVars::MULTIBANCO_ANTI_PHISHING_KEY);
        $this->deleteConfigValue(ConfigVars::MULTIBANCO_SEND_INVOICE_EMAIL);
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



    public function getOtherEntitiesSubEntityPairsInUse($thisEntity, $thisSubEntity)
    {
        $coreConfigTableName = $this->resourceConnection->getTableName('core_config_data');
        $dbConn = $this->resourceConnection->getConnection();

        $query = "SELECT scope, scope_id, value FROM {$coreConfigTableName} WHERE path = :path";
        $binds = [
            ':path' => ConfigVars::DB_CONFIG_PREFIX_MULTIBANCO . ConfigVars::MULTIBANCO_ENTITY
        ];
        $entityRecords = $dbConn->fetchAll($query, $binds);



        $query = "SELECT scope, scope_id, value FROM {$coreConfigTableName} WHERE path = :path";
        $binds = [
            ':path' => ConfigVars::DB_CONFIG_PREFIX_MULTIBANCO . ConfigVars::MULTIBANCO_SUB_ENTITY
        ];
        $subEntityRecords = $dbConn->fetchAll($query, $binds);

        $listOfUsed = $this->getPairsOfEntityAndSubentity($entityRecords, $subEntityRecords);


        foreach ($listOfUsed as $key => $item) {
            if ($item['entity'] === $thisEntity && $item['subEntity'] === $thisSubEntity) {
                unset($listOfUsed[$key]);
                break;
            }
        }


        return $listOfUsed;
    }

    private function getPairsOfEntityAndSubentity($entityArr, $subentityArr)
    {

        $newArr = [];
        foreach ($entityArr as $entity) {


            foreach ($subentityArr as $subEntity) {
                if ($entity['scope'] == $subEntity['scope'] && $entity['scope_id'] == $subEntity['scope_id']) {
                    $group = [
                        'entity' => $entity['value'],
                        'subEntity' => $subEntity['value']
                    ];
                    array_push($newArr, $group);
                }
            }
        }
        return $newArr;
    }



}
