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
use Magento\Sales\Model\Order\Invoice;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Ifthenpay\Payment\Lib\Contracts\Callback\CallbackProcessInterface;

class CallbackOnline extends CallbackProcess implements CallbackProcessInterface
{
    private $payment;
    private $redirectUrl;

    public function process()
    {
        try {
            $this->setPaymentData();

            if (empty($this->paymentData)) {
                $this->executePaymentNotFound();
            } else {
                $paymentStatus = $this->status->getTokenStatus(
                    $this->token->decrypt($this->request['qn'])
                );

                $this->setOrder();
                $this->payment = $this->order->getPayment();
                $this->redirectUrl =  $this->urlBuilder->getUrl('checkout/onepage/success');

                if ($paymentStatus === 'success') {
                    if ($this->request['sk'] !== $this->tokenExtra->encript(
                        $this->request['id'] . $this->request['amount'] . $this->request['requestId'],
                        $this->dataFactory->setType('ccard')->build()->getConfig()['ccardKey'])) {
                            throw new LocalizedException(__('Payment security token is invalid'));
                    }
                    $orderTotal = floatval($this->order->getGrandTotal());
                    $requestValor = floatval($this->request['amount']);
                    if (round($orderTotal, 2) !== round($requestValor, 2)) {
                        throw new LocalizedException(__('Payment value is invalid'));
                    }
                    $this->changeIfthenpayPaymentStatus('paid');
                    $this->payment->setAdditionalInformation('status', 'success');
                    $this->payment->setIsTransactionClosed(1);
                    $this->paymentRepository->save($this->payment);
                    $this->createInvoiceService->createInvoice($this->order, Invoice::CAPTURE_ONLINE);
                    $this->logger->debug('Callback online: Callback online executed with success - Payment By credit card confirmed');
                    return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setUrl($this->redirectUrl);
                } else if($paymentStatus === 'cancel') {
                    $this->changeIfthenpayPaymentStatus('cancel');
                    $this->order->setState(Order::STATE_CANCELED)
                    ->setStatus($this->order->getConfig()->getStateDefaultStatus(Order::STATE_CANCELED));
                    $this->orderRepository->save($this->order);
                    $this->payment->setAdditionalInformation('status', 'cancel');
                    $this->payment->setIsTransactionClosed(1);
                    $this->paymentRepository->save($this->payment);
                    $this->logger->debug('Callback online: Callback online executed with success - Payment By credit card canceled');
                    return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setUrl($this->redirectUrl);
                } else {
                    $this->changeIfthenpayPaymentStatus('error');
                    $this->order->setState(Order::STATE_PENDING_PAYMENT)
                    ->setStatus($this->order->getConfig()->getStateDefaultStatus(Order::STATE_PENDING_PAYMENT));
                    $this->orderRepository->save($this->order);
                    $this->payment->setAdditionalInformation('status', 'error');
                    $this->payment->setIsTransactionClosed(1);
                    $this->paymentRepository->save($this->payment);
                    $this->logger->debug('Callback online: Callback online executed with success - Payment By credit card error');
                    return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setUrl($this->redirectUrl);
                }
            }
        } catch (\Throwable $th) {
            $this->payment->setAdditionalInformation('status', 'error');
            $this->payment->setIsTransactionClosed(1);
            $this->paymentRepository->save($this->payment);
            $this->logger->debug('Callback online: Error executing callback online - ' . $th->getMessage());
            return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setUrl($this->redirectUrl);
        }
    }
}
