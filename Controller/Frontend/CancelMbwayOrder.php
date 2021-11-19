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
use Ifthenpay\Payment\Api\MbwayRepositoryInterface;
use Magento\Framework\Controller\Result\JsonFactory;


class CancelMbwayOrder extends Action
{

    private $resultJsonFactory;
    private $mbwayRepository;
    private $logger;

    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        MbwayRepositoryInterface $mbwayRepository,
        IfthenpayLogger $logger
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->mbwayRepository = $mbwayRepository;
        $this->logger = $logger;
    }

    public function execute()
    {
        try {
            $requestData = $this->getRequest()->getParams();
            $mbwayPayment = $this->mbwayRepository->getByOrderId($requestData['orderId']);
            $this->logger->debug('Cancel Mbway order with success', [
                'requestData' => $requestData,
                'mbwayPayment' => $mbwayPayment
            ]);
            return $this->resultJsonFactory->create()->setData(['orderStatus' => $mbwayPayment['status']]);
        } catch (\Throwable $th) {
            $this->logger->debug('Error cancel mbway order', [
                'error' => $th,
                'errorMessage' => $th->getMessage(),
                'requestData' => $requestData,
                'mbwayPayment' => $mbwayPayment
            ]);
            return $this->resultJsonFactory->create()->setData(['error' => $th->getMessage()]);
        }
    }
}
