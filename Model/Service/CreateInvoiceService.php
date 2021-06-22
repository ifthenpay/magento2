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

namespace Ifthenpay\Payment\Model\Service;

use Magento\Sales\Model\Order;
use Magento\Framework\DB\Transaction;
use Magento\Sales\Model\Service\InvoiceService;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;

class CreateInvoiceService
{
    protected $invoiceService;
    protected $transaction;
    protected $invoiceSender;

    public function __construct(
        InvoiceSender $invoiceSender,
        InvoiceService $invoiceService,
        Transaction $transaction
    ) {
        $this->invoiceSender = $invoiceSender;
        $this->invoiceService = $invoiceService;
        $this->transaction = $transaction;
    }

    public function createInvoice(Order $order, string $paymentType): bool
    {
        if(!$order->getId()) {
            return false;
        }

        if($order->canInvoice()) {
            $invoice = $this->invoiceService->prepareInvoice($order);
            $invoice->setRequestedCaptureCase($paymentType);
            $invoice->register();
            $invoice->save();

            $transactionSave = $this->transaction->addObject(
                $invoice
            )->addObject(
                $invoice->getOrder()
            );

            $transactionSave->save();

            $this->invoiceSender->send($invoice);

            $order->addStatusHistoryComment(
                __('Added invoice #%1 to customer order', $invoice->getId())
            )->setIsCustomerNotified(true)
            ->save();
        }
        return true;
    }
}