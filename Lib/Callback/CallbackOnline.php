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

declare(strict_types=1);

namespace Ifthenpay\Payment\Lib\Callback;

use Magento\Sales\Model\Order;
use Magento\Framework\UrlInterface;
use Magento\Sales\Model\Order\Invoice;
use Ifthenpay\Payment\Lib\Utility\Token;
use Ifthenpay\Payment\Lib\Utility\Status;
use Ifthenpay\Payment\Lib\Payments\Gateway;
use Ifthenpay\Payment\Lib\Utility\TokenExtra;
use Ifthenpay\Payment\Logger\IfthenpayLogger;
use Ifthenpay\Payment\Lib\Utility\ConvertEuros;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Ifthenpay\Payment\Helper\Factory\DataFactory;
use Ifthenpay\Payment\Lib\Callback\CallbackProcess;
use Magento\Framework\Exception\LocalizedException;
use Ifthenpay\Payment\Lib\Factory\Model\ModelFactory;
use Magento\Sales\Api\OrderPaymentRepositoryInterface;
use Ifthenpay\Payment\Model\Service\CreateInvoiceService;
use Ifthenpay\Payment\Lib\Factory\Model\RepositoryFactory;
use Ifthenpay\Payment\Lib\Factory\Callback\CallbackDataFactory;
use Ifthenpay\Payment\Lib\Contracts\Callback\CallbackProcessInterface;
use Magento\Sales\Api\OrderRepositoryInterface as OrderRepositoryInterface;

class CallbackOnline extends CallbackProcess implements CallbackProcessInterface
{
    private $payment;
    private $status;
    private $token;
    private $tokenExtra;
    private $convertEuros;
    protected $messageManager;

    public function __construct(
        CallbackDataFactory $callbackDataFactory,
        Status $status,
        Token $token,
        TokenExtra $tokenExtra,
        ModelFactory $modelFactory,
        CreateInvoiceService $createInvoiceService,
        DataFactory $dataFactory,
        RepositoryFactory $repositoryFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        OrderRepositoryInterface $orderRepository,
        OrderPaymentRepositoryInterface $paymentRepository,
        ResultFactory $resultFactory,
        UrlInterface $urlBuilder,
        IfthenpayLogger $logger,
        ConvertEuros $convertEuros,
        ManagerInterface $messageManager
    )
	{
        parent::__construct(
            $callbackDataFactory,
            $modelFactory,
            $createInvoiceService,
            $dataFactory,
            $repositoryFactory,
            $searchCriteriaBuilder,
            $orderRepository,
            $paymentRepository,
            $resultFactory,
            $urlBuilder,
            $logger
        );
        $this->status = $status;
        $this->token = $token;
        $this->tokenExtra = $tokenExtra;
        $this->convertEuros = $convertEuros;
        $this->messageManager = $messageManager;
	}

    public function process()
    {
        try {
            $this->setPaymentData();

            if (empty($this->paymentData)) {
                $this->executePaymentNotFound();
            } else {
                $this->setOrder();
                $this->payment = $this->order->getPayment();
                if ($this->paymentData['status'] === 'pending') {
                    $paymentStatus = $this->status->getTokenStatus(
                        $this->token->decrypt($this->request['qn'])
                    );
                    if ($paymentStatus === 'success') {
                        if ($this->request['sk'] !== $this->tokenExtra->encript(
                            $this->request['id'] . $this->request['amount'] . $this->request['requestId'],
                            $this->dataFactory->setType(Gateway::CCARD)->build()->getConfig()['ccardKey'])) {
                                throw new LocalizedException(__('Payment security token is invalid'));
                        }
                        $orderTotal = floatval($this->convertEuros->execute(
                                $this->order->getOrderCurrencyCode(),
                                $this->order->getGrandTotal()
                            )
                        );
                        $requestValor = floatval($this->request['amount']);
                        if (round($orderTotal, 2) !== round($requestValor, 2)) {
                            throw new LocalizedException(__('Payment value is invalid'));
                        }
                        $this->changeIfthenpayPaymentStatus('paid');
                        $this->payment->setAdditionalInformation('status', 'success');
                        $this->payment->setIsTransactionClosed(1);
                        $this->paymentRepository->save($this->payment);
                        $this->changeOrderStatus(Order::STATE_PROCESSING);
                        $this->createInvoice(Invoice::CAPTURE_ONLINE);
                        $this->logger->debug('Callback online executed with success', [
                            'requestData' => $this->request,
                            'order' => $this->order->getData(),
                            'paymentData' => $this->paymentData
                        ]);
                        $this->messageManager->addSuccessMessage(__('Payment by Credit Card made with success'));
                        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('checkout/onepage/success');
                    } else if($paymentStatus === 'cancel') {
                        $this->changeIfthenpayPaymentStatus('cancel');
                        $this->changeOrderStatus(Order::STATE_CANCELED);
                        $this->payment->setAdditionalInformation('status', 'cancel');
                        $this->payment->setIsTransactionClosed(1);
                        $this->paymentRepository->save($this->payment);
                        $this->logger->debug('Callback online executed with success - Payment By credit card canceled by user', [
                            'requestData' => $this->request,
                            'order' => $this->order->getData(),
                            'paymentData' => $this->paymentData
                        ]);
                        $this->messageManager->addErrorMessage(__('Payment by Credit Card canceled by the client.'));
                        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('checkout/onepage/success');
                    } else {
                        $this->changeIfthenpayPaymentStatus('error');
                        $this->payment->setAdditionalInformation('status', 'error');
                        $this->payment->setIsTransactionClosed(1);
                        $this->paymentRepository->save($this->payment);
                        $this->changeOrderStatus(Order::STATE_NEW);
                        $this->logger->debug('Callback online executed with success - Payment By credit card error', [
                            'requestData' => $this->request,
                            'order' => $this->order->getData(),
                            'paymentData' => $this->paymentData
                        ]);
                        $this->messageManager->addErrorMessage(__('Error processing payment by Credit Card.'));
                        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('checkout/onepage/success');
                    }
                } else {
                    $this->logger->debug('Callback online: Order is already paid', [
                        'requestData' => $this->request,
                        'order' => $this->order->getData(),
                        'paymentData' => $this->paymentData
                    ]);
                    $this->messageManager->addErrorMessage(__('Order is already paid.'));
                    return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('checkout/onepage/success');
                }

            }
        } catch (\Throwable $th) {
            if ($this->payment) {
                $this->payment->setAdditionalInformation('status', 'error');
                $this->payment->setIsTransactionClosed(1);
                $this->paymentRepository->save($this->payment);
            }
            if ($this->order) {
                $this->changeOrderStatus(Order::STATE_NEW);
            }
            $this->logger->debug('Error executing callback online', [
                'error' => $th,
                'errorMessage' => $th->getMessage(),
                'requestData' => $this->request
            ]);
            $this->messageManager->addErrorMessage(__('Error processing payment by Credit Card.'));
            return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('checkout/onepage/success');
        }
    }
}
