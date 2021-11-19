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

namespace Ifthenpay\Payment\Block\Checkout\Onepage\Success;

use Magento\Framework\UrlInterface;
use \Magento\Checkout\Model\Session;
use Ifthenpay\Payment\Lib\Payments\Gateway;
use Ifthenpay\Payment\Helper\Factory\DataFactory;
use Magento\Framework\View\Element\Template\Context;

class PaymentReturn extends \Magento\Framework\View\Element\Template
{
    
    public $_checkoutSession;
    private $gateway;
    private $urlBuilder;
    private $ifthenpayPaymentStatus;
    private $payment;
    private $dataFactory;

    public function __construct(
        Gateway $gateway,
        DataFactory $dataFactory,
        UrlInterface $urlBuilder,
        Context $context,
        Session $checkoutSession,
        array $data = []
    ) {
        $this->_checkoutSession = $checkoutSession;
        $this->gateway = $gateway;
        $this->urlBuilder = $urlBuilder;
        $this->dataFactory = $dataFactory;
        parent::__construct($context, $data);
    }

    public function isIfthenpayPayment(){
        $this->payment = $this->getOrder()->getPayment();
        if ($this->payment && $this->gateway->checkIfthenpayPaymentMethod($this->getPaymentMethod())){
            $this->getPaymentReturn();
            return true;
        }
        return false;
    }


    private function getPaymentReturn(): void
    {
        $this->ifthenpayGatewayResult = $this->payment->getAdditionalInformation();

        if ($this->ifthenpayGatewayResult && $this->ifthenpayGatewayResult['status'] === 'success') {
            $this->ifthenpayPaymentStatus = true;
        } else {
            $this->ifthenpayPaymentStatus = false;
        }
    }

    public function getValor()
    {
        return $this->getOrder()->formatPrice($this->getOrder()->getGrandTotal());
    }

    public function getOrder()
    {
        return $this->_checkoutSession->getLastRealOrder();
    }

    public function getPaymentMethod(): string
    {
        return $this->getOrder()->getPayment()->getMethod();
    }

    public function getMbwayCountdownShow()
    {
        return $this->getOrder()->getPayment()->getAdditionalInformation('mbwayCountdownShow');
    }

    public function getResendMbwayNotificationControllerUrl(): string
    {
        return $this->urlBuilder->getUrl('ifthenpay/Frontend/ResendMbwayNotification');
    }

    public function getPaymentResultStatus(): bool
    {
        return $this->ifthenpayPaymentStatus;
    }

    public function getOrderId(): string
    {
        return $this->getOrder()->getIncrementId();
    }

    public function getUrlCancelMbwayOrder(): string
    {
        return $this->urlBuilder->getUrl('ifthenpay/Frontend/CancelMbwayOrder');
    }

    public function getUrlCheckMbwayPaymentStatus(): string
    {
        return $this->urlBuilder->getUrl('ifthenpay/Frontend/CheckMbwayOrderStatus');
    }

    public function getStoreCurency(): string
    {
        return $this->dataFactory->setType($this->getPaymentMethod())->build()->getCurrentCurrencySymbol();
    }

    public function formatReference(string $reference): string
    {
        return trim(strrev(chunk_split(strrev($reference),3, ' ')));
    }
}
