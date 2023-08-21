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
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Store\Model\ScopeInterface;

class IfthenpayConfig extends GatewayConfig
{
    public const METHOD_CODE = ConfigVars::IFTHENPAY_CODE;
    private const CONFIG_PATH = ConfigVars::MODULE . '/' . ConfigVars::VENDOR . '/';
    private $scopeConfigResolver;
    private $storeManager;
    private $configWriter;
    private $scopeConfig;
    private $storeId;
    private $scope;
    private $scopeCode;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ScopeConfigResolver $scopeConfigResolver,
        StoreManagerInterface $storeManagerInterface,
        WriterInterface $configWriter,
        string $methodCode = self::METHOD_CODE
    ) {
        parent::__construct($scopeConfig, $methodCode);
        $this->scopeConfigResolver = $scopeConfigResolver;
        $this->storeManager = $storeManagerInterface;
        $this->configWriter = $configWriter;
        $this->scopeConfig = $scopeConfig;

        $this->scope = $this->scopeConfigResolver->scope;
        $this->scopeCode = $this->scopeConfigResolver->scopeCode;
    }
    public function getScope()
    {
        return $this->scope;
    }
    public function getScopeCode()
    {
        return $this->scopeCode;
    }

    public function setScopeAndScopeCode($scope, $scopeCode)
    {
        $this->scope = $scope;
        $this->scopeCode = $scopeCode;
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


    public function getSubEntities($paymentMethod, $entity)
    {
        $subEntities = [];
        $paymentMethodAccounts = $this->getUserPaymentMethodAccounts($paymentMethod);
        foreach ($paymentMethodAccounts as $account) {

            if ($account['Entidade'] === $entity) {
                $subEntities = $account['SubEntidade'];
            }
        }

        return $subEntities;
    }


    public function saveUserPaymentMethods(array $value)
    {
        $this->saveConfigValue(ConfigVars::USER_PAYMENT_METHODS, serialize($value));
        $this->scopeConfig->clean();
    }
    public function getUserPaymentMethods()
    {
        $value = $this->getConfigValue(ConfigVars::USER_PAYMENT_METHODS);

        if ($value === null || $value === '') {
            return [];
        }
        return unserialize($value);
    }


    public function saveUserAccounts(array $value)
    {
        $this->saveConfigValue(ConfigVars::USER_ACCOUNTS, serialize($value));
        $this->scopeConfig->clean();
    }


    public function getUserAccounts(): array
    {
        $value = $this->getConfigValue(ConfigVars::USER_ACCOUNTS);

        if ($value === null || $value === '') {
            return [];
        }
        return unserialize($value);
    }

    public function getUserPaymentMethodAccounts($paymentMethod)
    {
        $userAccounts = $this->getUserAccounts();
        $paymentMethodAccounts = [];

        if ($paymentMethod === ConfigVars::MULTIBANCO) {
            foreach ($userAccounts as $account) {
                if (is_numeric($account['Entidade']) || $account['Entidade'] === ConfigVars::MULTIBANCO_DYNAMIC) {
                    $paymentMethodAccounts[] = $account;
                }
            }
        } else {
            foreach ($userAccounts as $account) {
                if ($account['Entidade'] === strtoupper($paymentMethod)) {
                    $paymentMethodAccounts[] = $account;
                }
            }
        }
        return $paymentMethodAccounts;
    }


    public function getBackofficeKey()
    {
        $value = $this->getConfigValue(ConfigVars::BACKOFFICE_KEY);

        return $value ?? '';
    }

    public function getWebsiteBaseUrl(): string
    {
        $storeId = $this->storeManager->getStore()->getId();

        return $this->storeManager->getStore($storeId)->getBaseUrl();
    }

    public function deleteAllGeneralConfig(): void
    {
        $this->deleteConfigValue(ConfigVars::BACKOFFICE_KEY);
        $this->deleteConfigValue(ConfigVars::USER_PAYMENT_METHODS);
        $this->deleteConfigValue(ConfigVars::USER_ACCOUNTS);
        $this->deleteConfigValue(ConfigVars::REQUEST_TOKEN);

        $this->scopeConfig->clean();
    }


    public function getStorename()
    {
        $value = $this->getConfigValue(ConfigVars::GENERAL_NAME_PATH, true);
        return $value ?? '';
    }

    public function getStoreEmail()
    {
        $value = $this->getConfigValue(ConfigVars::GENERAL_EMAIL_PATH, true);
        return $value ?? '';
    }
    public function getRequestTokenAndSave()
    {
        $token = $this->getRequestToken();

        if ($token === '') {
            $token = md5((string) rand());
            $this->saveConfigValue(ConfigVars::REQUEST_TOKEN, $token);
            $this->scopeConfig->clean();
        }

        return $token;
    }

    public function getRequestToken()
    {
        $value = $this->getConfigValue(ConfigVars::REQUEST_TOKEN);

        return $value ?? '';
    }
    public function hasDynamicReferencesAccount()
    {
        $userAccounts = $this->getUserAccounts();
        foreach ($userAccounts as $account) {
            if ($account['Entidade'] === ConfigVars::MULTIBANCO_DYNAMIC) {
                return true;
            }
        }
        return false;
    }

}
