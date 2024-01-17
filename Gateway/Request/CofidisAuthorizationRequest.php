<?php
/**
 * @category    Gateway Payment
 * @package     Ifthenpay_Payment
 * @author      Ifthenpay
 * @copyright   Ifthenpay (https://www.ifthenpay.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Ifthenpay\Payment\Gateway\Request;

use Magento\Framework\UrlInterface;
use Magento\Payment\Gateway\ConfigInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Ifthenpay\Payment\Config\ConfigVars;
use Ifthenpay\Payment\Lib\Utility\Token;
use Ifthenpay\Payment\Lib\Utility\Currency;

class CofidisAuthorizationRequest implements BuilderInterface
{
    /**
     * @var ConfigInterface
     */
    private $config;
    private $urlBuilder;
    private $token;
    private $currency;
    /**
     * @param ConfigInterface $config
     */
    public function __construct(
        ConfigInterface $config,
        UrlInterface $urlBuilder,
        Token $token,
        Currency $currency
    ) {
        $this->config = $config;
        $this->urlBuilder = $urlBuilder;
        $this->token = $token;
        $this->currency = $currency;
    }

    /**
     * Builds ENV request
     *
     * @param array $buildSubject
     * @return array
     */
    public function build(array $buildSubject)
    {
        if (
            !isset($buildSubject['payment'])
            || !$buildSubject['payment'] instanceof PaymentDataObjectInterface
        ) {
            throw new \InvalidArgumentException('Payment data object should be provided');
        }

        $paymentDO = $buildSubject['payment'];
        $order = $paymentDO->getOrder();
        $orderId = $order->getOrderIncrementId();
        $currency = $order->getCurrencyCode();
        $orderTotal = $order->getGrandTotalAmount();
        $convertedOrderTotal = $this->currency->convertAndFormatToEuro($currency, $orderTotal);


        $hash = $this->generateHashString(20);

        $returnUrl = $this->getReturnUrl($orderId, $hash);

        $customerData = $this->getCustomerData($order);

        $payload = $customerData;

        $payload['orderid'] = $orderId;
        $payload['amount'] = $convertedOrderTotal;
        $payload['description'] = "Order {$orderId}";
        $payload['hash'] = $hash;
        $payload['returnUrl'] = $returnUrl;

        $key = $this->config->getValue('key');

        return [
            'url' => ConfigVars::API_URL_COFIDIS_SET_REQUEST . $key,
            'payload' => $payload,
        ];

    }
    private function getReturnUrl(string $orderId, string $hash)
    {
        $str = str_replace('[ORDER_ID]', $orderId, ConfigVars::COFIDIS_RETURN_URL_STRING);
        $str = str_replace('[HASH]', $hash, $str);

        return $this->urlBuilder->getUrl() . $str;
    }

    private function generateHashString(int $length): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $str = substr(str_shuffle($characters), 0, $length);
        return $str;
    }

    private function getCustomerData($order): array
    {
        $customerData = [];
        $billingAddress = $order->getBillingAddress();
        $shippingAddress = $order->getShippingAddress();

        if ($shippingAddress) {
            $firstName = $shippingAddress->getFirstname() ?? '';
            $middleName = $shippingAddress->getMiddlename() ?? '';
            $lastName = $shippingAddress->getLastname() ?? '';
            $email = $shippingAddress->getEmail() ?? '';
            $phone = $shippingAddress->getTelephone() ?? '';
            $streetLine1 = $shippingAddress->getStreetLine1() ?? '';
            $streetLine2 = $shippingAddress->getStreetLine2() ?? '';
            $zipCode = $shippingAddress->getPostcode() ?? '';
            $city = $shippingAddress->getCity() ?? '';

            $customerData['customerName'] = preg_replace('/\s+/', ' ', $firstName . ' ' . $middleName . ' ' . $lastName);
            $customerData['customerEmail'] = $email;
            $customerData['customerPhone'] = $phone;
            $customerData['deliveryAddress'] = preg_replace('/\s+/', ' ', $streetLine1 . ' ' . $streetLine2);
            $customerData['deliveryZipCode'] = $zipCode;
            $customerData['deliveryCity'] = $city;
        }

        if ($billingAddress) {
            $firstName = $billingAddress->getFirstname() ?? '';
            $middleName = $billingAddress->getMiddlename() ?? '';
            $lastName = $billingAddress->getLastname() ?? '';
            $email = $billingAddress->getEmail() ?? '';
            $phone = $billingAddress->getTelephone() ?? '';
            $streetLine1 = $billingAddress->getStreetLine1() ?? '';
            $streetLine2 = $billingAddress->getStreetLine2() ?? '';
            $zipCode = $billingAddress->getPostcode() ?? '';
            $city = $billingAddress->getCity() ?? '';

            $customerData['customerName'] = preg_replace('/\s+/', ' ', $firstName . ' ' . $middleName . ' ' . $lastName);
            $customerData['customerEmail'] = $email;
            $customerData['customerPhone'] = $phone;
            $customerData['billingAddress'] = preg_replace('/\s+/', ' ', $streetLine1 . ' ' . $streetLine2);
            $customerData['billingZipCode'] = $zipCode;
            $customerData['billingCity'] = $city;
        }


        return $customerData;
    }
}
