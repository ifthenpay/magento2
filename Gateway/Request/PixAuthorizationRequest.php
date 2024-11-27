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
use Magento\Framework\Exception\ValidatorException;

class PixAuthorizationRequest implements BuilderInterface
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


        $payload = [];

        $key = $this->config->getValue('key');

        $payload['orderid'] = $orderId;
        $payload['amount'] = $convertedOrderTotal;
        $payload["redirectUrl"] = $returnUrl;
        $payload['description'] = "Order {$orderId}";
        $payload['pix_key'] = $key;
        $payload['hash'] = $hash;


        $payload['customerName'] = $paymentDO->getPayment()->getAdditionalInformation('name');
        $payload['customerCpf'] = $paymentDO->getPayment()->getAdditionalInformation('cpf');
        $payload['customerEmail'] = $paymentDO->getPayment()->getAdditionalInformation('email');
        $payload['customerPhone'] = $paymentDO->getPayment()->getAdditionalInformation('phone');
        $payload['customerAddress'] = $paymentDO->getPayment()->getAdditionalInformation('address');
        $payload['customerStreetNumber'] = $paymentDO->getPayment()->getAdditionalInformation('streetNumber');
        $payload['customerCity'] = $paymentDO->getPayment()->getAdditionalInformation('city');
        $payload['customerZipCode'] = $paymentDO->getPayment()->getAdditionalInformation('zipCode');
        $payload['customerState'] = $paymentDO->getPayment()->getAdditionalInformation('state');

        // additional layer of validation besides frontend validation
        $this->validatePixData($payload);

        return [
            'url' => ConfigVars::API_URL_PIX_SET_REQUEST . $key,
            'payload' => $payload,
        ];
    }
    private function getReturnUrl(string $orderId, string $hash)
    {
        $str = str_replace('[ORDER_ID]', $orderId, ConfigVars::PIX_RETURN_URL_STRING);
        $str = str_replace('[HASH]', $hash, $str);

        return $this->urlBuilder->getUrl() . $str;
    }


    private function validatePixData($data)
    {
        // name
        if (!$data['customerName'] || $data['customerName'] == '') {
            throw new ValidatorException(__('Pix Name field is required.'));
        } else if (strlen($data['customerName']) > 150) {
            throw new ValidatorException(__('Pix Name field is invalid. Must not exceed 150 characters.'));
        }

        // CPF
        if (!$data['customerCpf'] || $data['customerCpf'] == '') {
            throw new ValidatorException(__('Pix CPF field is required.'));
        } else if (!preg_match("/^(\d{3}\.\d{3}\.\d{3}-\d{2}|\d{11})$/", $data['customerCpf'])) {
            throw new ValidatorException(__('Pix CPF field is invalid. Must be comprised of 11 digits with either of the following patterns: 111.111.111-11 or 11111111111.'));
        }
        // email
        if (!$data['customerEmail'] || $data['customerEmail'] == '') {
            throw new ValidatorException(__('Pix Email field is required.'));
        } else if (!filter_var($data['customerEmail'], FILTER_VALIDATE_EMAIL)) {
            throw new ValidatorException(__('Pix Email field is invalid. Must be a valid email address.'));
        } else if (strlen($data['customerEmail']) > 250) {
            throw new ValidatorException(__('Pix Email field is invalid. Must not exceed 250 characters.'));
        }
        // phone
        if ($data['customerPhone'] && $data['customerPhone'] != '' && strlen($data['customerPhone']) > 20) {
            throw new ValidatorException(__('Pix Phone Number field is invalid. Must not exceed 20 characters.'));
        }
        // address
        if ($data['customerAddress'] && $data['customerAddress'] != '' && strlen($data['customerAddress']) > 250) {
            throw new ValidatorException(__('Pix Address field is invalid. Must not exceed 250 characters.'));
        }
        // streetNumber
        if ($data['customerStreetNumber'] && $data['customerStreetNumber'] != '' && strlen($data['customerStreetNumber']) > 20) {
            throw new ValidatorException(__('Pix Street Number field is invalid. Must not exceed 20 characters.'));
        }
        // City
        if ($data['customerCity'] && $data['customerCity'] != '' && strlen($data['customerCity']) > 50) {
            throw new ValidatorException(__('Pix City field is invalid. Must not exceed 50 characters.'));
        }
        // Zip Code
        if ($data['customerZipCode'] && $data['customerZipCode'] != '' && strlen($data['customerZipCode']) > 20) {
            throw new ValidatorException(__('Pix Zip Code field is invalid. Must not exceed 20 characters.'));
        }
        // State
        if ($data['customerState'] && $data['customerState'] != '' && strlen($data['customerState']) > 50) {
            throw new ValidatorException(__('Pix State field is invalid. Must not exceed 50 characters.'));
        }
    }

    private function generateHashString(int $length): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $str = substr(str_shuffle($characters), 0, $length);
        return $str;
    }
}
