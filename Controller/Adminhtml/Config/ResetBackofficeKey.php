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

namespace Ifthenpay\Payment\Controller\Adminhtml\Config;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Ifthenpay\Payment\Logger\IfthenpayLogger;
use Ifthenpay\Payment\Helper\Factory\DataFactory;
use Ifthenpay\Payment\Lib\Payments\Gateway;
use Magento\Framework\Controller\Result\JsonFactory;


class ResetBackofficeKey extends Action
{
    private $resultJsonFactory;
    private $dataFactory;
    private $logger;

    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        DataFactory $dataFactory,
        IfthenpayLogger $logger
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->dataFactory = $dataFactory;
        $this->logger = $logger;
    }

    public function execute()
    {
        try {
            $data = $this->dataFactory->setType(Gateway::MULTIBANCO)->build();
            foreach ($data->getUserPaymentMethods() as $paymentMethod) {
                $this->dataFactory->setType($paymentMethod)->build()->deleteConfig();
                $this->logger->debug($paymentMethod . ' configuration deleted with success');
            }
            $data->deleteBackofficeKey();
            $this->logger->debug('Backoffice key deleted with success');

            return $this->resultJsonFactory->create()->setData(['success' => true]);
        } catch (\Throwable $th) {
            $this->logger->debug('Error Reseting backofficeKey', [
                'error' => $th,
                'errorMessage' => $th->getMessage()
            ]);
            return $this->resultJsonFactory->create()->setData(['error' => true]);
        }
    }
}
