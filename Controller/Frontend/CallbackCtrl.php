<?php
/**
 * @category    Gateway Payment
 * @package     Ifthenpay_Payment
 * @author      Ifthenpay
 * @copyright   Ifthenpay (https://www.ifthenpay.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Ifthenpay\Payment\Controller\Frontend;

use Ifthenpay\Payment\Config\ConfigVars;
use Ifthenpay\Payment\Lib\Services\CreateInvoiceService;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Response\Http;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\OrderManagementInterface;
use Ifthenpay\Payment\Logger\Logger;
use Ifthenpay\Payment\Lib\Factory\ConfigFactory;
use Ifthenpay\Payment\Lib\Factory\ServiceFactory;
use Ifthenpay\Payment\Lib\Utility\Token;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment\Transaction\BuilderInterface;
use Ifthenpay\Payment\Lib\Utility\Currency;
use Magento\Sales\Model\Order\Invoice\NotifierInterface;



/**
 * Class CallbackCtrl
 *
 * returns ok or fail
 * fail is returned if there is an error processing the callback
 * the code refers the nature of the error
 * 10 - StoredPaymentData not found in local table.
 * 20 - Invalid payment method.
 * 30 - Callback is not active.
 * 40 - Invalid anti-phishing key.
 * 50 - Order not found.
 * 60 - Invalid amount.
 */
class CallbackCtrl extends Action
{

    private $serviceFactory;
    private $orderRepository;
    private $configFactory;
    private $logger;
    private $config;
    private $orderManagement;
    private $createInvoiceService;
    private $orderFactory;
    private $order;
    private $builderInterface;
    private $tokenUtility;
    private $currency;
    private $invoiceNotifier;



    public function __construct(
        Context $context,
        ServiceFactory $serviceFactory,
        OrderRepositoryInterface $orderRepository,
        Logger $logger,
        ConfigFactory $configFactory,
        OrderManagementInterface $orderManagement,
        CreateInvoiceService $createInvoiceService,
        Order $orderFactory,
        BuilderInterface $builderInterface,
        Token $tokenUtility,
        Currency $currency,
        NotifierInterface $invoiceNotifier
    ) {
        parent::__construct($context);
        $this->serviceFactory = $serviceFactory;
        $this->orderRepository = $orderRepository;
        $this->logger = $logger;
        $this->configFactory = $configFactory;
        $this->orderManagement = $orderManagement;
        $this->createInvoiceService = $createInvoiceService;
        $this->orderFactory = $orderFactory;
        $this->builderInterface = $builderInterface;
        $this->tokenUtility = $tokenUtility;
        $this->currency = $currency;
        $this->invoiceNotifier = $invoiceNotifier;
    }



    public function execute()
    {
        try {
            $requestData = $this->getRequest()->getParams();

            $this->processCallback($requestData);

            $response = $this->getResponse()
                ->setStatusCode(Http::STATUS_CODE_200)
                ->setContent('ok');
            return $response;



        } catch (\Throwable $th) {
            $this->logger->error('Error Executing offline Callback', [
                'error' => $th,
                'requestData' => $requestData
            ]);

            $code = $th->getCode() ?? '000';
            $response = $this->getResponse()
                ->setStatusCode(Http::STATUS_CODE_400)
                ->setContent('fail - ' . $code);
            return $response;
        }
    }



    private function processCallback($requestData)
    {
        try {
            $this->config = $this->configFactory->createConfig(ConfigVars::VENDOR . '_' . $requestData['payment']);
            $this->config->setScopeAndScopeCode($requestData['scp'], $requestData['scpcd']);

            $service = $this->serviceFactory->createService(ConfigVars::VENDOR . '_' . $requestData['payment']);
            $storedPaymentData = $service->getPaymentByRequestData($requestData);

            // If payment is already paid, return
            if ($storedPaymentData['status'] !== 'pending') {
                return;
            }

            // Validate callback, throw exception if invalid
            $this->validateCallback($requestData, $storedPaymentData);



            if (isset($storedPaymentData['transaction_id']) && $storedPaymentData['transaction_id'] != '') {
                $transactionId = $storedPaymentData['transaction_id'];
                $isOnline = $requestData['payment'] == ConfigVars::PAYSHOP ? false : true;
            } else {
                $transactionId = $this->tokenUtility->generateString(20);
                $isOnline = false;
            }


            $this->handleCaptureAndInvoice($transactionId, $isOnline);


            // Update the payment status for ifthenpay table
            $storedPaymentData['status'] = 'paid';
            $service->setData(
                $storedPaymentData
            );
            $service->save();


        } catch (\Throwable $th) {
            throw $th;
        }
    }

    private function handleCaptureAndInvoice($transactionId, $isOnline)
    {
        $amount = $this->order->getGrandTotal();
        $payment = $this->order->getPayment();

        if ($isOnline) {
            $payment->setParentTransactionId($transactionId);
        }
        $payment->registerCaptureNotification($amount);

        $this->order->setState(Order::STATE_PROCESSING);
        $this->order->setStatus($this->order->getConfig()->getStateDefaultStatus(Order::STATE_PROCESSING));
        $this->orderRepository->save($this->order);

        // if is set to send invoice email
        if ($this->config->getCanNotifyInvoice()) {
            $invoice = $payment->getCreatedInvoice();
            $this->invoiceNotifier->notify($this->order, $invoice);

            $latestInvoice = $this->order->getInvoiceCollection()->getLastItem();
            $invoiceId = $latestInvoice->getIncrementId();
            $message = __('Sent invoice email to customer #') . $invoiceId;

            $this->order->addCommentToStatusHistory($message)->setIsCustomerNotified(true);
            $this->order->getStatusHistories();

            $this->orderRepository->save($this->order);
        }
    }

    private function validateCallback($requestData, $storedPaymentData)
    {
        if (!$storedPaymentData) {
            throw new \Exception('StoredPaymentData not found in local table.', 10);
        }

        // is valid payment method?
        if (!($requestData['payment'] == 'mbway' || $requestData['payment'] == 'multibanco' || $requestData['payment'] == 'payshop')) {
            throw new \Exception('Invalid payment method.', 20);
        }

        // is callback active?
        if (!$this->config->getIsCallbackActivated()) {
            throw new \Exception('Callback is not active.', 30);
        }

        // is anti-phishing key valid?
        $antiPhishingKey = $this->config->getAntiPhishingKey();
        if ($requestData['phish_key'] != $antiPhishingKey) {
            throw new \Exception('Invalid anti-phishing key.', 40);
        }

        // is order id valid? does it exist?
        $this->order = $this->orderFactory->loadByIncrementId($storedPaymentData['order_id']);
        if (!$this->order->getId()) {
            throw new \Exception('Order not found.', 50);
        }

        // is order amount valid?
        $requestAmount = $requestData['amount'];
        $orderTotal = $this->order->getGrandTotal();
        $currency = $this->order->getOrderCurrencyCode();
        $convertedOrderTotal = $this->currency->convertAndFormatToEuro($currency, $orderTotal);
        if ($requestAmount != $convertedOrderTotal) {
            throw new \Exception('Invalid amount.', 60);
        }
    }
}
