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
use Ifthenpay\Payment\Lib\Factory\Model\RepositoryFactory;
use Ifthenpay\Payment\Lib\Strategy\Payments\IfthenpayPaymentReturn;

class IfthenpayBeforeOrderPaymentSaveObserver implements ObserverInterface
{
    private $gateway;
    private $ifthenpayPaymentReturn;
    private $logger;
    private $repositoryFactory;

	public function __construct(
        Gateway $gateway,
        IfthenpayPaymentReturn $ifthenpayPaymentReturn,
        IfthenpayLogger $logger,
        RepositoryFactory $repositoryFactory
    )
	{
        $this->gateway = $gateway;
        $this->ifthenpayPaymentReturn = $ifthenpayPaymentReturn;
        $this->logger = $logger;
        $this->repositoryFactory = $repositoryFactory;
	}

    public function execute(Observer $observer)
    {
        $payment = $observer->getEvent()->getPayment();
        $paymentMethod = $payment->getMethod();
        $order = $payment->getOrder();
        $ifthenpayPayment = $this->repositoryFactory->setType($paymentMethod)->build()->getByOrderId($order->getIncrementId())->getData();
        try {
            if ($this->gateway->checkIfthenpayPaymentMethod($paymentMethod) && $paymentMethod !== Gateway::CCARD && empty($ifthenpayPayment)) {

                switch ($paymentMethod) {
                    case Gateway::MULTIBANCO:
                        if (!$payment->getAdditionalInformation('referencia')) {
                            $this->ifthenpayGatewayResult = $this->ifthenpayPaymentReturn->setOrder($order)->execute()->getPaymentGatewayResultData();
                            $payment->setAdditionalInformation('entidade', $this->ifthenpayGatewayResult->entidade);
                            $payment->setAdditionalInformation('referencia', $this->ifthenpayGatewayResult->referencia);
                        }
                        break;
                    case Gateway::MBWAY:
                        if (!$payment->getAdditionalInformation('idPedido')) {
                            $this->ifthenpayGatewayResult = $this->ifthenpayPaymentReturn->setOrder($order)->execute()->getPaymentGatewayResultData();
                            $payment->setAdditionalInformation('idPedido', $this->ifthenpayGatewayResult->idPedido);
                            $payment->setAdditionalInformation('telemovel', $this->ifthenpayGatewayResult->telemovel);
                            $payment->setAdditionalInformation('mbwayCountdownShow', true);
                        }
                        break;
                    case Gateway::PAYSHOP:
                        if (!$payment->getAdditionalInformation('referencia')) {
                            $this->ifthenpayGatewayResult = $this->ifthenpayPaymentReturn->setOrder($order)->execute()->getPaymentGatewayResultData();
                            $payment->setAdditionalInformation('idPedido', $this->ifthenpayGatewayResult->idPedido);
                            $payment->setAdditionalInformation('referencia', $this->ifthenpayGatewayResult->referencia);
                            $payment->setAdditionalInformation('validade', $this->ifthenpayGatewayResult->validade);
                        }
                        break;
                    default:
                        break;
                }
                $payment->setAdditionalInformation('totalToPay', $this->ifthenpayGatewayResult->totalToPay);
                $payment->setAdditionalInformation('status', 'success');
                $this->logger->debug('Payment return offline payment executed with success', [
                    'paymentMethod' => $paymentMethod,
                    'order' => $order->getData(),
                    'ifthenpayGatewayResult' => $this->ifthenpayGatewayResult
                ]);
            }
        } catch (\Throwable $th) {
            $payment->setAdditionalInformation('status', 'error');
            $this->logger->debug('Error executing Payment return offline payment', [
                'error' => $th,
                'errorMessage' => $th->getMessage(),
                'paymentMethod' => $paymentMethod,
                'order' => $order->getData(),
            ]);
            throw $th;
        }
    }
}
