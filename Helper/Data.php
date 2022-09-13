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

namespace Ifthenpay\Payment\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Locale\CurrencyInterface;

class Data extends AbstractHelper
{
    const IFTHENPAY_BACKOFFICE_KEY = 'payment/ifthenpay/backofficeKey';
    const USER_PAYMENT_METHODS = 'payment/ifthenpay/userPaymentMethods';
    const USER_ACCOUNT = 'payment/ifthenpay/userAccount';
    const IFTHENPAY_SANDBOX_MODE = 'payment/ifthenpay/sandboxMode';
    const ACTIVATE_CALLBACK = 'payment/ifthenpay/{paymentMethod}/activeCallback';
    const CALLBACK_URL = 'payment/ifthenpay/{paymentMethod}/callbackUrl';
    const CHAVE_ANTI_PHISHING = 'payment/ifthenpay/{paymentMethod}/chaveAntiPhishing';
    const CALLBACK_ACTIVATED = 'payment/ifthenpay/{paymentMethod}/callbackActivated';
    const CANCEL_ORDER = 'payment/ifthenpay/{paymentMethod}/cancelOrder';
    const UPDATE_USER_ACCOUNT_TOKEN = 'payment/ifthenpay/updateUserAccountToken';
    const SHOW_PAYMENT_ICON = 'payment/ifthenpay/{paymentMethod}/showPaymentIcon';
    const PAYMENT_VALIDADE = 'payment/ifthenpay/{paymentMethod}/validade';
    const PAYMENT_IS_ACTIVE = 'payment/{paymentMethod}/active';
    const IFTHENPAY_BASE_URL = 'web/secure/base_url';
    const SALES_EMAIL_PATH = 'trans_email/ident_sales/email';
    const SALES_NAME_PATH = 'trans_email/ident_sales/name';

