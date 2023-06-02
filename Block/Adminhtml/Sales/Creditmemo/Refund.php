<?php
/**
 * @category    Gateway Payment
 * @package     Ifthenpay_Payment
 * @author      Ifthenpay
 * @copyright   Ifthenpay (https://www.ifthenpay.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Ifthenpay\Payment\Block\Adminhtml\Sales\Creditmemo;

use Magento\Framework\View\Element\Template;
use Ifthenpay\Payment\Model\ScopeConfigResolver;
use Magento\Backend\Model\UrlInterface;
use Ifthenpay\Payment\Config\ConfigVars;
use Magento\Sales\Api\OrderRepositoryInterface;
use Ifthenpay\Payment\Logger\Logger;
use Ifthenpay\Payment\Lib\Factory\ConfigFactory;



class Refund extends Template
{

    private $scopeConfigResolver;
    private $urlBuilder;
    private $orderRepository;
    private $logger;
    private $configFactory;
    private $config;

    public function __construct(
        Template\Context $context,
        ScopeConfigResolver $scopeConfigResolver,
        UrlInterface $urlBuilder,
        OrderRepositoryInterface $orderRepository,
        ConfigFactory $configFactory,
        Logger $logger,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->urlBuilder = $urlBuilder;
        $this->scopeConfigResolver = $scopeConfigResolver;
        $this->orderRepository = $orderRepository;
        $this->configFactory = $configFactory;
        $this->logger = $logger;
    }

    public function getStoreId()
    {
        try {
            $orderId = $this->getRequest()->getParam('order_id');
            $order = $this->orderRepository->get($orderId);
            $storeId = $order->getStoreId();
            return $storeId;
        } catch (\Throwable $e) {
            $this->logger->error('admin/creditmemo/refund', [
                'error' => $e,
            ]);
            return '';
        }

        // $store = $this->scopeConfigResolver->storeManager->getStore($requestData['storeId']);

        // $mbwayKey = $store->getConfig('payment/ifthenpay_mbway/key');

    }

    public function getCreditmemo()
    {
        return $this->getParentBlock()->getCreditmemo();
    }



    public function getUrlRequestRefundToken(): string
    {
        return $this->urlBuilder->getUrl(ConfigVars::AJAX_URL_STR_GET_REQUEST_REFUND_TOKEN);
    }

    public function getUrlVerifyRefundToken(): string
    {
        return $this->urlBuilder->getUrl(ConfigVars::AJAX_URL_STR_GET_VERIFY_REFUND_TOKEN);
    }


    private function getPaymentMethod(): string
    {
        try {
            $orderId = $this->getRequest()->getParam('order_id');
            $order = $this->orderRepository->get($orderId);
            $paymentMethod = $order->getPayment()->getMethod();
            return $paymentMethod;
        } catch (\Throwable $e) {
            $this->logger->error('admin/creditmemo/refund', [
                'error' => $e,
            ]);
            return '';
        }
    }

    public function getIsPaymentMethodRefundable()
    {
        return in_array($this->getPaymentMethod(), ConfigVars::REFUNDABLE_PAYMENT_METHOD_CODES) ? 'true' : 'false';
    }


    public function getIsPaymentMethodRefundEnabled()
    {
        $paymentMethod = $this->getPaymentMethod();
        $paymentMethodCode = '';

        foreach (ConfigVars::REFUNDABLE_PAYMENT_METHOD_CODES as $pmCode) {
            if (strpos($paymentMethod, $pmCode) !== false) {
                $paymentMethodCode = $pmCode;
                break;
            }
        }


        $store = $this->scopeConfigResolver->storeManager->getStore($this->getStoreId());

        $isRefundEnabled = $store->getConfig('payment/' . $paymentMethod . '/' . ConfigVars::SHOW_REFUND);

        return $isRefundEnabled ? 'true' : 'false';
    }
}
