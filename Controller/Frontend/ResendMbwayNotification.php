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

namespace Ifthenpay\Payment\Controller\Frontend;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Ifthenpay\Payment\Lib\Payments\Gateway;
use Ifthenpay\Payment\Logger\IfthenpayLogger;
use \Magento\Sales\Api\OrderRepositoryInterface;
use Ifthenpay\Payment\Helper\Factory\DataFactory;
use Ifthenpay\Payment\Api\MbwayRepositoryInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Ifthenpay\Payment\Lib\Builders\GatewayDataBuilder;


class ResendMbwayNotification extends Action
{

    private $resultJsonFactory;
    private $mbwayRepository;
    private $ifthenpayGateway;
    private $gatewayDataBuilder;
    private $dataFactory;
    private $logger;

    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        MbwayRepositoryInterface $mbwayRepository,
        OrderRepositoryInterface $orderRepository,
        Gateway $ifthenpayGateway,
        GatewayDataBuilder $gatewayDataBuilder,
        DataFactory $dataFactory,
        IfthenpayLogger $logger
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->mbwayRepository = $mbwayRepository;
        $this->orderRepository = $orderRepository;
        $this->ifthenpayGateway = $ifthenpayGateway;
        $this->gatewayDataBuilder = $gatewayDataBuilder;
        $this->dataFactory = $dataFactory;
        $this->logger = $logger;
    }

    public function execute()
    {
        try {
            $requestData = $this->getRequest()->getParams();

            $mbwayPayment = $this->mbwayRepository->getByOrderId($requestData['orderId']);
            $configData = $this->dataFactory->setType(Gateway::MBWAY)->build()->getConfig();

            $paymentData = $this->gatewayDataBuilder
                ->setMbwayKey($configData['mbwayKey'])
                ->setTelemovel($requestData['mbwayPhoneNumber']);
            $gatewayResult = $this->ifthenpayGateway->execute(
                Gateway::MBWAY,
                $paymentData,
                strval($requestData['orderId']),
                strval($requestData['mbwayTotalToPay'])
            )->getData();

            $mbwayPayment->setId_transacao($gatewayResult->idPedido);
            $this->mbwayRepository->save($mbwayPayment);
            $this->logger->debug('Mbway notification resend with success', [
                'requestData' => $requestData,
                'mbwayPayment' => $mbwayPayment,
                'configData' => $configData,
                'gatewayResult' => $gatewayResult
            ]);
            return $this->resultJsonFactory->create()->setData(['success' => __('resendMbwayNotificationSuccess')]);
        } catch (\Throwable $th) {
            $this->logger->debug('Error resending mbway notification', [
                'error' => $th,
                'errorMessage' => $th->getMessage(),
                'requestData' => $requestData,
                'mbwayPayment' => $mbwayPayment,
                'configData' => $configData,
            ]);
            return $this->resultJsonFactory->create()->setData(['error' => __('resendMbwayNotificationError')]);
        }
    }
}
