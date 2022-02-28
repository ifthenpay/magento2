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

namespace Ifthenpay\Payment\Lib\Payments\Cancel;

use Ifthenpay\Payment\Lib\Payments\Gateway;
use Ifthenpay\Payment\Lib\Payments\Cancel\CancelOrder;
use Ifthenpay\Payment\Lib\Payments\Multibanco;

class CancelMultibancoOrder extends CancelOrder {

    protected $paymentMethod = Gateway::MULTIBANCO;

    public function cancelOrder(): void
    {
        try {
            if ($this->configData['cancelOrder'] && $this->configData['validade']) {
                $this->setPendingOrders();
                if ($this->pendingOrders->getSize()) {
                    foreach ($this->pendingOrders as $order) {
                        $referencia = $order->getPayment()->getAdditionalInformation('referencia');
                        $validade = $order->getPayment()->getAdditionalInformation('validade');
                        if ($referencia && $validade) {
                            $this->setGatewayDataBuilderBackofficeKey();
                            $this->gatewayDataBuilder->setEntidade($this->configData['entidade']);
                            $this->gatewayDataBuilder->setSubEntidade($this->configData['subEntidade']);
                            $this->gatewayDataBuilder->setReferencia($order->getPayment()->getAdditionalInformation('referencia'));
                            $this->gatewayDataBuilder->setTotalToPay($order->getGrandTotal());
                            if (!$this->paymentStatus->setData($this->gatewayDataBuilder)->getPaymentStatus()) {
                                if ($validade) {
                                    $this->checkTimeChangeStatus($order, null, $validade, 'd-m-Y');

                                } else {
                                    $this->checkTimeChangeStatus($order, $this->configData['validade'], null, null);
                                }
                            }
                            $this->logCancelOrder(Gateway::MULTIBANCO, $referencia, $order->getData());
                        }
                    };
                }
            }
        } catch (\Throwable $th) {
            $this->logErrorCancelOrder(Gateway::MULTIBANCO, $th);
            throw $th;
        }
    }
}
