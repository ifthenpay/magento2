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

namespace Ifthenpay\Payment\Cron;

use Magento\Sales\Model\Order;
use Ifthenpay\Payment\Logger\IfthenpayLogger;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Ifthenpay\Payment\Helper\Factory\DataFactory;
use Ifthenpay\Payment\Lib\Builders\GatewayDataBuilder;
use Ifthenpay\Payment\Lib\Payments\MbwayPaymentStatus;

class CancelMbwayOrder {

  protected $orderRepository;
  protected $searchCriteriaBuilder;
  protected $configData;
  protected $mbwayPaymentStatus;
  protected $gatewayDataBuilder;
  protected $logger;



  public function __construct(
    OrderRepositoryInterface $orderRepository,
    SearchCriteriaBuilder $searchCriteriaBuilder,
    DataFactory $dataFactory,
    MbwayPaymentStatus $mbwayPaymentStatus,
    GatewayDataBuilder $gatewayDataBuilder,
    IfthenpayLogger $logger
) {
    $this->orderRepository = $orderRepository;
    $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    $this->configData = $dataFactory->setType('mbway')->build()->getConfig();
    $this->mbwayPaymentStatus = $mbwayPaymentStatus;
    $this->gatewayDataBuilder = $gatewayDataBuilder;
    $this->logger = $logger;
}

    public function execute(): void
    {
        try {
            $this->logger->debug('Cron Works mbway');
            if ($this->configData['cancelMbwayOrder']) {
                $searchCriteria = $this->searchCriteriaBuilder->addFilter('status', 'pending', 'eq')->create();
                $orders = $this->orderRepository->getList($searchCriteria);
                foreach ($orders->getItems() as $order) {
                    $payment = $order->getPayment();
                    $idPedido = $order->getPayment()->getAdditionalInformation('idPedido');
                    if ($payment->getMethod() === 'mbway' && $idPedido) {
                        $this->gatewayDataBuilder->setMbwayKey($this->configData['mbwayKey']);
                        $this->gatewayDataBuilder->setIdPedido((string) $order->getPayment()->getAdditionalInformation('idPedido'));
                        if (!$this->mbwayPaymentStatus->setData($this->gatewayDataBuilder)->getPaymentStatus()) {
                            date_default_timezone_set('Europe/Lisbon');
                            $minutes_to_add = 30;
                            $time = new \DateTime($order->getCreatedAt());
                            $time->add(new \DateInterval('PT' . $minutes_to_add . 'M'));
                            $today = new \DateTime(date("Y-m-d G:i"));

                            if ($time < $today) {
                                $order->setState(Order::STATE_CANCELED)
                                ->setStatus($order->getConfig()->getStateDefaultStatus(Order::STATE_CANCELED));
                                $this->orderRepository->save($order);
                            }
                        }
                    }
                };
                $this->logger->debug('Cron Cancel Mbway order: Cron cancel mbway order executed with success');
            }
        } catch (\Throwable $th) {
            $this->logger->debug('Cron Cancel Mbway order: Error executing cron cancel mbway order - ' . $th->getMessage());
            throw $th;
        }
    }
}
