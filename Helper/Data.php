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
    const UPDATE_USER_ACCOUNT_TOKEN = 'payment/ifthenpay/updateUserAccountToken';

    protected $configWriter;
    protected $storeManager;
    protected $scopeConfig;
    protected $paymentMethod;
    protected $currency;

    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        WriterInterface $configWriter,
        ScopeConfigInterface $scopeConfig,
        CurrencyInterface $currency
    )
    {
        parent::__construct($context);
        $this->storeManager = $storeManager;
        $this->configWriter = $configWriter;
        $this->scopeConfig = $scopeConfig;
        $this->currency = $currency;
    }

    protected function getStoreCode()
    {
        return $this->storeManager->getStore()->getCode();
    }

    public function getSandboxMode()
    {
        return $this->scopeConfig->getValue(self::IFTHENPAY_SANDBOX_MODE, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $this->getStoreCode());
    }

    public function getBackofficeKey()
    {
        return $this->scopeConfig->getValue(self::IFTHENPAY_BACKOFFICE_KEY, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $this->getStoreCode());
    }

	public function getUserPaymentMethods()
	{
		return unserialize($this->scopeConfig->getValue(self::USER_PAYMENT_METHODS, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $this->getStoreCode()));
	}

    public function getUserAccount()
	{
		return unserialize($this->scopeConfig->getValue(self::USER_ACCOUNT, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $this->getStoreCode()));
	}

    public function saveUserPaymentMethods(array $value)
    {
        $this->configWriter->save(self::USER_PAYMENT_METHODS, serialize($value), ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);
    }

    public function saveUserAccount(array $value)
    {
        $this->configWriter->save(self::USER_ACCOUNT, serialize($value), ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);
    }

    private function replacePaymentMethodIndPath(string $adminConfigPath): string
    {
        return str_replace('{paymentMethod}', $this->paymentMethod, $adminConfigPath);
    }

    public function getConfig(): array
    {
        $dataActivateCallback = $this->scopeConfig->getValue($this->replacePaymentMethodIndPath(self::ACTIVATE_CALLBACK), ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $this->getStoreCode());
        $dataCallbackUrl = $this->scopeConfig->getValue($this->replacePaymentMethodIndPath(self::CALLBACK_URL), ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $this->getStoreCode());
        $dataChaveAntiPhishing = $this->scopeConfig->getValue($this->replacePaymentMethodIndPath(self::CHAVE_ANTI_PHISHING), ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $this->getStoreCode());
        $dataCallbackActivated = $this->scopeConfig->getValue($this->replacePaymentMethodIndPath(self::CALLBACK_ACTIVATED), ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $this->getStoreCode());

        return [
            'activateCallback' => $dataActivateCallback,
            'callbackUrl' => $dataCallbackUrl,
            'chaveAntiPhishing' => $dataChaveAntiPhishing,
            'callbackActivated' => $dataCallbackActivated
        ];
    }

    public function saveCallback(string $callbackUrl, string $chaveAntiPhishing, bool $activatedCallback): void
    {
        $this->configWriter->save($this->replacePaymentMethodIndPath(self::CALLBACK_URL), $callbackUrl, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);
        $this->configWriter->save($this->replacePaymentMethodIndPath(self::CHAVE_ANTI_PHISHING), $chaveAntiPhishing, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);
        $this->configWriter->save($this->replacePaymentMethodIndPath(self::CALLBACK_ACTIVATED), $activatedCallback, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);
        $this->scopeConfig->clean();
    }

    public function deleteConfig(): void
    {
        $this->configWriter->delete($this->replacePaymentMethodIndPath(self::ACTIVATE_CALLBACK), ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);
        $this->configWriter->delete($this->replacePaymentMethodIndPath(self::CALLBACK_URL), ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);
        $this->configWriter->delete($this->replacePaymentMethodIndPath(self::CHAVE_ANTI_PHISHING), ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);
        $this->configWriter->delete($this->replacePaymentMethodIndPath(self::CALLBACK_ACTIVATED), ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);
        $this->scopeConfig->clean();
    }

    public function getCurrentCurrencySymbol()
    {
        return $this->currency->getCurrency($this->storeManager->getStore()->getCurrentCurrency()->getCode())->getSymbol();
    }

    public function getStoreEmail()
    {
        return $this->scopeConfig->getValue(
            'trans_email/ident_sales/email',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getStorename()
    {
        return $this->scopeConfig->getValue(
            'trans_email/ident_sales/name',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function saveUpdateUserAccountToken(): string
    {
        $token = md5((string) rand());
        $this->configWriter->save(self::UPDATE_USER_ACCOUNT_TOKEN, $token, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);
        $this->scopeConfig->clean();
        return $token;
    }

    public function getUpdateUserAccountToken()
    {
        return $this->scopeConfig->getValue(self::UPDATE_USER_ACCOUNT_TOKEN, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $this->getStoreCode());
    }

    public function deleteUpdateUserAccountToken(): void
    {
        $this->configWriter->delete(self::UPDATE_USER_ACCOUNT_TOKEN, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);
        $this->scopeConfig->clean();
    }
}