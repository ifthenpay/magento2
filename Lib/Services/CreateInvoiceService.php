<?php
/**
 * @category    Gateway Payment
 * @package     Ifthenpay_Payment
 * @author      Ifthenpay
 * @copyright   Ifthenpay (https://www.ifthenpay.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Ifthenpay\Payment\Lib\Services;

use Magento\Sales\Model\Order;
use Magento\Framework\DB\Transaction;
use Magento\Sales\Model\Service\InvoiceService;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\InvoiceRepository;


class CreateInvoiceService
{
    protected $invoiceService;
    protected $transaction;
    protected $invoiceSender;
    private $invoiceRepository;


    public function __construct(
        InvoiceSender $invoiceSender,
        InvoiceService $invoiceService,
        Transaction $transaction,
        InvoiceRepository $invoiceRepository
    ) {
        $this->invoiceSender = $invoiceSender;
        $this->invoiceService = $invoiceService;
        $this->transaction = $transaction;
        $this->invoiceRepository = $invoiceRepository;
    }

    public function createInvoice(Order $order, bool $isOnline): bool
    {
        if (!$order->getId()) {
            return false;
        }

        if ($order->canInvoice()) {

            $invoice = $this->invoiceService->prepareInvoice($order);
            if ($isOnline) {
                $invoice->setRequestedCaptureCase(Invoice::CAPTURE_ONLINE);
            } else {
                $invoice->setRequestedCaptureCase(Invoice::CAPTURE_OFFLINE);
            }
            $invoice->register();
            $this->invoiceRepository->save($invoice);


            $transactionSave = $this->transaction->addObject(
                $invoice
            )->addObject(
                    $invoice->getOrder()
                );

            $transactionSave->save();

            $this->invoiceSender->send($invoice);

        }
        return true;
    }
}
