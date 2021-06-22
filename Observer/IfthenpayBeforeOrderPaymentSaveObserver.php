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

namespace Ifthenpay\Payment\Observer;

use Magento\Framework\Event\Observer;
use Ifthenpay\Payment\Lib\Payments\Gateway;
use Ifthenpay\Payment\Logger\IfthenpayLogger;
use Magento\Framework\Event\ObserverInterface;
use Ifthenpay\Payment\Lib\Strategy\Payments\IfthenpayPaymentReturn;
use Magento\Framework\App\Request\Http;

class IfthenpayBeforeOrderPaymentSaveObserver implements ObserverInterface
{
    private $gateway;
    private $ifthenpayPaymentReturn;
    private $logger;
    private $request;

	public function __construct(Gateway $gateway, IfthenpayPaymentReturn $ifthenpayPaymentReturn, Http $request,IfthenpayLogger $logger)
	{
        $this->gateway = $gateway;
        $this->ifthenpayPaymentReturn = $ifthenpayPaymentReturn;
        $this->request = $request;
        $this->logger = $logger;
	}

    public function execute(Observer $observer)
    {
        $payment = $observer->getEvent()->getPayment();
        $paymentMethod = $payment->getMethod();
        $order = $payment->getOrder();

        try {
            if (!empty($this->request->getParams()) && $this->gateway->checkIfthenpayPaymentMethod($paymentMethod) && $paymentMethod !== 'ccard') {
                $this->ifthenpayGatewayResult = $this->ifthenpayPaymentReturn->setOrder($order)->execute()->getPaymentGatewayResultData();
                switch ($paymentMethod) {
                    case 'multibanco':
                        $payment->setAdditionalInformation('entidade', $this->ifthenpayGatewayResult->entidade);
                        $payment->setAdditionalInformation('referencia', $this->ifthenpayGatewayResult->referencia);
                        break;
                    case 'mbway':
                        $payment->setAdditionalInformation('idPedido', $this->ifthenpayGatewayResult->idPedido);
                        $payment->setAdditionalInformation('telemovel', $this->ifthenpayGatewayResult->telemovel);
                        $payment->setAdditionalInformation('mbwayCountdownShow', true);
                        break;
                    case 'payshop':
                        $payment->setAdditionalInformation('idPedido', $this->ifthenpayGatewayResult->idPedido);
                        $payment->setAdditionalInformation('referencia', $this->ifthenpayGatewayResult->referencia);
                        $payment->setAdditionalInformation('validade', $this->ifthenpayGatewayResult->validade);
                        break;
                    default:
                        break;
                }
                $payment->setAdditionalInformation('totalToPay', $this->ifthenpayGatewayResult->totalToPay);
                $payment->setAdditionalInformation('status', 'success');
                $this->logger->debug('Payment Return: Payment return offline payment executed with success - orderId');
            }
        } catch (\Throwable $th) {
            $payment->setAdditionalInformation('status', 'error');
            $this->logger->debug('Payment Return: Error executing Payment return offline payment - ' . $th->getMessage());
            throw $th;
        }
    }
}
