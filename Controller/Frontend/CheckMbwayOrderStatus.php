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
use Ifthenpay\Payment\Logger\IfthenpayLogger;
use Ifthenpay\Payment\Helper\Factory\DataFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Ifthenpay\Payment\Lib\Builders\GatewayDataBuilder;
use Ifthenpay\Payment\Lib\Payments\MbwayPaymentStatus;


class CheckMbwayOrderStatus extends Action
{

    private $resultJsonFactory;
    private $logger;
    private $configData;
    private $mbwayPaymentStatus;
    private $gatewayDataBuilder;

    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        DataFactory $dataFactory,
        MbwayPaymentStatus $mbwayPaymentStatus,
        GatewayDataBuilder $gatewayDataBuilder,
        IfthenpayLogger $logger
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->configData = $dataFactory->setType('mbway')->build()->getConfig();
        $this->mbwayPaymentStatus = $mbwayPaymentStatus;
        $this->gatewayDataBuilder = $gatewayDataBuilder;
        $this->logger = $logger;
    }

    public function execute()
    {
        try {
            $requestData = $this->getRequest()->getParams();
            $this->gatewayDataBuilder->setMbwayKey($this->configData['mbwayKey']);
            $this->gatewayDataBuilder->setIdPedido((string)$requestData['idPedido']);
            if ($this->mbwayPaymentStatus->setData($this->gatewayDataBuilder)->getPaymentStatus()) {
                $this->logger->debug('CheckMbwayStatus: Mbway payment status is paid');
                return $this->resultJsonFactory->create()->setData(['orderStatus' => 'paid']);
            } else {
                $this->logger->debug('CheckMbwayStatus: Mbway payment status is not paid');
                return $this->resultJsonFactory->create()->setData(['orderStatus' => 'pending']);
            }
            
        } catch (\Throwable $th) {
            $this->logger->debug('CheckMbwayStatus: Error checking mbway status - ' . $th->getMessage());
            return $this->resultJsonFactory->create()->setData(['error' => $th->getMessage()]);
        }
    }
}