    protected $configWriter;
    protected $storeManager;
    protected $scopeConfig;
    protected $paymentMethod;
    protected $currency;
    protected $scopeType;
    protected $scopeId;

    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        WriterInterface $configWriter,
        ScopeConfigInterface $scopeConfig,
        CurrencyInterface $currency
    ) {
        parent::__construct($context);
        $this->storeManager = $storeManager;
        $this->configWriter = $configWriter;
        $this->scopeConfig = $scopeConfig;
        $this->currency = $currency;

        $this->setScope();  // sets scope and scopeId
    }

    public function getWebsiteBaseUrl()
    {
        return $this->scopeConfig->getValue(self::IFTHENPAY_BASE_URL, $this->scopeType, $this->scopeId);
    }

    protected function getStore()
    {
        return $this->storeManager->getStore();
    }

    protected function getStoreCode()
    {
        return $this->getStore()->getCode();
    }

    protected function getStoreId()
    {
        return $this->getStore()->getId();
    }

    public function getSandboxMode()
    {
        return $this->scopeConfig->getValue(self::IFTHENPAY_SANDBOX_MODE, $this->scopeType, $this->scopeId);
    }

    public function getBackofficeKey()
    {
        return $this->scopeConfig->getValue(self::IFTHENPAY_BACKOFFICE_KEY, $this->scopeType, $this->scopeId);
    }


    /**
     * sets scope type and id
     * if scope id gotten from _request is of type "website" then set it as such
     * otherwise default to setting as "default" an "0"
     *
     * @return void
     */
    public function setScope($scopeId = '')
    {
        $this->scopeId = $scopeId;
        if ($scopeId === '') {
            $this->scopeId = (int) $this->_request->getParam(\Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE, 0);
        }


        if ($this->scopeId == 0) {
            $this->scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT;
        } else {
            $this->scopeType = \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITES;
        }
        return $this;
    }

    public function getUserPaymentMethods()
    {
        return unserialize($this->scopeConfig->getValue(self::USER_PAYMENT_METHODS, $this->scopeType, $this->scopeId));
    }

    public function getUserAccount()
    {
        return unserialize($this->scopeConfig->getValue(self::USER_ACCOUNT, $this->scopeType, $this->scopeId));
    }


    public function getUserAccountByScopeId($scopeId){

        if ($scopeId == 0) {

            $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT;
        } else {
            $scopeType = \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITES;
        }

    return unserialize($this->scopeConfig->getValue(self::USER_ACCOUNT, $scopeType, $scopeId));
}
    
    public function saveUserPaymentMethods(array $value)
    {
        $this->configWriter->save(self::USER_PAYMENT_METHODS, serialize($value), $this->scopeType, $this->scopeId);
    }

    public function saveUserAccount(array $value)
    {
        $this->configWriter->save(self::USER_ACCOUNT, serialize($value), $this->scopeType, $this->scopeId);
    }

    private function replacePaymentMethodIndPath(string $adminConfigPath): string
    {
        return str_replace('{paymentMethod}', $this->paymentMethod, $adminConfigPath);
    }

    public function getConfig(): array
    {
        $dataActivateCallback = $this->scopeConfig->getValue($this->replacePaymentMethodIndPath(self::ACTIVATE_CALLBACK), $this->scopeType, $this->scopeId);
        $dataCallbackUrl = $this->scopeConfig->getValue($this->replacePaymentMethodIndPath(self::CALLBACK_URL), $this->scopeType, $this->scopeId);
        $dataChaveAntiPhishing = $this->scopeConfig->getValue($this->replacePaymentMethodIndPath(self::CHAVE_ANTI_PHISHING), $this->scopeType, $this->scopeId);
        $dataCallbackActivated = $this->scopeConfig->getValue($this->replacePaymentMethodIndPath(self::CALLBACK_ACTIVATED), $this->scopeType, $this->scopeId);
        $dataCancelOrder = $this->scopeConfig->getValue($this->replacePaymentMethodIndPath(self::CANCEL_ORDER), $this->scopeType, $this->scopeId);
        $showPaymentIcon = $this->scopeConfig->getValue($this->replacePaymentMethodIndPath(self::SHOW_PAYMENT_ICON), $this->scopeType, $this->scopeId);
        $paymentValidade = $this->scopeConfig->getValue($this->replacePaymentMethodIndPath(self::PAYMENT_VALIDADE), $this->scopeType, $this->scopeId);

        return [
            'backofficeKey' => $this->getBackofficeKey(),
            'activateCallback' => $dataActivateCallback,
            'callbackUrl' => $dataCallbackUrl,
            'chaveAntiPhishing' => $dataChaveAntiPhishing,
            'callbackActivated' => $dataCallbackActivated,
            'cancelOrder' => $dataCancelOrder,
            'showPaymentIcon' => $showPaymentIcon,
            'validade' => $paymentValidade
        ];
    }

    public function saveCallback(string $callbackUrl, string $chaveAntiPhishing, bool $activatedCallback): void
    {
        $this->configWriter->save($this->replacePaymentMethodIndPath(self::CALLBACK_URL), $callbackUrl, $this->scopeType, $this->scopeId);
        $this->configWriter->save($this->replacePaymentMethodIndPath(self::CHAVE_ANTI_PHISHING), $chaveAntiPhishing, $this->scopeType, $this->scopeId);
        $this->configWriter->save($this->replacePaymentMethodIndPath(self::CALLBACK_ACTIVATED), $activatedCallback, $this->scopeType, $this->scopeId);
        $this->scopeConfig->clean();
    }

    public function deleteConfig(): void
    {
        $this->configWriter->delete($this->replacePaymentMethodIndPath(self::ACTIVATE_CALLBACK), $this->scopeType, $this->scopeId);
        $this->configWriter->delete($this->replacePaymentMethodIndPath(self::CALLBACK_URL), $this->scopeType, $this->scopeId);
        $this->configWriter->delete($this->replacePaymentMethodIndPath(self::CHAVE_ANTI_PHISHING), $this->scopeType, $this->scopeId);
        $this->configWriter->delete($this->replacePaymentMethodIndPath(self::CALLBACK_ACTIVATED), $this->scopeType, $this->scopeId);
        $this->configWriter->delete($this->replacePaymentMethodIndPath(self::CANCEL_ORDER), $this->scopeType, $this->scopeId);
        $this->configWriter->delete($this->replacePaymentMethodIndPath(self::SHOW_PAYMENT_ICON), $this->scopeType, $this->scopeId);
        $this->configWriter->delete($this->replacePaymentMethodIndPath(self::PAYMENT_VALIDADE), $this->scopeType, $this->scopeId);
        $this->configWriter->delete($this->replacePaymentMethodIndPath(self::PAYMENT_IS_ACTIVE), $this->scopeType, $this->scopeId);
        $this->scopeConfig->clean();
    }

    public function deleteBackofficeKey(): void
    {
        $this->configWriter->delete(self::IFTHENPAY_BACKOFFICE_KEY, $this->scopeType, $this->scopeId);
        $this->configWriter->delete(self::USER_PAYMENT_METHODS, $this->scopeType, $this->scopeId);
        $this->configWriter->delete(self::USER_ACCOUNT, $this->scopeType, $this->scopeId);
        $this->scopeConfig->clean();
    }

    public function getCurrentCurrencySymbol()
    {
        return $this->currency->getCurrency($this->storeManager->getStore()->getCurrentCurrency()->getCode())->getSymbol();
    }

    public function getStoreEmail()
    {
        $storeEmail = $this->scopeConfig->getValue(self::SALES_EMAIL_PATH, $this->scopeType, $this->scopeId);

        return $storeEmail;
    }

    public function getStorename()
    {
        $storeName = $this->scopeConfig->getValue(self::SALES_NAME_PATH, $this->scopeType, $this->scopeId);

        return $storeName;
    }

    public function saveUpdateUserAccountToken(): string
    {
        $token = md5((string) rand());
        $this->configWriter->save(self::UPDATE_USER_ACCOUNT_TOKEN, $token, $this->scopeType, $this->scopeId);
        $this->scopeConfig->clean();
        return $token;
    }

    public function getUpdateUserAccountToken()
    {
        return $this->scopeConfig->getValue(self::UPDATE_USER_ACCOUNT_TOKEN, $this->scopeType, $this->scopeId);
    }

    public function deleteUpdateUserAccountToken(): void
    {
        $this->configWriter->delete(self::UPDATE_USER_ACCOUNT_TOKEN, $this->scopeType, $this->scopeId);
        $this->scopeConfig->clean();
    }

    /**
     * Get the value of scopeType
     */ 
    public function getScopeType()
    {
        return $this->scopeType;
    }

    /**
     * Get the value of scopeId
     */ 
    public function getScopeId()
    {
        return $this->scopeId;
    }

    /**
     * Set the value of scopeType
     *
     * @return  self
     */ 
    public function setScopeType($scopeType)
    {
        $this->scopeType = $scopeType;

        return $this;
    }

    /**
     * Set the value of scopeId
     *
     * @return  self
     */ 
    public function setScopeId($scopeId)
    {
        $this->scopeId = $scopeId;

        return $this;
    }
}
