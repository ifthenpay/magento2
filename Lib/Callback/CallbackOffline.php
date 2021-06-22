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

use Ifthenpay\Payment\Lib\Callback\CallbackProcess;
use Ifthenpay\Payment\Lib\Contracts\Callback\CallbackProcessInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Invoice;
use \Magento\Framework\App\Response\Http;


class CallbackOffline extends CallbackProcess implements CallbackProcessInterface
{
    public function process()
    {
        try {
            $this->setPaymentData();

            if (empty($this->paymentData)) {
                $this->executePaymentNotFound();
            } else {
                    $this->setOrder();
                    $this->callbackValidate->setHttpRequest($this->request)
                    ->setOrder($this->order->getData())
                    ->setConfigurationChaveAntiPhishing($this->dataFactory->setType($this->request['payment'])->build()->getConfig()['chaveAntiPhishing'])
                    ->setPaymentDataFromDb($this->paymentData)
                    ->validate();
                    $this->changeIfthenpayPaymentStatus('paid');
                    $this->order->setState(Order::STATE_PROCESSING)
                        ->setStatus($this->order->getConfig()->getStateDefaultStatus(Order::STATE_PROCESSING));
                    $this->orderRepository->save($this->order);
                    $this->createInvoiceService->createInvoice($this->order, Invoice::CAPTURE_OFFLINE);
                    $this->logger->debug('Callback offline: Callback offline executed with sucess');
                    return $this->callbackController->getResponse()
                    ->setStatusCode(Http::STATUS_CODE_400)
                    ->setContent('ok');
                
            }
        } catch (\Throwable $th) {
            $this->logger->debug('Callback offline: Error executing callback offlinet - ' . $th->getMessage());
            return $this->callbackController->getResponse()
                ->setStatusCode(Http::STATUS_CODE_400)
                ->setContent($th->getMessage());
        }
    }
}